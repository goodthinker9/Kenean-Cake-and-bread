<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 space-y-6">
    <div class="text-center space-y-2">
      <span class="text-xs uppercase tracking-wider font-bold text-[#BA5420]">Live Status</span>
      <h1 class="text-3xl font-serif font-bold text-[#2A1D15]">Track Your Bakery Order</h1>
      <p class="text-xs text-[#7A6B60]">Enter your order number to check live preparation and fulfillment status.</p>
    </div>

    <!-- Search Box -->
    <div class="flex items-center space-x-2 bg-white p-2 rounded-2xl border border-[#E9E0D4] shadow-xs max-w-md mx-auto">
      <input
        v-model="searchNumber"
        type="text"
        placeholder="e.g. ORD-1001"
        class="flex-1 px-4 py-2 text-xs font-mono font-bold text-[#2A1D15] focus:outline-none"
      />
      <button
        @click="searchOrder"
        class="px-5 py-2 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-bold rounded-xl transition shadow-xs"
      >
        Track
      </button>
    </div>

    <!-- Order Tracking Display -->
    <div v-if="order" class="bg-white p-6 sm:p-8 rounded-2xl border border-[#E9E0D4] shadow-xs space-y-6">
      <div class="flex items-center justify-between border-b border-[#EFE8DF] pb-4">
        <div>
          <span class="text-xs font-mono font-bold text-[#BA5420]">{{ order.order_number }}</span>
          <h2 class="text-xl font-serif font-bold text-[#2A1D15] mt-0.5">
            Fulfilling Branch: {{ order.branch?.branch_name }}
          </h2>
          <p class="text-xs text-[#7A6B60]">{{ order.fulfillment_method }} · Placed at {{ order.placed_at }}</p>
        </div>

        <div>
          <span
            class="px-3 py-1 text-xs font-bold rounded-full"
            :class="{
              'bg-[#FFF8E6] text-[#A67503] border border-[#FEE39E]': order.order_status === 'Pending',
              'bg-[#EAF5EA] text-[#2E7D32] border border-[#C8E6C9]': order.order_status === 'Confirmed' || order.order_status === 'Completed',
              'bg-[#EBF3FB] text-[#1565C0] border border-[#BBDEFB]': ['Preparing', 'Ready', 'Out for delivery'].includes(order.order_status),
              'bg-[#FDEAEA] text-[#C62828] border border-[#FFCDD2]': order.order_status === 'Cancelled'
            }"
          >
            {{ order.order_status }}
          </span>
        </div>
      </div>

      <!-- Lifecycle Stepper Timeline -->
      <div class="py-4">
        <div class="flex items-center justify-between relative">
          <!-- Connector Line -->
          <div class="absolute top-1/2 left-0 right-0 h-1 bg-[#EAE2D8] -translate-y-1/2 z-0"></div>

          <div
            v-for="(step, i) in steps"
            :key="step"
            class="relative z-10 flex flex-col items-center"
          >
            <div
              class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300"
              :class="isStepComplete(step) ? 'bg-[#BA5420] text-white shadow-xs' : 'bg-[#EAE2D8] text-[#8C7D73]'"
            >
              {{ i + 1 }}
            </div>
            <span
              class="text-[10px] mt-2 font-medium tracking-tight text-center max-w-[70px]"
              :class="isStepComplete(step) ? 'text-[#BA5420] font-bold' : 'text-[#8C7D73]'"
            >
              {{ step }}
            </span>
          </div>
        </div>
      </div>

      <!-- Order Items Detail -->
      <div class="border-t border-[#EFE8DF] pt-4 space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-[#8C7D73]">Purchased Items</h3>
        <div class="divide-y divide-[#F0E8DD]">
          <div
            v-for="item in order.items"
            :key="item.order_item_id"
            class="py-2.5 flex justify-between text-xs"
          >
            <span class="font-medium text-[#2A1D15]">{{ item.product_name }} × {{ item.quantity }}</span>
            <span class="font-bold text-[#2A1D15]">ETB {{ Number(item.subtotal).toLocaleString() }}</span>
          </div>
        </div>

        <div class="flex justify-between text-xs text-[#7A6B60] pt-2">
          <span>Delivery Fee</span>
          <span class="font-semibold text-[#2A1D15]">ETB {{ Number(order.delivery_fee).toLocaleString() }}</span>
        </div>

        <div class="flex justify-between text-sm font-bold text-[#2A1D15] border-t border-[#F0E8DD] pt-2">
          <span>Total Paid</span>
          <span class="text-[#BA5420]">ETB {{ Number(order.total_amount).toLocaleString() }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';

const route = useRoute();
const searchNumber = ref(route.query.order || 'ORD-1001');
const order = ref(null);

const steps = ['Pending', 'Confirmed', 'Preparing', 'Ready', 'Completed'];

const searchOrder = async () => {
  if (!searchNumber.value) return;
  try {
    const res = await api.get('/orders', {
      params: { search: searchNumber.value }
    });
    const list = Array.isArray(res.data) ? res.data : (res.data.orders || []);
    const found = list.find((o) => o.order_number?.toLowerCase() === searchNumber.value.trim().toLowerCase());
    if (found) {
      order.value = found;
    } else {
      alert(`Order ${searchNumber.value} not found.`);
    }
  } catch (err) {
    console.error(err);
  }
};

const isStepComplete = (step) => {
  if (!order.value) return false;
  const current = order.value.order_status;
  if (current === 'Cancelled') return false;
  const orderRank = {
    'Pending': 1,
    'Confirmed': 2,
    'Preparing': 3,
    'Ready': 4,
    'Out for delivery': 4,
    'Completed': 5,
  };
  return (orderRank[current] || 0) >= (orderRank[step] || 0);
};

onMounted(() => {
  searchOrder();
});
</script>
