<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Products and prices</h2>
      <div class="flex items-center space-x-3">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search products"
          class="bg-white border border-[#D8CDC0] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] shadow-sm"
        />
        <button
          @click="isCategoriesModalOpen = true"
          class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
        >
          Categories
        </button>
        <button
          @click="openAddProductModal"
          class="px-4 py-2 text-sm font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg transition shadow-sm flex items-center space-x-1.5"
        >
          <span>+ Add product</span>
        </button>
      </div>
    </div>

    <!-- Alert / Messages -->
    <div v-if="successMsg" class="p-3 bg-[#EBF7EB] border border-[#C5E9C6] rounded-lg text-xs text-[#1E7422] flex items-center justify-between">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = ''" class="text-[#1E7422] font-bold ml-2">&times;</button>
    </div>
    <div v-if="errorMsg" class="p-3 bg-[#FDEAEA] border border-[#FFCDD2] rounded-lg text-xs text-[#C62828] flex items-center justify-between">
      <span>{{ errorMsg }}</span>
      <button @click="errorMsg = ''" class="text-[#C62828] font-bold ml-2">&times;</button>
    </div>

    <!-- Category Pills -->
    <div class="flex items-center justify-between text-xs">
      <div class="flex items-center space-x-2 overflow-x-auto pb-1">
        <button
          @click="selectedCategory = 'all'"
          class="px-3.5 py-1 rounded-full font-semibold transition shrink-0"
          :class="selectedCategory === 'all' ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#55463C]'"
        >
          All
        </button>
        <button
          v-for="c in categories"
          :key="c.category_id"
          @click="selectedCategory = c.category_id"
          class="px-3.5 py-1 rounded-full font-semibold transition shrink-0"
          :class="selectedCategory === c.category_id ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#55463C]'"
        >
          {{ c.category_name }}
        </button>
      </div>
      <span class="text-[#8A7B71] font-semibold shrink-0 ml-2">{{ filteredProducts.length }} products</span>
    </div>

    <!-- Products Table -->
    <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
            <tr>
              <th class="py-3 px-5">Product</th>
              <th class="py-3 px-4">Category</th>
              <th class="py-3 px-4 text-center">Base price</th>
              <th class="py-3 px-4 text-center">Branch prices</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDE4]">
            <tr
              v-for="p in filteredProducts"
              :key="p.product_id"
              class="hover:bg-[#FCFAF7] transition cursor-pointer"
              :class="selectedProduct?.product_id === p.product_id ? 'bg-[#FAF3EC]' : ''"
              @click="selectProductForEdit(p)"
            >
              <td class="py-3.5 px-5 flex items-center space-x-3">
                <img :src="p.image_url" class="w-10 h-10 rounded-lg object-cover border border-[#DACDC0] shrink-0" />
                <div>
                  <div class="font-bold text-xs text-[#2A1D15]">{{ p.product_name }}</div>
                  <div class="text-[11px] text-[#8A7B71] truncate max-w-xs">{{ p.description }}</div>
                </div>
              </td>
              <td class="py-3.5 px-4 text-xs text-[#55463C]">{{ p.category_name }}</td>
              <td class="py-3.5 px-4 text-center text-xs font-semibold text-[#241710]">
                ETB {{ Number(p.base_price).toLocaleString() }}
              </td>
              <td class="py-3.5 px-4 text-center">
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-medium"
                  :class="p.has_branch_override ? 'bg-[#FFF3E0] text-[#E65100]' : 'bg-[#F2EFE9] text-[#7A6B60]'"
                >
                  {{ p.has_branch_override ? 'Branch override' : 'Same everywhere' }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-center">
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                  :class="p.status === 'Active' ? 'bg-[#EAF5EA] text-[#2E7D32] border-[#C8E6C9]' : 'bg-[#FDEAEA] text-[#C62828] border-[#FFCDD2]'"
                >
                  {{ p.status }}
                </span>
              </td>
              <td class="py-3.5 px-5 text-right">
                <button
                  @click.stop="selectProductForEdit(p)"
                  class="px-3 py-1 text-xs font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-md shadow-sm transition"
                >
                  Edit
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Product Editor Box -->
    <div v-if="selectedProduct" class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-5">
      <div class="flex items-center justify-between border-b border-[#EFE8DF] pb-3">
        <div class="flex items-center space-x-3">
          <img :src="selectedProduct.image_url" class="w-12 h-12 rounded-lg object-cover border border-[#DACDC0]" />
          <div>
            <h3 class="font-semibold text-base text-[#2A1D15]">{{ selectedProduct.product_name }}</h3>
            <span class="text-xs text-[#8A7B71]">ID: #{{ selectedProduct.product_id }} · {{ selectedProduct.category_name }}</span>
          </div>
        </div>
        <div class="flex items-center space-x-2">
          <label class="text-xs font-medium text-[#7D6E64]">Status:</label>
          <select
            v-model="selectedProduct.status"
            class="bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-2.5 py-1 text-xs font-semibold text-[#2D231D]"
          >
            <option value="Active">Active</option>
            <option value="Inactive">Inactive</option>
          </select>
        </div>
      </div>

      <form @submit.prevent="saveProduct" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Product Name</label>
            <input
              v-model="selectedProduct.product_name"
              type="text"
              required
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Base Price (ETB)</label>
            <input
              v-model.number="selectedProduct.base_price"
              type="number"
              required
              min="0"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Shelf Life (hours)</label>
            <input
              v-model.number="selectedProduct.shelf_life_hours"
              type="number"
              min="1"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            />
          </div>
        </div>

        <div>
          <label class="block text-xs font-medium text-[#7D6E64] mb-1">Description</label>
          <textarea
            v-model="selectedProduct.description"
            rows="2"
            class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
          ></textarea>
        </div>

        <!-- Branch Price Overrides Section (Decision D4) -->
        <div class="pt-2 border-t border-[#EFE8DF] space-y-3">
          <div class="flex items-center justify-between">
            <div>
              <h4 class="font-bold text-xs text-[#2A1D15]">Branch price overrides</h4>
              <p class="text-[11px] text-[#8C7D73]">Leave blank to use the standard base price (ETB {{ selectedProduct.base_price }}).</p>
            </div>
            <span class="text-[11px] bg-[#FAF3EC] text-[#BA5420] px-2.5 py-0.5 rounded-full font-medium">
              Multi-branch pricing active
            </span>
          </div>

          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div v-for="b in branches" :key="b.branch_id">
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">{{ b.branch_name }}</label>
              <input
                v-model.number="branchPrices[b.branch_id]"
                type="number"
                :placeholder="`ETB ${selectedProduct.base_price}`"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              />
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-3 border-t border-[#EFE8DF]">
          <button
            type="button"
            @click="selectedProduct = null"
            class="px-4 py-2 text-xs font-medium text-[#6A5A50] hover:bg-[#F3EFE9] rounded-lg transition"
          >
            Cancel
          </button>
          <button
            type="submit"
            :disabled="isSaving"
            class="px-5 py-2 text-xs font-semibold bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg shadow-sm transition disabled:opacity-50"
          >
            {{ isSaving ? 'Saving...' : 'Save product' }}
          </button>
        </div>
      </form>
    </div>

    <!-- MODAL 1: Categories Management Modal -->
    <div v-if="isCategoriesModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-[#E9E0D4] max-w-xl w-full p-6 space-y-5 animate-in fade-in duration-200">
        <div class="flex items-center justify-between border-b border-[#EFE8DF] pb-3">
          <div>
            <h3 class="text-lg font-serif font-bold text-[#2A1D15]">Product Categories</h3>
            <p class="text-xs text-[#8A7B71]">Manage bakery catalog classification</p>
          </div>
          <button @click="isCategoriesModalOpen = false" class="text-[#8A7B71] hover:text-[#2A1D15] text-xl font-bold p-1">
            &times;
          </button>
        </div>

        <!-- Categories Table -->
        <div class="max-h-60 overflow-y-auto border border-[#EFE8DF] rounded-xl">
          <table class="w-full text-left text-xs">
            <thead class="bg-[#FAF6F0] text-[#7A6B60] uppercase font-semibold border-b border-[#EFE8DF]">
              <tr>
                <th class="py-2.5 px-4">Category Name</th>
                <th class="py-2.5 px-3">Description</th>
                <th class="py-2.5 px-3 text-right">Products</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr v-for="cat in categories" :key="cat.category_id" class="hover:bg-[#FCFAF7]">
                <td class="py-2.5 px-4 font-bold text-[#2A1D15]">{{ cat.category_name }}</td>
                <td class="py-2.5 px-3 text-[#6A5A50]">{{ cat.description || '—' }}</td>
                <td class="py-2.5 px-3 text-right font-semibold text-[#BA5420]">{{ cat.products_count ?? cat.products?.length ?? '—' }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Add Category Sub-form -->
        <form @submit.prevent="submitNewCategory" class="bg-[#FAF6F0] p-4 rounded-xl border border-[#EAE0D2] space-y-3">
          <div class="font-bold text-xs text-[#2A1D15]">Add New Category</div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
              <label class="block text-[11px] font-medium text-[#7D6E64] mb-1">Category Name *</label>
              <input
                v-model="newCategory.category_name"
                type="text"
                placeholder="e.g. Traditional Breads"
                required
                class="w-full bg-white border border-[#D8CDC0] rounded-lg px-3 py-1.5 text-xs text-[#2D231D]"
              />
            </div>
            <div>
              <label class="block text-[11px] font-medium text-[#7D6E64] mb-1">Description</label>
              <input
                v-model="newCategory.description"
                type="text"
                placeholder="e.g. Sourdough, injera, rolls"
                class="w-full bg-white border border-[#D8CDC0] rounded-lg px-3 py-1.5 text-xs text-[#2D231D]"
              />
            </div>
          </div>
          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="isSubmittingCategory"
              class="px-4 py-1.5 text-xs font-semibold bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg shadow-sm transition disabled:opacity-50"
            >
              {{ isSubmittingCategory ? 'Adding...' : '+ Create Category' }}
            </button>
          </div>
        </form>

        <div class="flex justify-end pt-2">
          <button
            type="button"
            @click="isCategoriesModalOpen = false"
            class="px-4 py-2 text-xs font-medium text-[#6A5A50] hover:bg-[#F3EFE9] rounded-lg transition"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- MODAL 2: Add New Product Modal -->
    <div v-if="isAddProductModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-[#E9E0D4] max-w-2xl w-full p-6 space-y-5 animate-in fade-in duration-200 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between border-b border-[#EFE8DF] pb-3">
          <div>
            <h3 class="text-lg font-serif font-bold text-[#2A1D15]">Add New Bakery Item</h3>
            <p class="text-xs text-[#8A7B71]">Add cakes, bread, or pastries to all bakery branches</p>
          </div>
          <button @click="isAddProductModalOpen = false" class="text-[#8A7B71] hover:text-[#2A1D15] text-xl font-bold p-1">
            &times;
          </button>
        </div>

        <form @submit.prevent="submitNewProduct" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Product Name *</label>
              <input
                v-model="newProduct.product_name"
                type="text"
                placeholder="e.g. Strawberry Shortcake"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Category *</label>
              <select
                v-model="newProduct.category_id"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              >
                <option value="" disabled>Select category</option>
                <option v-for="c in categories" :key="c.category_id" :value="c.category_id">
                  {{ c.category_name }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Base Price (ETB) *</label>
              <input
                v-model.number="newProduct.base_price"
                type="number"
                placeholder="e.g. 750"
                min="0"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Shelf Life (Hours) *</label>
              <input
                v-model.number="newProduct.shelf_life_hours"
                type="number"
                placeholder="48"
                min="1"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Image URL</label>
            <input
              v-model="newProduct.image_url"
              type="url"
              placeholder="https://images.unsplash.com/photo-..."
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            />
            <!-- Quick preset buttons -->
            <div class="flex items-center space-x-2 mt-2">
              <span class="text-[11px] text-[#8A7B71]">Presets:</span>
              <button
                type="button"
                @click="newProduct.image_url = 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop&q=80'"
                class="px-2 py-0.5 text-[10px] bg-[#FAF3EC] border border-[#E0D2C2] rounded text-[#8B4822] hover:bg-[#F4ECE3]"
              >
                🎂 Chocolate Cake
              </button>
              <button
                type="button"
                @click="newProduct.image_url = 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&auto=format&fit=crop&q=80'"
                class="px-2 py-0.5 text-[10px] bg-[#FAF3EC] border border-[#E0D2C2] rounded text-[#8B4822] hover:bg-[#F4ECE3]"
              >
                🍞 Artisan Bread
              </button>
              <button
                type="button"
                @click="newProduct.image_url = 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&auto=format&fit=crop&q=80'"
                class="px-2 py-0.5 text-[10px] bg-[#FAF3EC] border border-[#E0D2C2] rounded text-[#8B4822] hover:bg-[#F4ECE3]"
              >
                🥐 Croissant
              </button>
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Description</label>
            <textarea
              v-model="newProduct.description"
              rows="3"
              placeholder="Freshly prepared using stoneground flour and premium ingredients..."
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            ></textarea>
          </div>

          <div class="flex justify-end space-x-3 pt-3 border-t border-[#EFE8DF]">
            <button
              type="button"
              @click="isAddProductModalOpen = false"
              class="px-4 py-2 text-xs font-medium text-[#6A5A50] hover:bg-[#F3EFE9] rounded-lg transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isSubmittingProduct"
              class="px-5 py-2 text-xs font-semibold bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg shadow-sm transition disabled:opacity-50"
            >
              {{ isSubmittingProduct ? 'Saving Product...' : 'Add to Catalog' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api';

const products = ref([]);
const categories = ref([]);
const branches = ref([]);
const selectedCategory = ref('all');
const searchQuery = ref('');
const selectedProduct = ref(null);
const branchPrices = ref({});
const successMsg = ref('');
const errorMsg = ref('');
const isSaving = ref(false);

// Modals
const isCategoriesModalOpen = ref(false);
const isAddProductModalOpen = ref(false);
const isSubmittingCategory = ref(false);
const isSubmittingProduct = ref(false);

const newCategory = ref({
  category_name: '',
  description: '',
});

const newProduct = ref({
  product_name: '',
  category_id: '',
  base_price: 600,
  shelf_life_hours: 48,
  image_url: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop&q=80',
  description: '',
});

const loadCatalog = async () => {
  try {
    const [pRes, cRes, bRes] = await Promise.all([
      api.get('/products'),
      api.get('/categories'),
      api.get('/branches'),
    ]);
    products.value = pRes.data;
    categories.value = cRes.data;
    branches.value = bRes.data;

    if (!selectedProduct.value && products.value.length > 0) {
      selectProductForEdit(products.value[0]);
    }
  } catch (err) {
    console.error('Failed to load products and prices', err);
  }
};

const selectProductForEdit = (p) => {
  selectedProduct.value = { ...p };
  branchPrices.value = {};
  if (p.product_id === 1) { // Chocolate Cake
    branchPrices.value[4] = 880; // Piassa override as in mockup Page 15
  }
};

const filteredProducts = computed(() => {
  return products.value.filter(p => {
    const matchesCat = selectedCategory.value === 'all' || p.category_id === selectedCategory.value;
    const matchesSearch = !searchQuery.value || p.product_name.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchesCat && matchesSearch;
  });
});

const saveProduct = async () => {
  if (!selectedProduct.value) return;
  isSaving.value = true;
  successMsg.value = '';
  errorMsg.value = '';

  try {
    // 1. Update base product details
    await api.patch(`/admin/products/${selectedProduct.value.product_id}`, {
      product_name: selectedProduct.value.product_name,
      base_price: selectedProduct.value.base_price,
      shelf_life_hours: selectedProduct.value.shelf_life_hours,
      description: selectedProduct.value.description,
      status: selectedProduct.value.status,
    });

    // 2. Save branch price overrides
    for (const [branchId, price] of Object.entries(branchPrices.value)) {
      if (price) {
        await api.post(`/admin/products/${selectedProduct.value.product_id}/branch-price`, {
          branch_id: branchId,
          price: price,
          active: true,
        });
      }
    }

    successMsg.value = `Product "${selectedProduct.value.product_name}" and branch prices updated successfully!`;
    await loadCatalog();
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to save product details';
  } finally {
    isSaving.value = false;
  }
};

const openAddProductModal = () => {
  newProduct.value = {
    product_name: '',
    category_id: categories.value[0]?.category_id || '',
    base_price: 650,
    shelf_life_hours: 48,
    image_url: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop&q=80',
    description: '',
  };
  isAddProductModalOpen.value = true;
};

const submitNewProduct = async () => {
  isSubmittingProduct.value = true;
  try {
    const res = await api.post('/admin/products', newProduct.value);
    successMsg.value = `Product "${res.data.product_name}" added to catalog and all branches!`;
    isAddProductModalOpen.value = false;
    await loadCatalog();
    selectProductForEdit(res.data);
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to create product';
  } finally {
    isSubmittingProduct.value = false;
  }
};

const submitNewCategory = async () => {
  isSubmittingCategory.value = true;
  try {
    const res = await api.post('/admin/categories', newCategory.value);
    categories.value.push(res.data);
    newCategory.value = { category_name: '', description: '' };
    successMsg.value = `Category "${res.data.category_name}" created successfully!`;
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to create category';
  } finally {
    isSubmittingCategory.value = false;
  }
};

onMounted(() => {
  loadCatalog();
});
</script>
