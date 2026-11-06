<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 font-sans">
    <div class="flex items-center justify-between border-b border-[#F8E7EA] pb-4">
      <div>
        <h1 class="text-3xl font-serif font-black text-[#38121A]">Checkout</h1>
        <p class="text-xs text-[#7A6B60] mt-1">Please enter your fulfillment details to place your bakery order.</p>
      </div>

      <router-link to="/cart" class="text-xs font-bold text-[#E11D48] underline">
        ← Back to Cart
      </router-link>
    </div>

    <!-- Active Branch Notice -->
    <div class="bg-white border border-[#FECDD3] px-4 py-3 rounded-2xl flex flex-col sm:flex-row items-center justify-between text-xs text-[#4B1A24] shadow-2xs gap-2">
      <div class="flex items-center space-x-2">
        <span class="w-2.5 h-2.5 rounded-full bg-[#10B981] animate-pulse"></span>
        <span>Fulfilling from <strong>{{ currentBranchName }} Branch</strong>. Prices and stock belong strictly to this branch.</span>
      </div>
      <router-link to="/branches" class="font-bold text-[#E11D48] hover:text-[#BE123C] underline text-xs shrink-0">
        Switch branch →
      </router-link>
    </div>

    <!-- Checkout Grid matching Image 2 -->
    <form @submit.prevent="handlePlaceOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left Column: Delivery Information (7 cols) -->
      <div class="lg:col-span-7 space-y-6">
        <div class="bg-white rounded-3xl border border-[#F3E8E8] p-6 space-y-5 shadow-2xs">
          <h2 class="text-base font-serif font-bold text-[#38121A] border-b border-[#F8E7EA] pb-3">
            Fulfillment Information
          </h2>

          <!-- Fulfillment Toggle matching Image 2 -->
          <div class="flex items-center space-x-3">
            <button
              type="button"
              @click="setMethod('Delivery')"
              class="flex-1 py-3 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center space-x-2"
              :class="fulfillmentMethod === 'Delivery' ? 'bg-[#E11D48] text-white shadow-xs' : 'bg-[#FAF7F5] border border-[#F2D7DC] text-[#4B1A24] hover:bg-[#FFF1F2]'"
            >
              <span>🚚</span>
              <span>Home Delivery</span>
            </button>

            <button
              type="button"
              @click="setMethod('Pickup')"
              class="flex-1 py-3 px-4 rounded-2xl text-xs font-bold transition flex items-center justify-center space-x-2"
              :class="fulfillmentMethod === 'Pickup' ? 'bg-[#E11D48] text-white shadow-xs' : 'bg-[#FAF7F5] border border-[#F2D7DC] text-[#4B1A24] hover:bg-[#FFF1F2]'"
            >
              <span>🏪</span>
              <span>Shop Pickup</span>
            </button>
          </div>

          <!-- Pickup Details Card if Shop Pickup -->
          <div v-if="fulfillmentMethod === 'Pickup'" class="p-4 bg-[#FBF8F3] rounded-2xl border border-[#EAE1D5] space-y-2 text-xs">
            <div class="font-bold text-[#2A1D15] flex items-center space-x-2">
              <span class="text-sm">📍</span>
              <span>Pickup Location: {{ currentBranchName }} Branch</span>
            </div>
            <div class="text-[#6A5A50]">
              {{ branchDetails?.address || 'Bole Medhanialem, Addis Ababa' }}
            </div>
            <div class="text-[#BA5420] font-semibold">
              🕒 Opening Hours: {{ branchDetails?.opening_hours || 'Mon - Sun: 07:00 - 20:00' }}
            </div>
            <div class="text-[#7A6B60]">
              📞 Store Phone: {{ branchDetails?.phone || '+251 911 123 456' }}
            </div>
            <div class="text-[11px] text-[#059669] font-bold pt-1">
              ✓ Free Shop Pickup (ETB 0 delivery fee)
            </div>
          </div>

          <!-- Form Fields matching Image 2 -->
          <div class="space-y-4 pt-2">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1.5">
                Full Name
              </label>
              <input
                v-model="customer.name"
                type="text"
                placeholder="John Doe"
                class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
                required
              />
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1.5">
                Phone Number
              </label>
              <input
                v-model="customer.phone"
                type="text"
                placeholder="+251 911 123 456"
                class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
                required
              />
            </div>

            <!-- Home Delivery: Active Delivery Zone Selector matching Section 5 rules -->
            <div v-if="fulfillmentMethod === 'Delivery'" class="space-y-3">
              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1.5">
                  Select Your Delivery Zone ({{ currentBranchName }} Coverage)
                </label>
                <select
                  v-model="selectedZoneId"
                  @change="onZoneChange"
                  class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48] font-medium"
                  required
                >
                  <option value="" disabled>-- Choose active delivery zone --</option>
                  <option
                    v-for="zone in activeZones"
                    :key="zone.zone_id"
                    :value="zone.zone_id"
                  >
                    {{ zone.zone_name }} — ETB {{ zone.delivery_fee }}
                  </option>
                  <option value="outside">Address is outside these active zones</option>
                </select>
              </div>

              <!-- Warning if outside zone -->
              <div v-if="selectedZoneId === 'outside'" class="p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-2xl text-xs text-[#991B1B] space-y-2">
                <div class="font-bold flex items-center space-x-1.5">
                  <span>⚠️</span>
                  <span>Outside Delivery Coverage</span>
                </div>
                <p>
                  {{ currentBranchName }} branch does not deliver to addresses outside its active delivery zones.
                </p>
                <div class="pt-1 flex items-center space-x-3">
                  <button
                    type="button"
                    @click="setMethod('Pickup')"
                    class="px-3 py-1.5 bg-[#BE123C] text-white rounded-xl font-bold text-xs shadow-xs"
                  >
                    Switch to Shop Pickup (ETB 0)
                  </button>
                  <router-link to="/branches" class="font-bold text-[#BE123C] underline">
                    Choose another branch
                  </router-link>
                </div>
              </div>

              <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1.5">
                  Street Address &amp; House Number
                </label>
                <input
                  v-model="customer.address"
                  type="text"
                  placeholder="e.g. Bole Medhanialem, Street 3, Villa 42"
                  class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
                  required
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1.5">
                Order Notes / Gate Instructions (Optional)
              </label>
              <textarea
                v-model="customer.notes"
                rows="2"
                placeholder="Special delivery instructions, gate code, or gift note..."
                class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl p-3 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
              ></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Order Summary & Payment Method (5 cols) matching Image 2 -->
      <div class="lg:col-span-5 space-y-6">
        <!-- Summary Box -->
        <div class="bg-white rounded-3xl border border-[#F3E8E8] p-6 space-y-5 shadow-2xs">
          <h2 class="text-base font-serif font-bold text-[#38121A] border-b border-[#F8E7EA] pb-3">
            Order Summary
          </h2>

          <!-- Purchased Items Thumbnails -->
          <div class="space-y-3 divide-y divide-[#F8E7EA] max-h-60 overflow-y-auto">
            <div
              v-for="item in cartItems"
              :key="item.product_id"
              class="pt-3 first:pt-0 flex items-center justify-between text-xs"
            >
              <div class="flex items-center space-x-3">
                <img
                  :src="item.image_url || 'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=100&auto=format&fit=crop&q=80'"
                  :alt="item.product_name"
                  class="w-12 h-12 rounded-xl object-cover border border-[#F2D7DC]"
                />
                <div>
                  <div class="font-bold text-[#38121A]">{{ item.product_name }}</div>
                  <div class="text-[11px] text-[#7A6B60]">ETB {{ item.price }} &times; {{ item.quantity }}</div>
                </div>
              </div>

              <div class="font-black text-[#881337]">
                ETB {{ (item.price * item.quantity).toLocaleString() }}
              </div>
            </div>
          </div>

          <!-- Subtotal & Delivery Breakdowns -->
          <div class="space-y-2.5 pt-3 border-t border-[#F8E7EA] text-xs">
            <div class="flex justify-between items-center text-[#55463C]">
              <span>Subtotal</span>
              <span class="font-bold text-[#38121A]">ETB {{ subtotal.toLocaleString() }}</span>
            </div>

            <div class="flex justify-between items-center text-[#55463C]">
              <span>Delivery Fee</span>
              <span class="font-bold text-[#38121A]">
                {{ fulfillmentMethod === 'Delivery' ? `ETB ${deliveryFee}` : 'ETB 0 (Pickup)' }}
              </span>
            </div>

            <div class="pt-3 border-t border-[#F8E7EA] flex justify-between items-center text-base font-black text-[#38121A]">
              <span>Total</span>
              <span class="text-[#E11D48]">ETB {{ grandTotal.toLocaleString() }}</span>
            </div>
          </div>

          <!-- Payment Method Radios matching Section 5 rules -->
          <div class="space-y-3 pt-3 border-t border-[#F8E7EA]">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[#38121A]">Payment Method</h3>

            <div class="space-y-2">
              <label
                class="flex items-center space-x-3 p-3.5 rounded-2xl border cursor-pointer transition"
                :class="paymentMethod === 'cod' ? 'border-[#E11D48] bg-[#FFF1F2]' : 'border-[#F2D7DC] bg-[#FAF7F5]'"
              >
                <input
                  type="radio"
                  value="cod"
                  v-model="paymentMethod"
                  class="text-[#E11D48] focus:ring-[#E11D48]"
                />
                <div>
                  <span class="text-xs font-bold text-[#38121A]">
                    💵 {{ fulfillmentMethod === 'Pickup' ? 'Pay on Pickup' : 'Pay on Delivery (Cash)' }}
                  </span>
                  <div class="text-[10px] text-[#7A6B60]">
                    {{ fulfillmentMethod === 'Pickup' ? 'Pay when collecting your freshly packed order' : 'Pay cash to rider upon doorstep delivery' }}
                  </div>
                </div>
              </label>

              <label
                class="flex items-center space-x-3 p-3.5 rounded-2xl border cursor-pointer transition"
                :class="paymentMethod === 'online' ? 'border-[#E11D48] bg-[#FFF1F2]' : 'border-[#F2D7DC] bg-[#FAF7F5]'"
              >
                <input
                  type="radio"
                  value="online"
                  v-model="paymentMethod"
                  class="text-[#E11D48] focus:ring-[#E11D48]"
                />
                <div class="text-xs">
                  <div class="font-bold text-[#38121A]">💳 Online Payment (Telebirr / Chapa)</div>
                  <div class="text-[10px] text-[#7A6B60]">Instant automated payment confirmation</div>
                </div>
              </label>
            </div>
          </div>

          <!-- Error Alert Banner if any -->
          <div v-if="orderError" class="p-3 bg-red-50 border border-red-200 text-xs text-red-700 rounded-xl">
            {{ orderError }}
          </div>

          <!-- Place Order Button matching Image 2 -->
          <button
            type="submit"
            :disabled="isSubmitting || (fulfillmentMethod === 'Delivery' && selectedZoneId === 'outside')"
            class="w-full py-4 bg-[#E11D48] hover:bg-[#BE123C] disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold uppercase tracking-wider rounded-2xl shadow-md hover:shadow-lg transition active:scale-98"
          >
            {{ isSubmitting ? 'Placing your order...' : `Place Order (ETB ${grandTotal.toLocaleString()}) →` }}
          </button>
        </div>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useBranchStore } from '../../stores/branch';
import { useCartStore } from '../../stores/cart';
import { useAuthStore } from '../../stores/auth';
import api from '../../api';

const router = useRouter();
const branchStore = useBranchStore();
const cartStore = useCartStore();
const authStore = useAuthStore();

const fulfillmentMethod = ref(cartStore.fulfillmentMethod || 'Delivery');
const paymentMethod = ref('online');
const deliveryFee = ref(60);
const selectedZoneId = ref(null);
const activeZones = ref([]);
const branchDetails = ref(null);
const isSubmitting = ref(false);
const orderError = ref(null);

const customer = ref({
  name: 'Sara Ahmed',
  phone: '+251 911 123 456',
  address: 'Bole Medhanialem, Addis Ababa',
  notes: '',
});

const currentBranchName = computed(() => branchStore.selectedBranch?.branch_name || 'Bole');

const cartItems = computed(() => {
  return cartStore.items;
});

const subtotal = computed(() => {
  return cartItems.value.reduce((sum, item) => sum + item.price * item.quantity, 0);
});

const grandTotal = computed(() => {
  const delivery = fulfillmentMethod.value === 'Delivery' ? deliveryFee.value : 0;
  return subtotal.value + delivery;
});

function setMethod(method) {
  fulfillmentMethod.value = method;
  cartStore.setFulfillment(method, method === 'Delivery' ? deliveryFee.value : 0, selectedZoneId.value);
}

function onZoneChange() {
  if (selectedZoneId.value && selectedZoneId.value !== 'outside') {
    const zone = activeZones.value.find((z) => z.zone_id === selectedZoneId.value);
    if (zone) {
      deliveryFee.value = parseFloat(zone.delivery_fee) || 60;
      cartStore.setFulfillment('Delivery', deliveryFee.value, zone.zone_id);
    }
  }
}

onMounted(async () => {
  if (authStore.user) {
    customer.value.name = authStore.user.full_name || 'Sara Ahmed';
    customer.value.phone = authStore.user.phone || '+251 911 123 456';
  }

  const branchId = branchStore.selectedBranchId || 2;

  // Load branch delivery zones
  try {
    const zoneRes = await api.get(`/branches/${branchId}/delivery-zones`);
    const zones = zoneRes.data || [];
    activeZones.value = zones.filter((z) => z.status === 'Active');

    if (activeZones.value.length > 0) {
      selectedZoneId.value = activeZones.value[0].zone_id;
      deliveryFee.value = parseFloat(activeZones.value[0].delivery_fee) || 60;
    }
  } catch (err) {
    console.error('Failed to load delivery zones', err);
    activeZones.value = [
      { zone_id: 1, zone_name: 'Bole Medhanialem', delivery_fee: 60, status: 'Active' },
      { zone_id: 2, zone_name: 'Atlas & Rwanda', delivery_fee: 50, status: 'Active' },
      { zone_id: 3, zone_name: 'Olympia & Meskel Flower', delivery_fee: 60, status: 'Active' },
    ];
    selectedZoneId.value = 1;
  }

  // Load branch details for pickup opening hours & address
  try {
    const branchRes = await api.get(`/branches/${branchId}`);
    branchDetails.value = branchRes.data;
  } catch (err) {
    console.error('Failed to load branch details', err);
  }
});

async function handlePlaceOrder() {
  orderError.value = null;

  if (cartItems.value.length === 0) {
    orderError.value = 'Your cart is empty. Please add items before checking out.';
    return;
  }

  if (fulfillmentMethod.value === 'Delivery' && selectedZoneId.value === 'outside') {
    orderError.value = 'Home delivery is only available within active delivery zones for this branch. Please choose an active zone or select Shop Pickup.';
    return;
  }

  isSubmitting.value = true;
  try {
    const mappedPayment = paymentMethod.value === 'online'
      ? 'Chapa'
      : (fulfillmentMethod.value === 'Pickup' ? 'Pay on pickup' : 'Pay on delivery');

    const payload = {
      branch_id: branchStore.selectedBranchId || 2,
      fulfillment_method: fulfillmentMethod.value,
      payment_method: mappedPayment,
      delivery_address: customer.value.address,
      order_notes: customer.value.notes,
      zone_id: fulfillmentMethod.value === 'Delivery' ? selectedZoneId.value : null,
      items: cartItems.value.map((i) => ({
        product_id: i.product_id,
        quantity: i.quantity,
      })),
    };

    const res = await api.post('/orders', payload);
    const orderNumber = res.data?.order_number || 'ORD-1001';
    cartStore.clearCart();
    router.push(`/orders/${orderNumber}`);
  } catch (err) {
    console.error('Failed to submit order', err);
    orderError.value = err.response?.data?.message || 'Failed to place order. Please review your stock or address.';
  } finally {
    isSubmitting.value = false;
  }
}
</script>
