<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Branch;
use App\Models\BranchProductPrice;
use App\Models\BranchInventory;
use App\Models\AuditLog;
use Illuminate\Support\Str;

class CatalogController extends Controller
{
    public function categories()
    {
        return response()->json(Category::withCount('products')->get());
    }

    public function products(Request $request)
    {
        $branchId = $request->query('branch_id');
        $categoryId = $request->query('category_id');
        $search = $request->query('search');

        $query = Product::with(['category', 'images', 'branchPrices']);

        if ($categoryId && $categoryId !== 'all') {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where('product_name', 'like', "%{$search}%");
        }

        $products = $query->get()->map(function ($product) use ($branchId) {
            $resolvedPrice = (float) $product->base_price;
            $hasOverride = false;

            if ($branchId && $branchId !== 'all') {
                $override = $product->branchPrices
                    ->where('branch_id', $branchId)
                    ->where('active', true)
                    ->first();
                if ($override) {
                    $resolvedPrice = (float) $override->price;
                    $hasOverride = true;
                }
            }

            // Get branch stock if branch specified
            $onHand = 0;
            $reserved = 0;
            $available = 0;
            $reorderLevel = 10;
            $stockStatus = 'Healthy';

            if ($branchId && $branchId !== 'all') {
                $inventory = BranchInventory::where('branch_id', $branchId)
                    ->where('product_id', $product->product_id)
                    ->first();

                if ($inventory) {
                    $onHand = (int) $inventory->quantity_on_hand;
                    $reserved = (int) $inventory->reserved_quantity;
                    $reorderLevel = (int) $inventory->reorder_level;
                    $available = max(0, $onHand - $reserved);
                    $stockStatus = $inventory->status;
                } else {
                    $stockStatus = 'Sold out';
                }
            }

            return [
                'product_id' => $product->product_id,
                'category_id' => $product->category_id,
                'category_name' => $product->category?->category_name,
                'product_name' => $product->product_name,
                'description' => $product->description,
                'base_price' => (float) $product->base_price,
                'resolved_price' => $resolvedPrice,
                'has_branch_override' => $hasOverride,
                'shelf_life_hours' => $product->shelf_life_hours,
                'status' => $product->status,
                'image_url' => $product->images->first()?->image_url ?? 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop&q=80',
                'images' => $product->images,
                'on_hand' => $onHand,
                'reserved' => $reserved,
                'available' => $available,
                'reorder_level' => $reorderLevel,
                'stock_status' => $stockStatus,
            ];
        });

        return response()->json($products);
    }

    public function show(Request $request, $id)
    {
        $branchId = $request->query('branch_id');
        $product = Product::with(['category', 'images', 'branchPrices'])->findOrFail($id);

        $resolvedPrice = (float) $product->base_price;
        $hasOverride = false;

        if ($branchId && $branchId !== 'all') {
            $override = $product->branchPrices
                ->where('branch_id', $branchId)
                ->where('active', true)
                ->first();
            if ($override) {
                $resolvedPrice = (float) $override->price;
                $hasOverride = true;
            }
        }

        $onHand = 0;
        $reserved = 0;
        $available = 0;
        $reorderLevel = 10;
        $stockStatus = 'Healthy';

        if ($branchId && $branchId !== 'all') {
            $inventory = BranchInventory::where('branch_id', $branchId)
                ->where('product_id', $product->product_id)
                ->first();

            if ($inventory) {
                $onHand = (int) $inventory->quantity_on_hand;
                $reserved = (int) $inventory->reserved_quantity;
                $reorderLevel = (int) $inventory->reorder_level;
                $available = max(0, $onHand - $reserved);
                $stockStatus = $inventory->status;
            } else {
                $stockStatus = 'Sold out';
            }
        }

        return response()->json([
            'product_id' => $product->product_id,
            'category_id' => $product->category_id,
            'category' => $product->category,
            'category_name' => $product->category?->category_name,
            'product_name' => $product->product_name,
            'description' => $product->description,
            'base_price' => (float) $product->base_price,
            'resolved_price' => $resolvedPrice,
            'has_branch_override' => $hasOverride,
            'shelf_life_hours' => $product->shelf_life_hours,
            'status' => $product->status,
            'image_url' => $product->images->first()?->image_url ?? 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop&q=80',
            'images' => $product->images,
            'on_hand' => $onHand,
            'reserved' => $reserved,
            'available' => $available,
            'reorder_level' => $reorderLevel,
            'stock_status' => $stockStatus,
        ]);
    }

    public function branchAvailability($id)
    {
        $product = Product::with(['branchPrices'])->findOrFail($id);
        $branches = Branch::where('status', 'Active')->get();

        $availability = $branches->map(function ($branch) use ($product) {
            $inventory = BranchInventory::where('branch_id', $branch->branch_id)
                ->where('product_id', $product->product_id)
                ->first();

            $onHand = $inventory ? (int) $inventory->quantity_on_hand : 0;
            $reserved = $inventory ? (int) $inventory->reserved_quantity : 0;
            $available = max(0, $onHand - $reserved);

            $override = $product->branchPrices
                ->where('branch_id', $branch->branch_id)
                ->where('active', true)
                ->first();
            $resolvedPrice = $override ? (float) $override->price : (float) $product->base_price;

            return [
                'branch_id' => $branch->branch_id,
                'branch_name' => $branch->branch_name,
                'branch_code' => $branch->branch_code,
                'city' => $branch->city,
                'address' => $branch->address,
                'phone' => $branch->phone,
                'opening_hours' => $branch->opening_hours,
                'available' => $available,
                'price' => $resolvedPrice,
                'has_override' => (bool) $override,
                'in_stock' => $available > 0,
            ];
        });

        return response()->json([
            'product_id' => $product->product_id,
            'product_name' => $product->product_name,
            'branches' => $availability,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,category_id',
            'base_price' => 'required|numeric|min:0',
            'shelf_life_hours' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string',
        ]);

        $product = Product::create([
            'product_name' => $validated['product_name'],
            'category_id' => $validated['category_id'],
            'base_price' => $validated['base_price'],
            'shelf_life_hours' => $validated['shelf_life_hours'] ?? 48,
            'description' => $validated['description'] ?? '',
            'status' => 'Active',
        ]);

        if (!empty($validated['image_url'])) {
            ProductImage::create([
                'product_id' => $product->product_id,
                'image_url' => $validated['image_url'],
                'sort_order' => 1,
            ]);
        }

        // Initialize branch inventory for all existing active branches
        $branches = Branch::where('status', 'Active')->get();
        foreach ($branches as $branch) {
            BranchInventory::firstOrCreate(
                ['branch_id' => $branch->branch_id, 'product_id' => $product->product_id],
                [
                    'quantity_on_hand' => 20,
                    'reserved_quantity' => 0,
                    'reorder_level' => 10,
                    'status' => 'Healthy',
                ]
            );
        }

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'Product created',
            'record' => $product->product_name,
            'entity_type' => 'Product',
            'entity_id' => (string) $product->product_id,
            'old_value' => '—',
            'new_value' => 'ETB ' . number_format($product->base_price, 0),
            'created_at' => now(),
        ]);

        return response()->json($product->load(['category', 'images']), 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'product_name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,category_id',
            'base_price' => 'sometimes|numeric|min:0',
            'shelf_life_hours' => 'sometimes|integer|min:1',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:Active,Inactive',
        ]);

        $oldPrice = $product->base_price;
        $product->update($validated);

        if (isset($validated['base_price']) && (float) $validated['base_price'] !== (float) $oldPrice) {
            AuditLog::create([
                'user_id' => $request->user()?->user_id,
                'user_name' => $request->user()?->full_name ?? 'Super Admin',
                'action' => 'Price change',
                'record' => 'Base · ' . $product->product_name,
                'entity_type' => 'Product',
                'entity_id' => (string) $product->product_id,
                'old_value' => 'ETB ' . number_format($oldPrice, 0),
                'new_value' => 'ETB ' . number_format($validated['base_price'], 0),
                'created_at' => now(),
            ]);
        }

        return response()->json($product->load(['category', 'images']));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'category_name' => 'required|string|max:255|unique:categories,category_name',
            'description' => 'nullable|string',
        ]);

        $category = Category::create([
            'category_name' => $validated['category_name'],
            'slug' => Str::slug($validated['category_name']),
            'description' => $validated['description'] ?? '',
        ]);

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'Category created',
            'record' => $category->category_name,
            'entity_type' => 'Category',
            'entity_id' => (string) $category->category_id,
            'old_value' => '—',
            'new_value' => $category->category_name,
            'created_at' => now(),
        ]);

        return response()->json($category, 201);
    }

    public function setBranchPrice(Request $request, $productId)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,branch_id',
            'price' => 'required|numeric|min:0',
            'active' => 'sometimes|boolean',
        ]);

        $product = Product::findOrFail($productId);
        $oldPrice = $product->getPriceForBranch($validated['branch_id']);

        $priceRecord = BranchProductPrice::updateOrCreate(
            ['branch_id' => $validated['branch_id'], 'product_id' => $productId],
            ['price' => $validated['price'], 'active' => $validated['active'] ?? true]
        );

        AuditLog::create([
            'user_id' => $request->user()?->user_id,
            'user_name' => $request->user()?->full_name ?? 'Super Admin',
            'action' => 'Price change',
            'record' => $priceRecord->branch->branch_name . ' · ' . $product->product_name,
            'entity_type' => 'BranchProductPrice',
            'entity_id' => (string) $priceRecord->id,
            'old_value' => 'ETB ' . number_format($oldPrice, 0),
            'new_value' => 'ETB ' . number_format($validated['price'], 0),
            'created_at' => now(),
        ]);

        return response()->json($priceRecord);
    }
}
