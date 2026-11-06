<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Branch;
use App\Models\AuditLog;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff'])) {
            return response()->json(['message' => 'Branch Admin and Staff are not authorized to view user management.'], 403);
        }

        $role = $request->query('role');
        $branchId = $request->query('branch_id');
        $tab = $request->query('tab', 'staff'); // 'staff' or 'customers'

        $query = User::with('branch');

        if ($user && $user->role === 'Main Branch Manager') {
            $mainBranch = Branch::where('branch_type', 'Main')->first();
            if ($mainBranch && $tab !== 'customers') {
                $query->where('branch_id', $mainBranch->branch_id);
            }
        }

        if ($tab === 'customers') {
            $query->where('role', 'Customer');
        } else {
            $query->where('role', '!=', 'Customer');
        }

        if ($role && $role !== 'all') {
            $query->where('role', $role);
        }

        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        $users = $query->get()->map(function ($u) {
            $initials = '';
            $parts = explode(' ', $u->full_name);
            foreach ($parts as $p) {
                $initials .= strtoupper(substr($p, 0, 1));
            }
            if (strlen($initials) < 2) $initials = strtoupper(substr($u->role, 0, 2));

            return [
                'user_id' => $u->user_id,
                'initials' => substr($initials, 0, 2),
                'full_name' => $u->full_name,
                'email' => $u->email,
                'phone' => $u->phone,
                'role' => $u->role,
                'branch_id' => $u->branch_id,
                'branch_name' => $u->branch ? $u->branch->branch_name : 'All branches',
                'last_sign_in' => 'Today',
                'status' => $u->status,
            ];
        });

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $currentUser = $request->user();
        if ($currentUser && in_array($currentUser->role, ['Branch Admin', 'Branch Staff'])) {
            return response()->json(['message' => 'Branch Admin and Staff are not authorized to create users.'], 403);
        }

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:Super Admin,Main Branch Manager,Branch Admin,Branch Staff,Customer',
            'branch_id' => 'nullable|exists:branches,branch_id',
        ]);

        if ($currentUser && $currentUser->role === 'Main Branch Manager') {
            $mainBranch = Branch::where('branch_type', 'Main')->first();
            if ($validated['role'] !== 'Branch Staff' && $validated['role'] !== 'Branch Admin') {
                return response()->json(['message' => 'Main Branch Manager can only create staff for the main branch.'], 403);
            }
            $validated['branch_id'] = $mainBranch?->branch_id;
        }

        // Constraint from spec Page 14:
        // Branch Admin and Branch Staff must have a branch. Customers and Super Admins have none.
        if (in_array($validated['role'], ['Branch Admin', 'Branch Staff']) && empty($validated['branch_id'])) {
            return response()->json(['message' => 'Branch Admin and Branch Staff must have an assigned branch.'], 422);
        }

        if (in_array($validated['role'], ['Super Admin', 'Customer'])) {
            $validated['branch_id'] = null;
        }

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'branch_id' => $validated['branch_id'],
            'password' => Hash::make('password123'),
            'status' => 'Active',
        ]);

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'User created',
            'record' => $user->full_name,
            'entity_type' => 'User',
            'entity_id' => (string) $user->user_id,
            'old_value' => '—',
            'new_value' => "{$user->role} · " . ($user->branch ? $user->branch->branch_name : 'All branches'),
            'created_at' => now(),
        ]);

        return response()->json($user->load('branch'), 201);
    }

    public function update(Request $request, $id)
    {
        $currentUser = $request->user();
        if ($currentUser && in_array($currentUser->role, ['Branch Admin', 'Branch Staff'])) {
            return response()->json(['message' => 'Branch Admin and Staff are not authorized to update users.'], 403);
        }

        $user = User::findOrFail($id);

        if ($currentUser && $currentUser->role === 'Main Branch Manager') {
            $mainBranch = Branch::where('branch_type', 'Main')->first();
            if ($user->branch_id != $mainBranch?->branch_id) {
                return response()->json(['message' => 'Main Branch Manager can only update users assigned to the Main Branch.'], 403);
            }
        }

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string',
            'role' => 'sometimes|in:Super Admin,Main Branch Manager,Branch Admin,Branch Staff,Customer',
            'branch_id' => 'nullable|exists:branches,branch_id',
            'status' => 'sometimes|in:Active,Inactive',
        ]);

        if (isset($validated['role'])) {
            if (in_array($validated['role'], ['Branch Admin', 'Branch Staff']) && empty($validated['branch_id']) && empty($user->branch_id)) {
                return response()->json(['message' => 'Branch Admin and Branch Staff must have an assigned branch.'], 422);
            }
            if (in_array($validated['role'], ['Super Admin', 'Customer'])) {
                $validated['branch_id'] = null;
            }
        }

        $oldVal = "{$user->role} · " . ($user->branch ? $user->branch->branch_name : 'All branches') . " · {$user->status}";
        $user->update($validated);
        $user->load('branch');
        $newVal = "{$user->role} · " . ($user->branch ? $user->branch->branch_name : 'All branches') . " · {$user->status}";

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'User updated',
            'record' => $user->full_name,
            'entity_type' => 'User',
            'entity_id' => (string) $user->user_id,
            'old_value' => $oldVal,
            'new_value' => $newVal,
            'created_at' => now(),
        ]);

        return response()->json($user);
    }
}
