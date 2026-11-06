<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Users and roles</h2>
      <div class="flex items-center space-x-3">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by name or email"
          class="bg-white border border-[#D8CDC0] rounded-lg px-3 py-1.5 text-xs text-[#2D231D] shadow-sm focus:ring-1 focus:ring-[#BA5420]"
        />
        <button
          @click="focusAddUser"
          class="px-4 py-2 text-sm font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg transition shadow-sm flex items-center space-x-1.5"
        >
          <span>+ Add user</span>
        </button>
      </div>
    </div>

    <!-- Alert / Messages -->
    <div v-if="successMsg" class="p-3 bg-[#EBF7EB] border border-[#C5E9C6] rounded-lg text-xs text-[#1E7422] flex items-center justify-between">
      <span>{{ successMsg }}</span>
      <button @click="successMsg = ''" class="text-[#1E7422] font-bold ml-2">&times;</button>
    </div>
    <div v-if="errorMsg" class="p-3 bg-[#FEECEC] border border-[#F5C2C2] rounded-lg text-xs text-[#B91C1C] flex items-center justify-between">
      <span>{{ errorMsg }}</span>
      <button @click="errorMsg = ''" class="text-[#B91C1C] font-bold ml-2">&times;</button>
    </div>

    <!-- Tabs -->
    <div class="flex items-center space-x-6 border-b border-[#EAE0D4] pb-2 text-xs font-semibold">
      <button
        @click="switchTab('staff')"
        :class="activeTab === 'staff' ? 'text-[#BA5420] border-b-2 border-[#BA5420] pb-2 font-bold' : 'text-[#7A6B60]'"
      >
        Staff and admins
      </button>
      <button
        @click="switchTab('customers')"
        :class="activeTab === 'customers' ? 'text-[#BA5420] border-b-2 border-[#BA5420] pb-2 font-bold' : 'text-[#7A6B60]'"
      >
        Customers
      </button>
      <button
        @click="switchTab('permissions')"
        :class="activeTab === 'permissions' ? 'text-[#BA5420] border-b-2 border-[#BA5420] pb-2 font-bold' : 'text-[#7A6B60]'"
      >
        Role permissions
      </button>
    </div>

    <!-- Role Filter Pills Bar -->
    <div v-if="activeTab !== 'permissions'" class="flex items-center justify-between text-xs">
      <div class="flex items-center space-x-2">
        <span class="px-3 py-1 rounded-full bg-white border border-[#DDD2C5] text-[#55463C] font-semibold">
          {{ activeTab === 'staff' ? 'All Staff Roles' : 'Registered Customers' }}
        </span>
        <span class="px-3 py-1 rounded-full bg-white border border-[#DDD2C5] text-[#55463C] font-semibold">All branches</span>
        <span class="px-3 py-1 rounded-full bg-[#EAE2D8] text-[#55463C] font-bold">Active</span>
      </div>
      <span class="text-[#8A7B71] font-semibold">{{ filteredUsers.length }} users</span>
    </div>

    <!-- Users Table (For Staff or Customers) -->
    <div v-if="activeTab !== 'permissions'" class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
          <thead class="bg-[#FAF6F0] text-[11px] font-bold text-[#827165] tracking-wider uppercase border-b border-[#EFE8DF]">
            <tr>
              <th class="py-3 px-5">User</th>
              <th class="py-3 px-4">Role</th>
              <th class="py-3 px-4">Branch</th>
              <th class="py-3 px-4">Last sign-in</th>
              <th class="py-3 px-4 text-center">Status</th>
              <th class="py-3 px-5 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#F3EDE4]">
            <tr v-for="u in filteredUsers" :key="u.user_id" class="hover:bg-[#FCFAF7] transition">
              <td class="py-3 px-5 flex items-center space-x-3">
                <span class="w-8 h-8 rounded-full bg-[#EAE2D8] text-[#55463C] font-bold text-xs flex items-center justify-center shrink-0">
                  {{ u.initials }}
                </span>
                <div>
                  <div class="font-bold text-xs text-[#2A1D15]">{{ u.full_name }}</div>
                  <div class="text-[11px] text-[#7A6B60]">{{ u.email }}</div>
                </div>
              </td>
              <td class="py-3 px-4">
                <span
                  class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                  :class="{
                    'bg-[#FFF3E0] text-[#E65100]': u.role === 'Super Admin',
                    'bg-[#EBF3FB] text-[#1565C0]': u.role === 'Main Branch Manager',
                    'bg-[#F2EFE9] text-[#5A4B41]': u.role === 'Branch Admin',
                    'bg-[#F6F4F0] text-[#7A6B60]': u.role === 'Branch Staff',
                    'bg-[#FDEAEA] text-[#9E4314]': u.role === 'Customer'
                  }"
                >
                  {{ u.role }}
                </span>
              </td>
              <td class="py-3 px-4 text-xs text-[#4A3B32] font-medium">{{ u.branch_name }}</td>
              <td class="py-3 px-4 text-xs text-[#7A6B60]">{{ u.last_sign_in }}</td>
              <td class="py-3 px-4 text-center">
                <span
                  class="inline-block px-2.5 py-0.5 text-xs font-semibold rounded-full border"
                  :class="u.status === 'Active' ? 'bg-[#EAF5EA] text-[#2E7D32] border-[#C8E6C9]' : 'bg-[#FFF8E6] text-[#A67503] border-[#FEE39E]'"
                >
                  {{ u.status }}
                </span>
              </td>
              <td class="py-3 px-5 text-right">
                <button
                  @click="openEditUserModal(u)"
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

    <!-- Bottom Two Columns: Add User Form + Permission Matrix -->
    <div id="add-user-section" class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
      <!-- Add User Form -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">Add new team member</h3>

        <form @submit.prevent="submitAddUser" class="space-y-4">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Full name *</label>
              <input
                v-model="newUser.full_name"
                type="text"
                placeholder="e.g. Tigist Alemu"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
                required
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Email *</label>
              <input
                v-model="newUser.email"
                type="email"
                placeholder="e.g. tigist@sweetbakes.com"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
                required
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Role *</label>
              <select
                v-model="newUser.role"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
                required
              >
                <option value="Super Admin">Super Admin</option>
                <option value="Main Branch Manager">Main Branch Manager</option>
                <option value="Branch Admin">Branch Admin</option>
                <option value="Branch Staff">Branch Staff</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Assigned branch</label>
              <select
                v-model="newUser.branch_id"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
                :disabled="newUser.role === 'Super Admin'"
              >
                <option :value="null">None (Super Admin)</option>
                <option v-for="b in branches" :key="b.branch_id" :value="b.branch_id">
                  {{ b.branch_name }}
                </option>
              </select>
            </div>
          </div>

          <div class="p-3 bg-[#FCFAF7] border border-[#EFE8DF] rounded-lg text-xs text-[#8A7B71] leading-relaxed">
            Branch Admin and Branch Staff must have an assigned branch. Customers and Super Admins have none.
          </div>

          <div class="flex justify-end space-x-3 pt-2">
            <button
              type="submit"
              :disabled="isAddingUser"
              class="px-5 py-2 text-xs font-semibold bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg shadow-sm transition disabled:opacity-50"
            >
              {{ isAddingUser ? 'Creating...' : 'Create User Account' }}
            </button>
          </div>
        </form>
      </div>

      <!-- What Each Role Can Do (Permission Matrix) -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">What each role can do</h3>

        <div class="overflow-x-auto border border-[#EFE8DF] rounded-xl">
          <table class="w-full text-left text-xs">
            <thead class="bg-[#FAF6F0] text-[#88786B] uppercase border-b border-[#EFE8DF] font-semibold">
              <tr>
                <th class="py-2.5 px-3">Action</th>
                <th class="py-2.5 px-2 text-center">Super</th>
                <th class="py-2.5 px-2 text-center">Main</th>
                <th class="py-2.5 px-2 text-center">Branch</th>
                <th class="py-2.5 px-2 text-center">Staff</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#F3EDE4]">
              <tr v-for="perm in permissionMatrix" :key="perm.action" class="hover:bg-[#FCFAF7]">
                <td class="py-2.5 px-3 font-medium text-[#382C24]">{{ perm.action }}</td>
                <td class="py-2.5 px-2 text-center font-bold" :class="perm.super === 'Yes' ? 'text-[#2E7D32]' : 'text-[#8A7B71]'">{{ perm.super }}</td>
                <td class="py-2.5 px-2 text-center font-bold" :class="perm.main === 'Yes' ? 'text-[#2E7D32]' : 'text-[#8A7B71]'">{{ perm.main }}</td>
                <td class="py-2.5 px-2 text-center font-bold" :class="perm.branch === 'Yes' ? 'text-[#2E7D32]' : 'text-[#8A7B71]'">{{ perm.branch }}</td>
                <td class="py-2.5 px-2 text-center font-bold" :class="perm.staff === 'Yes' ? 'text-[#2E7D32]' : 'text-[#8A7B71]'">{{ perm.staff }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- MODAL: Edit User Modal -->
    <div v-if="isEditModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs p-4">
      <div class="bg-white rounded-2xl shadow-2xl border border-[#E9E0D4] max-w-lg w-full p-6 space-y-5 animate-in fade-in duration-200">
        <div class="flex items-center justify-between border-b border-[#EFE8DF] pb-3">
          <div>
            <h3 class="text-lg font-serif font-bold text-[#2A1D15]">Edit User & Permissions</h3>
            <p class="text-xs text-[#8A7B71]">{{ editingUser?.email }}</p>
          </div>
          <button @click="isEditModalOpen = false" class="text-[#8A7B71] hover:text-[#2A1D15] text-xl font-bold p-1">
            &times;
          </button>
        </div>

        <form @submit.prevent="submitEditUser" class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Full name *</label>
            <input
              v-model="editUserForm.full_name"
              type="text"
              required
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Role *</label>
              <select
                v-model="editUserForm.role"
                required
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              >
                <option value="Super Admin">Super Admin</option>
                <option value="Main Branch Manager">Main Branch Manager</option>
                <option value="Branch Admin">Branch Admin</option>
                <option value="Branch Staff">Branch Staff</option>
                <option value="Customer">Customer</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Assigned branch</label>
              <select
                v-model="editUserForm.branch_id"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
                :disabled="editUserForm.role === 'Super Admin' || editUserForm.role === 'Customer'"
              >
                <option :value="null">None (Super Admin/Customer)</option>
                <option v-for="b in branches" :key="b.branch_id" :value="b.branch_id">
                  {{ b.branch_name }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Phone</label>
              <input
                v-model="editUserForm.phone"
                type="text"
                placeholder="+251 911 123 456"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Account Status</label>
              <select
                v-model="editUserForm.status"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D]"
              >
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>

          <div class="flex justify-end space-x-3 pt-3 border-t border-[#EFE8DF]">
            <button
              type="button"
              @click="isEditModalOpen = false"
              class="px-4 py-2 text-xs font-medium text-[#6A5A50] hover:bg-[#F3EFE9] rounded-lg transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="isSavingEdit"
              class="px-5 py-2 text-xs font-semibold bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg shadow-sm transition disabled:opacity-50"
            >
              {{ isSavingEdit ? 'Saving...' : 'Update User' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '../../api';

const activeTab = ref('staff');
const searchQuery = ref('');
const users = ref([]);
const branches = ref([]);
const successMsg = ref('');
const errorMsg = ref('');
const isAddingUser = ref(false);

// Edit User Modal
const isEditModalOpen = ref(false);
const editingUser = ref(null);
const isSavingEdit = ref(false);
const editUserForm = ref({
  full_name: '',
  role: 'Branch Staff',
  branch_id: null,
  phone: '',
  status: 'Active',
});

const newUser = ref({
  full_name: '',
  email: '',
  role: 'Branch Staff',
  branch_id: 2, // Bole
});

const permissionMatrix = [
  { action: 'Manage branches', super: 'Yes', main: 'No', branch: 'No', staff: 'No' },
  { action: 'Dispatch stock', super: 'Yes', main: 'Yes', branch: 'No', staff: 'No' },
  { action: 'Confirm received stock', super: 'Yes', main: 'No', branch: 'Yes', staff: 'Yes' },
  { action: 'View all orders', super: 'Yes', main: 'Yes', branch: 'No', staff: 'No' },
  { action: 'Manage own branch orders', super: 'Yes', main: 'Yes', branch: 'Yes', staff: 'Yes' },
  { action: 'View all sales reports', super: 'Yes', main: 'Yes', branch: 'No', staff: 'No' },
];

const loadUsers = async () => {
  try {
    const [uRes, bRes] = await Promise.all([
      api.get('/admin/users-list', { params: { tab: activeTab.value } }),
      api.get('/branches'),
    ]);
    users.value = uRes.data;
    branches.value = bRes.data;
  } catch (err) {
    console.error('Failed to load users', err);
  }
};

const switchTab = (tab) => {
  activeTab.value = tab;
  loadUsers();
};

const filteredUsers = computed(() => {
  if (!searchQuery.value) return users.value;
  return users.value.filter(u =>
    u.full_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
    u.email.toLowerCase().includes(searchQuery.value.toLowerCase())
  );
});

const submitAddUser = async () => {
  isAddingUser.value = true;
  successMsg.value = '';
  errorMsg.value = '';
  try {
    const res = await api.post('/admin/users', newUser.value);
    successMsg.value = `User invitation sent to ${newUser.value.email} (${newUser.value.role})!`;
    newUser.value = {
      full_name: '',
      email: '',
      role: 'Branch Staff',
      branch_id: branches.value[0]?.branch_id || null,
    };
    await loadUsers();
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to add user';
  } finally {
    isAddingUser.value = false;
  }
};

const focusAddUser = () => {
  const el = document.getElementById('add-user-section');
  if (el) el.scrollIntoView({ behavior: 'smooth' });
};

const openEditUserModal = (u) => {
  editingUser.value = u;
  editUserForm.value = {
    full_name: u.full_name,
    role: u.role,
    branch_id: u.branch_id,
    phone: u.phone || '',
    status: u.status,
  };
  isEditModalOpen.value = true;
};

const submitEditUser = async () => {
  if (!editingUser.value) return;
  isSavingEdit.value = true;
  successMsg.value = '';
  errorMsg.value = '';
  try {
    await api.patch(`/admin/users/${editingUser.value.user_id}`, editUserForm.value);
    successMsg.value = `User "${editUserForm.value.full_name}" updated successfully!`;
    isEditModalOpen.value = false;
    await loadUsers();
  } catch (err) {
    errorMsg.value = err.response?.data?.message || 'Failed to update user';
  } finally {
    isSavingEdit.value = false;
  }
};

onMounted(() => {
  loadUsers();
});
</script>
