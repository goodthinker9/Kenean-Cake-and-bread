<template>
  <div class="space-y-12 pb-12 font-sans">
    <!-- Hero Section matching Image 3 -->
    <section class="relative bg-gradient-to-b from-[#FFF1F2] via-[#FDF2F8] to-[#FAF7F5] pt-10 pb-16 overflow-hidden border-b border-[#FCE7F3]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
        <!-- Left Hero Content -->
        <div class="lg:col-span-6 space-y-6 text-center lg:text-left">
          <div class="inline-flex items-center space-x-2 bg-white/80 backdrop-blur-xs px-3.5 py-1.5 rounded-full border border-[#FECDD3] text-xs font-bold text-[#E11D48] shadow-2xs">
            <span>✨</span>
            <span>Artisan Bakery &amp; Bespoke Confectionery</span>
          </div>

          <h1 class="text-4xl sm:text-6xl font-serif font-black text-[#4A1521] leading-[1.15]">
            Freshly Baked <br />
            <span class="text-[#E11D48] italic font-normal">with Love</span>
            <span class="text-2xl sm:text-4xl ml-2">💖</span>
          </h1>

          <p class="text-sm sm:text-base text-[#6B2836] max-w-lg mx-auto lg:mx-0 leading-relaxed">
            Delicious cakes, breads and more... for every special moment. Handcrafted daily in our ovens at <strong>{{ currentBranchName }}</strong> branch.
          </p>

          <div class="flex flex-wrap items-center justify-center lg:justify-start gap-4 pt-2">
            <router-link
              to="/shop"
              class="px-8 py-3.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-bold text-sm rounded-full shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center space-x-2"
            >
              <span>Shop Now</span>
              <span>→</span>
            </router-link>

            <router-link
              to="/custom-cake"
              class="px-8 py-3.5 bg-white hover:bg-[#FFF1F2] text-[#881337] border-2 border-[#FECDD3] font-bold text-sm rounded-full shadow-2xs transition"
            >
              Explore Menu
            </router-link>
          </div>
        </div>

        <!-- Right Hero Visual Showcase matching Image 3 -->
        <div class="lg:col-span-6 flex justify-center relative">
          <!-- Decorative Glow circles -->
          <div class="absolute -top-10 -right-10 w-72 h-72 bg-[#FDA4AF]/30 rounded-full blur-3xl pointer-events-none"></div>
          <div class="absolute -bottom-10 -left-10 w-72 h-72 bg-[#F472B6]/20 rounded-full blur-3xl pointer-events-none"></div>

          <div class="relative z-10 w-full max-w-lg rounded-3xl p-4 bg-white/70 backdrop-blur-md border border-white/80 shadow-xl overflow-hidden group">
            <img
              src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=900&auto=format&fit=crop&q=80"
              alt="Freshly Baked Berry Drip Cake"
              class="w-full h-80 sm:h-96 object-cover rounded-2xl group-hover:scale-102 transition duration-500 shadow-sm"
            />
            <div class="absolute bottom-6 left-6 right-6 bg-white/90 backdrop-blur-md p-4 rounded-2xl border border-[#FEE2E2] shadow-md flex items-center justify-between">
              <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-[#E11D48]">Featured Specialty</span>
                <div class="text-sm font-serif font-bold text-[#38121A]">Belgian Berry Drip Cake</div>
                <div class="text-xs text-[#881337] font-semibold">ETB 850 &middot; Ready in 45 mins</div>
              </div>
              <router-link
                to="/product/1"
                class="px-4 py-2 bg-[#E11D48] text-white text-xs font-bold rounded-xl shadow-xs hover:bg-[#BE123C] transition"
              >
                Order Now
              </router-link>
            </div>
          </div>
        </div>
      </div>

      <!-- 4 Value Badges Floating Bar matching Image 3 -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
        <div class="bg-white rounded-2xl border border-[#F3E8E8] shadow-md p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 divide-y sm:divide-y-0 sm:divide-x divide-[#F8E7EA]">
          <div class="flex items-center space-x-3.5 pt-3 sm:pt-0 first:pt-0 sm:pl-3 first:pl-0">
            <div class="w-12 h-12 rounded-2xl bg-[#FFF1F2] text-[#E11D48] flex items-center justify-center text-2xl shrink-0 shadow-2xs">
              🚚
            </div>
            <div>
              <h4 class="text-xs font-bold text-[#38121A]">Fast &amp; Reliable Delivery</h4>
              <p class="text-[11px] text-[#7A6B60] mt-0.5">Dispatched fresh from local branch</p>
            </div>
          </div>

          <div class="flex items-center space-x-3.5 pt-3 sm:pt-0 sm:pl-4">
            <div class="w-12 h-12 rounded-2xl bg-[#FFF1F2] text-[#E11D48] flex items-center justify-center text-2xl shrink-0 shadow-2xs">
              🛡️
            </div>
            <div>
              <h4 class="text-xs font-bold text-[#38121A]">100% Fresh Ingredients</h4>
              <p class="text-[11px] text-[#7A6B60] mt-0.5">Organic flour &amp; Belgian cocoa</p>
            </div>
          </div>

          <div class="flex items-center space-x-3.5 pt-3 sm:pt-0 sm:pl-4">
            <div class="w-12 h-12 rounded-2xl bg-[#FFF1F2] text-[#E11D48] flex items-center justify-center text-2xl shrink-0 shadow-2xs">
              🎧
            </div>
            <div>
              <h4 class="text-xs font-bold text-[#38121A]">Easy Online Ordering</h4>
              <p class="text-[11px] text-[#7A6B60] mt-0.5">Telebirr, Chapa or Cash payment</p>
            </div>
          </div>

          <div class="flex items-center space-x-3.5 pt-3 sm:pt-0 sm:pl-4">
            <div class="w-12 h-12 rounded-2xl bg-[#FFF1F2] text-[#E11D48] flex items-center justify-center text-2xl shrink-0 shadow-2xs">
              ⭐
            </div>
            <div>
              <h4 class="text-xs font-bold text-[#38121A]">Customer Satisfaction</h4>
              <p class="text-[11px] text-[#7A6B60] mt-0.5">Guaranteed taste or instant replacement</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Our Popular Items Section matching Image 3 -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 pt-4">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-[#F8E7EA] pb-4">
        <div class="space-y-1">
          <div class="flex items-center space-x-2">
            <span class="text-2xl">🧁</span>
            <h2 class="text-2xl sm:text-3xl font-serif font-bold text-[#38121A]">Our Popular Items</h2>
          </div>
          <p class="text-xs text-[#7A6B60]">
            Discover our best selling cakes and freshly baked breads at {{ currentBranchName }}
          </p>
        </div>

        <router-link
          to="/shop"
          class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-full border border-[#FECDD3] text-xs font-bold text-[#E11D48] hover:bg-[#FFF1F2] transition"
        >
          <span>View All Products</span>
          <span>→</span>
        </router-link>
      </div>

      <!-- 5 Popular Product Cards matching Image 3 -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <div
          v-for="product in popularProducts"
          :key="product.product_id"
          class="bg-white rounded-2xl border border-[#F3E8E8] overflow-hidden shadow-2xs hover:shadow-md transition-all duration-300 flex flex-col justify-between group p-3.5"
        >
          <router-link :to="`/product/${product.product_id}`" class="block">
            <div class="relative w-full aspect-square rounded-xl overflow-hidden bg-[#FFF1F2] mb-3">
              <img
                :src="product.image_url"
                :alt="product.product_name"
                class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
              />
              <span
                v-if="product.discount"
                class="absolute top-2 left-2 bg-[#E11D48] text-white text-[10px] font-bold px-2 py-0.5 rounded-md shadow-xs"
              >
                {{ product.discount }}
              </span>
            </div>

            <h3 class="text-xs sm:text-sm font-serif font-bold text-[#38121A] group-hover:text-[#E11D48] transition line-clamp-1">
              {{ product.product_name }}
            </h3>

            <!-- Rating with review count matching Image 3 -->
            <div class="flex items-center space-x-1 text-[11px] text-[#F59E0B] mt-1">
              <span>★</span>
              <span class="font-bold text-[#38121A]">{{ product.rating || '4.8' }}</span>
              <span class="text-[#9CA3AF]">({{ product.reviewsCount || '120' }} reviews)</span>
            </div>
          </router-link>

          <!-- Price & Add To Cart Button matching Image 3 -->
          <div class="flex items-center justify-between mt-3 pt-2.5 border-t border-[#F8E7EA]">
            <div>
              <div class="text-xs sm:text-sm font-black text-[#881337]">
                ETB {{ product.resolved_price || product.base_price }}
              </div>
            </div>

            <button
              @click="addToCart(product)"
              class="w-8 h-8 rounded-full bg-[#E11D48] hover:bg-[#BE123C] text-white flex items-center justify-center transition shadow-xs active:scale-95"
              title="Add to Cart"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- Special Offer Pink Banner matching Image 3 -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="relative rounded-3xl bg-gradient-to-r from-[#FCE7F3] via-[#FFE4E6] to-[#FFF1F2] border border-[#FECDD3] p-8 sm:p-12 overflow-hidden shadow-md flex flex-col md:flex-row items-center justify-between gap-8">
        <!-- Scalloped Badge Graphic -->
        <div class="absolute -top-6 -right-6 w-32 h-32 bg-[#E11D48]/10 rounded-full blur-xl pointer-events-none"></div>

        <div class="space-y-4 max-w-xl text-center md:text-left z-10">
          <div class="inline-block px-3 py-1 bg-[#E11D48] text-white text-[11px] font-bold uppercase tracking-wider rounded-full shadow-xs">
            Special Offer!
          </div>

          <h2 class="text-3xl sm:text-4xl font-serif font-black text-[#4A1521] leading-tight">
            Get <span class="text-[#E11D48]">20% OFF</span> on all cakes this weekend!
          </h2>

          <p class="text-xs sm:text-sm text-[#7A3644] leading-relaxed">
            Celebrate life's sweetest milestones with our artisan layered cakes. Use coupon code <strong>BAKE20</strong> at checkout.
          </p>

          <div class="pt-2">
            <router-link
              to="/shop?category=Cakes"
              class="inline-flex items-center space-x-2 px-8 py-3.5 bg-[#881337] hover:bg-[#4A1521] text-white font-bold text-xs rounded-full shadow-md transition transform hover:-translate-y-0.5"
            >
              <span>Order Now</span>
              <span>→</span>
            </router-link>
          </div>
        </div>

        <!-- 20% OFF Emblem & Cake Visual -->
        <div class="relative z-10 flex items-center justify-center">
          <div class="relative">
            <!-- Red Scalloped 20% OFF Seal -->
            <div class="absolute -top-4 -left-4 z-20 w-16 h-16 rounded-full bg-[#E11D48] text-white border-2 border-white shadow-lg flex flex-col items-center justify-center font-black leading-none transform -rotate-12">
              <span class="text-sm">20%</span>
              <span class="text-[9px] uppercase tracking-wider">OFF</span>
            </div>

            <img
              src="https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=600&auto=format&fit=crop&q=80"
              alt="Delicious Strawberry Cake Slice"
              class="w-64 h-52 sm:w-80 sm:h-64 object-cover rounded-2xl shadow-lg border-2 border-white"
            />
          </div>
        </div>
      </div>
    </section>

    <!-- Custom Cake CTA Promo Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="bg-[#38121A] rounded-3xl p-8 sm:p-12 text-white flex flex-col lg:flex-row items-center justify-between gap-8 shadow-xl">
        <div class="space-y-3 max-w-xl text-center lg:text-left">
          <span class="text-xs uppercase font-bold tracking-widest text-[#F9A8D4]">Bespoke Confectionery</span>
          <h3 class="text-3xl sm:text-4xl font-serif font-black text-white">
            Design Your Dream Cake
          </h3>
          <p class="text-xs sm:text-sm text-[#FBCFE8] leading-relaxed">
            Have a custom wedding, anniversary, or birthday theme in mind? Upload your design photo, choose your preferred sponge and frosting, and get a quote within minutes.
          </p>
        </div>

        <router-link
          to="/custom-cake"
          class="px-8 py-4 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold uppercase tracking-wider rounded-full shadow-lg transition transform hover:scale-105 shrink-0"
        >
          Create Custom Cake →
        </router-link>
      </div>
    </section>

    <!-- Added to Cart Toast -->
    <div
      v-if="toastMessage"
      class="fixed bottom-6 right-6 z-50 bg-[#38121A] text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center space-x-3 text-xs border border-[#E11D48] animate-in slide-in-from-bottom"
    >
      <span class="text-lg">💖</span>
      <span>Added <strong>{{ toastMessage }}</strong> to your cart!</span>
      <router-link to="/cart" class="underline font-bold text-[#F43F5E] ml-2">View Cart →</router-link>
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

const toastMessage = ref(null);
const products = ref([]);

const currentBranchName = computed(() => {
  return branchStore.selectedBranch?.branch_name || 'Bole';
});

const defaultPopular = [
  {
    product_id: 1,
    product_name: 'Chocolate Cake',
    base_price: 850,
    resolved_price: 850,
    rating: 4.8,
    reviewsCount: 120,
    discount: '-15%',
    image_url: 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&auto=format&fit=crop&q=80',
    available: 12,
  },
  {
    product_id: 3,
    product_name: 'Vanilla Cake',
    base_price: 750,
    resolved_price: 750,
    rating: 4.7,
    reviewsCount: 95,
    discount: '-10%',
    image_url: 'https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=600&auto=format&fit=crop&q=80',
    available: 8,
  },
  {
    product_id: 2,
    product_name: 'Fresh Bread',
    base_price: 400,
    resolved_price: 400,
    rating: 4.8,
    reviewsCount: 200,
    image_url: 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=600&auto=format&fit=crop&q=80',
    available: 50,
  },
  {
    product_id: 4,
    product_name: 'Croissant',
    base_price: 450,
    resolved_price: 450,
    rating: 4.7,
    reviewsCount: 80,
    image_url: 'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=600&auto=format&fit=crop&q=80',
    available: 25,
  },
  {
    product_id: 5,
    product_name: 'Cupcakes',
    base_price: 300,
    resolved_price: 300,
    rating: 4.6,
    reviewsCount: 70,
    image_url: 'https://images.unsplash.com/photo-1587668178277-295251f900ce?w=600&auto=format&fit=crop&q=80',
    available: 30,
  },
];

const popularProducts = computed(() => {
  if (products.value.length >= 5) {
    return products.value.slice(0, 5).map((p, idx) => ({
      ...p,
      rating: (4.6 + (idx % 3) * 0.1).toFixed(1),
      reviewsCount: 70 + idx * 25,
      discount: idx === 0 ? '-15%' : idx === 1 ? '-10%' : null,
    }));
  }
  return defaultPopular;
});

onMounted(async () => {
  try {
    const branchId = branchStore.selectedBranchId || 2;
    const res = await api.get(`/catalog/products?branch_id=${branchId}`);
    if (res.data && res.data.length > 0) {
      products.value = res.data;
    }
  } catch (err) {
    console.error('Failed to fetch home catalog', err);
  }
});

function addToCart(p) {
  cartStore.addItem({
    product_id: p.product_id,
    product_name: p.product_name,
    resolved_price: p.resolved_price || p.base_price,
    image_url: p.image_url,
    available: p.available || 15,
  }, branchStore.selectedBranchId);

  toastMessage.value = p.product_name;
  setTimeout(() => {
    toastMessage.value = null;
  }, 2500);
}
</script>
