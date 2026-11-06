<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Branch management</h2>
      <button
        @click="openAddBranchModal"
        class="px-4 py-2 text-sm font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg transition shadow-sm flex items-center space-x-1.5"
      >
        <span>+ Add branch</span>
      </button>
    </div>

    <!-- Alert / Message -->
    <div v-if="successMsg" class="p-3 bg-[#EBF7EB] border border-[#C5E9C6] rounded-lg text-xs text-[#1E7422] flex items-center justify-between">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = ''" class="text-[#1E7422] font-bold ml-2">&times;</button>
    </div>
    <div v-if="errorMsg" class="p-3 bg-[#FEECEC] border border-[#F5C2C2] rounded-lg text-xs text-[#B91C1C] flex items-center justify-between">
      <span>{{ errorMsg }}</span>
      <button @click="errorMsg = ''" class="text-[#B91C1C] font-bold ml-2">&times;</button>
    </div>

    <!-- Branches List Table -->
    <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
            <tr>
              <th class="py-3 px-5">Code</th>
              <th class="py-3 px-4">Branch</th>
              <th class="py-3 px-4">Type</th>
              <th class="py-3 px-4">Manager</th>
              <th class="py-3 px-4">City</th>
              <th class="py-3 px-4">Phone</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDE4]">
            <tr
              v-for="b in branches"
              :key="b.branch_id"
              class="hover:bg-[#FCFAF7] transition cursor-pointer"
              :class="editingBranch?.branch_id === b.branch_id ? 'bg-[#FAF3EC]' : ''"
              @click="selectBranchForEdit(b)"
            >
              <td class="py-3.5 px-5 font-mono text-xs font-semibold text-[#55463C]">{{ b.branch_code }}</td>
              <td class="py-3.5 px-4 font-bold text-[#2A1D15]">{{ b.branch_name }}</td>
              <td class="py-3.5 px-4">
                <span
                  class="px-2 py-0.5 text-xs font-semibold rounded-full"
                  :class="b.branch_type === 'Main' ? 'bg-[#FFF3E0] text-[#E65100]' : 'bg-[#EAE2D8] text-[#55463C]'"
                >
                  {{ b.branch_type }}
                </span>
              </td>
              <td class="py-3.5 px-4 text-xs text-[#5A4B41]">{{ b.manager?.full_name || 'Assigned' }}</td>
              <td class="py-3.5 px-4 text-xs text-[#5A4B41]">{{ b.city }}</td>
              <td class="py-3.5 px-4 text-xs text-[#6A5A50] font-mono">{{ b.phone }}</td>
              <td class="py-3.5 px-4 text-center">
                <span
                  class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full border"
                  :class="b.status === 'Active' ? 'bg-[#EAF5EA] text-[#2E7D32] border-[#C8E6C9]' : 'bg-[#F3EFE9] text-[#7A6B60] border-[#DDD3C7]'"
                >
                  {{ b.status }}
                </span>
              </td>
              <td class="py-3.5 px-5 text-right">
                <button
                  @click.stop="selectBranchForEdit(b)"
                  class="px-3 py-1 text-xs font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-md shadow-sm transition"
                >
                  Edit
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Bottom Two Columns: Branch Details Editor + Delivery Zones -->
    <div v-if="editingBranch" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Left: Branch Details (Col span 2) -->
      <div class="lg:col-span-2 bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-[#EFE8DF] pb-3">
          <h3 class="font-semibold text-base text-[#2A1D15]">Branch details</h3>
          <span class="text-xs text-[#8A7B71]">Editing: <strong>{{ editingBranch.branch_name }}</strong></span>
        </div>

        <form @submit.prevent="saveBranch" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Branch name</label>
              <input
                v-model="editForm.branch_name"
                type="text"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
                required
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Branch code</label>
              <input
                v-model="editForm.branch_code"
                type="text"
                readonly
                class="w-full bg-[#F7F2EA] border border-[#DCD0C2] rounded-lg px-3 py-2 text-xs font-mono text-[#5A4A3E]"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Branch type</label>
              <input
                :value="editingBranch.branch_type === 'Main' ? 'Main branch' : 'Regular branch'"
                type="text"
                readonly
                class="w-full bg-[#F7F2EA] border border-[#DCD0C2] rounded-lg px-3 py-2 text-xs text-[#5A4A3E]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Manager</label>
              <input
                :value="editingBranch.manager?.full_name || 'Assigned Branch Admin'"
                type="text"
                readonly
                class="w-full bg-[#F7F2EA] border border-[#DCD0C2] rounded-lg px-3 py-2 text-xs text-[#5A4A3E]"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Region</label>
              <input
                v-model="editForm.region"
                type="text"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">City</label>
              <input
                v-model="editForm.city"
                type="text"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Phone</label>
            <input
              v-model="editForm.phone"
              type="text"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Address</label>
            <input
              v-model="editForm.address"
              type="text"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Opening hours</label>
            <input
              v-model="editForm.opening_hours"
              type="text"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
            />
          </div>

          <!-- Active Toggle -->
          <div class="flex items-center justify-between pt-2">
            <label class="flex items-center space-x-2 cursor-pointer">
              <input
                type="checkbox"
                v-model="editForm.isActive"
                class="w-4 h-4 text-[#BA5420] rounded border-[#D8CDC0]"
              />
              <span class="text-xs font-semibold text-[#2D231D]">Branch is active</span>
            </label>

            <div class="flex space-x-3">
              <button
                type="button"
                @click="editingBranch = null"
                class="px-4 py-2 text-xs font-medium text-[#6A5A50] hover:bg-[#F3EFE9] rounded-lg transition"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isSavingBranch"
                class="px-4 py-2 text-xs font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg shadow-sm transition disabled:opacity-50"
              >
                {{ isSavingBranch ? 'Saving...' : 'Save branch' }}
              </button>
            </div>
          </div>

          <div class="p-3 bg-[#FCFAF7] border border-[#EFE8DF] rounded-lg text-xs text-[#8A7B71]">
            A branch with open orders or transfers in transit cannot be deactivated.
          </div>
        </form>
      </div>

      <!-- Right: Delivery Zones -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="font-semibold text-base text-[#2A1D15]">Delivery zones</h3>
            <p class="text-[11px] text-[#8C7D73]">Delivery coverage for {{ editingBranch.branch_name }}</p>
          </div>
          <button
            @click="isAddZoneModalOpen = true"
            class="text-xs font-semibold text-[#BA5420] hover:text-[#9E4314] px-2.5 py-1 bg-[#FAF3EC] border border-[#E0D2C2] rounded-lg transition"
          >
            + Add zone
          </button>
        </div>

        <div class="overflow-x-auto border border-[#EFE8DF] rounded-xl">
          <table class="w-full text-left text-xs">
            <thead class="bg-[#FAF6F0] text-[#88786B] uppercase border-b border-[#EFE8DF] font-semibold">
              <tr>
                <th class="py-2.5 px-3">Zone</th>
                <th class="py-2.5 px-3 text-center">Fee</th>
                <th class="py-2.5 px-3 text-center">Status</th>
                <th class="py-2.5 px-3 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr v-for="z in editingBranch.delivery_zones" :key="z.zone_id" class="hover:bg-[#FCFAF7]">
                <td class="py-2.5 px-3 font-medium text-[#382C24]">{{ z.zone_name }}</td>
                <td class="py-2.5 px-3 text-center font-bold text-[#2A1D15]">ETB {{ Number(z.delivery_fee).toFixed(0) }}</td>
                <td class="py-2.5 px-3 text-center">
                  <span
                    class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                    :class="z.status === 'Active' ? 'bg-[#EAF5EA] text-[#2E7D32]' : 'bg-[#FFF3E0] text-[#E65100]'"
                  >
                    {{ z.status }}
                  </span>
                </td>
                <td class="py-2.5 px-3 text-right">
                  <button
                    @click="deleteDeliveryZone(z)"
                    class="text-red-500 hover:text-red-700 text-xs font-semibold"
                    title="Remove zone"
                  >
                    &times;
                  </button>
                </td>
              </tr>
              <tr v-if="!editingBranch.delivery_zones || editingBranch.delivery_zones.length === 0">
                <td colspan="4" class="py-4 text-center text-xs text-[#8A7B71]">No delivery zones defined yet.</td>
              </tr>
            </tbody>
          </table>
        </div>

        <p class="text-[11px] text-[#8F7F74] pt-1">
          Zone delivery fees are added dynamically to customer orders based on selected neighborhood.
        </p>
      </div>
    </div>

    <!-- MODAL 1: Add New Branch Modal -->
    <div v-if="isAddBranchModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-[#E9E0D4] max-w-lg w-full p-6 space-y-5 animate-in fade-in duration-200">
        <div class="flex items-center justify-between border-b border-[#EFE8DF] pb-3">
          <div>
            <h3 class="text-lg font-serif font-bold text-[#2A1D15]">Register New Bakery Branch</h3>
            <p class="text-xs text-[#8A7B71]">Expand bakery operations with a new branch location</p>
          </div>
          <button @click="isAddBranchModalOpen = false" class="text-[#8A7B71] hover:text-[#2A1D15] text-xl font-bold p-1">
            &times;
          </button>
        </div>

        <form @submit.prevent="submitNewBranch" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Branch Name *</label>
              <input
                v-model="newBranch.branch_name"
                type="text"
                placeholder="e.g. Kazanchis Branch"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Branch Code *</label>
              <input
                v-model="newBranch.branch_code"
                type="text"
                placeholder="e.g. KAZ-01"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs font-mono uppercase text-[#2D231D]"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Branch Type *</label>
              <select
                v-model="newBranch.branch_type"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              >
                <option value="Regular">Regular Branch</option>
                <option value="Main">Main Branch (Central Kitchen)</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Phone *</label>
              <input
                v-model="newBranch.phone"
                type="text"
                placeholder="+251 11 555 1234"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">City *</label>
              <input
                v-model="newBranch.city"
                type="text"
                placeholder="Addis Ababa"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Region *</label>
              <input
                v-model="newBranch.region"
                type="text"
                placeholder="Addis Ababa"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              />
            </div>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Full Street Address *</label>
            <input
              v-model="newBranch.address"
              type="text"
              placeholder="Kazanchis, Near UNECA Building"
              required
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Opening Hours</label>
            <input
              v-model="newBranch.opening_hours"
              type="text"
              placeholder="Mon–Sun 07:00–21:00"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
            />
          </div>

          <div class="flex justify-end space-x-3 pt-3 border-t border-[#EFE8DF]">
            <button
              type="button"
              @click="isAddBranchModalOpen = false"
              class="px-4 py-2 text-xs font-medium text-[#6A5A50] hover:bg-[#F3EFE9] rounded-lg transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isSubmittingBranch"
              class="px-5 py-2 text-xs font-semibold bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg shadow-sm transition disabled:opacity-50"
            >
              {{ isSubmittingBranch ? 'Creating...' : 'Register Branch' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- MODAL 2: Add Delivery Zone Modal -->
    <div v-if="isAddZoneModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-[#E9E0D4] max-w-md w-full p-6 space-y-5 animate-in fade-in duration-200">
        <div class="flex items-center justify-between border-b border-[#EFE8DF] pb-3">
          <div>
            <h3 class="text-lg font-serif font-bold text-[#2A1D15]">Add Delivery Zone</h3>
            <p class="text-xs text-[#8A7B71]">Add zone to {{ editingBranch?.branch_name }}</p>
          </div>
          <button @click="isAddZoneModalOpen = false" class="text-[#8A7B71] hover:text-[#2A1D15] text-xl font-bold p-1">
            &times;
          </button>
        </div>

        <form @submit.prevent="submitNewDeliveryZone" class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Zone Name *</label>
            <input
              v-model="newZone.zone_name"
              type="text"
              placeholder="e.g. Bole Medhanialem"
              required
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
            />
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Delivery Fee (ETB) *</label>
            <input
              v-model.number="newZone.delivery_fee"
              type="number"
              placeholder="75"
              min="0"
              required
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
            />
          </div>

          <div class="flex justify-end space-x-3 pt-3 border-t border-[#EFE8DF]">
            <button
              type="button"
              @click="isAddZoneModalOpen = false"
              class="px-4 py-2 text-xs font-medium text-[#6A5A50] hover:bg-[#F3EFE9] rounded-lg transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isSubmittingZone"
              class="px-5 py-2 text-xs font-semibold bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg shadow-sm transition disabled:opacity-50"
            >
              {{ isSubmittingZone ? 'Adding...' : 'Save Zone' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const branches = ref([]);
const editingBranch = ref(null);
const successMsg = ref('');
const errorMsg = ref('');
const isSavingBranch = ref(false);

// Modals
const isAddBranchModalOpen = ref(false);
const isSubmittingBranch = ref(false);
const isAddZoneModalOpen = ref(false);
const isSubmittingZone = ref(false);

const newBranch = ref({
  branch_name: '',
  branch_code: '',
  branch_type: 'Regular',
  city: 'Addis Ababa',
  region: 'Addis Ababa',
  address: '',
  phone: '',
  opening_hours: 'Mon–Sun 07:00–21:00',
});

const newZone = ref({
  zone_name: '',
  delivery_fee: 50,
});

const editForm = ref({
  branch_name: '',
  branch_code: '',
  region: '',
  city: '',
  phone: '',
  address: '',
  opening_hours: '',
  isActive: true,
});

const loadBranches = async () => {
  try {
    const res = await api.get('/branches');
    branches.value = res.data;
    if (!editingBranch.value && branches.value.length > 2) {
      // Default to Megenagna as in Mockup Page 13
      selectBranchForEdit(branches.value[2]);
    } else if (editingBranch.value) {
      const refreshed = branches.value.find(b => b.branch_id === editingBranch.value.branch_id);
      if (refreshed) editingBranch.value = refreshed;
    }
  } catch (err) {
    console.error('Failed to load branches', err);
  }
};

const selectBranchForEdit = (b) => {
  editingBranch.value = b;
  editForm.value = {
    branch_name: b.branch_name,
    branch_code: b.branch_code,
    region: b.region,
    city: b.city,
    phone: b.phone,
    address: b.address,
    opening_hours: b.opening_hours,
    isActive: b.status === 'Active',
  };
};

const saveBranch = async () => {
  if (!editingBranch.value) return;
  successMsg.value = '';
  errorMsg.value = '';
  isSavingBranch.value = true;

  try {
    await api.patch(`/admin/branches/${editingBranch.value.branch_id}`, {
      branch_name: editForm.value.branch_name,
      phone: editForm.value.phone,
      address: editForm.value.address,
      opening_hours: editForm.value.opening_hours,
      status: editForm.value.isActive ? 'Active' : 'Inactive',
    });
    successMsg.value = `Branch ${editForm.value.branch_name} updated successfully.`;
    await loadBranches();
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to update branch';
  } finally {
    isSavingBranch.value = false;
  }
};

const openAddBranchModal = () => {
  newBranch.value = {
    branch_name: '',
    branch_code: '',
    branch_type: 'Regular',
    city: 'Addis Ababa',
    region: 'Addis Ababa',
    address: '',
    phone: '',
    opening_hours: 'Mon–Sun 07:00–21:00',
  };
  isAddBranchModalOpen.value = true;
};

const submitNewBranch = async () => {
  isSubmittingBranch.value = true;
  successMsg.value = '';
  errorMsg.value = '';
  try {
    const res = await api.post('/admin/branches', newBranch.value);
    successMsg.value = `Branch "${res.data.branch_name}" (${res.data.branch_code}) successfully created!`;
    isAddBranchModalOpen.value = false;
    await loadBranches();
    selectBranchForEdit(res.data);
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to register branch';
  } finally {
    isSubmittingBranch.value = false;
  }
};

const submitNewDeliveryZone = async () => {
  if (!editingBranch.value) return;
  isSubmittingZone.value = true;
  successMsg.value = '';
  errorMsg.value = '';
  try {
    const res = await api.post(`/admin/branches/${editingBranch.value.branch_id}/delivery-zones`, newZone.value);
    if (!editingBranch.value.delivery_zones) editingBranch.value.delivery_zones = [];
    editingBranch.value.delivery_zones.push(res.data);
    successMsg.value = `Delivery zone "${res.data.zone_name}" added with fee ETB ${res.data.delivery_fee}`;
    isAddZoneModalOpen.value = false;
    newZone.value = { zone_name: '', delivery_fee: 50 };
    await loadBranches();
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to add delivery zone';
  } finally {
    isSubmittingZone.value = false;
  }
};

const deleteDeliveryZone = async (zone) => {
  if (!confirm(`Are you sure you want to remove delivery zone "${zone.zone_name}"?`)) return;
  try {
    await api.delete(`/admin/branches/${editingBranch.value.branch_id}/delivery-zones/${zone.zone_id}`);
    editingBranch.value.delivery_zones = editingBranch.value.delivery_zones.filter(z => z.zone_id !== zone.zone_id);
    successMsg.value = `Delivery zone "${zone.zone_name}" removed.`;
    await loadBranches();
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to remove delivery zone';
  }
};

onMounted(() => {
  loadBranches();
});
</script>
