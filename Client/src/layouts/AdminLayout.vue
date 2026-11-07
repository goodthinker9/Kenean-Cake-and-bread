<template>
  <div class="flex h-screen bg-[#F8F4EE] text-[#2D231D] font-sans antialiased overflow-hidden">
    <!-- Dark Sidebar tailored to active Role -->
    <aside class="w-64 bg-[#1C130D] flex flex-col justify-between shrink-0 select-none border-r border-[#2A1D15]">
      <div>
        <!-- Brand Logo Header -->
        <div class="px-6 py-6 border-b border-[#2C1F16]/60 flex items-center space-x-2.5">
          <span class="w-8 h-8 rounded-lg bg-[#BA5420] text-white flex items-center justify-center font-bold text-lg">G</span>
          <router-link :to="homePath" class="block">
            <h1 class="text-xl font-serif font-bold text-[#E5B869] tracking-wide">{{ appConfig.brandName }}</h1>
          </router-link>
        </div>

        <!-- Navigation Menu -->
        <nav class="px-3 py-4 space-y-5 overflow-y-auto max-h-[calc(100vh-160px)]">
          <!-- Overview Direct Link (Super Admin, Main Manager, Branch Admin) -->
          <div v-if="canAccess('overview')">
            <router-link
              to="/admin/overview"
              class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
              :class="$route.path === '/admin/overview' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
            >
              Overview
            </router-link>
          </div>

          <!-- BUSINESS Section (Super Admin & Main Manager) -->
          <div v-if="canAccess('branches') || canAccess('users') || canAccess('products')">
            <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-[#7E6A5C] uppercase">Business</div>
            <div class="space-y-1">
              <router-link
                v-if="canAccess('branches')"
                to="/admin/branches"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path.startsWith('/admin/branches') ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                Branches
              </router-link>

              <router-link
                v-if="canAccess('users')"
                to="/admin/users"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path.startsWith('/admin/users') ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                Users and roles
              </router-link>

              <router-link
                v-if="canAccess('products')"
                to="/admin/products"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path.startsWith('/admin/products') ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                Products and prices
              </router-link>
            </div>
          </div>

          <!-- STOCK Section -->
          <div>
            <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-[#7E6A5C] uppercase">Stock</div>
            <div class="space-y-1">
              <router-link
                v-if="canAccess('inventory')"
                to="/admin/inventory"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/inventory' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                {{ isBranchScoped ? 'Branch Inventory' : 'Inventory by branch' }}
              </router-link>

              <router-link
                v-if="canAccess('distribution')"
                to="/admin/distribution"
                class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/distribution' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                <span>{{ isBranchScoped ? 'Incoming transfers' : 'Distribution' }}</span>
                <span class="px-1.5 py-0.2 text-[11px] bg-[#33241A] text-[#D8C7BA] rounded font-bold">2</span>
              </router-link>

              <router-link
                v-if="canAccess('transfers/new')"
                to="/admin/transfers/new"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path.startsWith('/admin/transfers/new') ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                New transfer
              </router-link>
            </div>
          </div>

          <!-- SALES Section -->
          <div>
            <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-[#7E6A5C] uppercase">Sales</div>
            <div class="space-y-1">
              <router-link
                v-if="canAccess('orders')"
                to="/admin/orders"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/orders' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                {{ isBranchScoped ? 'Branch orders' : 'All orders' }}
              </router-link>

              <router-link
                v-if="canAccess('custom-cakes')"
                to="/admin/custom-cakes"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/custom-cakes' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                Custom cakes
              </router-link>

              <router-link
                v-if="canAccess('payments')"
                to="/admin/payments"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/payments' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                Payments
              </router-link>

              <router-link
                v-if="canAccess('reports')"
                to="/admin/reports"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/reports' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                {{ isBranchScoped ? 'Branch reports' : 'Reports' }}
              </router-link>
            </div>
          </div>

          <!-- SYSTEM Section -->
          <div v-if="canAccess('notifications') || canAccess('audit-logs') || canAccess('settings')">
            <div class="px-3 mb-2 text-[11px] font-bold tracking-wider text-[#7E6A5C] uppercase">System</div>
            <div class="space-y-1">
              <router-link
                v-if="canAccess('notifications')"
                to="/admin/notifications"
                class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/notifications' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                <span>Notifications</span>
                <span class="px-1.5 py-0.2 text-[11px] bg-[#33241A] text-[#D8C7BA] rounded font-bold">5</span>
              </router-link>

              <router-link
                v-if="canAccess('audit-logs')"
                to="/admin/audit-logs"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/audit-logs' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                Audit log
              </router-link>

              <router-link
                v-if="canAccess('settings')"
                to="/admin/settings"
                class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                :class="$route.path === '/admin/settings' ? 'bg-[#BA5420] text-white shadow-sm' : 'text-[#B8A79B] hover:bg-[#2A1D15] hover:text-white'"
              >
                Settings
              </router-link>
            </div>
          </div>
        </nav>
      </div>

      <!-- Bottom User Profile with Role & Branch Tag -->
      <div class="p-4 border-t border-[#2C1F16]/60 bg-[#160E0A]">
        <div>
          <div class="text-sm font-medium text-[#F4EDE4]">
            {{ authStore.user?.full_name || 'Admin User' }}
          </div>
          <div class="text-xs text-[#9B897D]">
            <span class="font-bold text-[#E5B869]">{{ currentRole }}</span>
            <span v-if="authStore.user?.branch"> · {{ authStore.user.branch.branch_name }}</span>
            <span v-else> · All Branches</span>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <!-- Quick Role Switcher Bar with Access Indicators -->
      <header class="h-14 bg-white border-b border-[#E7DFD5] px-6 flex items-center justify-between shrink-0 shadow-[0_1px_3px_rgba(0,0,0,0.02)]">
        <div class="flex items-center space-x-3 text-sm">
          <span class="text-[#88786B] font-medium text-xs">Simulate Persona:</span>
          <select
            :value="selectedRoleKey"
            @change="handleRoleChange($event.target.value)"
            class="bg-[#F8F4EE] border border-[#DACDC0] text-[#3A2E26] text-xs font-semibold rounded-lg px-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-[#BA5420]"
          >
            <option
              v-for="(r, idx) in authStore.availableRoles"
              :key="idx"
              :value="idx"
            >
              {{ r.label }}
            </option>
          </select>
          <span
            class="text-xs px-2.5 py-0.5 rounded-full font-semibold border"
            :class="{
              'bg-[#FFF3E0] text-[#E65100] border-[#FFE0B2]': currentRole === 'Super Admin',
              'bg-[#EBF3FB] text-[#1565C0] border-[#BBDEFB]': currentRole === 'Main Branch Manager',
              'bg-[#FAF3EC] text-[#BA5420] border-[#EACBB8]': currentRole === 'Branch Admin',
              'bg-[#F3EFE9] text-[#7A6B60] border-[#DDD3C7]': currentRole === 'Branch Staff'
            }"
          >
            Scope: {{ roleScope }}
          </span>
        </div>

        <div class="flex items-center space-x-3">
          <router-link
            to="/"
            class="inline-flex items-center text-xs font-semibold text-[#BA5420] bg-[#FAF2EC] border border-[#EACBB8] hover:bg-[#F6E6DC] px-3.5 py-1.5 rounded-lg transition"
          >
            🛍️ {{ appConfig.brandName }} Storefront
          </router-link>
        </div>
      </header>

      <!-- View Slot with Smooth Scrolling -->
      <main class="flex-1 overflow-y-auto p-8 bg-[#F8F4EE]">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import { appConfig } from '../config';

const router = useRouter();
const authStore = useAuthStore();

const currentRole = computed(() => authStore.user?.role || 'Super Admin');

const isBranchScoped = computed(() => {
  return currentRole.value === 'Branch Admin' || currentRole.value === 'Branch Staff';
});

const homePath = computed(() => {
  if (currentRole.value === 'Branch Staff') return '/admin/orders';
  return '/admin/overview';
});

const roleScope = computed(() => {
  if (currentRole.value === 'Super Admin') return 'Whole business (All branches)';
  if (currentRole.value === 'Main Branch Manager') return 'Main Branch & Distribution';
  if (currentRole.value === 'Branch Admin') return `${authStore.user?.branch?.branch_name || 'One assigned'} Branch`;
  if (currentRole.value === 'Branch Staff') return `${authStore.user?.branch?.branch_name || 'One assigned'} Branch (Staff)`;
  return 'Customer Storefront';
});

// Role-based permission map
const permissions = {
  'Super Admin': ['overview', 'branches', 'users', 'products', 'inventory', 'distribution', 'transfers/new', 'orders', 'custom-cakes', 'payments', 'reports', 'notifications', 'settings', 'audit-logs'],
  'Main Branch Manager': ['overview', 'users', 'inventory', 'distribution', 'transfers/new', 'orders', 'custom-cakes', 'payments', 'reports', 'notifications'],
  'Branch Admin': ['overview', 'inventory', 'distribution', 'orders', 'custom-cakes', 'reports', 'notifications'],
  'Branch Staff': ['inventory', 'distribution', 'orders', 'notifications'],
  'Customer': [],
};

const canAccess = (feature) => {
  const allowed = permissions[currentRole.value] || [];
  return allowed.includes(feature);
};

const selectedRoleKey = computed(() => {
  const index = authStore.availableRoles.findIndex(
    (r) => r.role === authStore.user?.role && (!r.branch_code || r.branch_code === authStore.user?.branch?.branch_code)
  );
  return index >= 0 ? index : 0;
});

const handleRoleChange = async (index) => {
  const roleItem = authStore.availableRoles[index];
  if (roleItem) {
    await authStore.switchRole(roleItem);
    if (roleItem.role === 'Customer') {
      router.push('/');
    } else if (roleItem.role === 'Branch Staff') {
      router.push('/admin/orders');
    } else {
      router.push('/admin/overview');
    }
  }
};
</script>
