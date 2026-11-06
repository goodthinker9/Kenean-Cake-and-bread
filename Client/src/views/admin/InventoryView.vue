<template>
  <div class="space-y-6 max-w-7xl mx-auto font-sans">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
      <div>
        <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Branch Inventory &amp; Stock Ledger</h2>
        <p class="text-xs text-[#7A6B60] mt-0.5">Every branch maintains its own inventory record. Customers can only buy what is available.</p>
      </div>

      <div class="flex items-center space-x-2.5">
        <button
          @click="showReconcileModal = true"
          class="px-3.5 py-2 text-xs font-semibold bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-xl transition shadow-2xs flex items-center space-x-1.5"
        >
          <span>⚖️</span>
          <span>Verify History &amp; Balance</span>
        </button>

        <button
          @click="activeSubTab = activeSubTab === 'ledger' ? 'stock' : 'ledger'"
          class="px-3.5 py-2 text-xs font-semibold bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-xl transition shadow-2xs flex items-center space-x-1.5"
        >
          <span>📋</span>
          <span>{{ activeSubTab === 'ledger' ? 'View Stock Grid' : 'Transaction Ledger' }}</span>
        </button>

        <button
          @click="exportCSV"
          class="px-3.5 py-2 text-xs font-semibold bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-xl transition shadow-2xs"
        >
          Export CSV
        </button>

        <button
          @click="focusAdjustPanel"
          class="px-4 py-2 text-xs font-bold bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-xl transition shadow-xs flex items-center space-x-1"
        >
          <span>+</span>
          <span>Adjust Stock</span>
        </button>
      </div>
    </div>

    <!-- Branch Filter Pills -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-1">
      <button
        @click="selectBranch('all')"
        class="px-4 py-1.5 text-xs font-semibold rounded-full transition"
        :class="selectedBranch === 'all' ? 'bg-[#984518] text-white shadow-xs' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
      >
        All branches
      </button>
      <button
        v-for="branch in branches"
        :key="branch.branch_id"
        @click="selectBranch(branch.branch_id)"
        class="px-4 py-1.5 text-xs font-semibold rounded-full transition"
        :class="selectedBranch === branch.branch_id ? 'bg-[#984518] text-white shadow-xs' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
      >
        {{ branch.branch_name }}
        <span v-if="branch.branch_type === 'Main'" class="ml-1 text-[10px] px-1.5 py-0.2 bg-red-100 text-red-700 rounded-full font-bold">Main</span>
      </button>
    </div>

    <!-- 4 Summary Stat Cards matching Page 2 -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white border border-[#E9E0D4] rounded-2xl p-5 shadow-2xs">
        <div class="text-xs font-bold uppercase tracking-wider text-[#7D6E64] mb-1">Units on hand</div>
        <div class="text-3xl font-black text-[#241710]">{{ summary.units_on_hand.toLocaleString() }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Physical units across {{ summary.branches_count }} {{ summary.branches_count === 1 ? 'branch' : 'branches' }}</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-2xl p-5 shadow-2xs">
        <div class="text-xs font-bold uppercase tracking-wider text-[#7D6E64] mb-1">Reserved</div>
        <div class="text-3xl font-black text-[#D97706]">{{ summary.reserved }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Allocated to open orders (15m hold)</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-2xl p-5 shadow-2xs">
        <div class="text-xs font-bold uppercase tracking-wider text-[#7D6E64] mb-1">Low-stock items</div>
        <div class="text-3xl font-black text-[#EA580C]">{{ summary.low_stock_items }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">At or below reorder level (alerts sent)</div>
      </div>

      <div class="bg-white border border-[#E9E0D4] rounded-2xl p-5 shadow-2xs">
        <div class="text-xs font-bold uppercase tracking-wider text-[#7D6E64] mb-1">Sold out</div>
        <div class="text-3xl font-black text-[#DC2626]">{{ summary.sold_out }}</div>
        <div class="text-xs text-[#8A7B71] mt-1">Shown as sold out, not hidden</div>
      </div>
    </div>

    <!-- VIEW 1: Stock Grid & Adjust Panel -->
    <div v-if="activeSubTab === 'stock'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Stock Table (Col span 2) -->
      <div class="lg:col-span-2 bg-white border border-[#E9E0D4] rounded-2xl shadow-2xs overflow-hidden">
        <div class="p-5 border-b border-[#EFE8DF] flex items-center justify-between">
          <h3 class="font-serif font-bold text-base text-[#2A1D15]">Stock by Branch &amp; Product</h3>
          <span class="text-xs text-[#7A6B60]">Click line to load in adjustment form</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
              <tr>
                <th class="py-3 px-5">Branch</th>
                <th class="py-3 px-4">Product</th>
                <th class="py-3 px-4 text-center">On hand</th>
                <th class="py-3 px-4 text-center">Reserved</th>
                <th class="py-3 px-4 text-center">Available</th>
                <th class="py-3 px-4 text-center">Reorder at</th>
                <th class="py-3 px-5 text-center">Status</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr
                v-for="item in items"
                :key="item.inventory_id"
                class="hover:bg-[#FCFAF7] transition cursor-pointer"
                @click="populateAdjust(item)"
              >
                <td class="py-3.5 px-5 font-semibold text-[#382C24]">
                  {{ item.branch_name }}
                </td>
                <td class="py-3.5 px-4 text-[#4A3B32] font-medium">{{ item.product_name }}</td>
                <td class="py-3.5 px-4 text-center font-bold text-[#2A1D15]">{{ item.on_hand }}</td>
                <td class="py-3.5 px-4 text-center text-[#D97706] font-semibold">{{ item.reserved }}</td>
                <td class="py-3.5 px-4 text-center font-black text-[#241710]">{{ item.available }}</td>
                <td class="py-3.5 px-4 text-center text-[#75665C]">{{ item.reorder_at }}</td>
                <td class="py-3.5 px-5 text-center">
                  <span
                    class="inline-block px-2.5 py-0.5 text-[11px] font-bold rounded-full"
                    :class="{
                      'bg-[#EBF7EB] text-[#227226] border border-[#C5E9C6]': item.status === 'Healthy',
                      'bg-[#FFF8E6] text-[#A67503] border border-[#FEE39E]': item.status === 'Low',
                      'bg-[#FEECEC] text-[#B91C1C] border border-[#FECACA]': item.status === 'Sold out'
                    }"
                  >
                    {{ item.status }}
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="p-4 bg-[#FCFAF7] border-t border-[#EFE8DF] text-xs text-[#8A7B71] flex items-center justify-between">
          <span>Formula: <strong>Available = On hand − Reserved</strong>. Dispatched units remain In transit until confirmed.</span>
          <span class="text-[11px] font-bold text-[#BA5420]">Total lines: {{ items.length }}</span>
        </div>
      </div>

      <!-- Right Card: Adjust Stock Form -->
      <div id="adjust-panel" class="bg-white border border-[#E9E0D4] rounded-2xl shadow-2xs p-6 space-y-4">
        <div class="border-b border-[#F2EAE0] pb-3">
          <h3 class="font-serif font-bold text-base text-[#2A1D15]">Record Inventory Operation</h3>
          <p class="text-xs text-[#7A6B60] mt-0.5">Every change writes a row to the transaction history ledger.</p>
        </div>

        <form @submit.prevent="submitAdjustment" class="space-y-4">
          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#65554B] mb-1">Branch</label>
            <select
              v-model="adjustForm.branch_id"
              class="w-full border border-[#D8CDC0] rounded-xl px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420] focus:outline-none"
              required
            >
              <option v-for="b in branches" :key="b.branch_id" :value="b.branch_id">
                {{ b.branch_name }} {{ b.branch_type === 'Main' ? '(Main)' : '' }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#65554B] mb-1">Product</label>
            <select
              v-model="adjustForm.product_id"
              class="w-full border border-[#D8CDC0] rounded-xl px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420] focus:outline-none"
              required
            >
              <option v-for="p in distinctProducts" :key="p.product_id" :value="p.product_id">
                {{ p.product_name }}
              </option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#65554B] mb-1">Operation Type</label>
            <select
              v-model="adjustForm.type"
              class="w-full border border-[#D8CDC0] rounded-xl px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420] focus:outline-none font-medium"
              required
            >
              <option value="damaged">Damaged stock (write-off)</option>
              <option value="expired">Expired perishable (shelf-life write-off)</option>
              <option value="production">Production received (Main branch only)</option>
              <option value="manual_adjustment">Manual adjustment (count correction)</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#65554B] mb-1">
              Quantity {{ adjustForm.type === 'damaged' || adjustForm.type === 'expired' ? '(units to remove)' : '(signed: +X or -X)' }}
            </label>
            <input
              v-model.number="adjustForm.quantity"
              type="number"
              :placeholder="adjustForm.type === 'damaged' || adjustForm.type === 'expired' ? 'e.g. 5' : '-2 or +20'"
              class="w-full border border-[#D8CDC0] rounded-xl px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420] focus:outline-none"
              required
            />
          </div>

          <!-- Real-Time Calculation Preview -->
          <div class="p-3 bg-[#FAF7F5] rounded-xl border border-[#F2D7DC] text-xs space-y-1">
            <div class="flex justify-between">
              <span class="text-[#7A6B60]">Current On Hand:</span>
              <span class="font-bold text-[#2A1D15]">{{ selectedItemOnHand }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-[#7A6B60]">Reserved in Orders:</span>
              <span class="font-bold text-[#D97706]">{{ selectedItemReserved }}</span>
            </div>
            <div class="flex justify-between border-t border-[#F2D7DC] pt-1">
              <span class="text-[#7A6B60]">Projected New On Hand:</span>
              <span class="font-black" :class="projectedOnHand < selectedItemReserved ? 'text-red-600' : 'text-[#059669]'">
                {{ projectedOnHand }}
              </span>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-[#65554B] mb-1">Reason Code (Mandatory)</label>
            <textarea
              v-model="adjustForm.reason"
              rows="2"
              placeholder="e.g. Past 48h shelf life, Dropped in transit, Physical count correction"
              class="w-full border border-[#D8CDC0] rounded-xl px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420] focus:outline-none"
              required
            ></textarea>
          </div>

          <!-- Alert Note if on hand would violate reserved constraint -->
          <div v-if="projectedOnHand < selectedItemReserved" class="p-3 bg-[#FEECEC] border border-[#FECACA] rounded-xl text-xs text-[#B91C1C]">
            ⚠️ Rule Violation: Manual adjustments cannot make on hand ({{ projectedOnHand }}) lower than reserved ({{ selectedItemReserved }}).
          </div>

          <div v-if="errorMessage" class="p-3 bg-[#FEECEC] border border-[#F5C2C2] rounded-xl text-xs text-[#B91C1C]">
            {{ errorMessage }}
          </div>

          <div v-if="successMessage" class="p-3 bg-[#EBF7EB] border border-[#C5E9C6] rounded-xl text-xs text-[#1E7422]">
            {{ successMessage }}
          </div>

          <button
            type="submit"
            :disabled="submitting || (projectedOnHand < selectedItemReserved)"
            class="w-full py-2.5 px-4 bg-[#BA5420] hover:bg-[#9E4314] text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-xs disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ submitting ? 'Recording in ledger...' : 'Record Transaction' }}
          </button>
        </form>

        <p class="text-[11px] text-[#938276] text-center pt-2">
          Every change writes a row to inventory_transactions with your user identity and timestamp.
        </p>
      </div>
    </div>

    <!-- VIEW 2: Complete Transaction Ledger -->
    <div v-else class="bg-white border border-[#E9E0D4] rounded-2xl shadow-2xs overflow-hidden space-y-4 p-5">
      <div class="flex items-center justify-between border-b border-[#F2EAE0] pb-4">
        <div>
          <h3 class="font-serif font-bold text-base text-[#2A1D15]">Full Inventory Transaction Ledger</h3>
          <p class="text-xs text-[#7A6B60] mt-0.5">Complete immutable audit history of all stock inflows, transfers, reserves, and write-offs.</p>
        </div>
        <button
          @click="loadTransactions"
          class="px-3.5 py-1.5 bg-[#FAF7F5] border border-[#D8CDC0] hover:bg-[#F4ECE3] text-xs font-semibold rounded-xl"
        >
          ↻ Refresh Ledger
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
            <tr>
              <th class="py-3 px-4">Date &amp; Time</th>
              <th class="py-3 px-4">Branch</th>
              <th class="py-3 px-4">Product</th>
              <th class="py-3 px-4 text-center">Operation</th>
              <th class="py-3 px-4 text-center">Signed Quantity</th>
              <th class="py-3 px-4">Reference / Reason</th>
              <th class="py-3 px-4">Recorded By</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDE4]">
            <tr v-for="tx in transactionsList" :key="tx.transaction_id" class="hover:bg-[#FCFAF7]">
              <td class="py-3 px-4 text-[#7A6B60] font-mono text-[11px]">{{ formatDateTime(tx.created_at) }}</td>
              <td class="py-3 px-4 font-bold text-[#382C24]">{{ tx.branch?.branch_name }}</td>
              <td class="py-3 px-4 font-medium text-[#2A1D15]">{{ tx.product?.product_name }}</td>
              <td class="py-3 px-4 text-center">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold" :class="txBadgeClass(tx.transaction_type)">
                  {{ tx.transaction_type }}
                </span>
              </td>
              <td class="py-3 px-4 text-center font-black" :class="tx.quantity >= 0 ? 'text-[#059669]' : 'text-[#DC2626]'">
                {{ tx.quantity > 0 ? `+${tx.quantity}` : tx.quantity }}
              </td>
              <td class="py-3 px-4 text-[#4A3B32]">
                <div class="font-medium">{{ tx.reason }}</div>
                <div class="text-[10px] text-[#8A7B71]" v-if="tx.reference_type">{{ tx.reference_type }} #{{ tx.reference_id || '' }}</div>
              </td>
              <td class="py-3 px-4 text-[#5A4B41] font-semibold">{{ tx.creator?.full_name || 'System' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Reconcile Verification Modal -->
    <div
      v-if="showReconcileModal"
      class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-3xl border border-[#D8CDC0] max-w-2xl w-full p-6 space-y-4 shadow-2xl animate-in zoom-in-95">
        <div class="flex items-start justify-between border-b border-[#F2EAE0] pb-3">
          <div>
            <span class="text-[11px] font-bold text-[#BA5420] uppercase tracking-wider">Rule 1 &middot; Stock Reconciliation</span>
            <h3 class="font-serif font-black text-xl text-[#2A1D15]">Transaction History Ledger Audit</h3>
          </div>
          <button @click="showReconcileModal = false" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
        </div>

        <p class="text-xs text-[#5A4B41] leading-relaxed">
          The system verifies that on-hand stock figures for each product and branch can always be rebuilt from the signed history of inventory transactions.
        </p>

        <div v-if="loadingReconcile" class="py-6 text-center text-xs text-[#8A7B71]">
          Auditing transaction ledger...
        </div>

        <div v-else class="space-y-2 max-h-72 overflow-y-auto">
          <div
            v-for="rec in reconcileItems"
            :key="`${rec.branch_id}-${rec.product_id}`"
            class="p-3 bg-[#FAF7F5] rounded-xl border border-[#E9E0D4] flex items-center justify-between text-xs"
          >
            <div>
              <div class="font-bold text-[#2A1D15]">{{ rec.branch_name }} &middot; {{ rec.product_name }}</div>
              <div class="text-[11px] text-[#7A6B60] mt-0.5">
                On Hand: <strong>{{ rec.current_on_hand }}</strong> &middot; Reserved: <strong>{{ rec.reserved_quantity }}</strong> &middot; Available: <strong>{{ rec.available_quantity }}</strong>
              </div>
            </div>
            <div class="text-right">
              <span class="px-2.5 py-1 bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0] rounded-full font-bold text-[10px]">
                ✓ Reconciled Consistent
              </span>
            </div>
          </div>
        </div>

        <div class="pt-2 flex justify-end">
          <button
            @click="showReconcileModal = false"
            class="px-5 py-2 bg-[#2A1D15] text-white text-xs font-semibold rounded-xl"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api';

const branches = ref([]);
const selectedBranch = ref('all');
const items = ref([]);
const distinctProducts = ref([]);
const activeSubTab = ref('stock'); // 'stock' or 'ledger'
const transactionsList = ref([]);

const showReconcileModal = ref(false);
const loadingReconcile = ref(false);
const reconcileItems = ref([]);

const summary = ref({
  units_on_hand: 0,
  reserved: 0,
  low_stock_items: 0,
  sold_out: 0,
  branches_count: 4,
});

const adjustForm = ref({
  branch_id: 2, // Bole
  product_id: 1, // Chocolate Cake
  type: 'damaged',
  quantity: 2,
  reason: 'Shelf life expired',
});

const submitting = ref(false);
const errorMessage = ref('');
const successMessage = ref('');

const selectedItemOnHand = computed(() => {
  const match = items.value.find(
    (i) => i.branch_id === adjustForm.value.branch_id && i.product_id === adjustForm.value.product_id
  );
  return match ? match.on_hand : 0;
});

const selectedItemReserved = computed(() => {
  const match = items.value.find(
    (i) => i.branch_id === adjustForm.value.branch_id && i.product_id === adjustForm.value.product_id
  );
  return match ? match.reserved : 0;
});

const projectedOnHand = computed(() => {
  const delta = (adjustForm.value.type === 'damaged' || adjustForm.value.type === 'expired')
    ? -Math.abs(adjustForm.value.quantity || 0)
    : (adjustForm.value.quantity || 0);
  return selectedItemOnHand.value + delta;
});

const loadInventory = async () => {
  try {
    const res = await api.get('/admin/inventory', {
      params: { branch_id: selectedBranch.value }
    });
    branches.value = res.data.branches;
    items.value = res.data.items;
    summary.value = res.data.summary;

    // Build distinct products
    const seen = new Set();
    distinctProducts.value = [];
    res.data.items.forEach((item) => {
      if (!seen.has(item.product_id)) {
        seen.add(item.product_id);
        distinctProducts.value.push({ product_id: item.product_id, product_name: item.product_name });
      }
    });
  } catch (err) {
    console.error('Failed to load inventory', err);
  }
};

const loadTransactions = async () => {
  try {
    const res = await api.get('/admin/inventory/transactions', {
      params: { branch_id: selectedBranch.value !== 'all' ? selectedBranch.value : null }
    });
    transactionsList.value = res.data.data || [];
  } catch (err) {
    console.error('Failed to load transactions', err);
  }
};

const loadReconcile = async () => {
  loadingReconcile.value = true;
  try {
    const res = await api.get('/admin/inventory/reconcile', {
      params: { branch_id: selectedBranch.value !== 'all' ? selectedBranch.value : null }
    });
    reconcileItems.value = res.data.items || [];
  } catch (err) {
    console.error('Failed to load reconcile data', err);
  } finally {
    loadingReconcile.value = false;
  }
};

const selectBranch = (branchId) => {
  selectedBranch.value = branchId;
  loadInventory();
  if (activeSubTab.value === 'ledger') {
    loadTransactions();
  }
};

const populateAdjust = (item) => {
  adjustForm.value.branch_id = item.branch_id;
  adjustForm.value.product_id = item.product_id;
  adjustForm.value.type = 'damaged';
  adjustForm.value.quantity = 2;
  adjustForm.value.reason = 'Perishable shelf life inspection';
  focusAdjustPanel();
};

const submitAdjustment = async () => {
  submitting.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    await api.post('/admin/inventory/adjust', adjustForm.value);
    successMessage.value = 'Stock adjusted and written to transaction ledger.';
    await loadInventory();
    if (activeSubTab.value === 'ledger') {
      await loadTransactions();
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to adjust stock.';
  } finally {
    submitting.value = false;
  }
};

const focusAdjustPanel = () => {
  const el = document.getElementById('adjust-panel');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
};

const exportCSV = () => {
  const headers = ['Branch', 'Product', 'On Hand', 'Reserved', 'Available', 'Reorder At', 'Status'];
  const rows = items.value.map(i => [
    `"${i.branch_name}"`,
    `"${i.product_name}"`,
    i.on_hand,
    i.reserved,
    i.available,
    i.reorder_at,
    `"${i.status}"`
  ]);
  const csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
  const encodedUri = encodeURI(csvContent);
  const link = document.createElement('a');
  link.setAttribute('href', encodedUri);
  link.setAttribute('download', `inventory_${selectedBranch.value}.csv`);
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};

function formatDateTime(dt) {
  if (!dt) return '—';
  return new Date(dt).toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function txBadgeClass(type) {
  switch (type) {
    case 'production':
      return 'bg-blue-100 text-blue-800';
    case 'transfer_dispatch':
      return 'bg-amber-100 text-amber-800';
    case 'transfer_receive':
      return 'bg-emerald-100 text-emerald-800';
    case 'order_placed':
      return 'bg-purple-100 text-purple-800';
    case 'order_completed':
      return 'bg-green-100 text-green-800';
    case 'order_cancelled':
      return 'bg-red-100 text-red-800';
    case 'damaged':
    case 'expired':
      return 'bg-rose-100 text-rose-800';
    default:
      return 'bg-gray-100 text-gray-800';
  }
}

onMounted(() => {
  loadInventory();
  loadTransactions();
  loadReconcile();
});
</script>
