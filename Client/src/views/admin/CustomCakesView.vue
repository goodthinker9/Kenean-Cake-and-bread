<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header with Branch Selector -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Custom cake requests</h2>
      <select
        v-model="selectedBranch"
        @change="loadRequests"
        class="bg-white border border-[#D8CDC0] rounded-lg px-4 py-2 text-xs font-semibold text-[#4A3B32] shadow-sm focus:ring-1 focus:ring-[#BA5420]"
      >
        <option value="all">All branches</option>
        <option v-for="b in branches" :key="b.branch_id" :value="b.branch_id">
          {{ b.branch_name }}
        </option>
      </select>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Submitted</div>
        <div class="text-3xl font-bold text-[#241710]">{{ summary.submitted || 3 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Waiting for a quote</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Quoted</div>
        <div class="text-3xl font-bold text-[#241710]">{{ summary.quoted || 2 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Waiting for customer</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Confirmed</div>
        <div class="text-3xl font-bold text-[#241710]">{{ summary.confirmed || 4 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Paid deposit or full</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Ready this week</div>
        <div class="text-3xl font-bold text-[#241710]">{{ summary.ready_this_week || 2 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Pickup or delivery</div>
      </div>
    </div>

    <!-- Main Content Grid: Requests Table + Quote Side Panel -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Requests Table (Col span 2) -->
      <div class="lg:col-span-2 bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-[#EFE8DF]">
          <h3 class="font-semibold text-base text-[#2A1D15]">Requests</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
              <tr>
                <th class="py-3 px-5">Request</th>
                <th class="py-3 px-4">Branch</th>
                <th class="py-3 px-4">Size · Flavor</th>
                <th class="py-3 px-4">Needed by</th>
                <th class="py-3 px-4">Quote</th>
                <th class="py-3 px-5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr
                v-for="req in requests"
                :key="req.request_id"
                @click="selectRequest(req)"
                class="hover:bg-[#FCFAF7] transition cursor-pointer"
                :class="selectedRequest?.request_id === req.request_id ? 'bg-[#FAF3EC]' : ''"
              >
                <td class="py-3.5 px-5 font-mono text-xs font-semibold text-[#382C24]">{{ req.request_number }}</td>
                <td class="py-3.5 px-4 text-xs text-[#4A3B32] font-medium">{{ req.branch?.branch_name }}</td>
                <td class="py-3.5 px-4 text-xs text-[#5A4B41]">{{ req.cake_size }} · {{ req.flavor }}</td>
                <td class="py-3.5 px-4 text-xs text-[#7A6B60]">{{ req.needed_by }}</td>
                <td class="py-3.5 px-4 text-xs font-semibold text-[#241710]">
                  {{ req.quoted_price ? `ETB ${Number(req.quoted_price).toLocaleString()}` : 'ETB —' }}
                </td>
                <td class="py-3.5 px-5 text-center">
                  <span
                    class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full"
                    :class="{
                      'bg-[#FFF8E6] text-[#A67503] border border-[#FEE39E]': req.status === 'Submitted' || req.status === 'Quoted',
                      'bg-[#EAF5EA] text-[#2E7D32] border border-[#C8E6C9]': req.status === 'Confirmed',
                      'bg-[#EBF3FB] text-[#1565C0] border border-[#BBDEFB]': req.status === 'Preparing' || req.status === 'Ready',
                      'bg-[#FDEAEA] text-[#C62828] border border-[#FFCDD2]': req.status === 'Rejected' || req.status === 'Cancelled'
                    }"
                  >
                    {{ req.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Right Side Panel: Quote & Details Form -->
      <div v-if="selectedRequest" class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <div>
          <h3 class="text-lg font-serif font-bold text-[#2A1D15]">
            {{ selectedRequest.request_number }} · {{ selectedRequest.branch?.branch_name }}
          </h3>
          <div class="text-xs text-[#6A5A50] space-y-1 mt-1.5">
            <div>{{ selectedRequest.cake_size }} · {{ selectedRequest.flavor }} · Pickup</div>
            <div>Message: <span class="font-medium text-[#241710]">{{ selectedRequest.message || '[MESSAGE]' }}</span></div>
            <div>Design notes: <span class="text-[#7A6B60]">{{ selectedRequest.design || '[DESIGN DESCRIPTION]' }}</span></div>
          </div>
        </div>

        <!-- Reference Images Gallery -->
        <div class="flex space-x-2 pt-1 overflow-x-auto pb-1">
          <img
            v-for="img in (selectedRequest.images && selectedRequest.images.length > 0 ? selectedRequest.images : sampleImages)"
            :key="img.image_url"
            :src="img.image_url"
            alt="Reference"
            class="w-16 h-16 object-cover rounded-lg border border-[#DACDC0] shadow-2xs shrink-0"
          />
        </div>

        <form @submit.prevent="submitQuote" class="space-y-3.5 pt-2">
          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Quoted price (ETB)</label>
            <input
              v-model.number="quoteForm.quoted_price"
              type="number"
              placeholder="e.g. 2400"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-sm text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Deposit required</label>
            <select
              v-model.number="quoteForm.deposit_percentage"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-sm text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option :value="30">30%</option>
              <option :value="50">50%</option>
              <option :value="100">100%</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Rejection reason (if rejecting)</label>
            <textarea
              v-model="rejectionReason"
              rows="2"
              placeholder="Fully booked or cannot fulfill..."
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
            ></textarea>
          </div>

          <div class="flex space-x-3 pt-2">
            <button
              type="submit"
              class="flex-1 py-2 px-3 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-semibold rounded-lg shadow-sm transition"
            >
              Send quote
            </button>
            <button
              type="button"
              @click="rejectRequest"
              class="px-4 py-2 bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#6A5A50] text-xs font-semibold rounded-lg transition"
            >
              Reject
            </button>
          </div>
        </form>

        <p class="text-[11px] text-[#8F7F74] leading-relaxed pt-2">
          Deposit is non-refundable once the branch starts work (assumption).
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const branches = ref([]);
const selectedBranch = ref('all');
const requests = ref([]);
const summary = ref({
  submitted: 3,
  quoted: 2,
  confirmed: 4,
  ready_this_week: 2,
});

const selectedRequest = ref(null);
const quoteForm = ref({
  quoted_price: 2400,
  deposit_percentage: 50,
});
const rejectionReason = ref('');

const sampleImages = [
  { image_url: 'https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=200&auto=format&fit=crop&q=80' },
  { image_url: 'https://images.unsplash.com/photo-1588195538326-c5b1e9f80a1b?w=200&auto=format&fit=crop&q=80' },
];

const loadRequests = async () => {
  try {
    const res = await api.get('/admin/custom-cakes', {
      params: { branch_id: selectedBranch.value }
    });
    requests.value = res.data.requests;
    summary.value = res.data.summary;

    if (!selectedRequest.value && requests.value.length > 0) {
      selectedRequest.value = requests.value[0];
    } else if (selectedRequest.value) {
      selectedRequest.value = requests.value.find((r) => r.request_id === selectedRequest.value.request_id) || requests.value[0];
    }

    if (selectedRequest.value?.quoted_price) {
      quoteForm.value.quoted_price = Number(selectedRequest.value.quoted_price);
    }
  } catch (err) {
    console.error('Failed to load custom cakes', err);
  }
};

const selectRequest = (req) => {
  selectedRequest.value = req;
  quoteForm.value.quoted_price = req.quoted_price ? Number(req.quoted_price) : 2400;
  quoteForm.value.deposit_percentage = req.deposit_required_percentage ? Number(req.deposit_required_percentage) : 50;
};

const submitQuote = async () => {
  if (!selectedRequest.value) return;
  try {
    await api.patch(`/admin/custom-cakes/${selectedRequest.value.request_id}/quote`, {
      quoted_price: quoteForm.value.quoted_price,
      deposit_required_percentage: quoteForm.value.deposit_percentage,
    });
    alert(`Quote sent to customer for ${selectedRequest.value.request_number}: ETB ${quoteForm.value.quoted_price.toLocaleString()}`);
    await loadRequests();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to submit quote');
  }
};

const rejectRequest = async () => {
  if (!selectedRequest.value) return;
  try {
    await api.patch(`/admin/custom-cakes/${selectedRequest.value.request_id}/status`, {
      status: 'Rejected',
      rejection_reason: rejectionReason.value || 'Cannot fulfill requested design on schedule.',
    });
    alert(`Request ${selectedRequest.value.request_number} rejected.`);
    await loadRequests();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to reject request');
  }
};

onMounted(async () => {
  try {
    const bRes = await api.get('/branches');
    branches.value = bRes.data;
  } catch (e) {}
  loadRequests();
});
</script>
