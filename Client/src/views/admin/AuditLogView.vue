<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Audit log</h2>
      <div class="flex items-center space-x-3">
        <span class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-[#EAE2D8] text-[#55463C] border border-[#D5C7B8]">
          Read only
        </span>
        <button
          @click="exportCSV"
          class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
        >
          Export CSV
        </button>
      </div>
    </div>

    <!-- Filters Row -->
    <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 bg-white p-3.5 border border-[#E9E0D4] rounded-xl shadow-sm">
      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">User</label>
        <select
          v-model="filters.user_id"
          @change="loadLogs"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">All users</option>
          <option v-for="u in usersList" :key="u.user_id" :value="u.user_id">
            {{ u.full_name }} ({{ u.role }})
          </option>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">Action</label>
        <select
          v-model="filters.action"
          @change="loadLogs"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">All actions</option>
          <option v-for="act in actionsList" :key="act" :value="act">
            {{ act }}
          </option>
        </select>
      </div>

      <div>
        <label class="block text-[11px] font-semibold text-[#8C7D73] uppercase mb-1">Branch</label>
        <select
          v-model="filters.branch"
          @change="loadLogs"
          class="w-full bg-[#FDFBF7] border border-[#DDD2C5] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">All branches</option>
          <option v-for="b in branchesList" :key="b.branch_id" :value="b.branch_name">
            {{ b.branch_name }}
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

    <!-- Audit Logs Table -->
    <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
            <tr>
              <th class="py-3 px-5">Time</th>
              <th class="py-3 px-4">User</th>
              <th class="py-3 px-4">Action</th>
              <th class="py-3 px-4">Record</th>
              <th class="py-3 px-4">Old value</th>
              <th class="py-3 px-5">New value</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDE4]">
            <tr v-for="log in logs" :key="log.log_id" class="hover:bg-[#FCFAF7] transition text-xs">
              <td class="py-3.5 px-5 font-mono text-[#7A6B60]">{{ log.time }}</td>
              <td class="py-3.5 px-4 font-medium text-[#382C24]">{{ log.user }}</td>
              <td class="py-3.5 px-4 text-[#4A3B32] font-semibold">{{ log.action }}</td>
              <td class="py-3.5 px-4 font-mono text-[#5A4B41]">{{ log.record }}</td>
              <td class="py-3.5 px-4 text-[#7A6B60]">{{ log.old_value }}</td>
              <td class="py-3.5 px-5 font-medium text-[#241710]">{{ log.new_value }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="p-4 bg-[#FCFAF7] border-t border-[#EFE8DF] text-xs text-[#8A7B71]">
        Audit records cannot be edited or deleted from the application. Only the Super Admin can view this page.
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const logs = ref([]);
const usersList = ref([]);
const actionsList = ref([]);
const branchesList = ref([]);

const filters = ref({
  user_id: 'all',
  action: 'all',
  branch: 'all',
  from_date: new Date().toISOString().split('T')[0],
  to_date: new Date().toISOString().split('T')[0],
});

const loadLogs = async () => {
  try {
    const res = await api.get('/admin/audit-logs', {
      params: filters.value,
    });
    logs.value = res.data.logs;
    usersList.value = res.data.users;
    actionsList.value = res.data.actions;
  } catch (err) {
    console.error('Failed to load audit logs', err);
  }
};

const exportCSV = () => {
  const headers = ['Time', 'User', 'Action', 'Record', 'Old Value', 'New Value'];
  const rows = logs.value.map(l => [
    `"${l.time}"`,
    `"${l.user}"`,
    `"${l.action}"`,
    `"${l.record}"`,
    `"${l.old_value}"`,
    `"${l.new_value}"`
  ]);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `audit_logs.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

onMounted(async () => {
  try {
    const bRes = await api.get('/branches');
    branchesList.value = bRes.data;
  } catch {
    // The API interceptor displays the request error.
  }
  loadLogs();
});
</script>
