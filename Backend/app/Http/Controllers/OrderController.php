<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\Branch;
use App\Models\Product;
use App\Models\BranchInventory;
use App\Models\InventoryTransaction;
use App\Models\AuditLog;
use App\Models\Notification;
use App\Models\DeliveryZone;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        self::releaseExpiredReservations();

        $user = $request->user();
        $query = Order::with(['branch', 'user', 'items.product', 'payment', 'statusHistory']);

        // Scoping rule: branch admin / staff see only their branch
        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id) {
            $query->where('branch_id', $user->branch_id);
        } elseif ($request->has('branch_id') && $request->branch_id && $request->branch_id !== 'all') {
            $query->where('branch_id', $request->branch_id);
        }

        // Customer scoping
        if ($user && $user->role === 'Customer') {
            $query->where('user_id', $user->user_id);
        }

        // Status filter
        if ($request->has('status') && $request->status && $request->status !== 'All') {
            $query->where('order_status', $request->status);
        }

        // Method filter
        if ($request->has('method') && $request->method && $request->method !== 'all') {
            $query->where('fulfillment_method', $request->method);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('phone', 'like', "%{$search}%")
                         ->orWhere('full_name', 'like', "%{$search}%");
                  });
            });
        }

        // Date filter
        if ($request->has('date') && $request->date === 'today') {
            $query->whereDate('placed_at', Carbon::today());
        }

        $orders = $query->latest('placed_at')->get();

        // Calculate counts for status tabs
        $countQuery = Order::query();
        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id) {
            $countQuery->where('branch_id', $user->branch_id);
        }
        $pendingCount = (clone $countQuery)->where('order_status', 'Pending')->count();

        return response()->json([
            'orders' => $orders,
            'pending_count' => $pendingCount,
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['branch', 'user', 'address', 'items.product', 'payments', 'payment', 'statusHistory.user'])
            ->findOrFail($id);

        $user = request()->user();
        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id && $order->branch_id != $user->branch_id) {
            return response()->json(['message' => 'Access denied: You can only view orders for your assigned branch.'], 403);
        }

        return response()->json($order);
    }

    public function store(Request $request)
    {
        self::releaseExpiredReservations();

        $user = $request->user();

        // Harmonize field aliases from storefront
        if (!$request->has('fulfillment_method') && $request->has('order_type')) {
            $request->merge(['fulfillment_method' => $request->order_type]);
        }
        if ($request->has('payment_method') || $request->has('payment_type')) {
            $rawPayment = $request->input('payment_method') ?? $request->input('payment_type');
            $mappedPayment = match ($rawPayment) {
                'Online', 'online', 'Chapa', 'chapa' => 'Chapa',
                'Telebirr', 'telebirr' => 'Telebirr',
                'Pickup', 'Pay on pickup', 'pay_on_pickup' => 'Pay on pickup',
                'Cash on Delivery', 'COD', 'cod', 'Pay on delivery', 'pay_on_delivery' => 'Pay on delivery',
                default => 'Pay on delivery',
            };
            $request->merge(['payment_method' => $mappedPayment]);
        }

        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,branch_id',
            'fulfillment_method' => 'required|in:Delivery,Pickup',
            'address_id' => 'nullable|exists:addresses,address_id',
            'delivery_address' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:Chapa,Telebirr,Pay on pickup,Pay on delivery',
        ]);

        $branch = Branch::findOrFail($validated['branch_id']);
        if ($branch->status !== 'Active') {
            return response()->json(['message' => 'Selected branch is currently inactive.'], 422);
        }

        // Auto-create or resolve address if delivery
        if ($validated['fulfillment_method'] === 'Delivery' && empty($validated['address_id'])) {
            $address = \App\Models\Address::create([
                'user_id' => $user?->user_id ?? 8,
                'city' => $request->city ?? 'Addis Ababa',
                'region' => $branch->branch_name,
                'street' => $request->delivery_address ?? 'Bole Road, Addis Ababa',
                'notes' => $request->order_notes ?? 'Deliver to doorstep',
                'is_default' => true,
            ]);
            $validated['address_id'] = $address->address_id;
        }

        $deliveryFee = 0.00;
        if ($validated['fulfillment_method'] === 'Delivery') {
            if ($request->filled('zone_id') || $request->filled('delivery_zone_id')) {
                $zoneId = $request->input('zone_id') ?? $request->input('delivery_zone_id');
                $zone = DeliveryZone::where('branch_id', $branch->branch_id)
                    ->where('zone_id', $zoneId)
                    ->where('status', 'Active')
                    ->first();
                if (!$zone) {
                    return response()->json(['message' => 'The selected delivery zone is not an active delivery zone for this branch.'], 422);
                }
                $deliveryFee = (float) $zone->delivery_fee;
            } else {
                $activeZone = DeliveryZone::where('branch_id', $branch->branch_id)->where('status', 'Active')->first();
                if (!$activeZone) {
                    return response()->json(['message' => "{$branch->branch_name} does not have active delivery zones. Please select Shop Pickup or another branch."], 422);
                }
                $deliveryFee = (float) $activeZone->delivery_fee;
            }
        }

        $order = DB::transaction(function () use ($validated, $branch, $deliveryFee, $user) {
            // 1. Generate Order Number
            $rand = mt_rand(1000, 9999);
            $orderNumber = "ORD-{$rand}";
            while (Order::where('order_number', $orderNumber)->exists()) {
                $orderNumber = "ORD-" . mt_rand(1000, 9999);
            }

            // 2. Validate Stock Availability with Row Locks
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);
                $qty = (int) $item['quantity'];

                $inv = BranchInventory::where('branch_id', $branch->branch_id)
                    ->where('product_id', $product->product_id)
                    ->lockForUpdate()
                    ->first();

                $available = $inv ? max(0, $inv->quantity_on_hand - $inv->reserved_quantity) : 0;

                if ($available < $qty) {
                    abort(422, "Insufficient stock for {$product->product_name} at {$branch->branch_name}. Requested: {$qty}, Available: {$available}.");
                }

                // Reserve stock
                $inv->reserved_quantity += $qty;
                $inv->save();

                // Low stock alert check: notify branch admin and Main Manager if stock reaches reorder level
                if ($inv->available_quantity <= $inv->reorder_level) {
                    Notification::create([
                        'user_id' => null,
                        'category' => 'Stock',
                        'title' => 'Low stock',
                        'message' => "{$branch->branch_name} has {$inv->available_quantity} {$product->product_name} left (at or below reorder level {$inv->reorder_level}).",
                        'link' => '/admin/inventory',
                    ]);
                }

                // Snapshot price (branch override if exists, else base price)
                $unitPrice = $product->getPriceForBranch($branch->branch_id);
                $lineTotal = $unitPrice * $qty;
                $subtotal += $lineTotal;

                $itemsData[] = [
                    'product_id' => $product->product_id,
                    'product_name' => $product->product_name,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineTotal,
                ];

                InventoryTransaction::create([
                    'branch_id' => $branch->branch_id,
                    'product_id' => $product->product_id,
                    'transaction_type' => 'order_placed',
                    'quantity' => $qty,
                    'reference_type' => 'Order',
                    'reason' => "Stock reserved for order {$orderNumber}",
                    'created_by' => $user?->user_id,
                ]);
            }

            $totalAmount = $subtotal + $deliveryFee;

            $initialStatus = in_array($validated['payment_method'], ['Pay on pickup', 'Pay on delivery']) ? 'Confirmed' : 'Pending';

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user?->user_id ?? 1,
                'branch_id' => $branch->branch_id,
                'address_id' => $validated['address_id'] ?? null,
                'fulfillment_method' => $validated['fulfillment_method'],
                'order_status' => $initialStatus,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => 0.00,
                'total_amount' => $totalAmount,
                'placed_at' => now(),
            ]);

            foreach ($itemsData as $item) {
                OrderItem::create([
                    'order_id' => $order->order_id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            // Status history
            OrderStatusHistory::create([
                'order_id' => $order->order_id,
                'old_status' => null,
                'new_status' => $initialStatus,
                'changed_by' => $user?->user_id,
                'note' => 'placed by customer',
                'changed_at' => now(),
            ]);

            // Payment record
            $paymentStatus = in_array($validated['payment_method'], ['Pay on pickup', 'Pay on delivery']) ? 'Pending' : 'Paid';
            $txRef = in_array($validated['payment_method'], ['Pay on pickup', 'Pay on delivery']) ? null : 'CH-' . strtoupper(substr(md5(uniqid()), 0, 6));

            Payment::create([
                'order_id' => $order->order_id,
                'payment_method' => $validated['payment_method'],
                'amount' => $totalAmount,
                'payment_status' => $paymentStatus,
                'transaction_reference' => $txRef,
            ]);

            if ($paymentStatus === 'Paid') {
                OrderStatusHistory::create([
                    'order_id' => $order->order_id,
                    'old_status' => 'Pending',
                    'new_status' => 'Pending',
                    'changed_by' => null,
                    'note' => 'Payment verified by webhook',
                    'changed_at' => now(),
                ]);
            }

            AuditLog::create([
                'user_id' => $user?->user_id,
                'user_name' => $user ? ($user->full_name . ' (' . $user->role . ')') : 'Customer',
                'action' => 'Order created',
                'record' => $orderNumber,
                'entity_type' => 'Order',
                'entity_id' => (string) $order->order_id,
                'old_value' => '—',
                'new_value' => $initialStatus . ' (ETB ' . number_format($totalAmount, 0) . ')',
                'created_at' => now(),
            ]);

            Notification::create([
                'user_id' => null,
                'category' => 'Orders',
                'title' => "New Order {$orderNumber}",
                'message' => "Order {$orderNumber} placed for {$branch->branch_name} (ETB " . number_format($totalAmount, 0) . ")",
                'link' => '/admin/orders',
            ]);

            return $order;
        });

        return response()->json($order->load(['branch', 'items.product', 'payment']), 201);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items')->findOrFail($id);
        $user = $request->user();

        if ($user && in_array($user->role, ['Branch Admin', 'Branch Staff']) && $user->branch_id && $order->branch_id != $user->branch_id) {
            return response()->json(['message' => 'Access denied: You can only manage orders for your assigned branch.'], 403);
        }

        if (!$request->has('order_status') && $request->has('status')) {
            $request->merge(['order_status' => $request->status]);
        }

        $validated = $request->validate([
            'order_status' => 'required|in:Pending,Confirmed,Preparing,Ready,Out for delivery,Completed,Cancelled',
            'note' => 'nullable|string',
        ]);

        $oldStatus = $order->order_status;
        $newStatus = $validated['order_status'];

        if ($oldStatus === $newStatus) {
            return response()->json($order);
        }

        DB::transaction(function () use ($order, $oldStatus, $newStatus, $validated, $user) {
            // When order is Completed: on_hand and reserved decrease
            if ($newStatus === 'Completed' && $oldStatus !== 'Completed') {
                foreach ($order->items as $item) {
                    $inv = BranchInventory::where('branch_id', $order->branch_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if ($inv) {
                        $inv->quantity_on_hand = max(0, $inv->quantity_on_hand - $item->quantity);
                        $inv->reserved_quantity = max(0, $inv->reserved_quantity - $item->quantity);
                        $inv->save();
                    }

                    InventoryTransaction::create([
                        'branch_id' => $order->branch_id,
                        'product_id' => $item->product_id,
                        'transaction_type' => 'order_completed',
                        'quantity' => -$item->quantity,
                        'reference_type' => 'Order',
                        'reference_id' => $order->order_id,
                        'reason' => "Order {$order->order_number} completed",
                        'created_by' => $user?->user_id,
                    ]);
                }
            }

            // When order is Cancelled: release reserved quantity
            if ($newStatus === 'Cancelled' && $oldStatus !== 'Cancelled' && $oldStatus !== 'Completed') {
                foreach ($order->items as $item) {
                    $inv = BranchInventory::where('branch_id', $order->branch_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if ($inv) {
                        $inv->reserved_quantity = max(0, $inv->reserved_quantity - $item->quantity);
                        $inv->save();
                    }

                    InventoryTransaction::create([
                        'branch_id' => $order->branch_id,
                        'product_id' => $item->product_id,
                        'transaction_type' => 'order_cancelled',
                        'quantity' => -$item->quantity,
                        'reference_type' => 'Order',
                        'reference_id' => $order->order_id,
                        'reason' => "Order {$order->order_number} cancelled, reservation released",
                        'created_by' => $user?->user_id,
                    ]);
                }
            }

            $order->order_status = $newStatus;
            $order->save();

            OrderStatusHistory::create([
                'order_id' => $order->order_id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $user?->user_id,
                'note' => $validated['note'] ?? "Status updated to {$newStatus}",
                'changed_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => $user?->user_id,
                'user_name' => $user ? ($user->full_name . ' (' . $user->role . ')') : 'System',
                'action' => 'Order status change',
                'record' => $order->order_number,
                'entity_type' => 'Order',
                'entity_id' => (string) $order->order_id,
                'old_value' => $oldStatus,
                'new_value' => $newStatus,
                'created_at' => now(),
            ]);
        });

        return response()->json($order->load(['items.product', 'payment', 'statusHistory']));
    }

    public static function releaseExpiredReservations()
    {
        $timeoutMinutes = 15;
        $setting = \App\Models\AppSetting::where('key', 'unpaid_order_timeout_minutes')->first();
        if ($setting && is_numeric($setting->value)) {
            $timeoutMinutes = (int) $setting->value;
        }

        $cutoff = Carbon::now()->subMinutes($timeoutMinutes);

        // Find pending online orders created before cutoff that have not been paid
        $expiredOrders = Order::with('items')
            ->where('order_status', 'Pending')
            ->where('placed_at', '<=', $cutoff)
            ->whereHas('payments', function ($q) {
                $q->where('payment_status', 'Pending')
                  ->whereIn('payment_method', ['Chapa', 'Telebirr', 'Online', 'online']);
            })
            ->get();

        $releasedCount = 0;

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order, $timeoutMinutes, &$releasedCount) {
                foreach ($order->items as $item) {
                    $inv = BranchInventory::where('branch_id', $order->branch_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if ($inv) {
                        $inv->reserved_quantity = max(0, $inv->reserved_quantity - $item->quantity);
                        $inv->save();
                    }

                    InventoryTransaction::create([
                        'branch_id' => $order->branch_id,
                        'product_id' => $item->product_id,
                        'transaction_type' => 'order_cancelled',
                        'quantity' => -$item->quantity,
                        'reference_type' => 'Order',
                        'reference_id' => $order->order_id,
                        'reason' => "Unpaid online order reservation timed out after {$timeoutMinutes} minutes",
                        'created_by' => null,
                    ]);
                }

                $order->order_status = 'Cancelled';
                $order->save();

                OrderStatusHistory::create([
                    'order_id' => $order->order_id,
                    'old_status' => 'Pending',
                    'new_status' => 'Cancelled',
                    'changed_by' => null,
                    'note' => "Reservation expired: payment not confirmed within {$timeoutMinutes} minutes",
                    'changed_at' => now(),
                ]);

                AuditLog::create([
                    'user_id' => null,
                    'user_name' => 'System',
                    'action' => 'Order reservation expired',
                    'record' => $order->order_number,
                    'entity_type' => 'Order',
                    'entity_id' => (string) $order->order_id,
                    'old_value' => 'Pending',
                    'new_value' => 'Cancelled',
                    'created_at' => now(),
                ]);

                $releasedCount++;
            });
        }

        return $releasedCount;
    }

    public function releaseExpiredEndpoint()
    {
        $count = self::releaseExpiredReservations();
        return response()->json([
            'message' => "Successfully released reservations for {$count} expired orders.",
            'released_count' => $count,
        ]);
    }
}
