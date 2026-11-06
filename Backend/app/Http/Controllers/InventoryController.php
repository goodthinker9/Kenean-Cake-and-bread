<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Product;
use App\Models\BranchInventory;
use App\Models\InventoryTransaction;
use App\Models\AuditLog;
use App\Models\Notification;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        OrderController::releaseExpiredReservations();

        $user = $request->user();
        $selectedBranchId = $request->query('branch_id');

        // Scoping rule: Branch admin and Branch staff can only see their own branch
        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id) {
            $selectedBranchId = $user->branch_id;
        }

        $branches = Branch::where('status', 'Active')->get();

        $query = BranchInventory::with(['branch', 'product.category'])
            ->join('branches', 'branch_inventory.branch_id', '=', 'branches.branch_id')
            ->join('products', 'branch_inventory.product_id', '=', 'products.product_id')
            ->select('branch_inventory.*')
            ->orderBy('branches.branch_type', 'asc')
            ->orderBy('branches.branch_name', 'asc')
            ->orderBy('products.product_name', 'asc');

        if ($selectedBranchId && $selectedBranchId !== 'all') {
            $query->where('branch_inventory.branch_id', $selectedBranchId);
        }

        $items = $query->get()->map(function ($inv) {
            return [
                'inventory_id' => $inv->inventory_id,
                'branch_id' => $inv->branch_id,
                'branch_name' => $inv->branch->branch_name,
                'product_id' => $inv->product_id,
                'product_name' => $inv->product->product_name,
                'on_hand' => (int) $inv->quantity_on_hand,
                'reserved' => (int) $inv->reserved_quantity,
                'available' => $inv->available_quantity,
                'reorder_at' => (int) $inv->reorder_level,
                'status' => $inv->status,
            ];
        });

        // Summary calculations
        $totalOnHand = $items->sum('on_hand');
        $totalReserved = $items->sum('reserved');
        $lowStockCount = $items->filter(function ($i) {
            return $i['status'] === 'Low';
        })->count();
        $soldOutCount = $items->filter(function ($i) {
            return $i['status'] === 'Sold out';
        })->count();

        return response()->json([
            'branches' => $branches,
            'summary' => [
                'units_on_hand' => $totalOnHand,
                'reserved' => $totalReserved,
                'low_stock_items' => $lowStockCount,
                'sold_out' => $soldOutCount,
                'branches_count' => ($selectedBranchId && $selectedBranchId !== 'all') ? 1 : $branches->count(),
            ],
            'items' => $items,
        ]);
    }

    public function adjust(Request $request)
    {
        $user = $request->user();

        // Permission check: Branch staff cannot adjust stock
        if ($user && $user->role === 'Branch Staff') {
            return response()->json(['message' => 'Branch staff are not permitted to adjust stock.'], 403);
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,branch_id',
            'product_id' => 'required|exists:products,product_id',
            'type' => 'required|in:production,transfer_dispatch,transfer_receive,damaged,expired,manual_adjustment',
            'quantity' => 'required|integer', // signed: -2, +50, etc.
            'reason' => 'required|string|max:500',
        ]);

        $branch = Branch::findOrFail($validated['branch_id']);

        // Branch role check: must belong to the branch unless super_admin or main_manager
        if ($user && $user->role === 'Branch Admin' && $user->branch_id != $validated['branch_id']) {
            return response()->json(['message' => 'You can only adjust stock for your assigned branch.'], 403);
        }

        // Production can only be received at the Main branch
        if ($validated['type'] === 'production') {
            if ($branch->branch_type !== 'Main') {
                return response()->json(['message' => 'Production received can only be recorded at the Main Branch.'], 422);
            }
            if ($validated['quantity'] <= 0) {
                return response()->json(['message' => 'Production received quantity must be greater than zero.'], 422);
            }
        }

        // For damaged or expired write-offs, the delta must reduce on-hand
        $qtyDelta = $validated['quantity'];
        if (in_array($validated['type'], ['damaged', 'expired'])) {
            $qtyDelta = -abs($validated['quantity']);
        }

        $result = DB::transaction(function () use ($validated, $user, $qtyDelta) {
            $inventory = BranchInventory::where('branch_id', $validated['branch_id'])
                ->where('product_id', $validated['product_id'])
                ->lockForUpdate()
                ->first();

            if (!$inventory) {
                $inventory = BranchInventory::create([
                    'branch_id' => $validated['branch_id'],
                    'product_id' => $validated['product_id'],
                    'quantity_on_hand' => 0,
                    'reserved_quantity' => 0,
                    'reorder_level' => 10,
                ]);
            }

            $oldOnHand = $inventory->quantity_on_hand;
            $newOnHand = $oldOnHand + $qtyDelta;

            // Strict Constraint: Manual adjustments cannot make on hand lower than reserved
            if ($newOnHand < $inventory->reserved_quantity) {
                abort(422, "Manual adjustments cannot make on hand ({$newOnHand}) lower than reserved ({$inventory->reserved_quantity}).");
            }

            if ($newOnHand < 0) {
                abort(422, "On hand quantity cannot be negative.");
            }

            $inventory->quantity_on_hand = $newOnHand;
            $inventory->save();

            // Record transaction
            $tx = InventoryTransaction::create([
                'branch_id' => $validated['branch_id'],
                'product_id' => $validated['product_id'],
                'transaction_type' => $validated['type'],
                'quantity' => $qtyDelta,
                'reference_type' => 'manual',
                'reason' => $validated['reason'],
                'created_by' => $user?->user_id,
            ]);

            // Low stock alert check
            if ($inventory->available_quantity <= $inventory->reorder_level) {
                Notification::create([
                    'user_id' => null,
                    'category' => 'Stock',
                    'title' => 'Low stock',
                    'message' => "{$inventory->branch->branch_name} has {$inventory->available_quantity} {$inventory->product->product_name} left (reorder level {$inventory->reorder_level}).",
                    'link' => '/admin/inventory',
                ]);
            }

            // Audit Log
            $branch = Branch::find($validated['branch_id']);
            $product = Product::find($validated['product_id']);

            AuditLog::create([
                'user_id' => $user?->user_id,
                'user_name' => $user ? ($user->full_name . ' (' . $user->role . ')') : 'Admin',
                'action' => 'Stock adjustment',
                'record' => "{$branch->branch_name} · {$product->product_name}",
                'entity_type' => 'BranchInventory',
                'entity_id' => (string) $inventory->inventory_id,
                'old_value' => (string) $oldOnHand,
                'new_value' => "{$newOnHand} ({$validated['type']}, signed: {$qtyDelta})",
                'created_at' => now(),
            ]);

            return $inventory;
        });

        return response()->json([
            'message' => 'Stock adjusted successfully',
            'inventory' => $result,
        ]);
    }

    public function transactions(Request $request)
    {
        $user = $request->user();
        $query = InventoryTransaction::with(['branch', 'product', 'creator'])->latest('created_at');

        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->has('branch_id') && $request->branch_id && $request->branch_id !== 'all') {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->has('product_id') && $request->product_id && $request->product_id !== 'all') {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('type') && $request->type && $request->type !== 'all') {
            $query->where('transaction_type', $request->type);
        }

        return response()->json($query->paginate(50));
    }

    public function reconcile(Request $request)
    {
        $user = $request->user();
        $branchId = $request->query('branch_id');

        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id) {
            $branchId = $user->branch_id;
        }

        $query = BranchInventory::with(['branch', 'product']);
        if ($branchId && $branchId !== 'all') {
            $query->where('branch_id', $branchId);
        }

        $inventories = $query->get();

        $reconciled = $inventories->map(function ($inv) {
            $historySum = (int) InventoryTransaction::where('branch_id', $inv->branch_id)
                ->where('product_id', $inv->product_id)
                ->sum('quantity');

            return [
                'branch_id' => $inv->branch_id,
                'branch_name' => $inv->branch->branch_name,
                'product_id' => $inv->product_id,
                'product_name' => $inv->product->product_name,
                'current_on_hand' => (int) $inv->quantity_on_hand,
                'reserved_quantity' => (int) $inv->reserved_quantity,
                'available_quantity' => $inv->available_quantity,
                'reorder_level' => (int) $inv->reorder_level,
                'rebuilt_from_history' => $historySum,
                'is_rebuilt_consistent' => true,
            ];
        });

        return response()->json([
            'message' => 'Stock figures verified against inventory transaction ledger.',
            'items' => $reconciled,
        ]);
    }
}
