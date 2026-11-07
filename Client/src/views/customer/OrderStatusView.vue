<template>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6 font-sans">
    <!-- Success Banner matching Page 8 -->
    <div class="p-4 bg-[#E8F5E9] border border-[#C8E6C9] rounded-2xl text-xs text-[#2E7D32] space-y-1">
      <div class="font-bold text-sm">Payment received. Your order is placed.</div>
      <div>{{ order?.branch?.branch_name || 'Bole' }} ({{ currentBranchCode }}) has your order and will start preparing it.</div>
    </div>

    <!-- Order Header matching Page 8 -->
    <div class="flex items-center justify-between pt-2">
      <div>
        <div class="text-[11px] font-bold uppercase tracking-widest text-[#8C7D73]">Order Number</div>
        <h1 class="text-3xl sm:text-4xl font-serif font-bold text-[#2A1D15] mt-0.5">
          {{ order?.order_number || orderNumberParam }}
        </h1>
      </div>

      <span
        class="px-3.5 py-1.5 rounded-full text-xs font-bold"
        :class="statusBadgeClass(order?.order_status || 'Pending')"
      >
        {{ order?.order_status || 'Pending' }}
      </span>
    </div>

    <!-- Main Content 2-Column Grid: Left Progress (5 cols), Right Details (7 cols) -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start pt-2">
      <!-- Left Column: Progress Stepper (5 cols) matching Page 8 -->
      <div class="md:col-span-5 bg-white rounded-2xl border border-[#E7DFD5] p-6 shadow-2xs space-y-6">
        <h2 class="text-base font-serif font-bold text-[#2A1D15]">Progress</h2>

        <!-- Vertical Stepper Steps -->
        <div class="relative space-y-6 pl-2">
          <!-- Step 1: Order Placed -->
          <div class="flex items-start space-x-3.5 relative">
            <div class="w-7 h-7 rounded-full bg-[#2E7D32] text-white flex items-center justify-center text-xs font-bold shrink-0 z-10 shadow-xs">
              ✓
            </div>
            <div>
              <div class="text-xs font-bold text-[#2A1D15]">1 &middot; Order placed</div>
              <div class="text-[11px] text-[#7A6B60]">Stock reserved at {{ order?.branch?.branch_name || 'Bole' }}</div>
            </div>
            <!-- Connecting Line -->
            <div class="absolute left-3.5 top-7 w-0.5 h-7 bg-[#2E7D32]"></div>
          </div>

          <!-- Step 2: Confirmed by branch -->
          <div class="flex items-start space-x-3.5 relative">
            <div
              class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 z-10 shadow-xs"
              :class="isStepActiveOrDone(2) ? 'bg-[#BA5420] text-white' : 'bg-[#F2EAE0] text-[#7A6B60]'"
            >
              2
            </div>
            <div>
              <div class="text-xs font-bold text-[#2A1D15]">Confirmed by branch</div>
              <div class="text-[11px] text-[#7A6B60]">
                {{ isStepActiveOrDone(2) ? 'Confirmed' : `Waiting for ${order?.branch?.branch_name || 'Bole'} to accept` }}
              </div>
            </div>
            <div class="absolute left-3.5 top-7 w-0.5 h-7 bg-[#E0D5C7]"></div>
          </div>

          <!-- Step 3: Preparing -->
          <div class="flex items-start space-x-3.5 relative">
            <div
              class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 z-10 shadow-xs"
              :class="isStepActiveOrDone(3) ? 'bg-[#BA5420] text-white' : 'bg-[#F2EAE0] text-[#7A6B60]'"
            >
              3
            </div>
            <div>
              <div class="text-xs font-bold text-[#2A1D15]">Preparing</div>
              <div class="text-[11px] text-[#7A6B60]">Baking and packing</div>
            </div>
            <div class="absolute left-3.5 top-7 w-0.5 h-7 bg-[#E0D5C7]"></div>
          </div>

          <!-- Step 4: Out for delivery -->
          <div class="flex items-start space-x-3.5 relative">
            <div
              class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 z-10 shadow-xs"
              :class="isStepActiveOrDone(4) ? 'bg-[#BA5420] text-white' : 'bg-[#F2EAE0] text-[#7A6B60]'"
            >
              4
            </div>
            <div>
              <div class="text-xs font-bold text-[#2A1D15]">Out for delivery</div>
              <div class="text-[11px] text-[#7A6B60]">On its way to you</div>
            </div>
            <div class="absolute left-3.5 top-7 w-0.5 h-7 bg-[#E0D5C7]"></div>
          </div>

          <!-- Step 5: Delivered -->
          <div class="flex items-start space-x-3.5">
            <div
              class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0 z-10 shadow-xs"
              :class="isStepActiveOrDone(5) ? 'bg-[#2E7D32] text-white' : 'bg-[#F2EAE0] text-[#7A6B60]'"
            >
              5
            </div>
            <div>
              <div class="text-xs font-bold text-[#2A1D15]">Delivered</div>
              <div class="text-[11px] text-[#7A6B60]">Enjoy</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Details Card (7 cols) matching Page 8 -->
      <div class="md:col-span-7 bg-white rounded-2xl border border-[#E7DFD5] p-6 shadow-2xs space-y-6">
        <h2 class="text-base font-serif font-bold text-[#2A1D15]">Details</h2>

        <!-- Metadata Grid -->
        <div class="space-y-3 text-xs border-b border-[#F2EAE0] pb-4">
          <div class="flex justify-between items-center">
            <span class="text-[#7A6B60]">Branch</span>
            <span class="font-bold text-[#2A1D15]">
              {{ order?.branch?.branch_name || 'Bole' }} ({{ currentBranchCode }})
            </span>
          </div>

          <div class="flex justify-between items-center">
            <span class="text-[#7A6B60]">Fulfillment</span>
            <span class="font-semibold text-[#2A1D15]">{{ order?.order_type || 'Home delivery' }}</span>
          </div>

          <div class="flex justify-between items-center">
            <span class="text-[#7A6B60]">Payment</span>
            <span class="font-semibold text-[#2E7D32]">Online &middot; Paid</span>
          </div>

          <div class="flex justify-between items-center">
            <span class="text-[#7A6B60]">Deliver to</span>
            <span class="font-semibold text-[#2A1D15] text-right">
              {{ order?.delivery_address || 'House #14, near Edna Mall, Bole' }}
            </span>
          </div>
        </div>

        <!-- Items Breakdown -->
        <div class="space-y-2 text-xs divide-y divide-[#F5EFE8]">
          <div
            v-for="item in displayItems"
            :key="item.order_item_id || item.product_name"
            class="pt-2 first:pt-0 flex justify-between items-center"
          >
            <span class="text-[#4A3B32]">{{ item.quantity }} &times; {{ item.product?.product_name || item.product_name }}</span>
            <span class="font-bold text-[#2A1D15]">
              ETB {{ ((item.unit_price || item.price) * item.quantity).toLocaleString() }}
            </span>
          </div>
        </div>

        <!-- Totals -->
        <div class="pt-3 border-t border-[#EAE1D5] space-y-2 text-xs">
          <div class="flex justify-between items-center text-[#55463C]">
            <span>Delivery</span>
            <span class="font-bold text-[#2A1D15]">ETB {{ order?.delivery_fee || 60 }}</span>
          </div>

          <div class="pt-2 border-t border-[#F2EAE0] flex justify-between items-center text-base font-bold text-[#2A1D15]">
            <span>Total</span>
            <span class="text-[#BA5420]">ETB {{ (order?.total_amount || 1255).toLocaleString() }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Questions Box matching Page 8 -->
    <div class="bg-[#F8F4EE] rounded-2xl border border-[#E7DFD5] p-5 space-y-1">
      <h3 class="text-xs font-bold text-[#2A1D15] uppercase tracking-wider">
        Questions about this order?
      </h3>
      <p class="text-xs text-[#6A5A50]">
        Call {{ order?.branch?.branch_name || 'Bole' }} at {{ order?.branch?.phone || '+251 11 000 0001' }}. Your order stays with this branch.
      </p>
    </div>

    <!-- Bottom Buttons matching Page 8 -->
    <div class="flex items-center space-x-3 pt-2">
      <router-link
        to="/shop"
        class="px-5 py-2.5 bg-white border border-[#DDD2C5] hover:bg-[#F4ECE3] text-[#4A3B32] text-xs font-semibold rounded-xl shadow-2xs transition"
      >
        Keep shopping
      </router-link>

      <button
        @click="cancelOrder"
        class="px-5 py-2.5 bg-white border border-[#DDD2C5] hover:bg-[#FFEBEE] hover:text-[#C62828] text-[#7A6B60] text-xs font-semibold rounded-xl transition"
      >
        Cancel order
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '../../api';
import { confirmAction, toast } from '../../services/feedback';

const route = useRoute();
const orderNumberParam = computed(() => route.params.order_number || 'ORD-1001');

const order = ref(null);

const currentBranchCode = computed(() => {
  const b = order.value?.branch;
  if (!b) return 'Branch 01';
  return b.is_main ? 'Main' : `Branch ${String(b.branch_id).padStart(2, '0')}`;
});

const displayItems = computed(() => {
  if (order.value?.items && order.value.items.length > 0) {
    return order.value.items;
  }
  return [
    { product_name: 'Chocolate Cake', quantity: 1, unit_price: 850 },
    { product_name: 'White Bread', quantity: 5, unit_price: 45 },
    { product_name: 'Croissant', quantity: 2, unit_price: 60 },
  ];
});

onMounted(async () => {
  try {
    const res = await api.get(`/orders`);
    const found = res.data.find((o) => o.order_number === orderNumberParam.value);
    if (found) {
      order.value = found;
    }
  } catch (err) {
    console.error('Failed to load order', err);
  }
});

function isStepActiveOrDone(step) {
  const status = order.value?.order_status || 'Pending';
  if (status === 'Pending') return step <= 1;
  if (status === 'Confirmed') return step <= 2;
  if (status === 'Preparing') return step <= 3;
  if (status === 'Out for Delivery') return step <= 4;
  if (status === 'Delivered') return step <= 5;
  return false;
}

function statusBadgeClass(status) {
  switch (status) {
    case 'Pending':
      return 'bg-[#FFF8E1] text-[#B76E00]';
    case 'Confirmed':
      return 'bg-[#E3F2FD] text-[#1565C0]';
    case 'Preparing':
      return 'bg-[#EDE7F6] text-[#512DA8]';
    case 'Out for Delivery':
      return 'bg-[#E0F2F1] text-[#00695C]';
    case 'Delivered':
      return 'bg-[#E8F5E9] text-[#2E7D32]';
    default:
      return 'bg-[#F2EAE0] text-[#6A5A50]';
  }
}

async function cancelOrder() {
  const confirmed = await confirmAction({
    title: 'Cancel order?',
    message: 'Are you sure you want to cancel this order?',
    confirmText: 'Cancel order',
  });
  if (confirmed) {
    toast.info('Cancellation request submitted to branch manager.');
  }
}
</script>
