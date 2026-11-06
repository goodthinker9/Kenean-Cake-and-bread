<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Branch;
use App\Models\User;
use App\Models\Address;
use App\Models\DeliveryZone;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\BranchProductPrice;
use App\Models\BranchInventory;
use App\Models\InventoryTransaction;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Payment;
use App\Models\CustomCakeRequest;
use App\Models\CustomCakeImage;
use App\Models\Notification;
use App\Models\AuditLog;
use App\Models\AppSetting;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        $settings = [
            'unpaid_reservation_minutes' => ['15', 'orders_stock'],
            'default_reorder_level' => ['10', 'orders_stock'],
            'pay_on_pickup_delivery' => ['Enabled', 'orders_stock'],
            'cancellation_allowed_until' => ['Preparing starts', 'orders_stock'],
            'approval_threshold_units' => ['200', 'transfers'],
            'unreceived_reminder_hours' => ['24', 'transfers'],
            'default_deposit_percent' => ['50', 'custom_cakes'],
            'deposit_refundable' => ['Before branch starts work', 'custom_cakes'],
            'gateways' => ['Chapa, Telebirr', 'payments'],
            'webhook_secret' => ['whsec_live_89f21a9chapa2026', 'payments'],
            'languages' => ['English, Amharic', 'localization'],
            'currency' => ['ETB', 'localization'],
            'admin_2fa' => ['Optional', 'security'],
            'backups' => ['Daily, kept 30 days', 'security'],
            'low_stock_notification' => ['Branch Admin and Main Manager', 'alerts'],
            'unreceived_transfer_reminder' => ['After 24 hours', 'alerts'],
            'daily_summary_email' => ['Owner, 20:00', 'alerts'],
        ];
        foreach ($settings as $k => $data) {
            AppSetting::updateOrCreate(['key' => $k], ['value' => $data[0], 'group' => $data[1]]);
        }

        // 2. Branches
        $mainBranch = Branch::create([
            'branch_name' => 'Main Branch',
            'branch_code' => 'MAIN01',
            'branch_type' => 'Main',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'address' => 'Mexico Square, Commercial Center Bldg',
            'phone' => '+251 11 551 0000',
            'opening_hours' => '06:00 - 22:00',
            'status' => 'Active',
        ]);

        $boleBranch = Branch::create([
            'branch_name' => 'Bole',
            'branch_code' => 'BR001',
            'branch_type' => 'Regular',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'address' => 'Bole Medhanialem, Edna Mall Area',
            'phone' => '+251 11 661 1111',
            'opening_hours' => '07:00 - 21:00',
            'status' => 'Active',
        ]);

        $megenagnaBranch = Branch::create([
            'branch_name' => 'Megenagna',
            'branch_code' => 'BR002',
            'branch_type' => 'Regular',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'address' => 'Megenagna Plaza, Zefmesh Grand Mall',
            'phone' => '+251 11 662 2222',
            'opening_hours' => '07:00 - 21:00',
            'status' => 'Active',
        ]);

        $piassaBranch = Branch::create([
            'branch_name' => 'Piassa',
            'branch_code' => 'BR003',
            'branch_type' => 'Regular',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'address' => 'Piassa Churchill Ave, Near Degol Square',
            'phone' => '+251 11 155 3333',
            'opening_hours' => '07:00 - 21:00',
            'status' => 'Active',
        ]);

        // Delivery Zones
        $zones = [
            [$mainBranch->branch_id, 'Mexico Square & Sarbet', 40.00],
            [$mainBranch->branch_id, 'Lideta & Stadium', 50.00],
            [$boleBranch->branch_id, 'Bole Medhanialem', 60.00],
            [$boleBranch->branch_id, 'Kazanchis & Atlas', 75.00],
            [$boleBranch->branch_id, 'Bole Bulbula', 80.00],
            [$megenagnaBranch->branch_id, 'Megenagna Central', 50.00],
            [$megenagnaBranch->branch_id, 'Gerji & Salite Mehret', 70.00],
            [$megenagnaBranch->branch_id, 'CMC & Ayat', 85.00],
            [$piassaBranch->branch_id, 'Piassa & Churchill Road', 40.00],
            [$piassaBranch->branch_id, 'Arat Kilo & 4 Kilo', 50.00],
            [$piassaBranch->branch_id, 'Sidist Kilo & Kazanchis North', 60.00],
        ];
        foreach ($zones as $z) {
            DeliveryZone::create([
                'branch_id' => $z[0],
                'zone_name' => $z[1],
                'delivery_fee' => $z[2],
                'status' => 'Active',
            ]);
        }

        // 3. Users
        $superAdmin = User::create([
            'full_name' => 'Abebe Kebede',
            'email' => 'admin@goldencrust.com',
            'phone' => '+251 91 100 0001',
            'password' => Hash::make('password123'),
            'role' => 'Super Admin',
            'branch_id' => null,
            'status' => 'Active',
        ]);

        $mainManager = User::create([
            'full_name' => 'Dawit Tadesse',
            'email' => 'main@goldencrust.com',
            'phone' => '+251 91 100 0002',
            'password' => Hash::make('password123'),
            'role' => 'Main Branch Manager',
            'branch_id' => $mainBranch->branch_id,
            'status' => 'Active',
        ]);
        $mainBranch->update(['manager_id' => $mainManager->user_id]);

        $boleAdmin = User::create([
            'full_name' => 'Almaz Haile',
            'email' => 'bole.admin@goldencrust.com',
            'phone' => '+251 91 100 0003',
            'password' => Hash::make('password123'),
            'role' => 'Branch Admin',
            'branch_id' => $boleBranch->branch_id,
            'status' => 'Active',
        ]);
        $boleBranch->update(['manager_id' => $boleAdmin->user_id]);

        $megenagnaAdmin = User::create([
            'full_name' => 'Kassahun Bekele',
            'email' => 'megenagna.admin@goldencrust.com',
            'phone' => '+251 91 100 0004',
            'password' => Hash::make('password123'),
            'role' => 'Branch Admin',
            'branch_id' => $megenagnaBranch->branch_id,
            'status' => 'Active',
        ]);
        $megenagnaBranch->update(['manager_id' => $megenagnaAdmin->user_id]);

        $piassaAdmin = User::create([
            'full_name' => 'Tigist Alemu',
            'email' => 'piassa.admin@goldencrust.com',
            'phone' => '+251 91 100 0005',
            'password' => Hash::make('password123'),
            'role' => 'Branch Admin',
            'branch_id' => $piassaBranch->branch_id,
            'status' => 'Active',
        ]);
        $piassaBranch->update(['manager_id' => $piassaAdmin->user_id]);

        $boleStaff = User::create([
            'full_name' => 'Yared Tesfaye',
            'email' => 'bole.staff@goldencrust.com',
            'phone' => '+251 91 100 0006',
            'password' => Hash::make('password123'),
            'role' => 'Branch Staff',
            'branch_id' => $boleBranch->branch_id,
            'status' => 'Active',
        ]);

        $customer1 = User::create([
            'full_name' => 'Sara Mengistu',
            'email' => 'customer@gmail.com',
            'phone' => '+251 91 234 5678',
            'password' => Hash::make('password123'),
            'role' => 'Customer',
            'branch_id' => null,
            'status' => 'Active',
        ]);

        $customer2 = User::create([
            'full_name' => 'Daniel Worku',
            'email' => 'daniel@gmail.com',
            'phone' => '+251 91 876 5432',
            'password' => Hash::make('password123'),
            'role' => 'Customer',
            'branch_id' => null,
            'status' => 'Active',
        ]);

        // Customer addresses
        $custAddress1 = Address::create([
            'user_id' => $customer1->user_id,
            'city' => 'Addis Ababa',
            'region' => 'Bole',
            'street' => 'House 420, Near Morning Star Mall',
            'notes' => 'Call upon arrival, 3rd floor gate',
            'is_default' => true,
        ]);

        // 4. Categories
        $catCakes = Category::create([
            'category_name' => 'Cakes',
            'slug' => 'cakes',
            'description' => 'Delicious handcrafted artisan cakes for every celebration',
        ]);

        $catBread = Category::create([
            'category_name' => 'Bread',
            'slug' => 'bread',
            'description' => 'Freshly baked wholesome loaves and sourdough',
        ]);

        $catPastries = Category::create([
            'category_name' => 'Pastries',
            'slug' => 'pastries',
            'description' => 'Buttery flakey croissants, tarts and rolls',
        ]);

        // 5. Products
        $chocCake = Product::create([
            'category_id' => $catCakes->category_id,
            'product_name' => 'Chocolate Cake',
            'description' => 'Rich Belgian chocolate sponge layered with luscious dark ganache.',
            'base_price' => 850.00,
            'shelf_life_hours' => 48,
            'status' => 'Active',
        ]);
        ProductImage::create([
            'product_id' => $chocCake->product_id,
            'image_url' => 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop&q=80',
            'sort_order' => 0,
        ]);

        $whiteBread = Product::create([
            'category_id' => $catBread->category_id,
            'product_name' => 'White Bread',
            'description' => 'Golden-crusted soft white bread, perfect for morning toasts.',
            'base_price' => 45.00,
            'shelf_life_hours' => 24,
            'status' => 'Active',
        ]);
        ProductImage::create([
            'product_id' => $whiteBread->product_id,
            'image_url' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&auto=format&fit=crop&q=80',
            'sort_order' => 0,
        ]);

        $vanillaCake = Product::create([
            'category_id' => $catCakes->category_id,
            'product_name' => 'Vanilla Cake',
            'description' => 'Classic Madagascar vanilla bean sponge with light buttercream.',
            'base_price' => 780.00,
            'shelf_life_hours' => 48,
            'status' => 'Active',
        ]);
        ProductImage::create([
            'product_id' => $vanillaCake->product_id,
            'image_url' => 'https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=600&auto=format&fit=crop&q=80',
            'sort_order' => 0,
        ]);

        $croissant = Product::create([
            'category_id' => $catPastries->category_id,
            'product_name' => 'Croissant',
            'description' => 'Traditional layered French butter croissant, crisp and airy.',
            'base_price' => 60.00,
            'shelf_life_hours' => 24,
            'status' => 'Active',
        ]);
        ProductImage::create([
            'product_id' => $croissant->product_id,
            'image_url' => 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&auto=format&fit=crop&q=80',
            'sort_order' => 0,
        ]);

        $wheatBread = Product::create([
            'category_id' => $catBread->category_id,
            'product_name' => 'Whole Wheat Bread',
            'description' => '100% whole grain stoneground wheat loaf packed with natural fiber.',
            'base_price' => 55.00,
            'shelf_life_hours' => 24,
            'status' => 'Active',
        ]);
        ProductImage::create([
            'product_id' => $wheatBread->product_id,
            'image_url' => 'https://images.unsplash.com/photo-1586444248902-2f64eddc13df?w=600&auto=format&fit=crop&q=80',
            'sort_order' => 0,
        ]);

        // Branch price override for Piassa Chocolate Cake (ETB 880) as in screenshot 10
        BranchProductPrice::create([
            'branch_id' => $piassaBranch->branch_id,
            'product_id' => $chocCake->product_id,
            'price' => 880.00,
            'active' => true,
        ]);

        // 6. Branch Inventory (Matching Screenshot Page 1 exactly!)
        // Main Branch
        BranchInventory::create([
            'branch_id' => $mainBranch->branch_id,
            'product_id' => $chocCake->product_id,
            'quantity_on_hand' => 60,
            'reserved_quantity' => 0,
            'reorder_level' => 20,
        ]);
        BranchInventory::create([
            'branch_id' => $mainBranch->branch_id,
            'product_id' => $whiteBread->product_id,
            'quantity_on_hand' => 220,
            'reserved_quantity' => 0,
            'reorder_level' => 80,
        ]);
        BranchInventory::create([
            'branch_id' => $mainBranch->branch_id,
            'product_id' => $croissant->product_id,
            'quantity_on_hand' => 90,
            'reserved_quantity' => 0,
            'reorder_level' => 30,
        ]);
        BranchInventory::create([
            'branch_id' => $mainBranch->branch_id,
            'product_id' => $vanillaCake->product_id,
            'quantity_on_hand' => 50,
            'reserved_quantity' => 0,
            'reorder_level' => 20,
        ]);

        // Bole Branch
        BranchInventory::create([
            'branch_id' => $boleBranch->branch_id,
            'product_id' => $chocCake->product_id,
            'quantity_on_hand' => 14,
            'reserved_quantity' => 2,
            'reorder_level' => 6,
        ]);
        BranchInventory::create([
            'branch_id' => $boleBranch->branch_id,
            'product_id' => $vanillaCake->product_id,
            'quantity_on_hand' => 9,
            'reserved_quantity' => 1,
            'reorder_level' => 6,
        ]);
        BranchInventory::create([
            'branch_id' => $boleBranch->branch_id,
            'product_id' => $whiteBread->product_id,
            'quantity_on_hand' => 52,
            'reserved_quantity' => 2,
            'reorder_level' => 30,
        ]);
        BranchInventory::create([
            'branch_id' => $boleBranch->branch_id,
            'product_id' => $croissant->product_id,
            'quantity_on_hand' => 27,
            'reserved_quantity' => 2,
            'reorder_level' => 15,
        ]);
        BranchInventory::create([
            'branch_id' => $boleBranch->branch_id,
            'product_id' => $wheatBread->product_id,
            'quantity_on_hand' => 0,
            'reserved_quantity' => 0,
            'reorder_level' => 20,
        ]);

        // Megenagna Branch
        BranchInventory::create([
            'branch_id' => $megenagnaBranch->branch_id,
            'product_id' => $chocCake->product_id,
            'quantity_on_hand' => 15,
            'reserved_quantity' => 0,
            'reorder_level' => 6,
        ]);
        BranchInventory::create([
            'branch_id' => $megenagnaBranch->branch_id,
            'product_id' => $whiteBread->product_id,
            'quantity_on_hand' => 24,
            'reserved_quantity' => 0,
            'reorder_level' => 30,
        ]);
        BranchInventory::create([
            'branch_id' => $megenagnaBranch->branch_id,
            'product_id' => $croissant->product_id,
            'quantity_on_hand' => 20,
            'reserved_quantity' => 0,
            'reorder_level' => 15,
        ]);

        // Piassa Branch
        BranchInventory::create([
            'branch_id' => $piassaBranch->branch_id,
            'product_id' => $chocCake->product_id,
            'quantity_on_hand' => 6,
            'reserved_quantity' => 0,
            'reorder_level' => 6,
        ]);
        BranchInventory::create([
            'branch_id' => $piassaBranch->branch_id,
            'product_id' => $croissant->product_id,
            'quantity_on_hand' => 0,
            'reserved_quantity' => 0,
            'reorder_level' => 15,
        ]);
        BranchInventory::create([
            'branch_id' => $piassaBranch->branch_id,
            'product_id' => $whiteBread->product_id,
            'quantity_on_hand' => 35,
            'reserved_quantity' => 0,
            'reorder_level' => 20,
        ]);

        // Inventory Transactions
        InventoryTransaction::create([
            'branch_id' => $boleBranch->branch_id,
            'product_id' => $chocCake->product_id,
            'transaction_type' => 'damaged',
            'quantity' => -2,
            'reference_type' => 'manual',
            'reason' => 'Dropped during packing',
            'created_by' => $boleAdmin->user_id,
            'created_at' => Carbon::now()->subHours(1),
        ]);

        // 7. Stock Transfers (Matching Screenshot Page 2 & Page 3)
        // Bole Disputed transfer TRF-2026-0003
        $trf3 = StockTransfer::create([
            'transfer_number' => 'TRF-2026-0003',
            'source_branch_id' => $mainBranch->branch_id,
            'destination_branch_id' => $boleBranch->branch_id,
            'status' => 'Disputed',
            'created_by' => $mainManager->user_id,
            'approved_by' => $superAdmin->user_id,
            'dispatched_at' => Carbon::now()->subHours(4),
            'received_at' => Carbon::now()->subMinutes(30),
            'notes' => 'Daily bakery morning delivery for Bole branch',
        ]);
        StockTransferItem::create([
            'transfer_id' => $trf3->transfer_id,
            'product_id' => $whiteBread->product_id,
            'quantity_sent' => 100,
            'quantity_received' => 95,
            'damaged_quantity' => 0,
            'missing_quantity' => 5,
            'discrepancy_reason' => '5 units not in the delivery crate',
        ]);

        // Megenagna In transit transfer TRF-2026-0004
        $trf4 = StockTransfer::create([
            'transfer_number' => 'TRF-2026-0004',
            'source_branch_id' => $mainBranch->branch_id,
            'destination_branch_id' => $megenagnaBranch->branch_id,
            'status' => 'In transit',
            'created_by' => $mainManager->user_id,
            'approved_by' => $superAdmin->user_id,
            'dispatched_at' => Carbon::now()->subHours(25),
            'notes' => 'Restock for weekend rush',
        ]);
        StockTransferItem::create([
            'transfer_id' => $trf4->transfer_id,
            'product_id' => $whiteBread->product_id,
            'quantity_sent' => 105,
            'quantity_received' => 0,
            'damaged_quantity' => 0,
            'missing_quantity' => 0,
        ]);

        // Piassa Partially received transfer TRF-2026-0002
        $trf2 = StockTransfer::create([
            'transfer_number' => 'TRF-2026-0002',
            'source_branch_id' => $mainBranch->branch_id,
            'destination_branch_id' => $piassaBranch->branch_id,
            'status' => 'Partially received',
            'created_by' => $mainManager->user_id,
            'approved_by' => $superAdmin->user_id,
            'dispatched_at' => Carbon::now()->subHours(8),
            'received_at' => Carbon::now()->subHours(2),
            'notes' => 'Special order breads and croissants',
        ]);
        StockTransferItem::create([
            'transfer_id' => $trf2->transfer_id,
            'product_id' => $whiteBread->product_id,
            'quantity_sent' => 80,
            'quantity_received' => 60,
            'damaged_quantity' => 0,
            'missing_quantity' => 20,
            'discrepancy_reason' => '20 units deferred to afternoon shipment',
        ]);

        // Main Branch Approved transfer TRF-2026-0005
        $trf5 = StockTransfer::create([
            'transfer_number' => 'TRF-2026-0005',
            'source_branch_id' => $mainBranch->branch_id,
            'destination_branch_id' => $piassaBranch->branch_id,
            'status' => 'Approved',
            'created_by' => $mainManager->user_id,
            'approved_by' => $superAdmin->user_id,
            'notes' => 'Awaiting driver loading',
        ]);
        StockTransferItem::create([
            'transfer_id' => $trf5->transfer_id,
            'product_id' => $croissant->product_id,
            'quantity_sent' => 40,
            'quantity_received' => 0,
            'damaged_quantity' => 0,
            'missing_quantity' => 0,
        ]);

        // Stock Request matching Page 2
        $req1 = StockRequest::create([
            'branch_id' => $boleBranch->branch_id,
            'status' => 'Pending',
            'requested_by' => $boleAdmin->user_id,
            'created_at' => Carbon::today()->setHour(8)->setMinute(10),
        ]);
        StockRequestItem::create([
            'request_id' => $req1->request_id,
            'product_id' => $whiteBread->product_id,
            'quantity_requested' => 40,
        ]);

        // 8. Orders matching Screenshot Page 4
        // ORD-1001 (Bole, Delivery, Online, Paid, Pending)
        $ord1001 = Order::create([
            'order_number' => 'ORD-1001',
            'user_id' => $customer1->user_id,
            'branch_id' => $boleBranch->branch_id,
            'address_id' => $custAddress1->address_id,
            'fulfillment_method' => 'Delivery',
            'order_status' => 'Pending',
            'delivery_fee' => 60.00,
            'discount_amount' => 0.00,
            'total_amount' => 1255.00,
            'placed_at' => Carbon::today()->setHour(10)->setMinute(42),
        ]);
        OrderItem::create([
            'order_id' => $ord1001->order_id,
            'product_id' => $chocCake->product_id,
            'product_name' => 'Chocolate Cake',
            'quantity' => 1,
            'unit_price' => 850.00,
            'subtotal' => 850.00,
        ]);
        OrderItem::create([
            'order_id' => $ord1001->order_id,
            'product_id' => $whiteBread->product_id,
            'product_name' => 'White Bread',
            'quantity' => 5,
            'unit_price' => 45.00,
            'subtotal' => 225.00,
        ]);
        OrderItem::create([
            'order_id' => $ord1001->order_id,
            'product_id' => $croissant->product_id,
            'product_name' => 'Croissant',
            'quantity' => 2,
            'unit_price' => 60.00,
            'subtotal' => 120.00,
        ]);
        OrderStatusHistory::create([
            'order_id' => $ord1001->order_id,
            'old_status' => null,
            'new_status' => 'Pending',
            'changed_by' => $customer1->user_id,
            'note' => 'placed by customer',
            'changed_at' => Carbon::today()->setHour(10)->setMinute(42),
        ]);
        OrderStatusHistory::create([
            'order_id' => $ord1001->order_id,
            'old_status' => 'Pending',
            'new_status' => 'Pending',
            'changed_by' => null,
            'note' => 'Payment verified by webhook',
            'changed_at' => Carbon::today()->setHour(10)->setMinute(43),
        ]);
        Payment::create([
            'order_id' => $ord1001->order_id,
            'payment_method' => 'Chapa',
            'amount' => 1255.00,
            'payment_status' => 'Paid',
            'transaction_reference' => 'CH-8F21A9',
        ]);

        // ORD-1002 (Megenagna, Pickup, Pay on pickup, Confirmed)
        $ord1002 = Order::create([
            'order_number' => 'ORD-1002',
            'user_id' => $customer2->user_id,
            'branch_id' => $megenagnaBranch->branch_id,
            'fulfillment_method' => 'Pickup',
            'order_status' => 'Confirmed',
            'delivery_fee' => 0.00,
            'discount_amount' => 0.00,
            'total_amount' => 780.00,
            'placed_at' => Carbon::today()->setHour(10)->setMinute(15),
        ]);
        OrderItem::create([
            'order_id' => $ord1002->order_id,
            'product_id' => $vanillaCake->product_id,
            'product_name' => 'Vanilla Cake',
            'quantity' => 1,
            'unit_price' => 780.00,
            'subtotal' => 780.00,
        ]);
        Payment::create([
            'order_id' => $ord1002->order_id,
            'payment_method' => 'Pay on pickup',
            'amount' => 780.00,
            'payment_status' => 'Pending',
            'transaction_reference' => null,
        ]);

        // ORD-1003 (Piassa, Delivery, Online, Paid, Preparing)
        $ord1003 = Order::create([
            'order_number' => 'ORD-1003',
            'user_id' => $customer1->user_id,
            'branch_id' => $piassaBranch->branch_id,
            'fulfillment_method' => 'Delivery',
            'order_status' => 'Preparing',
            'delivery_fee' => 40.00,
            'discount_amount' => 0.00,
            'total_amount' => 1710.00,
            'placed_at' => Carbon::today()->setHour(9)->setMinute(50),
        ]);
        OrderItem::create([
            'order_id' => $ord1003->order_id,
            'product_id' => $chocCake->product_id,
            'product_name' => 'Chocolate Cake',
            'quantity' => 1,
            'unit_price' => 880.00,
            'subtotal' => 880.00,
        ]);
        OrderItem::create([
            'order_id' => $ord1003->order_id,
            'product_id' => $vanillaCake->product_id,
            'product_name' => 'Vanilla Cake',
            'quantity' => 1,
            'unit_price' => 780.00,
            'subtotal' => 780.00,
        ]);
        Payment::create([
            'order_id' => $ord1003->order_id,
            'payment_method' => 'Telebirr',
            'amount' => 1710.00,
            'payment_status' => 'Paid',
            'transaction_reference' => 'TB-44C710',
        ]);

        // ORD-1004 (Bole, Pickup, Ready)
        $ord1004 = Order::create([
            'order_number' => 'ORD-1004',
            'user_id' => $customer2->user_id,
            'branch_id' => $boleBranch->branch_id,
            'fulfillment_method' => 'Pickup',
            'order_status' => 'Ready',
            'delivery_fee' => 0.00,
            'discount_amount' => 0.00,
            'total_amount' => 225.00,
            'placed_at' => Carbon::today()->setHour(9)->setMinute(31),
        ]);
        OrderItem::create([
            'order_id' => $ord1004->order_id,
            'product_id' => $whiteBread->product_id,
            'product_name' => 'White Bread',
            'quantity' => 5,
            'unit_price' => 45.00,
            'subtotal' => 225.00,
        ]);
        Payment::create([
            'order_id' => $ord1004->order_id,
            'payment_method' => 'Chapa',
            'amount' => 225.00,
            'payment_status' => 'Paid',
            'transaction_reference' => 'CH-9921B1',
        ]);

        // ORD-1005 (Main Branch, Delivery, Out for delivery)
        $ord1005 = Order::create([
            'order_number' => 'ORD-1005',
            'user_id' => $customer1->user_id,
            'branch_id' => $mainBranch->branch_id,
            'fulfillment_method' => 'Delivery',
            'order_status' => 'Out for delivery',
            'delivery_fee' => 40.00,
            'discount_amount' => 0.00,
            'total_amount' => 640.00,
            'placed_at' => Carbon::today()->setHour(9)->setMinute(2),
        ]);
        OrderItem::create([
            'order_id' => $ord1005->order_id,
            'product_id' => $croissant->product_id,
            'product_name' => 'Croissant',
            'quantity' => 10,
            'unit_price' => 60.00,
            'subtotal' => 600.00,
        ]);
        Payment::create([
            'order_id' => $ord1005->order_id,
            'payment_method' => 'Chapa',
            'amount' => 640.00,
            'payment_status' => 'Paid',
            'transaction_reference' => 'CH-8822A3',
        ]);

        // ORD-1006 (Bole, Pickup, Completed)
        $ord1006 = Order::create([
            'order_number' => 'ORD-1006',
            'user_id' => $customer2->user_id,
            'branch_id' => $boleBranch->branch_id,
            'fulfillment_method' => 'Pickup',
            'order_status' => 'Completed',
            'delivery_fee' => 0.00,
            'discount_amount' => 0.00,
            'total_amount' => 120.00,
            'placed_at' => Carbon::today()->setHour(8)->setMinute(40),
        ]);
        OrderItem::create([
            'order_id' => $ord1006->order_id,
            'product_id' => $croissant->product_id,
            'product_name' => 'Croissant',
            'quantity' => 2,
            'unit_price' => 60.00,
            'subtotal' => 120.00,
        ]);
        Payment::create([
            'order_id' => $ord1006->order_id,
            'payment_method' => 'Chapa',
            'amount' => 120.00,
            'payment_status' => 'Paid',
            'transaction_reference' => 'CH-7711X4',
        ]);

        // ORD-1007 (Megenagna, Delivery, Failed / Cancelled)
        $ord1007 = Order::create([
            'order_number' => 'ORD-1007',
            'user_id' => $customer1->user_id,
            'branch_id' => $megenagnaBranch->branch_id,
            'fulfillment_method' => 'Delivery',
            'order_status' => 'Cancelled',
            'delivery_fee' => 50.00,
            'discount_amount' => 0.00,
            'total_amount' => 850.00,
            'placed_at' => Carbon::today()->setHour(8)->setMinute(12),
        ]);
        OrderItem::create([
            'order_id' => $ord1007->order_id,
            'product_id' => $chocCake->product_id,
            'product_name' => 'Chocolate Cake',
            'quantity' => 1,
            'unit_price' => 800.00,
            'subtotal' => 800.00,
        ]);
        Payment::create([
            'order_id' => $ord1007->order_id,
            'payment_method' => 'Chapa',
            'amount' => 850.00,
            'payment_status' => 'Failed',
            'transaction_reference' => 'CH-7E0B33',
        ]);

        // Prior Refunded order ORD-0994 matching page 6
        $ord0994 = Order::create([
            'order_number' => 'ORD-0994',
            'user_id' => $customer2->user_id,
            'branch_id' => $boleBranch->branch_id,
            'fulfillment_method' => 'Pickup',
            'order_status' => 'Cancelled',
            'delivery_fee' => 0.00,
            'discount_amount' => 0.00,
            'total_amount' => 850.00,
            'placed_at' => Carbon::yesterday()->setHour(14)->setMinute(0),
        ]);
        Payment::create([
            'order_id' => $ord0994->order_id,
            'payment_method' => 'Chapa',
            'amount' => 850.00,
            'payment_status' => 'Refunded',
            'transaction_reference' => 'CH-6A19D2',
            'refund_amount' => 850.00,
            'refund_reason' => 'Branch could not fulfil: damaged batch',
        ]);

        // 9. Custom Cake Requests matching Page 5
        $cc112 = CustomCakeRequest::create([
            'request_number' => 'CC-0112',
            'user_id' => $customer1->user_id,
            'branch_id' => $boleBranch->branch_id,
            'cake_size' => 'Medium',
            'flavor' => 'Chocolate',
            'design' => 'Two tiers with rustic textured buttercream, edible 24k gold leaf flakes, and fresh maroon roses.',
            'message' => 'Happy 30th Birthday Aster!',
            'needed_by' => Carbon::now()->addDays(5),
            'quoted_price' => null,
            'deposit_required_percentage' => 50.00,
            'status' => 'Submitted',
        ]);
        CustomCakeImage::create([
            'request_id' => $cc112->request_id,
            'image_url' => 'https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=600&auto=format&fit=crop&q=80',
        ]);
        CustomCakeImage::create([
            'request_id' => $cc112->request_id,
            'image_url' => 'https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?w=600&auto=format&fit=crop&q=80',
        ]);

        $cc111 = CustomCakeRequest::create([
            'request_number' => 'CC-0111',
            'user_id' => $customer2->user_id,
            'branch_id' => $megenagnaBranch->branch_id,
            'cake_size' => 'Tiered',
            'flavor' => 'Vanilla',
            'design' => '3 Tier wedding cake with floral cascade',
            'message' => 'Congratulations Robel & Helen',
            'needed_by' => Carbon::now()->addDays(7),
            'quoted_price' => 6200.00,
            'deposit_required_percentage' => 50.00,
            'status' => 'Quoted',
        ]);

        $cc109 = CustomCakeRequest::create([
            'request_number' => 'CC-0109',
            'user_id' => $customer1->user_id,
            'branch_id' => $piassaBranch->branch_id,
            'cake_size' => 'Large',
            'flavor' => 'Other',
            'design' => 'Mocha coffee crunch theme with chocolate macaron crown',
            'message' => 'Happy Anniversary Mom & Dad',
            'needed_by' => Carbon::now()->addDays(4),
            'quoted_price' => 3400.00,
            'deposit_required_percentage' => 50.00,
            'deposit_paid' => 1700.00,
            'status' => 'Confirmed',
        ]);

        $cc108 = CustomCakeRequest::create([
            'request_number' => 'CC-0108',
            'user_id' => $customer2->user_id,
            'branch_id' => $boleBranch->branch_id,
            'cake_size' => 'Small',
            'flavor' => 'Chocolate',
            'design' => 'Spiderman theme with hand piped web detailing',
            'message' => 'Happy 5th Birthday Nahom',
            'needed_by' => Carbon::now()->addDays(2),
            'quoted_price' => 1100.00,
            'deposit_required_percentage' => 50.00,
            'deposit_paid' => 550.00,
            'status' => 'Preparing',
        ]);

        $cc105 = CustomCakeRequest::create([
            'request_number' => 'CC-0105',
            'user_id' => $customer1->user_id,
            'branch_id' => $mainBranch->branch_id,
            'cake_size' => 'Medium',
            'flavor' => 'Vanilla',
            'design' => 'Pastel pink ombre with edible pearls',
            'message' => 'Baby Shower Welcome Little Princess',
            'needed_by' => Carbon::now()->addDay(),
            'quoted_price' => 1900.00,
            'deposit_required_percentage' => 50.00,
            'deposit_paid' => 1900.00,
            'status' => 'Ready',
        ]);

        $cc103 = CustomCakeRequest::create([
            'request_number' => 'CC-0103',
            'user_id' => $customer2->user_id,
            'branch_id' => $piassaBranch->branch_id,
            'cake_size' => 'Large',
            'flavor' => 'Chocolate',
            'design' => 'Heavy sculpted 3D guitar cake',
            'message' => 'Rock on!',
            'needed_by' => Carbon::now()->addDays(1),
            'quoted_price' => null,
            'status' => 'Rejected',
            'rejection_reason' => 'Schedule fully booked for custom sculptures on requested date',
        ]);

        // 10. Notifications matching Page 8
        Notification::create([
            'user_id' => $superAdmin->user_id,
            'category' => 'Discrepancies',
            'title' => 'Transfer disputed',
            'message' => 'TRF-2026-0003: 100 sent to Bole, 95 received. Reason needed to resolve.',
            'link' => '/admin/transfers',
            'is_read' => false,
            'created_at' => Carbon::now()->subMinutes(10),
        ]);
        Notification::create([
            'user_id' => $superAdmin->user_id,
            'category' => 'Transfers',
            'title' => 'Transfer not received',
            'message' => 'TRF-2026-0004 to Megenagna unreceived after 24 hours.',
            'link' => '/admin/transfers',
            'is_read' => false,
            'created_at' => Carbon::now()->subHours(1),
        ]);
        Notification::create([
            'user_id' => $superAdmin->user_id,
            'category' => 'Stock',
            'title' => 'Low stock',
            'message' => 'Piassa has 6 Chocolate Cakes left (reorder level 6).',
            'link' => '/admin/inventory',
            'is_read' => false,
            'created_at' => Carbon::now()->subHours(2),
        ]);
        Notification::create([
            'user_id' => $superAdmin->user_id,
            'category' => 'Transfers',
            'title' => 'New stock request',
            'message' => 'Bole asked for 40 White Breads.',
            'link' => '/admin/distribution',
            'is_read' => false,
            'created_at' => Carbon::now()->subHours(3),
        ]);
        Notification::create([
            'user_id' => $superAdmin->user_id,
            'category' => 'Orders',
            'title' => 'Custom cake request',
            'message' => 'CC-0112 at Bole is waiting for a quote.',
            'link' => '/admin/custom-cakes',
            'is_read' => false,
            'created_at' => Carbon::today()->setHour(7)->setMinute(30),
        ]);

        // 11. Audit Logs matching Page 10
        $logs = [
            [$customer1->user_id, 'System', 'Order status change', 'ORD-1001', 'Order', '1001', '—', 'Pending (paid)', Carbon::today()->setHour(10)->setMinute(43)],
            [$mainManager->user_id, 'Dawit Tadesse (Main Manager)', 'Transfer dispatched', 'TRF-2026-0004', 'StockTransfer', '4', 'Approved', 'Dispatched', Carbon::today()->setHour(10)->setMinute(20)],
            [$boleAdmin->user_id, 'Almaz Haile (Branch Admin)', 'Transfer received', 'TRF-2026-0003', 'StockTransfer', '3', 'Dispatched', 'Disputed (95 of 100)', Carbon::today()->setHour(9)->setMinute(55)],
            [$boleAdmin->user_id, 'Almaz Haile (Branch Admin)', 'Stock adjustment', 'Bole · Chocolate Cake', 'BranchInventory', '3', '14', '12 (damaged, 2)', Carbon::today()->setHour(9)->setMinute(30)],
            [$superAdmin->user_id, 'Abebe Kebede (Super Admin)', 'Price change', 'Piassa · Chocolate Cake', 'BranchProductPrice', '1', 'ETB 850', 'ETB 880', Carbon::today()->setHour(9)->setMinute(5)],
            [$superAdmin->user_id, 'Abebe Kebede (Super Admin)', 'User created', 'Yared Tesfaye', 'User', '6', '—', 'Branch Staff · Bole', Carbon::today()->setHour(8)->setMinute(40)],
            [$boleAdmin->user_id, 'Almaz Haile (Branch Admin)', 'Refund issued', 'ORD-0994', 'Order', '994', '—', 'ETB 850', Carbon::today()->setHour(8)->setMinute(12)],
            [$superAdmin->user_id, 'Abebe Kebede (Super Admin)', 'Login', 'Super Admin', 'Auth', '1', '—', 'Success', Carbon::today()->setHour(8)->setMinute(0)],
        ];
        foreach ($logs as $l) {
            AuditLog::create([
                'user_id' => $l[0],
                'user_name' => $l[1],
                'action' => $l[2],
                'record' => $l[3],
                'entity_type' => $l[4],
                'entity_id' => $l[5],
                'old_value' => $l[6],
                'new_value' => $l[7],
                'created_at' => $l[8],
            ]);
        }
    }
}
