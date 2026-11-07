<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Reports</h2>
      <div class="flex items-center space-x-3">
        <button
          @click="exportCSV"
          class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
        >
          Export CSV
        </button>
        <button
          @click="exportPDF"
          class="px-4 py-2 text-sm font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg transition shadow-sm"
        >
          Export PDF
        </button>
      </div>
    </div>

    <!-- Report Tabs -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-1">
      <button
        v-for="t in ['Branch performance', 'Product performance', 'Distribution', 'Inventory', 'Waste', 'Discrepancy', 'Delivery', 'Payments']"
        :key="t"
        @click="activeTab = t"
        class="px-4 py-1.5 text-xs font-semibold rounded-full transition whitespace-nowrap"
        :class="activeTab === t ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
      >
        {{ t }}
      </button>
    </div>

    <!-- Filters Row -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-white p-3.5 border border-[#E9E0D4] rounded-xl shadow-sm">
      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">Branch</label>
        <select
          v-model="filters.branch_id"
          @change="loadReport"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">All branches</option>
          <option v-for="b in branches" :key="b.branch_id" :value="b.branch_id">
            {{ b.branch_name }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">Category</label>
        <select
          v-model="filters.category_id"
          @change="loadReport"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">All</option>
          <option v-for="c in categories" :key="c.category_id" :value="c.category_id">
            {{ c.category_name }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">From</label>
        <input
          v-model="filters.from_date"
          type="date"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        />
      </div>

      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">To</label>
        <input
          v-model="filters.to_date"
          type="date"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        />
      </div>
    </div>

    <!-- 4 Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Revenue</div>
        <div class="text-3xl font-bold text-[#241710]">ETB {{ Number(metrics.revenue).toLocaleString() }}</div>
        <div class="text-xs text-[#2E7D32] mt-1">{{ metrics.revenue_trend }}</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Orders</div>
        <div class="text-3xl font-bold text-[#241710]">{{ metrics.orders }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">{{ metrics.orders_sub }}</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Average order</div>
        <div class="text-3xl font-bold text-[#241710]">ETB {{ Number(metrics.avg_order).toLocaleString() }}</div>
        <div class="text-xs text-[#2E7D32] mt-1">{{ metrics.avg_order_trend }}</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Units sold</div>
        <div class="text-3xl font-bold text-[#241710]">{{ metrics.units_sold }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">{{ metrics.units_sub }}</div>
      </div>
    </div>

    <!-- Middle Grid: Revenue by branch (Horizontal Bar Chart) + Top Products -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
      <!-- Revenue by Branch Bar Chart -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">Revenue by branch</h3>

        <div class="space-y-4 pt-1">
          <div v-for="b in revenueByBranch" :key="b.branch" class="space-y-1.5">
            <div class="flex items-center justify-between text-xs font-medium">
              <span class="text-[#382C24]">{{ b.branch }}</span>
              <span class="text-[#241710] font-bold">ETB {{ Number(b.revenue).toLocaleString() }}</span>
            </div>
            <!-- Progress Bar -->
            <div class="w-full bg-[#EFE8DF] h-3.5 rounded-full overflow-hidden">
              <div
                class="bg-[#BA5420] h-full rounded-full transition-all duration-500"
                :style="{ width: `${Math.min(100, Math.round((b.revenue / maxBranchRevenue) * 100))}%` }"
              ></div>
            </div>
          </div>
        </div>

        <p class="text-[11px] text-[#8F7F74] pt-2 border-t border-[#EFE8DF]">
          Sample data. Real values come from branch sales and reconcile with transaction history.
        </p>
      </div>

      <!-- Top Products Table -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">Top products</h3>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="text-[#88786B] uppercase border-b border-[#EFE8DF] font-semibold">
              <tr>
                <th class="pb-2">Product</th>
                <th class="pb-2 text-center">Units</th>
                <th class="pb-2 text-right">Revenue</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr v-for="tp in topProducts" :key="tp.product">
                <td class="py-2.5 font-medium text-[#382C24]">{{ tp.product }}</td>
                <td class="py-2.5 text-center text-[#55463C] font-semibold">{{ tp.units }}</td>
                <td class="py-2.5 text-right font-bold text-[#241710]">ETB {{ Number(tp.revenue).toLocaleString() }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Bottom: Branch Performance Table -->
    <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
      <div class="p-5 border-b border-[#EFE8DF]">
        <h3 class="font-semibold text-base text-[#2A1D15]">Branch performance</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
            <tr>
              <th class="py-3 px-5">Branch</th>
              <th class="py-3 px-4 text-center">Orders</th>
              <th class="py-3 px-4 text-center">Units</th>
              <th class="py-3 px-4 text-center">Revenue</th>
              <th class="py-3 px-5 text-right">Avg Order</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDE4]">
            <tr v-for="bp in branchPerformance" :key="bp.branch" class="hover:bg-[#FCFAF7] transition">
              <td class="py-3.5 px-5 font-medium text-[#382C24]">{{ bp.branch }}</td>
              <td class="py-3.5 px-4 text-center text-[#55463C]">{{ bp.orders }}</td>
              <td class="py-3.5 px-4 text-center text-[#55463C]">{{ bp.units }}</td>
              <td class="py-3.5 px-4 text-center font-bold text-[#241710]">ETB {{ Number(bp.revenue).toLocaleString() }}</td>
              <td class="py-3.5 px-5 text-right text-[#7A6B60] font-semibold">ETB {{ Number(bp.avg_order).toLocaleString() }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api';

const activeTab = ref('Branch performance');
const branches = ref([]);
const categories = ref([]);

const filters = ref({
  branch_id: 'all',
  category_id: 'all',
  from_date: new Date().toISOString().split('T')[0],
  to_date: new Date().toISOString().split('T')[0],
});

const metrics = ref({
  revenue: 48200,
  revenue_trend: '+12% vs yesterday',
  orders: 64,
  orders_sub: '9 pending',
  avg_order: 753,
  avg_order_trend: '+3%',
  units_sold: 412,
  units_sub: 'Cakes 96 · Bread 316',
});

const revenueByBranch = ref([
  { branch: 'Bole', revenue: 21300 },
  { branch: 'Megenagna', revenue: 11900 },
  { branch: 'Main Branch', revenue: 9400 },
  { branch: 'Piassa', revenue: 5600 },
]);

const topProducts = ref([
  { product: 'White Bread', units: 210, revenue: 9450 },
  { product: 'Chocolate Cake', units: 58, revenue: 49300 },
  { product: 'Croissant', units: 96, revenue: 5760 },
  { product: 'Vanilla Cake', units: 31, revenue: 24180 },
]);

const branchPerformance = ref([
  { branch: 'Bole', orders: 27, units: 160, revenue: 21300, avg_order: 789 },
  { branch: 'Megenagna', orders: 19, units: 118, revenue: 11900, avg_order: 626 },
  { branch: 'Main Branch', orders: 8, units: 74, revenue: 9400, avg_order: 1175 },
  { branch: 'Piassa', orders: 10, units: 60, revenue: 5600, avg_order: 560 },
]);

const maxBranchRevenue = computed(() => {
  const max = Math.max(...revenueByBranch.value.map(b => b.revenue));
  return max > 0 ? max : 21300;
});

const loadReport = async () => {
  try {
    const res = await api.get('/admin/reports/summary', {
      params: filters.value,
    });
    metrics.value = res.data.metrics;
    revenueByBranch.value = res.data.revenue_by_branch;
    topProducts.value = res.data.top_products;
    branchPerformance.value = res.data.branch_performance;
  } catch (err) {
    console.error('Failed to load reports summary', err);
  }
};

const exportCSV = () => {
  const headers = ['Branch', 'Orders', 'Units', 'Revenue', 'Avg Order'];
  const rows = branchPerformance.value.map(b => [
    `"${b.branch}"`,
    b.orders,
    b.units,
    b.revenue,
    b.avg_order
  ]);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `branch_performance_report.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

const exportPDF = () => {
  window.print();
};

onMounted(async () => {
  try {
    const [bRes, cRes] = await Promise.all([
      api.get('/branches'),
      api.get('/categories'),
    ]);
    branches.value = bRes.data;
    categories.value = cRes.data;
  } catch {
    // The API interceptor displays the request error.
  }
  loadReport();
});
</script>
