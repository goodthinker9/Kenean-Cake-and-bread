<template>
  <div class="space-y-6 max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <h2 class="text-2xl font-serif font-bold text-[#2A1D15]">Settings</h2>
      <div class="flex items-center space-x-3">
        <button
          @click="loadSettings"
          class="px-4 py-2 text-sm font-medium bg-white border border-[#D8CDC0] hover:bg-[#F4ECE3] text-[#4A3B32] rounded-lg transition shadow-sm"
        >
          Cancel
        </button>
        <button
          @click="saveSettings"
          :disabled="saving"
          class="px-4 py-2 text-sm font-medium bg-[#BA5420] hover:bg-[#9E4314] text-white rounded-lg transition shadow-sm disabled:opacity-50"
        >
          {{ saving ? 'Saving...' : 'Save settings' }}
        </button>
      </div>
    </div>

    <!-- Alert Banner -->
    <div v-if="successMsg" class="p-3 bg-[#EBF7EB] border border-[#C5E9C6] rounded-lg text-xs text-[#1E7422]">
      {{ successMsg }}
    </div>

    <!-- Two-Column Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
      <!-- Left Column: Orders, Transfers, Custom Cakes -->
      <div class="space-y-6">
        <!-- Orders and stock -->
        <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
          <h3 class="font-semibold text-base text-[#2A1D15]">Orders and stock</h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Unpaid order reservation (minutes)</label>
              <input
                v-model="form.unpaid_reservation_minutes"
                type="number"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Default reorder level</label>
              <input
                v-model="form.default_reorder_level"
                type="number"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Pay on pickup or delivery</label>
              <select
                v-model="form.pay_on_pickup_delivery"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              >
                <option value="Enabled">Enabled</option>
                <option value="Disabled">Disabled</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Cancellation allowed until</label>
              <select
                v-model="form.cancellation_allowed_until"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              >
                <option value="Preparing starts">Preparing starts</option>
                <option value="Confirmed">Confirmed</option>
                <option value="Ready">Ready</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Transfers -->
        <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
          <h3 class="font-semibold text-base text-[#2A1D15]">Transfers</h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Super Admin approval required above (units)</label>
              <input
                v-model="form.approval_threshold_units"
                type="number"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              />
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Unreceived reminder after (hours)</label>
              <input
                v-model="form.unreceived_reminder_hours"
                type="number"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              />
            </div>
          </div>
        </div>

        <!-- Custom cakes -->
        <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
          <h3 class="font-semibold text-base text-[#2A1D15]">Custom cakes</h3>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Default deposit</label>
              <select
                v-model="form.default_deposit_percent"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              >
                <option value="30">30%</option>
                <option value="50">50%</option>
                <option value="100">100%</option>
              </select>
            </div>
            <div>
              <label class="block text-xs font-medium text-[#7D6E64] mb-1">Deposit refundable</label>
              <select
                v-model="form.deposit_refundable"
                class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
              >
                <option value="Before branch starts work">Before branch starts work</option>
                <option value="Non-refundable">Non-refundable</option>
                <option value="Within 24 hours">Within 24 hours</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column: Payments, Localization, Security -->
      <div class="space-y-6">
        <!-- Payments -->
        <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
          <h3 class="font-semibold text-base text-[#2A1D15]">Payments</h3>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Gateways</label>
            <select
              v-model="form.gateways"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option value="Chapa, Telebirr">Chapa, Telebirr</option>
              <option value="Chapa only">Chapa only</option>
              <option value="Telebirr only">Telebirr only</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Webhook signing secret</label>
            <input
              v-model="form.webhook_secret"
              type="password"
              placeholder="••••••••••••"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            />
          </div>
        </div>

        <!-- Language and currency -->
        <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
          <h3 class="font-semibold text-base text-[#2A1D15]">Language and currency</h3>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Languages</label>
            <select
              v-model="form.languages"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option value="English, Amharic">English, Amharic</option>
              <option value="English only">English only</option>
              <option value="Amharic only">Amharic only</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Currency</label>
            <select
              v-model="form.currency"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option value="ETB">ETB</option>
              <option value="USD">USD</option>
            </select>
          </div>
        </div>

        <!-- Security -->
        <div class="bg-white border border-[#E9E0D4] rounded-xl shadow-sm p-6 space-y-4">
          <h3 class="font-semibold text-base text-[#2A1D15]">Security</h3>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Admin two-factor sign-in</label>
            <select
              v-model="form.admin_2fa"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option value="Optional">Optional</option>
              <option value="Enforced">Enforced</option>
              <option value="Disabled">Disabled</option>
            </select>
          </div>

          <div>
            <label class="block text-xs font-medium text-[#7D6E64] mb-1">Backups</label>
            <select
              v-model="form.backups"
              class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-lg px-3 py-2 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            >
              <option value="Daily, kept 30 days">Daily, kept 30 days</option>
              <option value="Weekly, kept 60 days">Weekly, kept 60 days</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '../../api';

const form = ref({
  unpaid_reservation_minutes: 15,
  default_reorder_level: 10,
  pay_on_pickup_delivery: 'Enabled',
  cancellation_allowed_until: 'Preparing starts',
  approval_threshold_units: 200,
  unreceived_reminder_hours: 24,
  default_deposit_percent: '50',
  deposit_refundable: 'Before branch starts work',
  gateways: 'Chapa, Telebirr',
  webhook_secret: 'whsec_live_99a8b7c6d5e4f3a2b1',
  languages: 'English, Amharic',
  currency: 'ETB',
  admin_2fa: 'Optional',
  backups: 'Daily, kept 30 days',
});

const saving = ref(false);
const successMsg = ref('');

const loadSettings = async () => {
  try {
    const res = await api.get('/admin/settings');
    Object.assign(form.value, res.data);
  } catch (err) {
    console.error('Failed to load settings', err);
  }
};

const saveSettings = async () => {
  saving.value = true;
  successMsg.value = '';
  try {
    await api.post('/admin/settings', form.value);
    successMsg.value = 'Settings saved successfully.';
    setTimeout(() => { successMsg.value = ''; }, 3000);
  } catch {
    // The API interceptor displays the request error.
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  loadSettings();
});
</script>
