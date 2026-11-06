<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\DeliveryZone;
use App\Models\AuditLog;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $query = Branch::with(['manager', 'deliveryZones']);

        if ($request->has('active_only') && $request->active_only) {
            $query->where('status', 'Active');
        }

        $branches = $query->orderBy('branch_type', 'asc')->orderBy('branch_name', 'asc')->get();
        return response()->json($branches);
    }

    public function show($id)
    {
        $branch = Branch::with(['manager', 'deliveryZones', 'inventory.product'])->findOrFail($id);
        return response()->json($branch);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_code' => 'required|string|unique:branches,branch_code|max:50',
            'branch_type' => 'required|in:Main,Regular',
            'region' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'opening_hours' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,user_id',
        ]);

        if ($validated['branch_type'] === 'Main') {
            $existingMain = Branch::where('branch_type', 'Main')->first();
            if ($existingMain) {
                return response()->json(['message' => 'Only one main branch is allowed.'], 422);
            }
        }

        $branch = Branch::create($validated);

        // Audit log
        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'Branch created',
            'record' => $branch->branch_code,
            'entity_type' => 'Branch',
            'entity_id' => (string) $branch->branch_id,
            'old_value' => '—',
            'new_value' => $branch->branch_name,
            'created_at' => now(),
        ]);

        return response()->json($branch->load('deliveryZones'), 201);
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'branch_name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string',
            'address' => 'sometimes|string',
            'opening_hours' => 'sometimes|string',
            'status' => 'sometimes|in:Active,Inactive',
            'manager_id' => 'nullable|exists:users,user_id',
        ]);

        // Constraint check: cannot deactivate branch if open orders or transfers in transit
        if (isset($validated['status']) && $validated['status'] === 'Inactive' && $branch->status === 'Active') {
            $hasOpenOrders = $branch->orders()->whereIn('order_status', ['Pending', 'Confirmed', 'Preparing', 'Ready', 'Out for delivery'])->exists();
            $hasActiveTransfers = $branch->transfersReceived()->whereIn('status', ['Dispatched', 'In transit', 'Partially received'])->exists();

            if ($hasOpenOrders || $hasActiveTransfers) {
                return response()->json([
                    'message' => 'Cannot deactivate branch while open orders or active stock transfers exist.'
                ], 422);
            }
        }

        $oldStatus = $branch->status;
        $branch->update($validated);

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Admin',
            'action' => 'Branch updated',
            'record' => $branch->branch_code,
            'entity_type' => 'Branch',
            'entity_id' => (string) $branch->branch_id,
            'old_value' => $oldStatus,
            'new_value' => $branch->status,
            'created_at' => now(),
        ]);

        return response()->json($branch->load('deliveryZones'));
    }

    public function deliveryZones($id)
    {
        $branch = Branch::findOrFail($id);
        return response()->json($branch->deliveryZones);
    }

    public function addDeliveryZone(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'zone_name' => 'required|string|max:255',
            'delivery_fee' => 'required|numeric|min:0',
            'status' => 'sometimes|in:Active,Inactive',
        ]);

        $zone = DeliveryZone::create([
            'branch_id' => $branch->branch_id,
            'zone_name' => $validated['zone_name'],
            'delivery_fee' => $validated['delivery_fee'],
            'status' => $validated['status'] ?? 'Active',
        ]);

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'Delivery zone added',
            'record' => $branch->branch_name . ' · ' . $zone->zone_name,
            'entity_type' => 'DeliveryZone',
            'entity_id' => (string) $zone->zone_id,
            'old_value' => '—',
            'new_value' => 'ETB ' . number_format($zone->delivery_fee, 0),
            'created_at' => now(),
        ]);

        return response()->json($zone, 201);
    }

    public function deleteDeliveryZone(Request $request, $id, $zoneId)
    {
        $branch = Branch::findOrFail($id);
        $zone = DeliveryZone::where('branch_id', $branch->branch_id)->where('zone_id', $zoneId)->firstOrFail();
        $zoneName = $zone->zone_name;
        $zone->delete();

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'Delivery zone removed',
            'record' => $branch->branch_name . ' · ' . $zoneName,
            'entity_type' => 'DeliveryZone',
            'entity_id' => (string) $zoneId,
            'old_value' => $zoneName,
            'new_value' => 'Removed',
            'created_at' => now(),
        ]);

        return response()->json(['message' => 'Delivery zone deleted successfully.']);
    }
}
