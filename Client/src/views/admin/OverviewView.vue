<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Overview</h2>
      <div class="flex items-center space-x-3">
        <select
          v-model="selectedBranch"
          class="bg-white border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs font-semibold text-[#4A3B32] shadow-sm"
        >
          <option value="all">All branches</option>
          <option v-for="b in branches" :key="b.branch_id" :value="b.branch_id">
            {{ b.branch_name }}
          </option>
        </select>
        <select
          v-model="selectedDate"
          class="bg-white border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs font-semibold text-[#4A3B32] shadow-sm"
        >
          <option value="today">Today</option>
          <option value="this_week">This week</option>
        </select>
        <router-link
          to="/admin/transfers/new"
          class="px-4 py-2 text-sm font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg transition shadow-sm"
        >
          New transfer
        </router-link>
      </div>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Revenue today</div>
        <div class="text-3xl font-bold text-[#241710]">ETB {{ Number(data.revenue_today).toLocaleString() }}</div>
        <div class="text-xs text-[#2E7D32] mt-1">+12% vs yesterday</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Orders today</div>
        <div class="text-3xl font-bold text-[#241710]">{{ data.orders_today }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">{{ data.pending_orders }} pending</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Active branches</div>
        <div class="text-3xl font-bold text-[#241710]">{{ data.active_branches }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Main plus 3 regular</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Units in transit</div>
        <div class="text-3xl font-bold text-[#241710]">{{ data.units_in_transit }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">1 transfer to Megenagna</div>
      </div>
    </div>

    <!-- Middle Grid: Sales by branch, this week (Stacked Bar Chart) + Needs attention -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
      <!-- Stacked Bar Chart Box -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-semibold text-base text-[#2A1D15]">Sales by branch, this week</h3>
          <div class="flex items-center space-x-4 text-xs">
            <span class="flex items-center space-x-1.5">
              <span class="w-3 h-3 rounded bg-[#BA5420]"></span>
              <span class="text-[#65554B]">Cakes</span>
            </span>
            <span class="flex items-center space-x-1.5">
              <span class="w-3 h-3 rounded bg-[#E4A373]"></span>
              <span class="text-[#65554B]">Bread and pastry</span>
            </span>
          </div>
        </div>

        <!-- Vertical Stacked Bars -->
        <div class="h-64 flex items-end justify-between px-6 pt-6 border-b border-[#EFE8DF]">
          <div
            v-for="sb in data.sales_by_branch"
            :key="sb.branch"
            class="flex flex-col items-center space-y-2 w-16"
          >
            <div class="w-12 rounded-t-md overflow-hidden flex flex-col justify-end bg-transparent h-48">
              <!-- Cakes (top block) -->
              <div
                class="w-full bg-[#BA5420] transition-all duration-500"
                :style="{ height: `${Math.round((sb.cakes / 22000) * 100)}%` }"
              ></div>
              <!-- Bread (bottom block) -->
              <div
                class="w-full bg-[#E4A373] transition-all duration-500"
                :style="{ height: `${Math.round((sb.bread / 22000) * 100)}%` }"
              ></div>
            </div>
            <span class="text-xs font-semibold text-[#5A4A3E]">{{ sb.branch }}</span>
          </div>
        </div>

        <p class="text-[11px] text-[#8F7F74] pt-1">
          Sample data. Real values come from branch sales.
        </p>
      </div>

      <!-- Needs Attention Feed -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-3.5">
        <h3 class="font-semibold text-base text-[#2A1D15]">Needs attention</h3>

        <div class="space-y-2.5">
          <div
            v-for="(item, idx) in data.needs_attention"
            :key="idx"
            class="p-3.5 rounded-lg border text-xs font-medium leading-relaxed transition"
            :class="{
              'bg-[#FDEAEA] text-[#C62828] border-[#FFCDD2]': item.type === 'disputed',
              'bg-[#FFF8E6] text-[#A67503] border-[#FEE39E]': item.type === 'in_transit' || item.type === 'low_stock',
              'bg-[#EBF3FB] text-[#1565C0] border-[#BBDEFB]': item.type === 'request'
            }"
          >
            {{ item.text }}
          </div>
        </div>
      </div>
    </div>

    <!-- Branches Today Table -->
    <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
      <div class="p-5 border-b border-[#EFE8DF] flex items-center justify-between">
        <h3 class="font-semibold text-base text-[#2A1D15]">Branches today</h3>
        <router-link
          to="/admin/branches"
          class="text-xs font-semibold text-[#BA5420] hover:text-[#9E4314]"
        >
          Manage branches
        </router-link>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
            <tr>
              <th class="py-3 px-5">Branch</th>
              <th class="py-3 px-4 text-center">Orders</th>
              <th class="py-3 px-4 text-center">Revenue</th>
              <th class="py-3 px-4 text-center">Stock Health</th>
              <th class="py-3 px-5 text-right">Open Transfers</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDE4]">
            <tr v-for="bt in data.branches_today" :key="bt.branch_id" class="hover:bg-[#FCFAF7] transition">
              <td class="py-3.5 px-5 font-medium text-[#382C24]">{{ bt.branch }}</td>
              <td class="py-3.5 px-4 text-center text-[#55463C]">{{ bt.orders }}</td>
              <td class="py-3.5 px-4 text-center font-bold text-[#241710]">ETB {{ Number(bt.revenue).toLocaleString() }}</td>
              <td class="py-3.5 px-4 text-center">
                <span
                  class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-[#EAF5EA] text-[#2E7D32] border border-[#C8E6C9]': bt.stock_health === 'Healthy',
                    'bg-[#FFF8E6] text-[#A67503] border border-[#FEE39E]': bt.stock_health.includes('low')
                  }"
                >
                  {{ bt.stock_health }}
                </span>
              </td>
              <td class="py-3.5 px-5 text-right text-xs font-medium text-[#65554B]">
                {{ bt.open_transfers }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const branches = ref([]);
const selectedBranch = ref('all');
const selectedDate = ref('today');

const data = ref({
  revenue_today: 48200,
  orders_today: 64,
  pending_orders: 9,
  active_branches: 4,
  units_in_transit: 105,
  sales_by_branch: [
    { branch: 'Main', cakes: 3800, bread: 5600 },
    { branch: 'Bole', cakes: 14200, bread: 7100 },
    { branch: 'Megenagna', cakes: 6500, bread: 5400 },
    { branch: 'Piassa', cakes: 3200, bread: 2400 },
  ],
  needs_attention: [],
  branches_today: [],
});

const loadOverview = async () => {
  try {
    const [ovRes, brRes] = await Promise.all([
      api.get('/admin/overview'),
      api.get('/branches'),
    ]);
    data.value = ovRes.data;
    branches.value = brRes.data;
  } catch (err) {
    console.error('Failed to load overview', err);
  }
};

onMounted(() => {
  loadOverview();
});
</script>
