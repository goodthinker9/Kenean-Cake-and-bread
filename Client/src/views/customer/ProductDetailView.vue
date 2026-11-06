<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10 font-sans">
    <!-- Breadcrumb matching Image 1 -->
    <nav class="flex items-center space-x-2 text-xs font-semibold text-[#8C7D73]">
      <router-link to="/" class="hover:text-[#E11D48]">🏠</router-link>
      <span>&gt;</span>
      <router-link to="/shop" class="hover:text-[#E11D48]">Shop</router-link>
      <span>&gt;</span>
      <router-link :to="`/shop?category=${product?.category_name || 'Cakes'}`" class="hover:text-[#E11D48]">
        {{ product?.category_name || 'Cakes' }}
      </router-link>
      <span>&gt;</span>
      <span class="text-[#261814]">{{ product?.product_name || 'Chocolate Cake' }}</span>
    </nav>

    <!-- Product Showcase: Left Gallery & Right Specification matching Image 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
      <!-- Left Visual Gallery (6 cols) -->
      <div class="lg:col-span-6 flex gap-4">
        <!-- 4 Thumbnails Vertical Bar with up/down arrows -->
        <div class="flex flex-col items-center space-y-3 shrink-0">
          <button @click="prevThumbnail" class="text-[#9CA3AF] hover:text-[#E11D48] text-xs">▲</button>
          <div
            v-for="(img, idx) in galleryImages"
            :key="idx"
            @click="activeImageIdx = idx"
            class="w-16 h-16 rounded-2xl overflow-hidden border-2 cursor-pointer transition shadow-2xs"
            :class="activeImageIdx === idx ? 'border-[#E11D48] ring-2 ring-[#E11D48]/20' : 'border-[#F2D7DC] opacity-70 hover:opacity-100'"
          >
            <img :src="img" alt="Thumbnail" class="w-full h-full object-cover" />
          </div>
          <button @click="nextThumbnail" class="text-[#9CA3AF] hover:text-[#E11D48] text-xs">▼</button>
        </div>

        <!-- Large Main Image Card -->
        <div class="relative flex-1 aspect-square rounded-3xl overflow-hidden bg-[#FFF1F2] border border-[#FECDD3] shadow-md group">
          <!-- Best Seller Badge -->
          <span class="absolute top-4 left-4 z-10 bg-[#E11D48] text-white text-xs font-bold px-3 py-1 rounded-full shadow-xs">
            Best Seller
          </span>

          <!-- Wishlist Heart Button -->
          <button
            @click="isWishlisted = !isWishlisted"
            class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/90 backdrop-blur-xs flex items-center justify-center text-sm shadow-xs hover:scale-110 transition"
            :class="isWishlisted ? 'text-[#E11D48]' : 'text-[#9CA3AF]'"
          >
            {{ isWishlisted ? '♥' : '♡' }}
          </button>

          <!-- Main Image -->
          <img
            :src="galleryImages[activeImageIdx]"
            :alt="product?.product_name"
            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
          />

          <!-- View Larger Button -->
          <button
            @click="showZoom = true"
            class="absolute bottom-4 right-4 bg-black/60 hover:bg-black/80 text-white text-xs font-bold px-3 py-1.5 rounded-full flex items-center space-x-1 backdrop-blur-xs transition"
          >
            <span>🔍</span>
            <span>View Larger</span>
          </button>
        </div>
      </div>

      <!-- Right Details & Actions (6 cols) matching Image 1 -->
      <div class="lg:col-span-6 space-y-6">
        <div>
          <h1 class="text-3xl sm:text-4xl font-serif font-black text-[#38121A]">
            {{ product?.product_name || 'Chocolate Cake' }}
          </h1>

          <!-- Ratings & Stock Pill matching Image 1 -->
          <div class="flex flex-wrap items-center gap-3 mt-2.5">
            <div class="flex items-center space-x-1 text-sm text-[#F59E0B]">
              <span>★★★★★</span>
              <span class="font-bold text-[#38121A] ml-1">4.8</span>
              <span class="text-[#7A6B60] text-xs">(124 Reviews)</span>
            </div>

            <span class="text-[#D1D5DB]">|</span>

            <span
              v-if="(product?.available ?? 0) > 0"
              class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full text-xs font-bold text-[#059669] bg-[#ECFDF5] border border-[#A7F3D0]"
            >
              <span>In Stock ({{ product?.available }} available)</span>
              <span>✔</span>
            </span>
            <span
              v-else
              class="inline-flex items-center space-x-1.5 px-3 py-0.5 rounded-full text-xs font-bold text-[#DC2626] bg-[#FEF2F2] border border-[#FECACA]"
            >
              <span>Sold Out at {{ currentBranchName }} Branch</span>
              <span>✖</span>
            </span>
          </div>

          <!-- Price & Discount Badge matching Image 1 -->
          <div class="flex items-baseline space-x-3 mt-4">
            <span class="text-3xl font-black text-[#E11D48]">
              ETB {{ currentPrice }}
            </span>
            <span class="text-sm font-semibold text-[#9CA3AF] line-through">
              ETB {{ Math.round(currentPrice * 1.15) }}
            </span>
            <span class="px-2.5 py-0.5 bg-[#FFE4E6] text-[#E11D48] text-xs font-bold rounded-md">
              15% OFF
            </span>
          </div>

          <p class="text-xs sm:text-sm text-[#5B2934] mt-3 leading-relaxed">
            {{ product?.description || 'Rich chocolate cake made with premium ingredients, perfect for birthdays, celebrations and special moments.' }}
          </p>
        </div>

        <!-- Size Selection matching Image 1 -->
        <div class="space-y-2 pt-2 border-t border-[#F8E7EA]">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">Size:</label>
          <div class="flex items-center space-x-3">
            <button
              v-for="s in sizeOptions"
              :key="s.label"
              @click="selectedSize = s.label"
              class="px-4 py-2 rounded-xl text-xs font-bold transition shadow-2xs"
              :class="selectedSize === s.label ? 'border-2 border-[#E11D48] bg-[#FFF1F2] text-[#E11D48]' : 'border border-[#F2D7DC] bg-white text-[#4B1A24] hover:bg-[#FAF7F5]'"
            >
              {{ s.label }}
            </button>
          </div>
        </div>

        <!-- Quantity Stepper matching Image 1 -->
        <div class="flex items-center space-x-4 pt-1">
          <span class="text-xs font-bold uppercase tracking-wider text-[#38121A]">Quantity:</span>
          <div class="flex items-center border border-[#F2D7DC] rounded-xl bg-white shadow-2xs">
            <button
              @click="decreaseQty"
              class="px-3.5 py-1.5 text-xs font-bold text-[#4B1A24] hover:bg-[#FFF1F2] rounded-l-xl disabled:opacity-40"
              :disabled="quantity <= 1 || (product?.available ?? 0) <= 0"
            >
              &minus;
            </button>
            <span class="px-4 py-1.5 text-xs font-bold text-[#38121A] min-w-[32px] text-center">
              {{ quantity }}
            </span>
            <button
              @click="increaseQty"
              class="px-3.5 py-1.5 text-xs font-bold text-[#4B1A24] hover:bg-[#FFF1F2] rounded-r-xl disabled:opacity-40"
              :disabled="quantity >= (product?.available ?? 0) || (product?.available ?? 0) <= 0"
            >
              +
            </button>
          </div>
          <span v-if="(product?.available ?? 0) > 0 && quantity >= (product?.available ?? 0)" class="text-[11px] text-[#DC2626] font-semibold">
            Max available stock reached
          </span>
        </div>

        <!-- Action Buttons: Add to Cart & Add to Wishlist matching Image 1 -->
        <div class="space-y-3 pt-2">
          <div class="flex flex-col sm:flex-row items-center gap-3">
            <button
              v-if="(product?.available ?? 0) <= 0"
              disabled
              class="w-full sm:flex-1 py-3.5 bg-[#F3F4F6] text-[#9CA3AF] font-bold text-xs uppercase tracking-wider rounded-2xl cursor-not-allowed flex items-center justify-center space-x-2"
            >
              <span>Sold out at {{ currentBranchName }} Branch</span>
            </button>
            <button
              v-else
              @click="handleAddToCart"
              class="w-full sm:flex-1 py-3.5 bg-[#E11D48] hover:bg-[#BE123C] text-white font-bold text-xs uppercase tracking-wider rounded-2xl shadow-md hover:shadow-lg transition flex items-center justify-center space-x-2 active:scale-98"
            >
              <span>🛒</span>
              <span>Add to Cart</span>
            </button>

            <button
              @click="isWishlisted = !isWishlisted"
              class="w-full sm:w-auto px-6 py-3.5 bg-white border border-[#FECDD3] hover:bg-[#FFF1F2] text-[#881337] font-bold text-xs rounded-2xl shadow-2xs transition flex items-center justify-center space-x-2"
            >
              <span :class="isWishlisted ? 'text-[#E11D48]' : 'text-[#881337]'">♥</span>
              <span>{{ isWishlisted ? 'Wishlisted' : 'Add to Wishlist' }}</span>
            </button>
          </div>

          <!-- Nearby branch suggestions card if short or sold out -->
          <div v-if="otherBranchesWithStock.length > 0" class="p-3.5 bg-[#FFFBEB] rounded-2xl border border-[#FDE68A] text-xs space-y-2">
            <div class="font-bold text-[#92400E] flex items-center space-x-1.5">
              <span>📍</span>
              <span>Stock available at nearby branches:</span>
            </div>
            <div class="space-y-2">
              <div
                v-for="alt in otherBranchesWithStock"
                :key="alt.branch_id"
                class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs bg-white/70 p-2.5 rounded-xl border border-[#FCD34D]"
              >
                <div>
                  <span class="font-bold text-[#78350F]">{{ alt.branch_name }}</span>
                  <span class="text-[#92400E] ml-1.5">({{ alt.available }} available &middot; ETB {{ alt.price }})</span>
                </div>
                <button
                  type="button"
                  @click="switchBranch(alt)"
                  class="px-3 py-1.5 bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-xl font-bold text-[11px] self-start sm:self-auto transition shadow-xs"
                >
                  Switch to {{ alt.branch_name }} →
                </button>
              </div>
            </div>
            <div class="text-[10px] text-[#A16207]">
              The system does not switch branches automatically. Choose a branch above to fulfill your order.
            </div>
          </div>
        </div>

        <!-- 3 Benefit Badges matching Image 1 -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-4 border-t border-[#F8E7EA]">
          <div class="flex items-center space-x-3 p-3 bg-white rounded-2xl border border-[#F3E8E8] shadow-2xs">
            <div class="text-xl">🚚</div>
            <div>
              <div class="text-xs font-bold text-[#38121A]">Free Delivery</div>
              <div class="text-[10px] text-[#7A6B60]">On Orders Over ETB 1,000</div>
            </div>
          </div>

          <div class="flex items-center space-x-3 p-3 bg-white rounded-2xl border border-[#F3E8E8] shadow-2xs">
            <div class="text-xl">🛡️</div>
            <div>
              <div class="text-xs font-bold text-[#38121A]">Secure Payment</div>
              <div class="text-[10px] text-[#7A6B60]">Cash or Online</div>
            </div>
          </div>

          <div class="flex items-center space-x-3 p-3 bg-white rounded-2xl border border-[#F3E8E8] shadow-2xs">
            <div class="text-xl">🎧</div>
            <div>
              <div class="text-xs font-bold text-[#38121A]">24/7 Support</div>
              <div class="text-[10px] text-[#7A6B60]">We're here to help</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bottom Section: 3-Card Split matching Image 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start pt-6 border-t border-[#F8E7EA]">
      <!-- Card 1: Description & Ingredients Tabs (5 cols) -->
      <div class="lg:col-span-5 bg-white rounded-3xl border border-[#F3E8E8] p-6 space-y-4 shadow-2xs">
        <div class="flex items-center space-x-4 border-b border-[#F8E7EA] pb-3 text-xs font-bold">
          <button
            @click="activeTab = 'desc'"
            :class="activeTab === 'desc' ? 'text-[#E11D48] border-b-2 border-[#E11D48] pb-1' : 'text-[#7A6B60] hover:text-[#38121A]'"
          >
            Description
          </button>
          <button
            @click="activeTab = 'ingr'"
            :class="activeTab === 'ingr' ? 'text-[#E11D48] border-b-2 border-[#E11D48] pb-1' : 'text-[#7A6B60] hover:text-[#38121A]'"
          >
            Ingredients
          </button>
          <button
            @click="activeTab = 'rev'"
            :class="activeTab === 'rev' ? 'text-[#E11D48] border-b-2 border-[#E11D48] pb-1' : 'text-[#7A6B60] hover:text-[#38121A]'"
          >
            Reviews (124)
          </button>
        </div>

        <div v-if="activeTab === 'desc'" class="space-y-3">
          <h3 class="font-serif font-bold text-sm text-[#38121A]">About This Cake</h3>
          <p class="text-xs text-[#5B2934] leading-relaxed">
            Our Chocolate Cake is a perfect blend of rich cocoa, soft sponge and creamy chocolate ganache. Made with the finest ingredients, it's sure to make any occasion sweeter.
          </p>

          <div class="space-y-1.5 pt-2 text-xs font-semibold text-[#38121A]">
            <div class="flex items-center space-x-2 text-[#059669]">
              <span>✔</span>
              <span class="text-[#38121A]">Premium quality ingredients</span>
            </div>
            <div class="flex items-center space-x-2 text-[#059669]">
              <span>✔</span>
              <span class="text-[#38121A]">Moist and delicious taste</span>
            </div>
            <div class="flex items-center space-x-2 text-[#059669]">
              <span>✔</span>
              <span class="text-[#38121A]">Perfect for all occasions</span>
            </div>
          </div>
        </div>

        <div v-else-if="activeTab === 'ingr'" class="space-y-2 text-xs text-[#5B2934]">
          <h3 class="font-serif font-bold text-sm text-[#38121A]">Pure &amp; Natural Ingredients</h3>
          <p>Belgian cocoa 70%, organic wheat flour, fresh farm eggs, unsalted cultured butter, brown cane sugar, Madagascar vanilla bean extract, heavy dairy cream.</p>
        </div>

        <div v-else class="space-y-3 text-xs">
          <div class="p-3 bg-[#FAF7F5] rounded-xl border border-[#F2D7DC]">
            <div class="font-bold text-[#38121A]">Abebe Kebede &middot; ★★★★★</div>
            <p class="text-[#7A6B60] mt-1">Exceptional chocolate flavor, super moist and loved by everyone at the birthday!</p>
          </div>
          <div class="p-3 bg-[#FAF7F5] rounded-xl border border-[#F2D7DC]">
            <div class="font-bold text-[#38121A]">Sara Ahmed &middot; ★★★★★</div>
            <p class="text-[#7A6B60] mt-1">Delivered fresh within 40 minutes to Bole. Best bakery in town.</p>
          </div>
        </div>
      </div>

      <!-- Card 2: Customer Reviews Distribution (4 cols) matching Image 1 -->
      <div class="lg:col-span-4 bg-white rounded-3xl border border-[#F3E8E8] p-6 space-y-4 shadow-2xs">
        <h3 class="font-serif font-bold text-sm text-[#38121A]">Customer Reviews</h3>

        <div class="flex items-center space-x-3">
          <div class="text-4xl font-serif font-black text-[#38121A]">4.8</div>
          <div>
            <div class="text-sm text-[#F59E0B]">★★★★★</div>
            <div class="text-[11px] text-[#7A6B60]">Based on 124 reviews</div>
          </div>
        </div>

        <!-- Rating Distribution Bars -->
        <div class="space-y-1.5 text-[11px]">
          <div class="flex items-center space-x-2">
            <span class="w-3 text-[#38121A]">5</span>
            <span class="text-[#F59E0B]">★</span>
            <div class="flex-1 h-2 bg-[#F2D7DC] rounded-full overflow-hidden">
              <div class="h-full bg-[#F59E0B] rounded-full" style="width: 87%"></div>
            </div>
            <span class="w-8 text-right font-semibold text-[#7A6B60]">87%</span>
          </div>

          <div class="flex items-center space-x-2">
            <span class="w-3 text-[#38121A]">4</span>
            <span class="text-[#F59E0B]">★</span>
            <div class="flex-1 h-2 bg-[#F2D7DC] rounded-full overflow-hidden">
              <div class="h-full bg-[#F59E0B] rounded-full" style="width: 8%"></div>
            </div>
            <span class="w-8 text-right font-semibold text-[#7A6B60]">8%</span>
          </div>

          <div class="flex items-center space-x-2">
            <span class="w-3 text-[#38121A]">3</span>
            <span class="text-[#F59E0B]">★</span>
            <div class="flex-1 h-2 bg-[#F2D7DC] rounded-full overflow-hidden">
              <div class="h-full bg-[#F59E0B] rounded-full" style="width: 3%"></div>
            </div>
            <span class="w-8 text-right font-semibold text-[#7A6B60]">3%</span>
          </div>

          <div class="flex items-center space-x-2">
            <span class="w-3 text-[#38121A]">2</span>
            <span class="text-[#F59E0B]">★</span>
            <div class="flex-1 h-2 bg-[#F2D7DC] rounded-full overflow-hidden">
              <div class="h-full bg-[#F59E0B] rounded-full" style="width: 1%"></div>
            </div>
            <span class="w-8 text-right font-semibold text-[#7A6B60]">1%</span>
          </div>

          <div class="flex items-center space-x-2">
            <span class="w-3 text-[#38121A]">1</span>
            <span class="text-[#F59E0B]">★</span>
            <div class="flex-1 h-2 bg-[#F2D7DC] rounded-full overflow-hidden">
              <div class="h-full bg-[#F59E0B] rounded-full" style="width: 1%"></div>
            </div>
            <span class="w-8 text-right font-semibold text-[#7A6B60]">1%</span>
          </div>
        </div>

        <button
          @click="showReviewModal = true"
          class="w-full py-2.5 bg-white border border-[#FECDD3] hover:bg-[#FFF1F2] text-[#E11D48] text-xs font-bold rounded-xl transition shadow-2xs"
        >
          Write a Review
        </button>
      </div>

      <!-- Card 3: Make Every Moment Sweeter Promo matching Image 1 (3 cols) -->
      <div class="lg:col-span-3 bg-gradient-to-br from-[#FFF1F2] to-[#FCE7F3] rounded-3xl border border-[#FECDD3] p-6 text-center space-y-4 shadow-2xs">
        <div class="text-2xl">💖</div>
        <h3 class="font-serif font-black text-xl text-[#4A1521] leading-tight">
          Make Every Moment Sweeter!
        </h3>
        <p class="text-xs text-[#7A3644] leading-relaxed">
          Order your favorite cake today and spread the happiness!
        </p>

        <img
          src="https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=300&auto=format&fit=crop&q=80"
          alt="Sweet Moment Cake"
          class="w-32 h-32 mx-auto rounded-2xl object-cover shadow-sm border border-white"
        />

        <router-link
          to="/shop"
          class="inline-block w-full py-2.5 bg-[#881337] hover:bg-[#4A1521] text-white text-xs font-bold rounded-xl shadow-xs transition"
        >
          Shop More →
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useBranchStore } from '../../stores/branch';
import { useCartStore } from '../../stores/cart';
import api from '../../api';

const route = useRoute();
const router = useRouter();
const branchStore = useBranchStore();
const cartStore = useCartStore();

const product = ref(null);
const activeTab = ref('desc');
const activeImageIdx = ref(0);
const isWishlisted = ref(false);
const quantity = ref(1);
const selectedSize = ref('8 Inch');
const showReviewModal = ref(false);
const showZoom = ref(false);
const otherBranchesWithStock = ref([]);

const currentBranchName = computed(() => branchStore.selectedBranch?.branch_name || 'Bole');

const sizeOptions = [
  { label: '6 Inch', multiplier: 0.8 },
  { label: '8 Inch', multiplier: 1.0 },
  { label: '10 Inch', multiplier: 1.3 },
  { label: '12 Inch', multiplier: 1.6 },
];

const galleryImages = ref([
  'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=800&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=800&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1587668178277-295251f900ce?w=800&auto=format&fit=crop&q=80',
]);

const currentPrice = computed(() => {
  const base = product.value?.resolved_price || product.value?.base_price || 850;
  const sizeObj = sizeOptions.find((s) => s.label === selectedSize.value);
  return Math.round(base * (sizeObj?.multiplier || 1.0));
});

async function loadProduct(branchId) {
  const id = route.params.id;
  try {
    const res = await api.get(`/catalog/products/${id}?branch_id=${branchId}`);
    product.value = res.data;
    if (res.data.images && res.data.images.length > 0) {
      galleryImages.value = res.data.images.map((img) => img.image_url);
    }

    // Ensure quantity does not exceed available
    const avail = product.value.available || 0;
    if (avail > 0 && quantity.value > avail) {
      quantity.value = avail;
    }

    // If stock is short or zero, fetch other branch availability
    if (avail <= 5) {
      fetchOtherBranchAvailability(id, branchId);
    } else {
      otherBranchesWithStock.value = [];
    }
  } catch (err) {
    product.value = {
      product_id: parseInt(id) || 1,
      product_name: 'Chocolate Cake',
      category_name: 'Cakes',
      base_price: 850,
      resolved_price: 850,
      available: 12,
      description: 'Rich chocolate cake made with premium ingredients, perfect for birthdays, celebrations and special moments.',
    };
  }
}

async function fetchOtherBranchAvailability(productId, currentBranchId) {
  try {
    const res = await api.get(`/catalog/products/${productId}/branch-availability`);
    const branches = res.data?.branches || [];
    otherBranchesWithStock.value = branches.filter((b) => b.branch_id !== currentBranchId && b.available > 0);
  } catch (err) {
    console.error('Failed checking other branches', err);
    otherBranchesWithStock.value = [];
  }
}

function switchBranch(branch) {
  branchStore.setSelectedBranch(branch.branch_id);
  // Watcher will reload product
}

onMounted(() => {
  const branchId = branchStore.selectedBranchId || 2;
  loadProduct(branchId);
});

watch(
  () => branchStore.selectedBranchId,
  (newBranchId) => {
    if (newBranchId) {
      loadProduct(newBranchId);
    }
  }
);

function prevThumbnail() {
  if (activeImageIdx.value > 0) activeImageIdx.value--;
  else activeImageIdx.value = galleryImages.value.length - 1;
}

function nextThumbnail() {
  if (activeImageIdx.value < galleryImages.value.length - 1) activeImageIdx.value++;
  else activeImageIdx.value = 0;
}

function increaseQty() {
  const maxAvail = product.value?.available || 0;
  if (quantity.value < maxAvail) {
    quantity.value++;
  }
}

function decreaseQty() {
  if (quantity.value > 1) quantity.value--;
}

function handleAddToCart() {
  if (!product.value) return;
  const avail = typeof product.value.available === 'number' ? product.value.available : 0;
  if (avail <= 0) {
    alert(`${product.value.product_name} is sold out at ${currentBranchName.value} branch.`);
    return;
  }

  const res = cartStore.addItem({
    product_id: product.value.product_id,
    product_name: `${product.value.product_name} (${selectedSize.value})`,
    resolved_price: currentPrice.value,
    image_url: galleryImages.value[activeImageIdx.value],
    available: avail,
  }, branchStore.selectedBranchId, quantity.value);

  if (!res.success) {
    alert(res.message);
  }

  router.push('/cart');
}
</script>
