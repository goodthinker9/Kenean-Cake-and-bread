<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">All orders</h2>
      <button
        @click="exportCSV"
        class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
      >
        Export CSV
      </button>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-1">
      <button
        @click="setStatusFilter('All')"
        class="px-4 py-1.5 text-xs font-semibold rounded-full transition"
        :class="activeStatus === 'All' ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
      >
        All
      </button>
      <button
        @click="setStatusFilter('Pending')"
        class="px-4 py-1.5 text-xs font-semibold rounded-full transition flex items-center space-x-1"
        :class="activeStatus === 'Pending' ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
      >
        <span>Pending</span>
        <span class="opacity-80">({{ pendingCount }})</span>
      </button>
      <button
        v-for="st in ['Confirmed', 'Preparing', 'Ready', 'Out for delivery', 'Completed', 'Cancelled']"
        :key="st"
        @click="setStatusFilter(st)"
        class="px-4 py-1.5 text-xs font-semibold rounded-full transition"
        :class="activeStatus === st ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
      >
        {{ st }}
      </button>
    </div>

    <!-- Filter Bar -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-white p-3.5 border border-[#E9E0D4] rounded-xl shadow-sm">
      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">Search</label>
        <input
          v-model="filters.search"
          @input="applyFilters"
          type="text"
          placeholder="Order number or phone"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        />
      </div>

      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">Branch</label>
        <select
          v-model="filters.branch_id"
          @change="applyFilters"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">All branches</option>
          <option v-for="b in branches" :key="b.branch_id" :value="b.branch_id">
            {{ b.branch_name }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">Method</label>
        <select
          v-model="filters.method"
          @change="applyFilters"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">Delivery and pickup</option>
          <option value="Delivery">Delivery only</option>
          <option value="Pickup">Pickup only</option>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">Date</label>
        <select
          v-model="filters.date"
          @change="applyFilters"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="today">Today</option>
          <option value="all">All time</option>
        </select>
      </div>
    </div>

    <!-- Main Content Grid: Table + Side Order Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Orders Table (Col span 2) -->
      <div class="lg:col-span-2 bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
              <tr>
                <th class="py-3 px-4">Order</th>
                <th class="py-3 px-3">Branch</th>
                <th class="py-3 px-3">Customer</th>
                <th class="py-3 px-3">Method</th>
                <th class="py-3 px-3">Payment</th>
                <th class="py-3 px-3">Total</th>
                <th class="py-3 px-3 text-center">Status</th>
                <th class="py-3 px-4 text-right">Placed</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr
                v-for="ord in orders"
                :key="ord.order_id"
                @click="selectOrder(ord)"
                class="hover:bg-[#FCFAF7] transition cursor-pointer"
                :class="selectedOrder?.order_id === ord.order_id ? 'bg-[#FAF3EC]' : ''"
              >
                <td class="py-3 px-4 font-mono text-xs font-semibold text-[#382C24]">{{ ord.order_number }}</td>
                <td class="py-3 px-3 text-xs text-[#4A3B32]">{{ ord.branch?.branch_name }}</td>
                <td class="py-3 px-3 text-xs text-[#5A4B41]">{{ ord.user?.full_name || '[CUSTOMER]' }}</td>
                <td class="py-3 px-3 text-xs text-[#6A5A50]">{{ ord.fulfillment_method }}</td>
                <td class="py-3 px-3 text-xs text-[#6A5A50]">
                  {{ ord.payment ? `${ord.payment.payment_method} · ${ord.payment.payment_status}` : '—' }}
                </td>
                <td class="py-3 px-3 text-xs font-semibold text-[#241710]">
                  ETB {{ Number(ord.total_amount).toLocaleString() }}
                </td>
                <td class="py-3 px-3 text-center">
                  <span
                    class="inline-block px-2.5 py-0.5 text-[11px] font-semibold rounded-full"
                    :class="{
                      'bg-[#FFF8E6] text-[#A67503] border border-[#FEE39E]': ord.order_status === 'Pending',
                      'bg-[#EAF5EA] text-[#2E7D32] border border-[#C8E6C9]': ord.order_status === 'Confirmed' || ord.order_status === 'Completed',
                      'bg-[#EBF3FB] text-[#1565C0] border border-[#BBDEFB]': ord.order_status === 'Preparing' || ord.order_status === 'Ready' || ord.order_status === 'Out for delivery',
                      'bg-[#FDEAEA] text-[#C62828] border border-[#FFCDD2]': ord.order_status === 'Cancelled'
                    }"
                  >
                    {{ ord.order_status }}
                  </span>
                </td>
                <td class="py-3 px-4 text-right text-xs text-[#8A7B71]">
                  {{ formatTime(ord.placed_at) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right Side Panel: Order Details -->
      <div v-if="selectedOrder" class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <div>
          <h3 class="text-lg font-serif font-bold text-[#2A1D15]">
            {{ selectedOrder.order_number }} · {{ selectedOrder.branch?.branch_name }}
          </h3>
          <p class="text-[11px] text-[#8A7B71] leading-relaxed mt-1">
            {{ selectedOrder.fulfillment_method }} · {{ selectedOrder.payment?.payment_method }} · {{ selectedOrder.payment?.payment_status }} · branch cannot be changed after payment without Super Admin action.
          </p>
        </div>

        <!-- Items Table -->
        <div class="border-t border-b border-[#EFE8DF] py-3 space-y-2">
          <div class="flex items-center justify-between text-[11px] font-bold text-[#88786B] uppercase">
            <span>Item</span>
            <div class="flex space-x-6">
              <span>Qty</span>
              <span>Price</span>
            </div>
          </div>

          <div
            v-for="item in selectedOrder.items"
            :key="item.order_item_id"
            class="flex items-center justify-between text-xs py-1"
          >
            <span class="text-[#382C24] font-medium">{{ item.product_name }}</span>
            <div class="flex items-center space-x-6 text-right">
              <span class="text-[#6A5A50] w-6 text-center">{{ item.quantity }}</span>
              <span class="font-semibold text-[#241710] w-16">ETB {{ Number(item.subtotal).toLocaleString() }}</span>
            </div>
          </div>

          <div v-if="Number(selectedOrder.delivery_fee) > 0" class="flex items-center justify-between text-xs py-1 text-[#6A5A50]">
            <span>Delivery</span>
            <span class="font-semibold text-[#241710]">ETB {{ Number(selectedOrder.delivery_fee).toLocaleString() }}</span>
          </div>

          <div class="flex items-center justify-between text-sm font-bold text-[#241710] pt-2 border-t border-[#F0E9DF]">
            <span>Total</span>
            <span class="text-[#BA5420]">ETB {{ Number(selectedOrder.total_amount).toLocaleString() }}</span>
          </div>
        </div>

        <!-- Status History Timeline -->
        <div class="space-y-2">
          <div class="text-xs font-bold text-[#2A1D15]">Status history</div>
          <div class="space-y-1.5 text-xs text-[#7A6B60]">
            <div
              v-for="hist in selectedOrder.status_history"
              :key="hist.history_id"
              class="flex items-start space-x-2"
            >
              <span class="text-[#BA5420]">•</span>
              <div>
                <span class="font-medium text-[#382C24]">{{ hist.new_status }}</span>
                <span> → {{ hist.note || 'updated' }}, {{ formatTime(hist.changed_at) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="pt-3 flex space-x-3">
          <button
            v-if="selectedOrder.order_status === 'Pending'"
            @click="updateOrderStatus(selectedOrder, 'Confirmed')"
            class="flex-1 py-2 px-3 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-semibold rounded-lg shadow-sm transition"
          >
            Mark confirmed
          </button>
          <button
            v-else-if="selectedOrder.order_status === 'Confirmed'"
            @click="updateOrderStatus(selectedOrder, 'Preparing')"
            class="flex-1 py-2 px-3 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-semibold rounded-lg shadow-sm transition"
          >
            Start preparing
          </button>
          <button
            v-else-if="selectedOrder.order_status === 'Preparing'"
            @click="updateOrderStatus(selectedOrder, 'Ready')"
            class="flex-1 py-2 px-3 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-semibold rounded-lg shadow-sm transition"
          >
            Mark ready
          </button>
          <button
            v-else-if="selectedOrder.order_status === 'Ready'"
            @click="updateOrderStatus(selectedOrder, selectedOrder.fulfillment_method === 'Delivery' ? 'Out for delivery' : 'Completed')"
            class="flex-1 py-2 px-3 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-semibold rounded-lg shadow-sm transition"
          >
            {{ selectedOrder.fulfillment_method === 'Delivery' ? 'Dispatch for delivery' : 'Mark completed' }}
          </button>
          <button
            v-else-if="selectedOrder.order_status === 'Out for delivery'"
            @click="updateOrderStatus(selectedOrder, 'Completed')"
            class="flex-1 py-2 px-3 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-semibold rounded-lg shadow-sm transition"
          >
            Mark delivered
          </button>

          <button
            v-if="selectedOrder.order_status !== 'Cancelled' && selectedOrder.order_status !== 'Completed'"
            @click="cancelOrder(selectedOrder)"
            class="px-4 py-2 bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#6A5A50] text-xs font-semibold rounded-lg transition"
          >
            Cancel order
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';
import { confirmAction } from '../../services/feedback';

const orders = ref([]);
const branches = ref([]);
const pendingCount = ref(9);
const activeStatus = ref('All');
const selectedOrder = ref(null);

const filters = ref({
  search: '',
  branch_id: 'all',
  method: 'all',
  date: 'today',
});

const loadOrders = async () => {
  try {
    const res = await api.get('/admin/orders', {
      params: {
        status: activeStatus.value,
        branch_id: filters.value.branch_id,
        method: filters.value.method,
        search: filters.value.search,
        date: filters.value.date,
      }
    });
    orders.value = res.data.orders;
    pendingCount.value = res.data.pending_count;

    if (!selectedOrder.value && orders.value.length > 0) {
      selectedOrder.value = orders.value[0];
    } else if (selectedOrder.value) {
      // sync current selected
      selectedOrder.value = orders.value.find((o) => o.order_id === selectedOrder.value.order_id) || orders.value[0];
    }
  } catch (err) {
    console.error('Failed to load orders', err);
  }
};

const setStatusFilter = (st) => {
  activeStatus.value = st;
  loadOrders();
};

const applyFilters = () => {
  loadOrders();
};

const selectOrder = (ord) => {
  selectedOrder.value = ord;
};

const updateOrderStatus = async (ord, newStatus) => {
  try {
    const res = await api.patch(`/admin/orders/${ord.order_id}/status`, {
      order_status: newStatus,
      note: `Updated to ${newStatus} by admin`,
    });
    selectedOrder.value = res.data;
    await loadOrders();
  } catch {
    // The API interceptor displays the request error.
  }
};

const cancelOrder = async (ord) => {
  const confirmed = await confirmAction({
    title: 'Cancel order?',
    message: `Cancel order ${ord.order_number}? Its reservation will be released.`,
    confirmText: 'Cancel order',
  });
  if (confirmed) {
    await updateOrderStatus(ord, 'Cancelled');
  }
};

const formatTime = (dt) => {
  if (!dt) return '10:42';
  try {
    const d = new Date(dt);
    return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false });
  } catch {
    return '10:42';
  }
};

const exportCSV = () => {
  const headers = ['Order', 'Branch', 'Customer', 'Method', 'Total', 'Status', 'Placed'];
  const rows = orders.value.map(o => [
    `"${o.order_number}"`,
    `"${o.branch?.branch_name}"`,
    `"${o.user?.full_name || 'Customer'}"`,
    `"${o.fulfillment_method}"`,
    o.total_amount,
    `"${o.order_status}"`,
    `"${o.placed_at}"`
  ]);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `orders.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

onMounted(async () => {
  try {
    const bRes = await api.get('/branches');
    branches.value = bRes.data;
  } catch {
    // The API interceptor displays the request error.
  }
  loadOrders();
});
</script>
