<template>
  <div class="space-y-8 pb-16 font-sans">
    <!-- Top Hero Banner matching Image 4 -->
    <div class="relative bg-gradient-to-r from-[#FFF1F2] via-[#FCE7F3] to-[#FFF1F2] border-b border-[#FECDD3] py-10 px-4 sm:px-8 overflow-hidden">
      <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
        <div class="space-y-2 text-center md:text-left">
          <span class="text-xs font-bold uppercase tracking-widest text-[#E11D48]">Shop Our Bakery</span>
          <h1 class="text-3xl sm:text-5xl font-serif font-black text-[#4A1521]">
            Our Delicious Products
          </h1>
          <p class="text-xs sm:text-sm text-[#7A3644] flex items-center justify-center md:justify-start space-x-1.5 font-medium">
            <span>Freshly baked with love, just for you!</span>
            <span>💖</span>
          </p>
        </div>

        <!-- Decorative Mini Showcase -->
        <div class="flex items-center space-x-3">
          <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden border-2 border-white shadow-md">
            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300&auto=format&fit=crop&q=80" alt="Cake" class="w-full h-full object-cover" />
          </div>
          <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden border-2 border-white shadow-md">
            <img src="https://images.unsplash.com/photo-1509440159596-0249088772ff?w=300&auto=format&fit=crop&q=80" alt="Bread" class="w-full h-full object-cover" />
          </div>
          <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden border-2 border-white shadow-md">
            <img src="https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=300&auto=format&fit=crop&q=80" alt="Croissant" class="w-full h-full object-cover" />
          </div>
        </div>
      </div>
    </div>

    <!-- Active Branch Notice -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-white border border-[#FECDD3] px-4 py-3 rounded-2xl flex flex-col sm:flex-row items-center justify-between text-xs text-[#4B1A24] shadow-2xs gap-2">
        <div class="flex items-center space-x-2">
          <span class="w-2.5 h-2.5 rounded-full bg-[#10B981] animate-pulse"></span>
          <span>Showing prices and live stock for <strong>{{ currentBranchName }} Branch</strong>. Stock cannot be transferred automatically.</span>
        </div>
        <router-link to="/branches" class="font-bold text-[#E11D48] hover:text-[#BE123C] underline text-xs shrink-0 flex items-center space-x-1">
          <span>Choose another branch</span>
          <span>→</span>
        </router-link>
      </div>
    </div>

    <!-- Main Content: Sidebar (3 cols) & Products Grid (9 cols) matching Image 4 -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left Filter Sidebar matching Image 4 -->
      <aside class="lg:col-span-3 bg-white rounded-3xl border border-[#F3E8E8] p-6 space-y-6 shadow-2xs">
        <div class="flex items-center justify-between border-b border-[#F8E7EA] pb-3">
          <div class="flex items-center space-x-2 text-[#38121A] font-bold text-sm">
            <span>⚙️</span>
            <span>Filter Products</span>
          </div>
          <button @click="resetFilters" class="text-[11px] text-[#E11D48] hover:underline font-semibold">
            Clear all
          </button>
        </div>

        <!-- Categories -->
        <div class="space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-wider text-[#38121A]">Categories</h4>
          <div class="space-y-2 text-xs">
            <label class="flex items-center justify-between cursor-pointer group">
              <div class="flex items-center space-x-2">
                <input
                  type="checkbox"
                  :checked="selectedCategories.length === 0"
                  @change="selectedCategories = []"
                  class="rounded text-[#E11D48] focus:ring-[#E11D48]"
                />
                <span :class="selectedCategories.length === 0 ? 'font-bold text-[#E11D48]' : 'text-[#4B1A24]'">All Categories</span>
              </div>
              <span class="text-[11px] text-[#9CA3AF]">({{ allProducts.length }})</span>
            </label>

            <label
              v-for="cat in categoryList"
              :key="cat.name"
              class="flex items-center justify-between cursor-pointer group"
            >
              <div class="flex items-center space-x-2">
                <input
                  type="checkbox"
                  :value="cat.name"
                  v-model="selectedCategories"
                  class="rounded text-[#E11D48] focus:ring-[#E11D48]"
                />
                <span :class="selectedCategories.includes(cat.name) ? 'font-bold text-[#E11D48]' : 'text-[#4B1A24]'">
                  {{ cat.name }}
                </span>
              </div>
              <span class="text-[11px] text-[#9CA3AF]">({{ cat.count }})</span>
            </label>
          </div>
        </div>

        <!-- Price Range Slider matching Image 4 -->
        <div class="space-y-3 border-t border-[#F8E7EA] pt-4">
          <h4 class="text-xs font-bold uppercase tracking-wider text-[#38121A]">Price Range</h4>
          <input
            v-model.number="maxPrice"
            type="range"
            min="50"
            max="1500"
            step="50"
            class="w-full accent-[#E11D48] cursor-pointer"
          />
          <div class="flex items-center justify-between text-xs font-bold text-[#881337]">
            <span>ETB 50</span>
            <span>Up to ETB {{ maxPrice }}</span>
          </div>
        </div>

        <!-- Rating Filter matching Image 4 -->
        <div class="space-y-3 border-t border-[#F8E7EA] pt-4">
          <h4 class="text-xs font-bold uppercase tracking-wider text-[#38121A]">Rating</h4>
          <div class="space-y-2 text-xs">
            <label
              v-for="r in [5, 4, 3]"
              :key="r"
              class="flex items-center space-x-2 cursor-pointer"
            >
              <input
                type="radio"
                :value="r"
                v-model="minRating"
                class="text-[#E11D48] focus:ring-[#E11D48]"
              />
              <span class="text-[#F59E0B]">{'★'.repeat(r)}</span>
              <span class="text-[#4B1A24] font-medium">&amp; Up</span>
            </label>
          </div>
        </div>

        <!-- Reset Button matching Image 4 -->
        <button
          @click="resetFilters"
          class="w-full py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold rounded-xl transition shadow-xs flex items-center justify-center space-x-1.5"
        >
          <span>↺ Reset Filters</span>
        </button>
      </aside>

      <!-- Right Products Grid (9 cols) matching Image 4 -->
      <section class="lg:col-span-9 space-y-6">
        <!-- Top Toolbar -->
        <div class="bg-white rounded-2xl border border-[#F3E8E8] p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-2xs">
          <div class="text-xs font-medium text-[#7A6B60]">
            Showing <strong class="text-[#38121A]">{{ paginatedProducts.length }}</strong> of {{ filteredProducts.length }} products
          </div>

          <div class="flex items-center space-x-3">
            <div class="flex items-center space-x-1.5 text-xs">
              <span class="text-[#7A6B60]">Sort by:</span>
              <select
                v-model="sortBy"
                class="bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-3 py-1.5 text-xs font-semibold text-[#4B1A24] focus:outline-none focus:ring-1 focus:ring-[#E11D48]"
              >
                <option value="popular">Most Popular</option>
                <option value="price_low">Price: Low to High</option>
                <option value="price_high">Price: High to Low</option>
                <option value="rating">Highest Rated</option>
              </select>
            </div>

            <!-- Grid vs List View Toggles -->
            <div class="flex items-center space-x-1 border border-[#F2D7DC] rounded-xl p-0.5">
              <button
                @click="viewMode = 'grid'"
                class="p-1.5 rounded-lg text-xs"
                :class="viewMode === 'grid' ? 'bg-[#E11D48] text-white' : 'text-[#7A6B60] hover:bg-[#FAF7F5]'"
              >
                ▦
              </button>
              <button
                @click="viewMode = 'list'"
                class="p-1.5 rounded-lg text-xs"
                :class="viewMode === 'list' ? 'bg-[#E11D48] text-white' : 'text-[#7A6B60] hover:bg-[#FAF7F5]'"
              >
                ☰
              </button>
            </div>
          </div>
        </div>

        <!-- Products Grid matching Image 4 -->
        <div
          v-if="viewMode === 'grid'"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"
        >
          <div
            v-for="product in paginatedProducts"
            :key="product.product_id"
            class="bg-white rounded-2xl border border-[#F3E8E8] overflow-hidden shadow-2xs hover:shadow-md transition-all duration-300 flex flex-col justify-between group p-3.5 relative"
          >
            <!-- Badges: Sold Out or Discount -->
            <div class="absolute top-5 left-5 z-10 flex flex-col space-y-1">
              <span
                v-if="(product.available ?? 0) <= 0"
                class="bg-[#374151] text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs uppercase tracking-wider"
              >
                Sold Out
              </span>
              <span
                v-else-if="product.discount"
                class="bg-[#E11D48] text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs"
              >
                {{ product.discount }}
              </span>
            </div>

            <!-- Wishlist Heart Button -->
            <button
              @click="toggleWishlist(product)"
              class="absolute top-5 right-5 z-10 w-7 h-7 rounded-full bg-white/90 backdrop-blur-xs flex items-center justify-center text-xs shadow-xs hover:scale-110 transition"
              :class="isWishlisted(product.product_id) ? 'text-[#E11D48]' : 'text-[#9CA3AF]'"
            >
              {{ isWishlisted(product.product_id) ? '♥' : '♡' }}
            </button>

            <!-- Card Thumbnail -->
            <router-link :to="`/product/${product.product_id}`" class="block">
              <div class="w-full aspect-square rounded-xl overflow-hidden bg-[#FFF1F2] mb-3 relative">
                <img
                  :src="product.image_url"
                  :alt="product.product_name"
                  class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
                  :class="(product.available ?? 0) <= 0 ? 'grayscale-40 opacity-80' : ''"
                />
              </div>

              <h3 class="text-xs sm:text-sm font-serif font-bold text-[#38121A] group-hover:text-[#E11D48] transition line-clamp-1">
                {{ product.product_name }}
              </h3>

              <!-- Rating with Review Count -->
              <div class="flex items-center space-x-1 text-[11px] text-[#F59E0B] mt-1">
                <span>★</span>
                <span class="font-bold text-[#38121A]">{{ product.rating || '4.8' }}</span>
                <span class="text-[#9CA3AF]">({{ product.reviewsCount || '120' }})</span>
              </div>
            </router-link>

            <!-- Price & Add To Cart Button matching Image 4 -->
            <div class="mt-3 pt-2.5 border-t border-[#F8E7EA] space-y-2">
              <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                  <span class="text-sm font-black text-[#881337]">
                    ETB {{ product.resolved_price || product.base_price }}
                  </span>
                  <span v-if="product.originalPrice" class="text-xs text-[#9CA3AF] line-through">
                    ETB {{ product.originalPrice }}
                  </span>
                </div>
                <span v-if="(product.available ?? 0) > 0" class="text-[10px] text-[#059669] font-bold">
                  {{ product.available }} in stock
                </span>
                <span v-else class="text-[10px] text-[#DC2626] font-bold">
                  Sold out
                </span>
              </div>

              <div v-if="(product.available ?? 0) <= 0" class="space-y-1">
                <button
                  disabled
                  class="w-full py-2 bg-[#F3F4F6] text-[#9CA3AF] text-xs font-bold rounded-xl cursor-not-allowed flex items-center justify-center space-x-1"
                >
                  <span>Sold out at {{ currentBranchName }}</span>
                </button>
                <button
                  type="button"
                  @click="openAvailabilityModal(product)"
                  class="w-full py-1 text-[11px] text-[#BA5420] hover:text-[#9E4314] font-semibold underline text-center"
                >
                  Check other branches →
                </button>
              </div>

              <button
                v-else
                @click="addToCart(product)"
                class="w-full py-2 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center space-x-1.5 active:scale-95"
              >
                <span>🛒</span>
                <span>Add to Cart</span>
              </button>
            </div>
          </div>
        </div>

        <!-- List View -->
        <div v-else class="space-y-4">
          <div
            v-for="product in paginatedProducts"
            :key="product.product_id"
            class="bg-white rounded-2xl border border-[#F3E8E8] p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-2xs hover:shadow-xs transition"
          >
            <div class="flex items-center space-x-4">
              <img
                :src="product.image_url"
                :alt="product.product_name"
                class="w-20 h-20 rounded-xl object-cover shrink-0"
                :class="(product.available ?? 0) <= 0 ? 'grayscale-40 opacity-80' : ''"
              />
              <div>
                <h3 class="font-serif font-bold text-sm text-[#38121A]">{{ product.product_name }}</h3>
                <div class="flex items-center space-x-1 text-xs text-[#F59E0B] mt-1">
                  <span>★</span>
                  <span class="font-bold text-[#38121A]">{{ product.rating }}</span>
                  <span class="text-[#9CA3AF]">({{ product.reviewsCount }} reviews)</span>
                </div>
                <p class="text-xs text-[#7A6B60] mt-1 max-w-md line-clamp-1">{{ product.description }}</p>
              </div>
            </div>

            <div class="flex items-center space-x-4 shrink-0">
              <div class="text-right">
                <div class="text-sm font-black text-[#881337]">ETB {{ product.resolved_price || product.base_price }}</div>
                <div v-if="product.originalPrice" class="text-[11px] text-[#9CA3AF] line-through">ETB {{ product.originalPrice }}</div>
                <div class="text-[10px] font-bold" :class="(product.available ?? 0) > 0 ? 'text-[#059669]' : 'text-[#DC2626]'">
                  {{ (product.available ?? 0) > 0 ? `${product.available} available` : 'Sold out' }}
                </div>
              </div>

              <div v-if="(product.available ?? 0) <= 0" class="space-y-1 text-right">
                <button disabled class="px-4 py-2.5 bg-[#F3F4F6] text-[#9CA3AF] text-xs font-bold rounded-xl cursor-not-allowed">
                  Sold Out
                </button>
                <div>
                  <button @click="openAvailabilityModal(product)" class="text-[11px] text-[#BA5420] underline font-semibold">
                    Other branches
                  </button>
                </div>
              </div>

              <button
                v-else
                @click="addToCart(product)"
                class="px-5 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold rounded-xl shadow-xs"
              >
                Add to Cart
              </button>
            </div>
          </div>
        </div>

        <!-- Pagination matching Image 4 -->
        <div class="flex items-center justify-center space-x-2 pt-6">
          <button
            v-for="page in totalPages"
            :key="page"
            @click="currentPage = page"
            class="w-9 h-9 rounded-xl text-xs font-bold transition flex items-center justify-center"
            :class="currentPage === page ? 'bg-[#E11D48] text-white shadow-xs' : 'bg-white border border-[#F2D7DC] text-[#4B1A24] hover:bg-[#FFF1F2]'"
          >
            {{ page }}
          </button>
          <button
            v-if="currentPage < totalPages"
            @click="currentPage++"
            class="px-3.5 h-9 rounded-xl text-xs font-bold bg-white border border-[#F2D7DC] text-[#4B1A24] hover:bg-[#FFF1F2]"
          >
            Next →
          </button>
        </div>
      </section>
    </div>

    <!-- Branch Availability Modal -->
    <div
      v-if="modalProduct"
      class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-3xl border border-[#FECDD3] max-w-lg w-full p-6 space-y-4 shadow-2xl animate-in zoom-in-95">
        <div class="flex items-start justify-between border-b border-[#F8E7EA] pb-3">
          <div>
            <span class="text-[11px] font-bold text-[#E11D48] uppercase tracking-wider">Branch Stock Finder</span>
            <h3 class="font-serif font-black text-xl text-[#38121A]">{{ modalProduct.product_name }}</h3>
          </div>
          <button @click="modalProduct = null" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
        </div>

        <p class="text-xs text-[#5B2934] leading-relaxed">
          This item is short or sold out at <strong>{{ currentBranchName }}</strong>. The branches below have live inventory available. Choose one to switch your shopping location:
        </p>

        <div v-if="loadingAvailability" class="py-6 text-center text-xs text-[#8C7D73]">
          Checking branch inventories...
        </div>

        <div v-else class="space-y-2.5 max-h-60 overflow-y-auto">
          <div
            v-for="b in modalAvailabilityBranches"
            :key="b.branch_id"
            class="p-3 rounded-2xl border flex items-center justify-between"
            :class="b.available > 0 ? 'bg-[#FDFBF7] border-[#E7DFD5]' : 'bg-gray-50 border-gray-200 opacity-60'"
          >
            <div>
              <div class="font-bold text-xs text-[#2A1D15] flex items-center space-x-1.5">
                <span>{{ b.branch_name }}</span>
                <span v-if="b.available > 0" class="text-[10px] text-[#059669] font-bold bg-[#ECFDF5] px-2 py-0.5 rounded-full">
                  {{ b.available }} in stock
                </span>
                <span v-else class="text-[10px] text-[#DC2626] font-bold bg-[#FEF2F2] px-2 py-0.5 rounded-full">
                  Sold out
                </span>
              </div>
              <div class="text-[11px] text-[#7A6B60] mt-0.5">{{ b.address || b.city }} &middot; ETB {{ b.price }}</div>
            </div>

            <button
              v-if="b.available > 0 && b.branch_id !== branchStore.selectedBranchId"
              @click="switchBranchTo(b)"
              class="px-3 py-1.5 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-bold rounded-xl transition shadow-xs"
            >
              Switch branch
            </button>
            <span v-else-if="b.branch_id === branchStore.selectedBranchId" class="text-xs text-[#881337] font-bold">
              Current branch
            </span>
          </div>
        </div>

        <div class="pt-2 flex justify-end">
          <button
            @click="modalProduct = null"
            class="px-5 py-2 bg-[#FAF7F5] border border-[#F2D7DC] text-[#4B1A24] hover:bg-[#FFF1F2] text-xs font-semibold rounded-xl"
          >
            Close
          </button>
        </div>
      </div>
    </div>

    <!-- Added to Cart Toast -->
    <div
      v-if="toastMessage"
      class="fixed bottom-6 right-6 z-50 bg-[#38121A] text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center space-x-3 text-xs border border-[#E11D48] animate-in slide-in-from-bottom"
    >
      <span class="text-lg">💖</span>
      <span>{{ toastMessage }}</span>
      <router-link to="/cart" class="underline font-bold text-[#F43F5E] ml-2">View Cart →</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
import { useBranchStore } from '../../stores/branch';
import { useCartStore } from '../../stores/cart';
import api from '../../api';

const route = useRoute();
const branchStore = useBranchStore();
const cartStore = useCartStore();

const products = ref([]);
const selectedCategories = ref([]);
const maxPrice = ref(1500);
const minRating = ref(0);
const sortBy = ref('popular');
const viewMode = ref('grid');
const currentPage = ref(1);
const pageSize = 12;
const toastMessage = ref(null);
const wishlistedIds = ref([1, 4]);

const modalProduct = ref(null);
const modalAvailabilityBranches = ref([]);
const loadingAvailability = ref(false);

const categoryList = [
  { name: 'Birthday Cakes', count: 12 },
  { name: 'Wedding Cakes', count: 8 },
  { name: 'Bread', count: 10 },
  { name: 'Cupcakes', count: 14 },
  { name: 'Pastries', count: 11 },
  { name: 'Cookies', count: 7 },
];

const currentBranchName = computed(() => branchStore.selectedBranch?.branch_name || 'Bole');

async function loadProducts(branchId) {
  try {
    const res = await api.get(`/catalog/products?branch_id=${branchId}`);
    if (res.data && res.data.length > 0) {
      products.value = res.data.map((p, idx) => ({
        ...p,
        originalPrice: idx % 2 === 0 ? Math.round((p.resolved_price || p.base_price) * 1.15) : null,
        discount: idx % 2 === 0 ? '-15%' : null,
        rating: (4.6 + (idx % 4) * 0.1).toFixed(1),
        reviewsCount: 60 + idx * 15,
      }));
    }
  } catch (err) {
    console.error('Failed to fetch products', err);
  }
}

onMounted(async () => {
  if (route.query.category) {
    selectedCategories.value = [route.query.category];
  }
  const branchId = branchStore.selectedBranchId || 2;
  await loadProducts(branchId);
});

// Watch branch switch and refresh products automatically
watch(
  () => branchStore.selectedBranchId,
  (newBranchId) => {
    if (newBranchId) {
      loadProducts(newBranchId);
    }
  }
);

const allProducts = computed(() => products.value);

const filteredProducts = computed(() => {
  return allProducts.value.filter((p) => {
    const price = p.resolved_price || p.base_price;
    if (price > maxPrice.value) return false;
    if (minRating.value > 0 && Number(p.rating || 0) < minRating.value) return false;
    if (selectedCategories.value.length > 0) {
      const match = selectedCategories.value.some((c) =>
        (p.category_name || p.category || '').toLowerCase().includes(c.toLowerCase()) ||
        p.product_name.toLowerCase().includes(c.toLowerCase())
      );
      if (!match) return false;
    }
    return true;
  });
});

const sortedProducts = computed(() => {
  const list = [...filteredProducts.value];
  if (sortBy.value === 'price_low') {
    return list.sort((a, b) => (a.resolved_price || a.base_price) - (b.resolved_price || b.base_price));
  }
  if (sortBy.value === 'price_high') {
    return list.sort((a, b) => (b.resolved_price || b.base_price) - (a.resolved_price || a.base_price));
  }
  if (sortBy.value === 'rating') {
    return list.sort((a, b) => Number(b.rating || 0) - Number(a.rating || 0));
  }
  return list;
});

const totalPages = computed(() => Math.max(1, Math.ceil(sortedProducts.value.length / pageSize)));

const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return sortedProducts.value.slice(start, start + pageSize);
});

function resetFilters() {
  selectedCategories.value = [];
  maxPrice.value = 1500;
  minRating.value = 0;
  sortBy.value = 'popular';
}

function isWishlisted(id) {
  return wishlistedIds.value.includes(id);
}

function toggleWishlist(p) {
  const idx = wishlistedIds.value.indexOf(p.product_id);
  if (idx >= 0) {
    wishlistedIds.value.splice(idx, 1);
  } else {
    wishlistedIds.value.push(p.product_id);
  }
}

function addToCart(p) {
  const res = cartStore.addItem({
    product_id: p.product_id,
    product_name: p.product_name,
    resolved_price: p.resolved_price || p.base_price,
    image_url: p.image_url,
    available: typeof p.available === 'number' ? p.available : 0,
  }, branchStore.selectedBranchId, 1);

  if (res.success) {
    toastMessage.value = `Added ${p.product_name} to your cart!`;
  } else {
    toastMessage.value = res.message;
  }

  setTimeout(() => {
    toastMessage.value = null;
  }, 3000);
}

async function openAvailabilityModal(product) {
  modalProduct.value = product;
  loadingAvailability.value = true;
  try {
    const res = await api.get(`/catalog/products/${product.product_id}/branch-availability`);
    modalAvailabilityBranches.value = res.data?.branches || [];
  } catch (err) {
    console.error('Failed fetching branch availability', err);
    modalAvailabilityBranches.value = [];
  } finally {
    loadingAvailability.value = false;
  }
}

function switchBranchTo(b) {
  branchStore.setSelectedBranch(b.branch_id);
  modalProduct.value = null;
  toastMessage.value = `Switched shopping location to ${b.branch_name}.`;
  setTimeout(() => {
    toastMessage.value = null;
  }, 3000);
}
</script>
