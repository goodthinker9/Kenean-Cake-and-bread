<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Notifications</h2>
      <button
        @click="markAllAsRead"
        class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
      >
        Mark all as read
      </button>
    </div>

    <!-- Category Tabs -->
    <div class="flex items-center space-x-2 overflow-x-auto pb-1">
      <button
        @click="setCategory('All')"
        class="px-4 py-1.5 text-xs font-semibold rounded-full transition"
        :class="activeCategory === 'All' ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
      >
        All ({{ unreadCount }})
      </button>
      <button
        v-for="cat in ['Stock', 'Transfers', 'Discrepancies', 'Orders', 'System']"
        :key="cat"
        @click="setCategory(cat)"
        class="px-4 py-1.5 text-xs font-semibold rounded-full transition"
        :class="activeCategory === cat ? 'bg-[#984518] text-white shadow-sm' : 'bg-white border border-[#DDD2C5] text-[#5A4B41] hover:bg-[#F2EAE0]'"
      >
        {{ cat }}
      </button>
    </div>

    <!-- Main Content: Notifications List + Alert Rules -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Notifications List (Col span 2) -->
      <div class="lg:col-span-2 space-y-3">
        <div
          v-for="n in notifications"
          :key="n.notification_id"
          class="bg-white border border-[#E9E0D4] rounded-xl p-4 shadow-sm flex items-center justify-between transition hover:border-[#D0C2B0]"
          :class="!n.is_read ? 'border-l-4 border-l-[#BA5420]' : ''"
        >
          <div class="flex items-start space-x-3.5">
            <span
              class="px-2 py-0.5 text-[11px] font-semibold rounded-md shrink-0 uppercase tracking-wide mt-0.5"
              :class="{
                'bg-[#FDEAEA] text-[#C62828] border border-[#FFCDD2]': n.category === 'Discrepancies',
                'bg-[#FFF3E0] text-[#E65100] border border-[#FFE0B2]': n.category === 'Transfers' || n.category === 'Request',
                'bg-[#FFF8E6] text-[#A67503] border border-[#FEE39E]': n.category === 'Stock',
                'bg-[#EBF3FB] text-[#1565C0] border border-[#BBDEFB]': n.category === 'Orders',
                'bg-[#F3EFE9] text-[#7A6B60] border border-[#DDD3C7]': n.category === 'System'
              }"
            >
              {{ n.category === 'Discrepancies' ? 'Discrepancy' : n.category === 'Transfers' ? 'Transfer' : n.category === 'Orders' ? 'Order' : n.category }}
            </span>

            <div class="space-y-1">
              <div class="text-sm font-semibold text-[#2A1D15]">{{ n.title }}</div>
              <div class="text-xs text-[#6A5A50] leading-relaxed">{{ n.message }}</div>
            </div>
          </div>

          <div class="flex items-center space-x-3 shrink-0 ml-4">
            <span class="text-xs text-[#8A7B71]">{{ n.time_ago }}</span>
            <router-link
              v-if="n.link"
              :to="n.link"
              class="px-3 py-1.5 text-xs font-semibold bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-2xs"
            >
              Open
            </router-link>
          </div>
        </div>
      </div>

      <!-- Right Side Card: Alert rules -->
      <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
        <h3 class="font-semibold text-base text-[#2A1D15]">Alert rules</h3>

        <form @submit.prevent="saveAlertRules" class="space-y-4">
          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Low stock goes to</label>
            <select
              v-model="alertRules.low_stock_goes_to"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option value="Branch Admin and Main Manager">Branch Admin and Main Manager</option>
              <option value="Super Admin only">Super Admin only</option>
              <option value="All managers">All managers</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Unreceived transfer reminder</label>
            <select
              v-model="alertRules.unreceived_reminder"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option value="After 24 hours">After 24 hours</option>
              <option value="After 12 hours">After 12 hours</option>
              <option value="After 48 hours">After 48 hours</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#65554B] mb-1">Daily summary email</label>
            <select
              v-model="alertRules.daily_summary"
              class="w-full border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] bg-[#FDFBF7] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option value="Owner, 20:00">Owner, 20:00</option>
              <option value="Owner & Managers, 19:00">Owner & Managers, 19:00</option>
              <option value="Disabled">Disabled</option>
            </select>
          </div>

          <button
            type="submit"
            class="w-full py-2.5 px-4 bg-[#BA5420] hover:bg-[#9E4314] text-white font-medium text-xs rounded-lg transition shadow-sm"
          >
            Save alert rules
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

const activeCategory = ref('All');
const unreadCount = ref(5);
const notifications = ref([]);

const alertRules = ref({
  low_stock_goes_to: 'Branch Admin and Main Manager',
  unreceived_reminder: 'After 24 hours',
  daily_summary: 'Owner, 20:00',
});

const loadNotifications = async () => {
  try {
    const res = await api.get('/admin/notifications', {
      params: { category: activeCategory.value }
    });
    notifications.value = res.data.notifications;
    unreadCount.value = res.data.unread_count;
    if (res.data.alert_rules) {
      alertRules.value = res.data.alert_rules;
    }
  } catch (err) {
    console.error('Failed to load notifications', err);
  }
};

const setCategory = (cat) => {
  activeCategory.value = cat;
  loadNotifications();
};

const markAllAsRead = async () => {
  try {
    await api.post('/admin/notifications/mark-read');
    await loadNotifications();
  } catch (err) {
    console.error(err);
  }
};

const saveAlertRules = async () => {
  try {
    await api.post('/admin/notifications/alert-rules', alertRules.value);
    toast.success('Alert rules saved successfully.');
  } catch {
    // The API interceptor displays the request error.
  }
};

onMounted(() => {
  loadNotifications();
});
</script>
