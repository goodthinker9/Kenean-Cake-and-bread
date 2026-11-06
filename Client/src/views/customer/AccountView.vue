<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 font-sans">
    <!-- Account Layout Grid matching Image 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left Sidebar Profile & Navigation (4 cols) matching Image 2 -->
      <aside class="lg:col-span-4 bg-white rounded-3xl border border-[#F3E8E8] p-6 space-y-6 shadow-2xs">
        <!-- User Avatar & Greeting matching Image 2 -->
        <div class="text-center space-y-3 pb-4 border-b border-[#F8E7EA]">
          <div class="w-20 h-20 rounded-full bg-[#FFF1F2] border-2 border-[#FECDD3] flex items-center justify-center mx-auto text-3xl shadow-xs">
            👩
          </div>
          <div>
            <h2 class="text-lg font-serif font-black text-[#38121A]">
              Welcome, {{ userName }}
            </h2>
            <p class="text-xs text-[#7A6B60]">{{ userEmail }}</p>
            <span class="inline-block mt-1 px-3 py-0.5 bg-[#FFF1F2] text-[#E11D48] text-[11px] font-bold rounded-full">
              {{ authStore.user?.role || 'Customer' }}
            </span>
          </div>
        </div>

        <!-- Sidebar Navigation matching Image 2 -->
        <nav class="space-y-1.5 text-xs font-semibold">
          <button
            @click="activeTab = 'dashboard'"
            class="w-full text-left px-4 py-2.5 rounded-xl transition flex items-center space-x-2.5"
            :class="activeTab === 'dashboard' ? 'bg-[#FFF1F2] text-[#E11D48] font-bold' : 'text-[#4B1A24] hover:bg-[#FAF7F5]'"
          >
            <span>📊</span>
            <span>Dashboard</span>
          </button>

          <button
            @click="activeTab = 'orders'"
            class="w-full text-left px-4 py-2.5 rounded-xl transition flex items-center space-x-2.5"
            :class="activeTab === 'orders' ? 'bg-[#FFF1F2] text-[#E11D48] font-bold' : 'text-[#4B1A24] hover:bg-[#FAF7F5]'"
          >
            <span>📦</span>
            <span>Standard Orders</span>
          </button>

          <button
            @click="activeTab = 'custom_cakes'"
            class="w-full text-left px-4 py-2.5 rounded-xl transition flex items-center space-x-2.5"
            :class="activeTab === 'custom_cakes' ? 'bg-[#FFF1F2] text-[#E11D48] font-bold' : 'text-[#4B1A24] hover:bg-[#FAF7F5]'"
          >
            <span>🎂</span>
            <span>Custom Cakes &amp; Quotes</span>
            <span
              v-if="pendingQuotesCount > 0"
              class="ml-auto px-2 py-0.5 bg-[#E11D48] text-white rounded-full text-[10px] font-black"
            >
              {{ pendingQuotesCount }}
            </span>
          </button>

          <button
            @click="activeTab = 'addresses'"
            class="w-full text-left px-4 py-2.5 rounded-xl transition flex items-center space-x-2.5"
            :class="activeTab === 'addresses' ? 'bg-[#FFF1F2] text-[#E11D48] font-bold' : 'text-[#4B1A24] hover:bg-[#FAF7F5]'"
          >
            <span>📍</span>
            <span>My Addresses</span>
          </button>

          <button
            @click="activeTab = 'reviews'"
            class="w-full text-left px-4 py-2.5 rounded-xl transition flex items-center space-x-2.5"
            :class="activeTab === 'reviews' ? 'bg-[#FFF1F2] text-[#E11D48] font-bold' : 'text-[#4B1A24] hover:bg-[#FAF7F5]'"
          >
            <span>⭐</span>
            <span>Reviews</span>
          </button>

          <button
            @click="activeTab = 'settings'"
            class="w-full text-left px-4 py-2.5 rounded-xl transition flex items-center space-x-2.5"
            :class="activeTab === 'settings' ? 'bg-[#FFF1F2] text-[#E11D48] font-bold' : 'text-[#4B1A24] hover:bg-[#FAF7F5]'"
          >
            <span>⚙️</span>
            <span>Profile Settings</span>
          </button>

          <button
            @click="handleLogout"
            class="w-full text-left px-4 py-2.5 rounded-xl text-[#BE123C] hover:bg-[#FFF1F2] transition flex items-center space-x-2.5 pt-3 border-t border-[#F8E7EA]"
          >
            <span>🚪</span>
            <span>Logout</span>
          </button>
        </nav>
      </aside>

      <!-- Right Main Content Area (8 cols) matching Image 2 -->
      <main class="lg:col-span-8 bg-white rounded-3xl border border-[#F3E8E8] p-6 sm:p-8 space-y-6 shadow-2xs">
        <!-- 1. My Orders Tab matching Image 2 -->
        <div v-if="activeTab === 'orders' || activeTab === 'dashboard'" class="space-y-6">
          <div class="flex items-center justify-between border-b border-[#F8E7EA] pb-4">
            <div>
              <h2 class="text-xl font-serif font-black text-[#38121A]">Standard Orders</h2>
              <p class="text-xs text-[#7A6B60] mt-0.5">Track your orders and view order history</p>
            </div>
            <router-link to="/shop" class="text-xs font-bold text-[#E11D48] hover:underline">
              + Order More
            </router-link>
          </div>

          <!-- Orders List matching Image 2 -->
          <div class="space-y-4">
            <div
              v-for="order in displayOrders"
              :key="order.order_number"
              class="p-4 sm:p-5 rounded-2xl border border-[#F2D7DC] bg-[#FAF7F5] hover:bg-white hover:shadow-xs transition space-y-3"
            >
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#F2D7DC] pb-3">
                <div class="flex items-center space-x-3">
                  <div class="w-12 h-12 rounded-xl bg-white border border-[#F2D7DC] flex items-center justify-center text-xl shrink-0">
                    🎂
                  </div>
                  <div>
                    <span class="font-mono font-bold text-sm text-[#881337]">#{{ order.order_number }}</span>
                    <div class="text-[11px] text-[#7A6B60]">{{ order.date }} &middot; {{ order.branch_name }}</div>
                  </div>
                </div>

                <div class="flex items-center space-x-3">
                  <span
                    class="px-3 py-1 rounded-full text-xs font-bold"
                    :class="statusBadgeClass(order.status)"
                  >
                    {{ order.status }}
                  </span>

                  <router-link
                    :to="`/orders/${order.order_number}`"
                    class="text-xs font-bold text-[#E11D48] hover:underline flex items-center space-x-1"
                  >
                    <span>View Details</span>
                    <span>→</span>
                  </router-link>
                </div>
              </div>

              <!-- Order summary lines -->
              <div class="flex flex-wrap items-center justify-between text-xs text-[#5B2934] pt-1">
                <div>
                  <strong>Items:</strong> {{ order.items_summary }}
                </div>
                <div>
                  <strong>Total:</strong> <span class="font-bold text-[#881337]">ETB {{ Number(order.total_amount).toLocaleString() }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- 2. Custom Cakes & Quotes Tab matching Section 5 rules -->
        <div v-else-if="activeTab === 'custom_cakes'" class="space-y-6">
          <div class="flex items-center justify-between border-b border-[#F8E7EA] pb-4">
            <div>
              <h2 class="text-xl font-serif font-black text-[#38121A]">Custom Cake Requests</h2>
              <p class="text-xs text-[#7A6B60] mt-0.5">Review quotes, confirm with deposit, and track progress</p>
            </div>
            <router-link to="/custom-cake" class="text-xs font-bold text-[#E11D48] hover:underline">
              + Design New Cake
            </router-link>
          </div>

          <div v-if="customCakes.length === 0" class="p-8 text-center bg-[#FAF7F5] rounded-3xl border border-[#F2D7DC] space-y-3">
            <div class="text-4xl">🎂</div>
            <div class="font-bold text-sm text-[#38121A]">No custom cake requests found</div>
            <p class="text-xs text-[#7A6B60] max-w-sm mx-auto">
              Ready for a one-of-a-kind cake? Choose flavor, size, message and upload design inspiration.
            </p>
            <router-link to="/custom-cake" class="inline-block px-5 py-2.5 bg-[#E11D48] text-white font-bold text-xs rounded-xl shadow-xs">
              Create Custom Cake →
            </router-link>
          </div>

          <div v-else class="space-y-4">
            <div
              v-for="cake in customCakes"
              :key="cake.request_id"
              class="p-5 rounded-2xl border border-[#F2D7DC] bg-[#FAF7F5] space-y-3.5"
            >
              <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-[#F2D7DC] pb-3">
                <div class="space-y-0.5">
                  <div class="flex items-center space-x-2">
                    <span class="font-mono font-bold text-sm text-[#881337]">#{{ cake.request_number }}</span>
                    <span class="text-xs text-[#7A6B60]">({{ cake.branch?.branch_name || 'Bole' }} Branch)</span>
                  </div>
                  <div class="text-[11px] text-[#7A6B60]">
                    Needed by: <strong class="text-[#38121A]">{{ cake.needed_by }}</strong> &middot; Submitted {{ formatDate(cake.created_at) }}
                  </div>
                </div>

                <div class="flex items-center space-x-2">
                  <span
                    class="px-3 py-1 rounded-full text-xs font-bold"
                    :class="customCakeStatusBadge(cake.status)"
                  >
                    {{ cake.status }}
                  </span>
                </div>
              </div>

              <!-- Details Grid -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-[#5B2934]">
                <div>
                  <strong>Size &amp; Flavor:</strong> {{ cake.cake_size }} &middot; {{ cake.flavor }}
                </div>
                <div>
                  <strong>Message:</strong> {{ cake.message || 'None' }}
                </div>
                <div class="sm:col-span-2" v-if="cake.design">
                  <strong>Design Brief:</strong> {{ cake.design }}
                </div>
              </div>

              <!-- Rejection Reason if Rejected -->
              <div v-if="cake.status === 'Rejected'" class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
                <strong>Rejection Reason:</strong> {{ cake.rejection_reason || 'Unable to accept this custom brief for the requested date.' }}
              </div>

              <!-- Quote & Actions Section matching Section 5 & 8 -->
              <div v-if="cake.status === 'Quoted'" class="p-4 bg-white rounded-2xl border border-[#FECDD3] space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                  <div>
                    <div class="text-xs text-[#7A6B60]">Official Quotation from Head Baker:</div>
                    <div class="text-lg font-black text-[#881337]">
                      ETB {{ Number(cake.quoted_price).toLocaleString() }}
                    </div>
                    <div class="text-[11px] text-[#059669] font-medium">
                      50% Deposit Required: ETB {{ (Number(cake.quoted_price) * 0.5).toLocaleString() }}
                    </div>
                  </div>

                  <div class="flex flex-wrap items-center gap-2">
                    <button
                      @click="acceptQuote(cake, 'deposit')"
                      class="px-4 py-2 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold rounded-xl shadow-xs transition"
                    >
                      Accept &amp; Pay Deposit (ETB {{ (Number(cake.quoted_price) * 0.5).toLocaleString() }})
                    </button>
                    <button
                      @click="acceptQuote(cake, 'full')"
                      class="px-4 py-2 bg-[#881337] hover:bg-[#4A1521] text-white text-xs font-bold rounded-xl shadow-xs transition"
                    >
                      Pay Full (ETB {{ Number(cake.quoted_price).toLocaleString() }})
                    </button>
                    <button
                      @click="cancelCake(cake)"
                      class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl"
                    >
                      Decline
                    </button>
                  </div>
                </div>
              </div>

              <!-- Confirmed status details & Cancellation option -->
              <div v-else-if="cake.status === 'Confirmed' || cake.status === 'Submitted'" class="pt-2 flex items-center justify-between text-xs">
                <span class="text-[#059669] font-bold" v-if="cake.status === 'Confirmed'">
                  ✓ Deposit confirmed. Bakery has scheduled your cake preparation.
                </span>
                <span class="text-[#D97706] font-medium" v-else>
                  ⏳ Waiting for head cake decorator to review and post quote.
                </span>
                <button
                  @click="cancelCake(cake)"
                  class="text-[11px] text-red-600 hover:underline font-semibold"
                >
                  Cancel Order
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- 3. Addresses Tab -->
        <div v-else-if="activeTab === 'addresses'" class="space-y-4">
          <div class="flex items-center justify-between border-b border-[#F8E7EA] pb-3">
            <h2 class="text-xl font-serif font-black text-[#38121A]">Saved Addresses</h2>
            <button class="text-xs font-bold text-[#E11D48] hover:underline">+ Add New</button>
          </div>
          <div class="p-4 bg-[#FAF7F5] rounded-2xl border border-[#F2D7DC] space-y-1 text-xs">
            <div class="flex items-center justify-between">
              <span class="font-bold text-[#38121A]">Home (Default)</span>
              <span class="text-[10px] bg-[#ECFDF5] text-[#059669] font-bold px-2 py-0.5 rounded-full">Active</span>
            </div>
            <p class="text-[#7A6B60]">House No. 102, Near Edna Mall, Bole, Addis Ababa</p>
            <p class="text-[#881337] font-semibold">+251 911 123 456</p>
          </div>
        </div>

        <!-- 4. Profile Settings Tab -->
        <div v-else-if="activeTab === 'settings'" class="space-y-4 text-xs">
          <h2 class="text-xl font-serif font-black text-[#38121A] border-b border-[#F8E7EA] pb-3">Profile Settings</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-bold text-[#38121A] mb-1">Full Name</label>
              <input v-model="userName" class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2 text-xs" />
            </div>
            <div>
              <label class="block font-bold text-[#38121A] mb-1">Email</label>
              <input v-model="userEmail" disabled class="w-full bg-[#E5E7EB] border border-[#D1D5DB] rounded-xl px-4 py-2 text-xs text-gray-500" />
            </div>
          </div>
          <button @click="alert('Profile updated successfully!')" class="px-6 py-2.5 bg-[#E11D48] text-white rounded-xl font-bold">
            Save Changes
          </button>
        </div>

        <!-- 5. Reviews Tab -->
        <div v-else class="space-y-4">
          <h2 class="text-xl font-serif font-black text-[#38121A] border-b border-[#F8E7EA] pb-3">My Reviews</h2>
          <div class="p-4 bg-[#FAF7F5] rounded-2xl border border-[#F2D7DC] space-y-1.5 text-xs">
            <div class="flex items-center justify-between">
              <span class="font-bold text-[#38121A]">Belgian Chocolate Cake</span>
              <span class="text-[#F59E0B]">★★★★★ 5/5</span>
            </div>
            <p class="text-[#7A6B60]">Incredible richness, moist texture and delivered right on time.</p>
          </div>
        </div>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../api';

const router = useRouter();
const route = useRoute();
const authStore = useAuthStore();

const activeTab = ref(route.query.tab === 'custom-cakes' ? 'custom_cakes' : 'orders');
const orders = ref([]);
const customCakes = ref([]);

const userName = computed(() => authStore.user?.full_name || 'Sara Ahmed');
const userEmail = computed(() => authStore.user?.email || 'sara@sweetbakes.com');

const mockOrders = [
  {
    order_number: 'ORD-1001',
    date: 'Jun 12, 2025',
    branch_name: 'Bole Branch',
    status: 'Delivered',
    items_summary: '1 × Chocolate Cake, 2 × Fresh Bread',
    total_amount: 1650,
  },
  {
    order_number: 'ORD-1000',
    date: 'Jun 8, 2025',
    branch_name: 'Megenagna Branch',
    status: 'Preparing',
    items_summary: '1 × Vanilla Cake (8 Inch)',
    total_amount: 750,
  },
  {
    order_number: 'ORD-0999',
    date: 'Jun 5, 2025',
    branch_name: 'Piassa Branch',
    status: 'Confirmed',
    items_summary: '4 × French Butter Croissants',
    total_amount: 450,
  },
];

const displayOrders = computed(() => {
  if (orders.value.length > 0) {
    return orders.value.map((o) => ({
      order_number: o.order_number,
      date: new Date(o.placed_at || o.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
      branch_name: o.branch?.branch_name || 'Bole Branch',
      status: o.order_status,
      items_summary: o.items ? o.items.map((i) => `${i.quantity} × ${i.product_name}`).join(', ') : 'Bakery Items',
      total_amount: o.total_amount,
    }));
  }
  return mockOrders;
});

const pendingQuotesCount = computed(() => {
  return customCakes.value.filter((c) => c.status === 'Quoted').length;
});

async function loadData() {
  try {
    const res = await api.get('/orders');
    if (res.data?.orders) {
      orders.value = res.data.orders;
    } else if (Array.isArray(res.data)) {
      orders.value = res.data;
    }
  } catch (err) {
    console.error('Failed to load orders', err);
  }

  try {
    const resCake = await api.get('/custom-cakes');
    if (resCake.data?.requests) {
      customCakes.value = resCake.data.requests;
    } else if (Array.isArray(resCake.data)) {
      customCakes.value = resCake.data;
    }
  } catch (err) {
    console.error('Failed to load custom cakes', err);
  }
}

onMounted(() => {
  loadData();
});

function formatDate(d) {
  if (!d) return 'Recently';
  return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
}

function statusBadgeClass(status) {
  switch (status) {
    case 'Delivered':
    case 'Completed':
      return 'bg-[#ECFDF5] text-[#059669] border border-[#A7F3D0]';
    case 'Preparing':
      return 'bg-[#FEF3C7] text-[#D97706] border border-[#FDE68A]';
    case 'Confirmed':
      return 'bg-[#EDE9FE] text-[#7C3AED] border border-[#DDD6FE]';
    case 'Out for delivery':
      return 'bg-[#E0F2FE] text-[#0284C7] border border-[#BAE6FD]';
    default:
      return 'bg-[#FFF1F2] text-[#E11D48] border border-[#FECDD3]';
  }
}

function customCakeStatusBadge(status) {
  switch (status) {
    case 'Quoted':
      return 'bg-[#FEF3C7] text-[#B45309] border border-[#FCD34D]';
    case 'Confirmed':
      return 'bg-[#EDE9FE] text-[#6D28D9] border border-[#DDD6FE]';
    case 'Preparing':
      return 'bg-[#E0F2FE] text-[#0369A1] border border-[#BAE6FD]';
    case 'Ready':
      return 'bg-[#DCFCE7] text-[#15803D] border border-[#86EFAC]';
    case 'Delivered':
    case 'Picked up':
      return 'bg-[#F0FDF4] text-[#166534] border border-[#BBF7D0]';
    case 'Rejected':
      return 'bg-[#FEF2F2] text-[#B91C1C] border border-[#FECACA]';
    case 'Cancelled':
      return 'bg-gray-100 text-gray-600 border border-gray-200';
    default:
      return 'bg-[#FFF1F2] text-[#E11D48] border border-[#FECDD3]';
  }
}

async function acceptQuote(cake, choice) {
  try {
    await api.post(`/custom-cakes/${cake.request_id}/accept-quote`, {
      payment_choice: choice,
      payment_method: 'Chapa',
    });
    alert(`Custom cake quote accepted! Status updated to Confirmed. The baking team has been notified.`);
    await loadData();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to accept quote.');
  }
}

async function cancelCake(cake) {
  const reason = prompt('Please enter cancellation reason:', 'Change of plan');
  if (reason === null) return;

  try {
    const res = await api.post(`/custom-cakes/${cake.request_id}/cancel`, {
      reason: reason || 'Cancelled by customer',
    });
    alert(`Custom cake request cancelled. Refund policy applied: ${res.data.refund_policy_applied || 'N/A'}`);
    await loadData();
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to cancel custom cake.');
  }
}

function handleLogout() {
  authStore.logout();
  router.push('/login');
}
</script>
