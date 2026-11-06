<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div class="flex items-center space-x-3">
        <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">New transfer</h2>
        <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-[#EAE2D8] text-[#55463C] border border-[#D5C7B8]">
          Draft
        </span>
      </div>
      <div class="flex items-center space-x-3">
        <button
          @click="saveTransfer('Draft')"
          :disabled="submitting"
          class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
        >
          Save draft
        </button>
        <button
          @click="saveTransfer('Approved')"
          :disabled="submitting"
          class="px-4 py-2 text-sm font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg transition shadow-sm"
        >
          Submit for approval
        </button>
      </div>
    </div>

    <!-- Alert / Messages -->
    <div v-if="successMsg" class="p-3 bg-[#EBF7EB] border border-[#C5E9C6] rounded-lg text-xs text-[#1E7422]">
      {{ successMsg }}
    </div>
    <div v-if="errorMsg" class="p-3 bg-[#FEECEC] border border-[#F5C2C2] rounded-lg text-xs text-[#B91C1C]">
      {{ errorMsg }}
    </div>

    <!-- Top Grid: 1. Route & 2. Products to send -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
      <!-- 1. Route Box -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">1. Route</h3>

        <div class="grid grid-cols-3 gap-3">
          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Transfer number</label>
            <input
              type="text"
              :value="transferNumber"
              readonly
              class="w-full bg-[#F7F2EA] border border-[#DCD0C2] rounded-lg px-3 py-2 text-xs font-mono text-[#5A4A3E]"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Source</label>
            <input
              type="text"
              value="Main Branch (fixed in v1)"
              readonly
              class="w-full bg-[#F7F2EA] border border-[#DCD0C2] rounded-lg px-3 py-2 text-xs font-medium text-[#5A4A3E]"
            />
          </div>
          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Destination</label>
            <select
              v-model="destinationBranchId"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs font-medium text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option v-for="b in regularBranches" :key="b.branch_id" :value="b.branch_id">
                {{ b.branch_name }} ({{ b.branch_code }})
              </option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-xs font-medium text-[#7D6E64] mb-1">Notes</label>
          <textarea
            v-model="transferNotes"
            rows="3"
            placeholder="Special delivery instructions, timing, etc."
            class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
          ></textarea>
        </div>
      </div>

      <!-- 2. Products to send Box -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">2. Products to send</h3>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="text-[#88786B] uppercase border-b border-[#EFE8DF] font-semibold">
              <tr>
                <th class="pb-2">Product</th>
                <th class="pb-2 text-center">Main on hand</th>
                <th class="pb-2 text-center w-24">Send</th>
                <th class="pb-2 text-center">Left at Main</th>
                <th class="pb-2 w-8"></th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr v-for="(p, index) in productsToSend" :key="index">
                <td class="py-2.5 font-medium text-[#382C24]">
                  <select
                    v-model="p.product_id"
                    @change="onProductChange(p)"
                    class="bg-[#FDFBF7] border border-[#DDD2C5] rounded px-2 py-1 text-xs"
                  >
                    <option v-for="prod in catalogProducts" :key="prod.product_id" :value="prod.product_id">
                      {{ prod.product_name }}
                    </option>
                  </select>
                </td>
                <td class="py-2.5 text-center text-[#55463C] font-semibold">{{ p.main_on_hand }}</td>
                <td class="py-2.5 text-center">
                  <input
                    type="number"
                    v-model.number="p.quantity_sent"
                    min="1"
                    :max="p.main_on_hand"
                    class="w-20 border border-[#D8CDC0] rounded px-2 py-1 text-center font-bold text-[#2A1D15] bg-[#FDFBF7]"
                  />
                </td>
                <td class="py-2.5 text-center font-medium" :class="(p.main_on_hand - p.quantity_sent) < 0 ? 'text-red-600 font-bold' : 'text-[#7A6B60]'">
                  {{ p.main_on_hand - p.quantity_sent }}
                </td>
                <td class="py-2.5 text-right">
                  <button
                    v-if="productsToSend.length > 1"
                    @click="removeProductRow(index)"
                    class="text-red-500 hover:text-red-700 text-sm font-bold"
                  >
                    ✕
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="flex items-center justify-between pt-2">
          <button
            @click="addProductRow"
            class="text-xs font-semibold text-[#BA5420] hover:text-[#9E4314] flex items-center space-x-1"
          >
            <span>+ Add product</span>
          </button>
          <div class="text-xs text-[#7A6B60] font-medium">
            Total units to send: <span class="font-bold text-[#241710]">{{ totalSendUnits }}</span>
          </div>
        </div>

        <p class="text-[11px] text-[#8F7F74] pt-1">
          Sent quantity cannot exceed Main Branch on hand at dispatch time.
        </p>
      </div>
    </div>

    <!-- Bottom Grid: 3. Receiving at destination & Workflow -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- 3. Receiving at destination (Branch 01 example) (Col span 2) -->
      <div class="lg:col-span-2 bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
          <h3 class="font-semibold text-base text-[#2A1D15]">
            3. Receiving at destination (Branch {{ destinationBranchName }} example)
          </h3>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="text-[#88786B] uppercase border-b border-[#EFE8DF] font-semibold">
              <tr>
                <th class="pb-2">Product</th>
                <th class="pb-2 text-center">Sent</th>
                <th class="pb-2 text-center w-20">Received</th>
                <th class="pb-2 text-center w-20">Damaged</th>
                <th class="pb-2 text-center w-20">Missing</th>
                <th class="pb-2 text-center">Check</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr v-for="(p, idx) in receivingExample" :key="idx">
                <td class="py-2.5 font-medium text-[#382C24]">{{ p.name }}</td>
                <td class="py-2.5 text-center text-[#55463C] font-semibold">{{ p.sent }}</td>
                <td class="py-2.5 text-center">
                  <input
                    type="number"
                    v-model.number="p.received"
                    class="w-16 border border-[#D8CDC0] rounded px-2 py-1 text-center font-bold text-[#2A1D15] bg-[#FDFBF7]"
                  />
                </td>
                <td class="py-2.5 text-center">
                  <input
                    type="number"
                    v-model.number="p.damaged"
                    class="w-16 border border-[#D8CDC0] rounded px-2 py-1 text-center font-bold text-[#2A1D15] bg-[#FDFBF7]"
                  />
                </td>
                <td class="py-2.5 text-center">
                  <input
                    type="number"
                    v-model.number="p.missing"
                    class="w-16 border border-[#D8CDC0] rounded px-2 py-1 text-center font-bold text-[#2A1D15] bg-[#FDFBF7]"
                  />
                </td>
                <td class="py-2.5 text-center">
                  <span
                    class="px-2 py-0.5 rounded text-[11px] font-mono font-medium"
                    :class="(p.received + p.damaged + p.missing) === p.sent ? 'bg-[#EAF5EA] text-[#2E7D32]' : 'bg-[#FDEAEA] text-[#C62828]'"
                  >
                    {{ p.received }} + {{ p.damaged }} + {{ p.missing }} = {{ p.received + p.damaged + p.missing }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div>
          <label class="block text-xs font-medium text-[#7D6E64] mb-1">
            Discrepancy reason (required when different)
          </label>
          <input
            v-model="discrepancyReason"
            type="text"
            placeholder="e.g. 5 units not in the delivery crate"
            class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
          />
        </div>

        <div class="flex items-center justify-between pt-2">
          <button
            @click="testReceiptConfirmation"
            class="px-4 py-2 bg-[#BA5420] text-white hover:bg-[#9E4314] text-xs font-medium rounded-lg shadow-sm transition"
          >
            Confirm receipt
          </button>
          <span class="text-[11px] text-[#8F7F74]">
            Received + damaged + missing must equal sent before saving.
          </span>
        </div>
      </div>

      <!-- Workflow Guide Card -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">Workflow</h3>

        <div class="space-y-2.5 text-xs text-[#55463C]">
          <div class="flex items-start space-x-2">
            <span class="font-bold text-[#BA5420]">1</span>
            <span><strong>Create</strong> (Main Manager)</span>
          </div>
          <div class="flex items-start space-x-2">
            <span class="font-bold text-[#BA5420]">2</span>
            <span><strong>Approve</strong> (Super Admin, if required)</span>
          </div>
          <div class="flex items-start space-x-2">
            <span class="font-bold text-[#BA5420]">3</span>
            <span><strong>Dispatch</strong>: stock moves to in transit</span>
          </div>
          <div class="flex items-start space-x-2">
            <span class="font-bold text-[#BA5420]">4</span>
            <span><strong>Receive</strong> and count at branch</span>
          </div>
          <div class="flex items-start space-x-2">
            <span class="font-bold text-[#BA5420]">5</span>
            <span><strong>Close</strong>: Received, Partially received or Disputed</span>
          </div>
        </div>

        <div class="p-3 bg-[#FCFAF7] border border-[#EBE2D7] rounded-lg text-xs text-[#7A6B60] leading-relaxed">
          Total units: <strong class="text-[#241710]">{{ totalSendUnits }}</strong>. Approval needed:
          <strong :class="totalSendUnits > 200 ? 'text-[#C62828]' : 'text-[#2E7D32]'">
            {{ totalSendUnits > 200 ? 'yes (exceeds threshold of 200)' : 'no (under threshold of 200)' }}
          </strong>.
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../../api';

const router = useRouter();

const transferNumber = ref('TRF-2026-0006 (auto)');
const destinationBranchId = ref(3); // Megenagna
const transferNotes = ref('');
const regularBranches = ref([]);
const catalogProducts = ref([]);

const productsToSend = ref([
  { product_id: 1, name: 'Chocolate Cake', main_on_hand: 60, quantity_sent: 20 },
  { product_id: 2, name: 'White Bread', main_on_hand: 220, quantity_sent: 100 },
  { product_id: 4, name: 'Croissant', main_on_hand: 90, quantity_sent: 40 },
]);

const receivingExample = ref([
  { name: 'White Bread', sent: 100, received: 95, damaged: 0, missing: 5 }
]);
const discrepancyReason = ref('5 units not in the delivery crate');

const submitting = ref(false);
const successMsg = ref('');
const errorMsg = ref('');

const totalSendUnits = computed(() => {
  return productsToSend.value.reduce((acc, p) => acc + (p.quantity_sent || 0), 0);
});

const destinationBranchName = computed(() => {
  const b = regularBranches.value.find((br) => br.branch_id === destinationBranchId.value);
  return b ? b.branch_name : 'Megenagna';
});

const loadInitialData = async () => {
  try {
    const branchesRes = await api.get('/branches');
    regularBranches.value = branchesRes.data.filter((b) => b.branch_type !== 'Main');
    if (regularBranches.value.length > 0 && !destinationBranchId.value) {
      destinationBranchId.value = regularBranches.value[0].branch_id;
    }

    const mainBranch = branchesRes.data.find((b) => b.branch_type === 'Main') || branchesRes.data[0];
    const productsRes = await api.get(`/branches/${mainBranch.branch_id}/products`);
    catalogProducts.value = productsRes.data;

    // Sync on_hand for productsToSend
    productsToSend.value.forEach((p) => {
      const match = catalogProducts.value.find((cp) => cp.product_id === p.product_id);
      if (match) p.main_on_hand = match.on_hand;
    });
  } catch (err) {
    console.error('Failed to load transfer initial data', err);
  }
};

const onProductChange = (p) => {
  const match = catalogProducts.value.find((cp) => cp.product_id === p.product_id);
  if (match) {
    p.name = match.product_name;
    p.main_on_hand = match.on_hand;
    p.quantity_sent = Math.min(10, match.on_hand);
  }
};

const addProductRow = () => {
  const first = catalogProducts.value[0] || { product_id: 1, product_name: 'Product', on_hand: 50 };
  productsToSend.value.push({
    product_id: first.product_id,
    name: first.product_name,
    main_on_hand: first.on_hand,
    quantity_sent: 10,
  });
};

const removeProductRow = (index) => {
  productsToSend.value.splice(index, 1);
};

const saveTransfer = async (status) => {
  submitting.value = true;
  successMsg.value = '';
  errorMsg.value = '';

  try {
    const payload = {
      destination_branch_id: destinationBranchId.value,
      notes: transferNotes.value,
      items: productsToSend.value.map((p) => ({
        product_id: p.product_id,
        quantity_sent: p.quantity_sent,
      })),
      status: status,
    };

    const res = await api.post('/admin/transfers', payload);
    successMsg.value = `Stock transfer ${res.data.transfer_number} created with status: ${res.data.status}!`;
    setTimeout(() => {
      router.push('/admin/distribution');
    }, 1200);
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to create stock transfer';
  } finally {
    submitting.value = false;
  }
};

const testReceiptConfirmation = () => {
  const line = receivingExample.value[0];
  if (line.received + line.damaged + line.missing !== line.sent) {
    alert('Received + damaged + missing must equal sent (100).');
    return;
  }
  alert(`Receipt verified: ${line.received} units added to destination on hand. Discrepancy logged: ${line.missing} missing.`);
};

onMounted(() => {
  loadInitialData();
});
</script>
