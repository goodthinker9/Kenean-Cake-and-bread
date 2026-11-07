<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Payments</h2>
      <button
        @click="exportCSV"
        class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
      >
        Export CSV
      </button>
    </div>

    <!-- 4 Summary Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Online revenue today</div>
        <div class="text-2xl sm:text-3xl font-bold text-[#241710]">ETB {{ Number(summary.online_revenue_today || 41300).toLocaleString() }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Chapa and Telebirr</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Pay on pickup/delivery</div>
        <div class="text-2xl sm:text-3xl font-bold text-[#241710]">ETB {{ Number(summary.pay_on_pickup_delivery || 6900).toLocaleString() }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">To be collected</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Failed</div>
        <div class="text-2xl sm:text-3xl font-bold text-[#241710]">{{ summary.failed_today || 3 }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Today</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-xl p-5 shadow-sm">
        <div class="text-xs font-medium text-[#7D6E64] mb-1">Refunds</div>
        <div class="text-2xl sm:text-3xl font-bold text-[#241710]">ETB {{ Number(summary.refunds_this_week || 850).toLocaleString() }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">1 refund this week</div>
      </div>
    </div>

    <!-- Main Content: Transactions Table + Refund Form -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Transactions Table (Col span 2) -->
      <div class="lg:col-span-2 bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-[#EFE8DF]">
          <h3 class="font-semibold text-base text-[#2A1D15]">Transactions</h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
              <tr>
                <th class="py-3 px-5">Transaction Ref</th>
                <th class="py-3 px-4">Order</th>
                <th class="py-3 px-4">Branch</th>
                <th class="py-3 px-4">Method</th>
                <th class="py-3 px-4">Amount</th>
                <th class="py-3 px-5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr
                v-for="p in payments"
                :key="p.payment_id"
                @click="populateRefund(p)"
                class="hover:bg-[#FCFAF7] transition cursor-pointer"
              >
                <td class="py-3.5 px-5 font-mono text-xs text-[#5A4A3E]">{{ p.transaction_ref }}</td>
                <td class="py-3.5 px-4 font-mono text-xs font-semibold text-[#382C24]">{{ p.order_number }}</td>
                <td class="py-3.5 px-4 text-xs text-[#4A3B32] font-medium">{{ p.branch_name }}</td>
                <td class="py-3.5 px-4 text-xs text-[#6A5A50]">{{ p.method }}</td>
                <td class="py-3.5 px-4 text-xs font-semibold text-[#241710]">ETB {{ Number(p.amount).toLocaleString() }}</td>
                <td class="py-3.5 px-5 text-center">
                  <span
                    class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full"
                    :class="{
                      'bg-[#EAF5EA] text-[#2E7D32] border border-[#C8E6C9]': p.status === 'Paid',
                      'bg-[#FFF8E6] text-[#A67503] border border-[#FEE39E]': p.status === 'Pending',
                      'bg-[#FDEAEA] text-[#C62828] border border-[#FFCDD2]': p.status === 'Failed',
                      'bg-[#F3EFE9] text-[#7A6B60] border border-[#DDD3C7]': p.status === 'Refunded'
                    }"
                  >
                    {{ p.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-4 bg-[#FCFAF7] border-t border-[#EFE8DF] text-xs text-[#8A7B71]">
          Orders are confirmed only on a signed gateway webhook, not on the browser redirect. Duplicate callbacks are ignored by transaction reference.
        </div>
      </div>

      <!-- Right Card: Refund Form -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">Refund</h3>

        <form @submit.prevent="submitRefund" class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Order</label>
            <input
              v-model="refundForm.order_number"
              type="text"
              placeholder="e.g. ORD-1007"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-sm text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Amount (ETB)</label>
            <input
              v-model.number="refundForm.amount"
              type="number"
              placeholder="850"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-sm text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Reason</label>
            <textarea
              v-model="refundForm.reason"
              rows="3"
              placeholder="Branch could not fulfil: damaged batch"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-sm text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
              required
            ></textarea>
          </div>

          <div class="p-3 bg-[#FCFAF7] border border-[#EFE8DF] rounded-lg text-xs text-[#7A6B60]">
            Refund goes to the original payment method and is logged.
          </div>

          <button
            type="submit"
            :disabled="refunding"
            class="w-full py-2.5 px-4 bg-[#BA5420] hover:bg-[#9E4314] text-white font-medium text-sm rounded-lg transition shadow-sm disabled:opacity-50"
          >
            {{ refunding ? 'Processing...' : 'Issue refund' }}
          </button>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';
import { toast } from '../../services/feedback';

const payments = ref([]);
const summary = ref({
  online_revenue_today: 41300,
  pay_on_pickup_delivery: 6900,
  failed_today: 3,
  refunds_this_week: 850,
});

const refundForm = ref({
  order_number: 'ORD-1007',
  amount: 850,
  reason: 'Branch could not fulfil: damaged batch',
});

const refunding = ref(false);

const loadPayments = async () => {
  try {
    const res = await api.get('/admin/payments');
    payments.value = res.data.payments;
    summary.value = res.data.summary;
  } catch (err) {
    console.error('Failed to load payments', err);
  }
};

const populateRefund = (p) => {
  if (p.order_number && p.order_number !== '—') {
    refundForm.value.order_number = p.order_number;
    refundForm.value.amount = p.amount;
  }
};

const submitRefund = async () => {
  refunding.value = true;
  try {
    await api.post('/admin/payments/refund', refundForm.value);
    toast.success(`Refund issued successfully for ${refundForm.value.order_number} (ETB ${refundForm.value.amount.toLocaleString()})`);
    await loadPayments();
  } catch {
    // The API interceptor displays the request error.
  } finally {
    refunding.value = false;
  }
};

const exportCSV = () => {
  const headers = ['Transaction Ref', 'Order', 'Branch', 'Method', 'Amount', 'Status'];
  const rows = payments.value.map(p => [
    `"${p.transaction_ref}"`,
    `"${p.order_number}"`,
    `"${p.branch_name}"`,
    `"${p.method}"`,
    p.amount,
    `"${p.status}"`
  ]);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `payments.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

onMounted(() => {
  loadPayments();
});
</script>
