<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 font-sans">
    <!-- Header with Continue Shopping matching Image 2 -->
    <div class="flex items-center justify-between border-b border-[#F8E7EA] pb-4">
      <div class="flex items-center space-x-2">
        <span class="text-2xl">🛒</span>
        <h1 class="text-3xl font-serif font-black text-[#38121A]">Your Cart</h1>
      </div>

      <router-link
        to="/shop"
        class="text-xs font-bold text-[#E11D48] hover:underline flex items-center space-x-1"
      >
        <span>Continue Shopping</span>
        <span>→</span>
      </router-link>
    </div>

    <!-- Active Branch Notice -->
    <div class="bg-white border border-[#FECDD3] px-4 py-3 rounded-2xl flex flex-col sm:flex-row items-center justify-between text-xs text-[#4B1A24] shadow-2xs gap-2">
      <div class="flex items-center space-x-2">
        <span class="w-2.5 h-2.5 rounded-full bg-[#10B981] animate-pulse"></span>
        <span>Ordering from <strong>{{ currentBranchName }} Branch</strong>. Stock and prices are validated strictly for this branch.</span>
      </div>
      <router-link to="/branches" class="font-bold text-[#E11D48] hover:text-[#BE123C] underline text-xs shrink-0">
        Change branch →
      </router-link>
    </div>

    <!-- Revalidation Warnings Banner matching Section 5 rules -->
    <div
      v-if="cartStore.revalidationWarnings.length > 0"
      class="bg-[#FEF3C7] border-2 border-[#F59E0B] rounded-3xl p-5 shadow-xs space-y-3"
    >
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-2 text-xs font-bold text-[#92400E]">
          <span class="text-base">⚠️</span>
          <span>Stock &amp; Price Revalidation Notice for {{ currentBranchName }} Branch</span>
        </div>
        <button
          @click="cartStore.clearWarnings"
          class="text-xs font-bold text-[#92400E] hover:underline"
        >
          Dismiss All
        </button>
      </div>

      <div class="space-y-3">
        <div
          v-for="(warn, wIdx) in cartStore.revalidationWarnings"
          :key="wIdx"
          class="bg-white/90 rounded-2xl p-3.5 border border-[#FDE68A] text-xs space-y-2 shadow-2xs"
        >
          <div class="flex items-start justify-between">
            <div class="text-[#78350F] font-semibold">
              {{ warn.message }}
            </div>
            <button
              @click="cartStore.dismissWarning(wIdx)"
              class="text-gray-400 hover:text-gray-600 text-xs ml-2"
            >
              ✕
            </button>
          </div>

          <!-- Alternative branch suggestions if short or sold out -->
          <div v-if="warn.suggestions && warn.suggestions.length > 0" class="pt-1 space-y-1.5 border-t border-[#FEF3C7]">
            <div class="text-[11px] font-bold text-[#92400E]">
              Nearby branches that can fulfill {{ warn.product_name }}:
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <div
                v-for="alt in warn.suggestions"
                :key="alt.branch_id"
                class="inline-flex items-center space-x-2 bg-[#FFFBEB] border border-[#FCD34D] px-2.5 py-1 rounded-xl text-[11px]"
              >
                <span class="font-bold text-[#78350F]">{{ alt.branch_name }}</span>
                <span class="text-[#92400E]">({{ alt.available }} available &middot; ETB {{ alt.price }})</span>
                <button
                  type="button"
                  @click="switchBranch(alt)"
                  class="px-2 py-0.5 bg-[#BA5420] text-white rounded-lg font-bold hover:bg-[#9E4314] text-[10px] transition"
                >
                  Switch to {{ alt.branch_name }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="cartStore.items.length === 0" class="bg-white rounded-3xl border border-[#F3E8E8] p-12 text-center space-y-4 shadow-2xs">
      <div class="text-5xl">🧁</div>
      <h2 class="text-xl font-serif font-bold text-[#38121A]">Your shopping bag is empty!</h2>
      <p class="text-xs text-[#7A6B60] max-w-sm mx-auto">
        Explore our freshly baked artisan cakes, warm loaves, and flaky French butter croissants.
      </p>
      <router-link
        to="/shop"
        class="inline-block px-8 py-3 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold rounded-full shadow-md transition"
      >
        Start Shopping Now →
      </router-link>
    </div>

    <!-- Cart Layout Grid: Items Table (8 cols), Summary (4 cols) matching Image 2 -->
    <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left Column: Items Table (8 cols) -->
      <div class="lg:col-span-8 space-y-6">
        <div class="bg-white rounded-3xl border border-[#F3E8E8] overflow-hidden shadow-2xs">
          <!-- Table Header -->
          <div class="bg-[#FAF7F5] px-6 py-3.5 border-b border-[#F8E7EA] grid grid-cols-12 text-[11px] font-bold uppercase tracking-wider text-[#7A6B60]">
            <div class="col-span-6">Product</div>
            <div class="col-span-2 text-center">Price</div>
            <div class="col-span-2 text-center">Quantity</div>
            <div class="col-span-2 text-right">Total</div>
          </div>

          <!-- Items Lines -->
          <div class="divide-y divide-[#F8E7EA]">
            <div
              v-for="item in cartStore.items"
              :key="item.product_id"
              class="px-6 py-4 grid grid-cols-12 items-center gap-3 text-xs"
              :class="(item.available ?? 0) <= 0 ? 'bg-red-50/40' : ''"
            >
              <!-- Product info (6 cols) -->
              <div class="col-span-6 flex items-center space-x-3.5">
                <img
                  :src="item.image_url || 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=120&auto=format&fit=crop&q=80'"
                  :alt="item.product_name"
                  class="w-16 h-16 rounded-2xl object-cover shrink-0 border border-[#F2D7DC]"
                  :class="(item.available ?? 0) <= 0 ? 'grayscale-50' : ''"
                />
                <div>
                  <h3 class="font-serif font-bold text-sm text-[#38121A]">{{ item.product_name }}</h3>
                  <div v-if="(item.available ?? 0) > 0" class="text-[11px] text-[#059669] font-medium mt-0.5">
                    In Stock ({{ item.available }} available)
                  </div>
                  <div v-else class="text-[11px] text-[#DC2626] font-bold mt-0.5 flex items-center space-x-1">
                    <span>Sold out at {{ currentBranchName }}</span>
                  </div>
                </div>
              </div>

              <!-- Price (2 cols) -->
              <div class="col-span-2 text-center font-bold text-[#4B1A24]">
                ETB {{ item.price }}
              </div>

              <!-- Quantity Stepper (2 cols) -->
              <div class="col-span-2 flex items-center justify-center">
                <div v-if="(item.available ?? 0) > 0" class="flex items-center border border-[#F2D7DC] rounded-xl bg-white shadow-2xs">
                  <button
                    @click="decreaseQty(item)"
                    class="px-2.5 py-1 text-xs font-bold text-[#4B1A24] hover:bg-[#FFF1F2] rounded-l-xl"
                  >
                    &minus;
                  </button>
                  <span class="px-2.5 py-1 text-xs font-bold text-[#38121A] min-w-[24px] text-center">
                    {{ item.quantity }}
                  </span>
                  <button
                    @click="increaseQty(item)"
                    class="px-2.5 py-1 text-xs font-bold text-[#4B1A24] hover:bg-[#FFF1F2] rounded-r-xl disabled:opacity-40"
                    :disabled="item.quantity >= (item.available ?? 0)"
                  >
                    +
                  </button>
                </div>
                <div v-else class="text-[11px] font-bold text-[#DC2626]">
                  Unavailable
                </div>
              </div>

              <!-- Total & Trash Icon (2 cols) -->
              <div class="col-span-2 flex items-center justify-end space-x-3">
                <span class="font-black text-[#881337] text-sm">
                  ETB {{ (item.price * item.quantity).toLocaleString() }}
                </span>
                <button
                  @click="cartStore.removeItem(item.product_id)"
                  class="text-[#9CA3AF] hover:text-[#E11D48] transition text-base"
                  title="Remove item"
                >
                  🗑️
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 10% OFF Promo Card matching Image 2 -->
        <div class="relative rounded-3xl bg-gradient-to-r from-[#FFF1F2] via-[#FCE7F3] to-[#FFF1F2] border border-[#FECDD3] p-6 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xs">
          <div class="flex items-center space-x-4">
            <img
              src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=160&auto=format&fit=crop&q=80"
              alt="Promo Cake"
              class="w-20 h-20 rounded-2xl object-cover shadow-xs border border-white shrink-0"
            />
            <div>
              <h4 class="font-serif font-black text-base text-[#4A1521]">
                Enjoy 10% OFF on your next order!
              </h4>
              <p class="text-xs text-[#7A6B60] mt-0.5">
                Apply promo code to enjoy exclusive bakery savings.
              </p>
            </div>
          </div>

          <div class="flex items-center space-x-2 shrink-0">
            <span class="px-3.5 py-1.5 bg-white border border-[#FECDD3] text-[#E11D48] font-mono font-black text-xs rounded-xl shadow-2xs">
              Use code: BAKE10
            </span>
            <button
              @click="applyPromo('BAKE10')"
              class="px-4 py-1.5 bg-[#881337] hover:bg-[#4A1521] text-white text-xs font-bold rounded-xl transition shadow-xs"
            >
              Apply
            </button>
          </div>
        </div>
      </div>

      <!-- Right Column: Order Summary (4 cols) matching Image 2 -->
      <div class="lg:col-span-4 bg-white rounded-3xl border border-[#F3E8E8] p-6 space-y-5 shadow-2xs">
        <h2 class="text-base font-serif font-bold text-[#38121A] border-b border-[#F8E7EA] pb-3">
          Order Summary
        </h2>

        <div class="space-y-3 text-xs">
          <div class="flex justify-between items-center text-[#55463C]">
            <span>Subtotal</span>
            <span class="font-bold text-[#38121A]">ETB {{ subtotal.toLocaleString() }}</span>
          </div>

          <div class="flex justify-between items-center text-[#55463C]">
            <span>Estimated Delivery Fee</span>
            <span class="font-bold text-[#38121A]">ETB {{ deliveryFee }}</span>
          </div>

          <div v-if="discount > 0" class="flex justify-between items-center text-[#059669]">
            <span>Promo Discount (10%)</span>
            <span class="font-bold">- ETB {{ discount.toLocaleString() }}</span>
          </div>

          <div class="pt-3 border-t border-[#F8E7EA] flex justify-between items-center text-base font-black text-[#38121A]">
            <span>Total</span>
            <span class="text-[#E11D48]">ETB {{ total.toLocaleString() }}</span>
          </div>
        </div>

        <div class="space-y-2.5 pt-2">
          <!-- Checkout button: disabled if any sold out item present -->
          <button
            v-if="cartStore.hasSoldOutItems"
            disabled
            class="block w-full py-3.5 bg-gray-200 text-gray-500 text-xs font-bold uppercase tracking-wider rounded-2xl text-center cursor-not-allowed"
          >
            Remove Sold-Out Items to Proceed
          </button>
          <router-link
            v-else
            to="/checkout"
            class="block w-full py-3.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold uppercase tracking-wider rounded-2xl text-center shadow-md hover:shadow-lg transition active:scale-98"
          >
            Proceed to Checkout →
          </router-link>

          <router-link
            to="/shop"
            class="block w-full py-2.5 bg-white border border-[#F2D7DC] hover:bg-[#FAF7F5] text-[#4B1A24] text-xs font-bold rounded-2xl text-center shadow-2xs transition"
          >
            + Add More Items
          </router-link>
        </div>

        <div class="pt-2 text-[11px] text-[#7A6B60] text-center space-y-1">
          <div class="flex items-center justify-center space-x-1 text-[#059669] font-semibold">
            <span>🛡️</span>
            <span>Safe &amp; Secure Checkout Guaranteed</span>
          </div>
          <div>Telebirr &middot; Chapa &middot; Pay on Pickup/Delivery</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useBranchStore } from '../../stores/branch';
import { useCartStore } from '../../stores/cart';

const branchStore = useBranchStore();
const cartStore = useCartStore();

const deliveryFee = ref(60);
const promoApplied = ref(false);

const currentBranchName = computed(() => branchStore.selectedBranch?.branch_name || 'Bole');

onMounted(async () => {
  // If cart has items, revalidate against current branch
  if (cartStore.items.length > 0) {
    await cartStore.revalidateForBranch(branchStore.selectedBranchId || 2);
  }
});

const subtotal = computed(() => {
  return cartStore.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
});

const discount = computed(() => {
  return promoApplied.value ? Math.round(subtotal.value * 0.1) : 0;
});

const total = computed(() => {
  return Math.max(0, subtotal.value + deliveryFee.value - discount.value);
});

function increaseQty(item) {
  const res = cartStore.updateQuantity(item.product_id, item.quantity + 1);
  if (res && !res.success && res.message) {
    alert(res.message);
  }
}

function decreaseQty(item) {
  cartStore.updateQuantity(item.product_id, item.quantity - 1);
}

function switchBranch(alt) {
  branchStore.setSelectedBranch(alt.branch_id);
}

function applyPromo(code) {
  if (code.toUpperCase() === 'BAKE10') {
    promoApplied.value = true;
    alert('Coupon code BAKE10 applied! 10% discount subtracted from your total.');
  }
}
</script>
