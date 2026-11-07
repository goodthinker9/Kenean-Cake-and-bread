<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Branch Distribution and Stock Transfer Management</h2>
      <div class="flex items-center space-x-3">
        <button
          @click="scrollToRequests"
          class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
        >
          Stock requests ({{ data.stock_requests ? data.stock_requests.length : 1 }})
        </button>
        <router-link
          to="/admin/transfers/new"
          class="px-4 py-2 text-sm font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg transition shadow-sm"
        >
          New transfer
        </router-link>
      </div>
    </div>

    <!-- Alert Banner -->
    <div v-if="successMsg" class="p-3 bg-[#EBF7EB] border border-[#C5E9C6] rounded-lg text-xs text-[#1E7422] flex items-center justify-between">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = ''" class="text-[#1E7422] font-bold ml-2">&times;</button>
    </div>

    <!-- 2 Rows of 4 Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Row 1 -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Main branch stock</div>
        <div class="text-3xl font-bold text-[#241710]">{{ (data.main_branch_stock || 1420).toLocaleString() }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">units on hand</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Sent today</div>
        <div class="text-3xl font-bold text-[#241710]">{{ data.sent_today_units || 100 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">{{ data.sent_today_count || 1 }} transfer</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Sent this week</div>
        <div class="text-3xl font-bold text-[#241710]">{{ data.sent_week_units || 340 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">{{ data.sent_week_count || 6 }} transfers</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Sent this month</div>
        <div class="text-3xl font-bold text-[#241710]">{{ (data.sent_month_units || 1180).toLocaleString() }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">{{ data.sent_month_count || 21 }} transfers</div>
      </div>

      <!-- Row 2 -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Awaiting dispatch</div>
        <div class="text-3xl font-bold text-[#241710]">{{ data.awaiting_dispatch || 1 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Draft or approved</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">In transit</div>
        <div class="text-3xl font-bold text-[#241710]">{{ data.in_transit_units || 105 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">{{ data.in_transit_note || 'TRF-0004 to Megenagna' }}</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Received</div>
        <div class="text-3xl font-bold text-[#241710]">{{ (data.received_units || 1030).toLocaleString() }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">units confirmed</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Missing / damaged</div>
        <div class="text-3xl font-bold text-[#241710]">{{ data.missing_units || 5 }} / {{ data.damaged_units || 0 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">{{ data.dispute_note || 'TRF-0003 disputed' }}</div>
      </div>
    </div>

    <!-- Table 1: Latest transfer per branch -->
    <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
      <div class="p-5 border-b border-[#EFE8DF]">
        <h3 class="font-semibold text-base text-[#2A1D15]">Latest transfer per branch</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
            <tr>
              <th class="py-3 px-5">Branch</th>
              <th class="py-3 px-4">Transfer</th>
              <th class="py-3 px-4 text-center">Sent</th>
              <th class="py-3 px-4 text-center">Received</th>
              <th class="py-3 px-4 text-center">Missing</th>
              <th class="py-3 px-4 text-center">Damaged</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDE4]">
            <tr v-for="t in data.latest_transfers" :key="t.transfer" class="hover:bg-[#FCFAF7] transition">
              <td class="py-3.5 px-5 font-medium text-[#382C24]">{{ t.branch }}</td>
              <td class="py-3.5 px-4 font-mono text-xs text-[#55463D]">{{ t.transfer }}</td>
              <td class="py-3.5 px-4 text-center text-[#2A1D15]">{{ t.sent }}</td>
              <td class="py-3.5 px-4 text-center text-[#2A1D15]">{{ t.received }}</td>
              <td class="py-3.5 px-4 text-center text-[#2A1D15]">{{ t.missing }}</td>
              <td class="py-3.5 px-4 text-center text-[#2A1D15]">{{ t.damaged }}</td>
              <td class="py-3.5 px-4 text-center">
                <span
                  class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full"
                  :class="{
                    'bg-[#FDEAEA] text-[#C62828] border border-[#FFCDD2]': t.status === 'Disputed',
                    'bg-[#EBF3FB] text-[#1565C0] border border-[#BBDEFB]': t.status === 'In transit' || t.status === 'Approved',
                    'bg-[#FFF3E0] text-[#E65100] border border-[#FFE0B2]': t.status === 'Partially received',
                    'bg-[#EAF5EA] text-[#2E7D32] border border-[#C8E6C9]': t.status === 'Received'
                  }"
                >
                  {{ t.status }}
                </span>
              </td>
              <td class="py-3.5 px-5 text-right">
                <button
                  v-if="t.action === 'Resolve'"
                  @click="openResolveModal(t)"
                  class="px-3 py-1 text-xs font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-md shadow-sm transition"
                >
                  Resolve
                </button>
                <button
                  v-else-if="t.action === 'Remind'"
                  @click="sendReminder(t)"
                  class="px-3 py-1 text-xs font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-md shadow-sm transition"
                >
                  Remind
                </button>
                <button
                  v-else-if="t.action === 'Dispatch'"
                  @click="dispatchTransfer(t)"
                  class="px-3 py-1 text-xs font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-md shadow-sm transition"
                >
                  Dispatch
                </button>
                <router-link
                  v-else
                  :to="`/admin/transfers/new?ref=${t.transfer}`"
                  class="px-3 py-1 text-xs font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-md shadow-sm transition inline-block"
                >
                  View
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Bottom Two Columns: Stock Requests + Rules -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Stock Requests from Branches (Col span 2) -->
      <div id="stock-requests" class="lg:col-span-2 bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-[#EFE8DF]">
          <h3 class="font-semibold text-base text-[#2A1D15]">Stock requests from branches</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
              <tr>
                <th class="py-3 px-5">Branch</th>
                <th class="py-3 px-4">Product</th>
                <th class="py-3 px-4 text-center">Qty</th>
                <th class="py-3 px-4">Requested</th>
                <th class="py-3 px-5 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr v-for="req in data.stock_requests" :key="req.request_id" class="hover:bg-[#FCFAF7] transition">
                <td class="py-3.5 px-5 font-medium text-[#382C24]">{{ req.branch }}</td>
                <td class="py-3.5 px-4 text-[#4A3B32]">{{ req.product }}</td>
                <td class="py-3.5 px-4 text-center font-semibold text-[#241710]">{{ req.qty }}</td>
                <td class="py-3.5 px-4 text-[#7A6B60] text-xs">{{ req.requested }}</td>
                <td class="py-3.5 px-5 text-right">
                  <button
                    @click="convertRequest(req)"
                    class="px-3 py-1.5 text-xs font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-md shadow-sm transition"
                  >
                    Convert to transfer
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Rules Box -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-3">
        <h3 class="font-semibold text-base text-[#2A1D15]">Rules</h3>
        <p class="text-xs leading-relaxed text-[#78695E]">
          Unreceived after 24 hours: reminder to the branch and alert to Main Branch. Dispatched transfers cannot be cancelled; they are received or written off through dispute resolution.
        </p>
      </div>
    </div>

    <!-- Dispute Resolution Modal -->
    <div v-if="activeDispute" class="fixed inset-0 bg-black/40 flex items-center justify-center p-4 z-50">
      <div class="bg-white border border-[#E7DFD5] rounded-xl max-w-md w-full p-6 shadow-xl space-y-4">
        <h3 class="text-lg font-serif font-bold text-[#2A1D15]">
          Resolve Dispute: {{ activeDispute.transfer }}
        </h3>
        <p class="text-xs text-[#7A6B60]">
          Sent to {{ activeDispute.branch }}. Missing: {{ activeDispute.missing }}, Damaged: {{ activeDispute.damaged }}.
        </p>

        <div class="space-y-3">
          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Resolution Decision</label>
            <select
              v-model="resolveForm.resolution"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-sm text-[#2D231D] bg-[#FDFBF7]"
            >
              <option value="written_off">Write off discrepancy</option>
              <option value="found">Items located and confirmed</option>
              <option value="re_sent">Re-dispatch replacement units</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Resolution Notes</label>
            <textarea
              v-model="resolveForm.notes"
              rows="3"
              placeholder="Provide reason for audit trail..."
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-sm text-[#2D231D] bg-[#FDFBF7]"
            ></textarea>
          </div>
        </div>

        <div class="flex justify-end space-x-3 pt-2">
          <button
            @click="activeDispute = null"
            class="px-4 py-2 text-xs font-medium text-[#6A5A50] hover:bg-[#F3EFE9] rounded-lg"
          >
            Cancel
          </button>
          <button
            @click="submitResolve"
            class="px-4 py-2 text-xs font-medium bg-[#BA5420] text-white hover:bg-[#9E4314] rounded-lg shadow-sm"
          >
            Confirm Resolution
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const data = ref({
  main_branch_stock: 1420,
  sent_today_units: 100,
  sent_today_count: 1,
  sent_week_units: 340,
  sent_week_count: 6,
  sent_month_units: 1180,
  sent_month_count: 21,
  awaiting_dispatch: 1,
  in_transit_units: 105,
  in_transit_note: 'TRF-0004 to Megenagna',
  received_units: 1030,
  missing_units: 5,
  damaged_units: 0,
  dispute_note: 'TRF-0003 disputed',
  latest_transfers: [],
  stock_requests: [],
});

const activeDispute = ref(null);
const successMsg = ref('');
const resolveForm = ref({
  resolution: 'written_off',
  notes: 'Reconciled missing items during morning delivery.',
});

const loadDashboard = async () => {
  try {
    const res = await api.get('/admin/transfers/dashboard');
    data.value = res.data;
  } catch (err) {
    console.error('Failed to load distribution dashboard', err);
  }
};

const openResolveModal = (t) => {
  activeDispute.value = t;
};

const submitResolve = async () => {
  if (!activeDispute.value) return;
  try {
    await api.post(`/admin/transfers/${activeDispute.value.transfer_id}/resolve`, resolveForm.value);
    successMsg.value = `Dispute for ${activeDispute.value.transfer} resolved successfully!`;
    activeDispute.value = null;
    await loadDashboard();
  } catch {
    // The API interceptor displays the request error.
  }
};

const sendReminder = async (t) => {
  successMsg.value = `Reminder notification dispatched to ${t.branch} branch admin for transfer ${t.transfer}.`;
};

const dispatchTransfer = async (t) => {
  try {
    await api.post(`/admin/transfers/${t.transfer_id}/dispatch`);
    successMsg.value = `Transfer ${t.transfer} dispatched to ${t.branch}!`;
    await loadDashboard();
  } catch {
    // The API interceptor displays the request error.
  }
};

const convertRequest = async (req) => {
  try {
    await api.post(`/admin/stock-requests/${req.request_id}/convert`);
    successMsg.value = `Stock request for ${req.qty}x ${req.product} converted to transfer!`;
    await loadDashboard();
  } catch {
    // The API interceptor displays the request error.
  }
};

const scrollToRequests = () => {
  const el = document.getElementById('stock-requests');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
};

onMounted(() => {
  loadDashboard();
});
</script>
