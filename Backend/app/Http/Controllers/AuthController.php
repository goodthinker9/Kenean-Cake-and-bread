<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Branch;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid email or password',
            ], 401);
        }

        if ($user->status !== 'Active') {
            return response()->json([
                'message' => 'This account has been deactivated.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        AuditLog::create([
            'user_id' => $user->user_id,
            'user_name' => $user->full_name . ' (' . $user->role . ')',
            'action' => 'Login',
            'record' => $user->role,
            'entity_type' => 'Auth',
            'entity_id' => (string) $user->user_id,
            'old_value' => '—',
            'new_value' => 'Success',
            'created_at' => now(),
        ]);

        return response()->json([
            'token' => $token,
            'user' => $user->load('branch'),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:30',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'Customer',
            'branch_id' => null,
            'status' => 'Active',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function user(Request $request)
    {
        return response()->json($request->user()->load('branch'));
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    // Role switcher helper for demonstration & evaluation
    public function switchRole(Request $request)
    {
        $validated = $request->validate([
            'role' => 'required|string',
            'branch_code' => 'nullable|string',
        ]);

        $query = User::where('role', $validated['role']);
        if (!empty($validated['branch_code'])) {
            $branch = Branch::where('branch_code', $validated['branch_code'])->first();
            if ($branch) {
                $query->where('branch_id', $branch->branch_id);
            }
        }

        $user = $query->first();

        if (!$user) {
            // fallback to first of that role
            $user = User::where('role', $validated['role'])->first();
        }

        if (!$user) {
            return response()->json(['message' => 'Role user not found'], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('branch'),
        ]);
    }

    public function listUsers()
    {
        $users = User::with('branch')->get();
        return response()->json($users);
    }
}
