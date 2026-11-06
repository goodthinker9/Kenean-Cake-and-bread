<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 font-sans">
    <!-- Branch Context Banner matching Page 4 -->
    <div class="flex items-center justify-between bg-[#F4ECE3] border border-[#E3D6C8] px-4 py-2.5 rounded-xl text-xs text-[#523F33]">
      <div class="flex items-center space-x-2">
        <span class="w-2 h-2 rounded-full bg-[#2E7D32]"></span>
        <span>
          Showing stock for <strong>{{ currentBranchName }}</strong>. You can only order quantities this branch has available.
        </span>
      </div>
      <router-link
        to="/branches"
        class="font-semibold text-[#BA5420] hover:text-[#9E4314] underline text-xs ml-2 shrink-0"
      >
        Switch branch
      </router-link>
    </div>

    <!-- Page Title & Sort Row -->
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pt-2">
      <div>
        <div class="text-[11px] font-bold uppercase tracking-widest text-[#8C7D73]">
          {{ currentBranchName.toUpperCase() }} &middot; {{ currentBranchCode }}
        </div>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-[#2A1D15] mt-1">
          Cakes and bread
        </h1>
      </div>

      <!-- Sort dropdown -->
      <div class="flex items-center space-x-2">
        <span class="text-xs text-[#7A6B60] font-medium">Sort:</span>
        <select
          v-model="sortBy"
          class="bg-white border border-[#DDD2C5] rounded-xl px-3 py-1.5 text-xs font-semibold text-[#4A3B32] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="popular">Popular</option>
          <option value="price_low">Price: Low to High</option>
          <option value="price_high">Price: High to Low</option>
          <option value="name">Product Name</option>
        </select>
      </div>
    </div>

    <!-- Category Filter Pills matching Page 4 -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-1">
      <button
        v-for="filter in filterOptions"
        :key="filter.id"
        @click="activeFilter = filter.id"
        class="px-4 py-2 rounded-full text-xs font-semibold transition shrink-0"
        :class="activeFilter === filter.id ? 'bg-[#2A1D15] text-white shadow-xs' : 'bg-white border border-[#DDD2C5] text-[#55463C] hover:bg-[#F2EAE0]'"
      >
        {{ filter.label }}
      </button>
    </div>

    <!-- Products Grid -->
    <div v-if="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-2">
      <div v-for="i in 4" :key="i" class="h-80 bg-white/60 animate-pulse rounded-2xl border border-[#EAE1D5]"></div>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-2">
      <div
        v-for="product in sortedProducts"
        :key="product.product_id"
        class="bg-white rounded-2xl border border-[#EAE1D5] overflow-hidden flex flex-col justify-between p-4 shadow-2xs hover:shadow-sm transition group"
      >
        <!-- Product Image Preview (warm minimalist illustrated SVG card) -->
        <router-link :to="`/product/${product.product_id}`" class="block">
          <div class="w-full aspect-square bg-[#F7F0E6] rounded-xl flex items-center justify-center p-6 mb-4 group-hover:scale-102 transition duration-200">
            <!-- Illustrated Cake SVG -->
            <svg
              v-if="product.category?.category_name === 'Cakes' || product.category_id === 1"
              class="w-24 h-24 drop-shadow-xs"
              viewBox="0 0 100 100"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <!-- Cherry / Berry topping -->
              <circle cx="50" cy="24" r="5" fill="#D32F2F" />
              <!-- Top Tier -->
              <rect x="30" y="32" width="40" height="20" rx="4" :fill="product.product_name.includes('Vanilla') ? '#F2DEB9' : '#4E3120'" />
              <path d="M 30 42 Q 40 46 50 42 Q 60 46 70 42" stroke="#FFFFFF" stroke-width="2.5" stroke-linecap="round" fill="none" />
              <!-- Bottom Tier -->
              <rect x="20" y="52" width="60" height="28" rx="6" :fill="product.product_name.includes('Vanilla') ? '#E8CFA0' : '#3E2415'" />
              <path d="M 20 64 Q 35 70 50 64 Q 65 70 80 64" stroke="#FFFFFF" stroke-width="3" stroke-linecap="round" fill="none" />
            </svg>

            <!-- Illustrated Croissant / Pastry SVG -->
            <svg
              v-else-if="product.category?.category_name === 'Pastries' || product.product_name.toLowerCase().includes('croissant')"
              class="w-24 h-24 drop-shadow-xs"
              viewBox="0 0 100 100"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <ellipse cx="50" cy="55" rx="36" ry="24" fill="#C98845" />
              <path d="M 20 62 C 30 40 70 40 80 62 C 70 70 30 70 20 62 Z" fill="#E2A662" />
              <path d="M 36 45 L 34 68 M 48 42 L 48 69 M 62 45 L 64 68" stroke="#A8682A" stroke-width="2.5" stroke-linecap="round" />
            </svg>

            <!-- Illustrated Bread Loaf SVG -->
            <svg
              v-else
              class="w-24 h-24 drop-shadow-xs"
              viewBox="0 0 100 100"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <ellipse cx="50" cy="54" rx="34" ry="22" :fill="product.product_name.includes('Wheat') ? '#8D5832' : '#D18B47'" />
              <path d="M 35 44 L 39 58 M 49 42 L 51 58 M 63 44 L 61 58" stroke="#FDEED9" stroke-width="3" stroke-linecap="round" />
            </svg>
          </div>

          <!-- Product Name -->
          <h3 class="text-sm font-serif font-bold text-[#2A1D15] hover:text-[#BA5420] transition">
            {{ product.product_name }}
          </h3>
        </router-link>

        <!-- Stock Availability Badge matching Page 4 exactly -->
        <div class="mt-1.5 mb-3">
          <span
            v-if="product.available > 10"
            class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-[#2E7D32] bg-[#E8F5E9]"
          >
            {{ product.available }} available
          </span>
          <span
            v-else-if="product.available > 0"
            class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-[#B76E00] bg-[#FFF8E1]"
          >
            Only {{ product.available }} left
          </span>
          <span
            v-else
            class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold text-[#C62828] bg-[#FFEBEE]"
          >
            Sold out here
          </span>
        </div>

        <!-- Price & Action Button matching Page 4 -->
        <div class="flex items-center justify-between pt-2 border-t border-[#F5EFE8]">
          <span class="text-sm font-bold text-[#2A1D15]">
            ETB {{ product.resolved_price || product.base_price }}
          </span>

          <button
            v-if="product.available > 0"
            @click="addToCart(product)"
            class="px-3.5 py-1.5 bg-[#BA5420] hover:bg-[#9E4314] active:scale-95 text-white text-xs font-bold rounded-lg transition shadow-2xs flex items-center space-x-1"
          >
            <span>Add to cart</span>
          </button>

          <button
            v-else
            @click="notifyMe(product)"
            class="px-3 py-1.5 bg-white border border-[#D8CDC0] hover:bg-[#F2EAE0] text-[#7A6B60] text-xs font-semibold rounded-lg transition shadow-2xs"
          >
            Notify me
          </button>
        </div>
      </div>
    </div>

    <!-- Notification Toast if item added -->
    <div
      v-if="addedToast"
      class="fixed bottom-6 right-6 z-50 bg-[#2A1D15] text-white px-4 py-3 rounded-xl shadow-lg flex items-center space-x-3 text-xs animate-in slide-in-from-bottom"
    >
      <span class="w-2 h-2 rounded-full bg-[#2E7D32]"></span>
      <span>Added <strong>{{ addedToast }}</strong> to your cart!</span>
      <router-link to="/cart" class="underline text-[#E5B869] font-bold">View Cart</router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useBranchStore } from '../../stores/branch';
import { useCartStore } from '../../stores/cart';
import api from '../../api';

const branchStore = useBranchStore();
const cartStore = useCartStore();

const products = ref([]);
const loading = ref(true);
const sortBy = ref('popular');
const activeFilter = ref('all');
const addedToast = ref(null);

const filterOptions = [
  { id: 'all', label: 'All' },
  { id: 'cakes', label: 'Cakes' },
  { id: 'bread', label: 'Bread' },
  { id: 'pastries', label: 'Pastries' },
  { id: 'available', label: 'Available now' },
];

const currentBranchName = computed(() => {
  return branchStore.selectedBranch?.branch_name || 'Bole';
});

const currentBranchCode = computed(() => {
  const b = branchStore.selectedBranch;
  return b?.is_main ? 'Main' : `BRANCH ${String(b?.branch_id || 1).padStart(2, '0')}`;
});

onMounted(() => {
  loadProducts();
});

watch(
  () => branchStore.selectedBranchId,
  () => {
    loadProducts();
  }
);

async function loadProducts() {
  loading.value = true;
  try {
    const branchId = branchStore.selectedBranchId || 2;
    const res = await api.get(`/catalog/products?branch_id=${branchId}`);
    products.value = res.data;
  } catch (err) {
    console.error('Failed loading products', err);
  } finally {
    loading.value = false;
  }
}

const filteredProducts = computed(() => {
  return products.value.filter((p) => {
    const catName = p.category?.category_name?.toLowerCase() || '';
    if (activeFilter.value === 'cakes') return catName.includes('cake');
    if (activeFilter.value === 'bread') return catName.includes('bread');
    if (activeFilter.value === 'pastries') return catName.includes('pastry') || p.product_name.toLowerCase().includes('croissant');
    if (activeFilter.value === 'available') return p.available > 0;
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
  if (sortBy.value === 'name') {
    return list.sort((a, b) => a.product_name.localeCompare(b.product_name));
  }
  return list;
});

function addToCart(product) {
  const success = cartStore.addItem(product, branchStore.selectedBranchId);
  if (success) {
    addedToast.value = product.product_name;
    setTimeout(() => {
      addedToast.value = null;
    }, 2800);
  }
}

function notifyMe(product) {
  alert(`You will be notified as soon as ${product.product_name} is restocked at ${currentBranchName.value}!`);
}
</script>
