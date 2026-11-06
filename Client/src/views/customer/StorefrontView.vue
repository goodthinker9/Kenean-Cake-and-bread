<template>
  <div class="space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Hero Banner -->
    <div class="relative rounded-3xl overflow-hidden bg-gradient-to-r from-[#2B1D15] to-[#452D20] text-white p-8 sm:p-12 shadow-md">
      <div class="max-w-xl space-y-4">
        <span class="inline-block px-3 py-1 bg-[#BA5420]/80 text-white rounded-full text-xs font-semibold uppercase tracking-wider">
          Fresh from our Ovens Daily
        </span>
        <h1 class="text-3xl sm:text-5xl font-serif font-bold leading-tight text-[#FAF3EB]">
          Artisan Bakery & Bespoke Cakes
        </h1>
        <p class="text-sm text-[#D7C7BA] leading-relaxed">
          Order online from your local <strong>{{ currentBranch?.branch_name || 'Golden Crust' }}</strong> branch. Every loaf and slice baked with traditional sourdough techniques and premium ingredients.
        </p>
        <div class="flex items-center space-x-3 pt-2">
          <router-link
            to="/custom-cake"
            class="px-5 py-2.5 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-bold rounded-xl transition shadow-sm"
          >
            Design Custom Cake
          </router-link>
          <a
            href="#menu"
            class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-xl transition border border-white/20"
          >
            Browse Today's Stock
          </a>
        </div>
      </div>
    </div>

    <!-- Category Filters and Search -->
    <div id="menu" class="space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <!-- Categories -->
        <div class="flex items-center space-x-2 overflow-x-auto pb-1">
          <button
            @click="selectedCategory = 'all'"
            class="px-4 py-2 text-xs font-bold rounded-full transition"
            :class="selectedCategory === 'all' ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
          >
            All Items
          </button>
          <button
            v-for="cat in categories"
            :key="cat.category_id"
            @click="selectedCategory = cat.category_id"
            class="px-4 py-2 text-xs font-bold rounded-full transition whitespace-nowrap"
            :class="selectedCategory === cat.category_id ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
          >
            {{ cat.category_name }}
          </button>
        </div>

        <!-- Search Bar -->
        <div class="w-full sm:w-64">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search cakes, bread..."
            class="w-full bg-white border border-[#D8CDC0] rounded-xl px-4 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
          />
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div v-if="filteredProducts.length === 0" class="text-center py-16 bg-white rounded-2xl border border-[#E9E0D4] p-8">
      <p class="text-[#7A6B60] text-sm">No bakery items found matching your filters for {{ currentBranch?.branch_name }}.</p>
    </div>

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div
        v-for="prod in filteredProducts"
        :key="prod.product_id"
        class="bg-white border border-[#E9E0D4] rounded-2xl overflow-hidden shadow-xs flex flex-col justify-between hover:shadow-md transition"
      >
        <div>
          <!-- Product Image & Status Tag -->
          <div class="relative h-48 bg-[#F5EFE7] overflow-hidden">
            <img :src="prod.image_url" :alt="prod.product_name" class="w-full h-full object-cover transition-transform duration-300 hover:scale-105" />
            <div class="absolute top-3 right-3">
              <span
                class="px-2.5 py-1 text-[11px] font-bold rounded-full shadow-xs"
                :class="{
                  'bg-[#EAF5EA] text-[#2E7D32] border border-[#C8E6C9]': prod.stock_status === 'Healthy',
                  'bg-[#FFF8E6] text-[#A67503] border border-[#FEE39E]': prod.stock_status === 'Low',
                  'bg-[#F3EFE9] text-[#7A6B60] border border-[#DDD3C7]': prod.stock_status === 'Sold out'
                }"
              >
                {{ prod.stock_status === 'Sold out' ? 'Sold out' : prod.stock_status === 'Low' ? `Low: ${prod.available} left` : `${prod.available} available` }}
              </span>
            </div>
            <div v-if="prod.has_branch_override" class="absolute bottom-3 left-3">
              <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-[#BA5420] text-white">
                Branch special price
              </span>
            </div>
          </div>

          <!-- Content -->
          <div class="p-5 space-y-2">
            <div class="text-[11px] font-semibold text-[#8C7D73] uppercase tracking-wider">{{ prod.category_name }}</div>
            <h3 class="font-bold text-base text-[#2A1D15]">{{ prod.product_name }}</h3>
            <p class="text-xs text-[#7A6B60] line-clamp-2 leading-relaxed">{{ prod.description }}</p>
          </div>
        </div>

        <!-- Price & Action -->
        <div class="p-5 pt-0">
          <div class="flex items-center justify-between border-t border-[#F2ECE3] pt-3">
            <div>
              <span class="text-xs text-[#8C7D73]">Price: </span>
              <span class="text-base font-serif font-bold text-[#BA5420]">
                ETB {{ prod.resolved_price.toLocaleString() }}
              </span>
            </div>

            <button
              v-if="prod.available > 0"
              @click="addToCart(prod)"
              class="px-4 py-2 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-bold rounded-xl transition shadow-xs"
            >
              Add to cart
            </button>
            <button
              v-else
              disabled
              class="px-4 py-2 bg-[#EAE3D8] text-[#8C7D73] text-xs font-bold rounded-xl cursor-not-allowed"
            >
              Sold out
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useBranchStore } from '../../stores/branch';
import { useCartStore } from '../../stores/cart';
import api from '../../api';

const branchStore = useBranchStore();
const cartStore = useCartStore();

const categories = ref([]);
const products = ref([]);
const selectedCategory = ref('all');
const searchQuery = ref('');

const currentBranch = computed(() => branchStore.selectedBranch);

const loadData = async () => {
  if (!branchStore.selectedBranchId) return;
  try {
    const [catRes, prodRes] = await Promise.all([
      api.get('/categories'),
      api.get(`/branches/${branchStore.selectedBranchId}/products`),
    ]);
    categories.value = catRes.data;
    products.value = prodRes.data;
  } catch (err) {
    console.error('Failed to load menu data', err);
  }
};

const filteredProducts = computed(() => {
  return products.value.filter((p) => {
    const matchesCat = selectedCategory.value === 'all' || p.category_id === selectedCategory.value;
    const matchesSearch = !searchQuery.value || p.product_name.toLowerCase().includes(searchQuery.value.toLowerCase());
    return matchesCat && matchesSearch;
  });
});

const addToCart = (product) => {
  const success = cartStore.addItem(product, branchStore.selectedBranchId);
  if (success) {
    alert(`Added ${product.product_name} to cart!`);
  }
};

onMounted(() => {
  loadData();
});
</script>
