<template>
  <div class="max-w-3xl mx-auto px-4 sm:px-6 py-8 space-y-6">
    <div class="text-center space-y-2">
      <span class="text-xs uppercase tracking-wider font-bold text-[#BA5420]">Celebrations & Weddings</span>
      <h1 class="text-3xl sm:text-4xl font-serif font-bold text-[#2A1D15]">Request a Bespoke Custom Cake</h1>
      <p class="text-xs text-[#7A6B60] max-w-lg mx-auto leading-relaxed">
        Our pastry chefs craft personalized celebratory cakes. Submit your desired design, flavor, and needed date to receive a customized quote from your selected branch.
      </p>
    </div>

    <!-- Alert / Message -->
    <div v-if="successMsg" class="p-4 bg-[#EBF7EB] border border-[#C5E9C6] rounded-2xl text-xs text-[#1E7422] space-y-1">
      <div class="font-bold">{{ successMsg }}</div>
      <div>Our branch admin will review your design and provide a quote. Track the progress anytime on the staff dashboard or by request number.</div>
    </div>

    <form @submit.prevent="submitCakeRequest" class="bg-white p-6 sm:p-8 rounded-2xl border border-[#E9E0D4] shadow-xs space-y-5">
      <!-- 1. Preparing Branch -->
      <div>
        <label class="block text-xs font-bold text-[#65554B] mb-1">Select Preparing Branch</label>
        <select
          v-model="form.branch_id"
          class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-4 py-2.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
          required
        >
          <option v-for="b in branches" :key="b.branch_id" :value="b.branch_id">
            {{ b.branch_name }} — {{ b.address }}
          </option>
        </select>
      </div>

      <!-- 2. Size and Flavor -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-[#65554B] mb-1">Cake Size / Servings</label>
          <select
            v-model="form.cake_size"
            class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-4 py-2.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            required
          >
            <option value="Small">Small (6 - 8 servings)</option>
            <option value="Medium">Medium (12 - 16 servings)</option>
            <option value="Large">Large (20 - 28 servings)</option>
            <option value="Tiered">Tiered (30+ celebratory servings)</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold text-[#65554B] mb-1">Sponge & Filling Flavor</label>
          <select
            v-model="form.flavor"
            class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-4 py-2.5 text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
            required
          >
            <option value="Chocolate">Belgian Dark Chocolate Ganache</option>
            <option value="Vanilla">Madagascar Vanilla Buttercream</option>
            <option value="Red Velvet">Classic Red Velvet & Cream Cheese</option>
            <option value="Mocha">Ethiopian Mocha Coffee Crunch</option>
            <option value="Other">Custom Flavor Blend</option>
          </select>
        </div>
      </div>

      <!-- 3. Message on Cake & Date Needed -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block text-xs font-bold text-[#65554B] mb-1">Message on Cake (Optional)</label>
          <input
            v-model="form.message"
            type="text"
            placeholder="e.g. Happy 30th Birthday Aster!"
            class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-4 py-2.5 text-xs text-[#2D231D]"
          />
        </div>

        <div>
          <label class="block text-xs font-bold text-[#65554B] mb-1">Date Needed By</label>
          <input
            v-model="form.needed_by"
            type="date"
            :min="minDate"
            class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-4 py-2.5 text-xs text-[#2D231D]"
            required
          />
        </div>
      </div>

      <!-- 4. Design & Special Notes -->
      <div>
        <label class="block text-xs font-bold text-[#65554B] mb-1">Design Notes & Theme Specifications</label>
        <textarea
          v-model="form.design"
          rows="3"
          placeholder="Describe theme colors, piping styles, toppers, edible florals or specific details..."
          class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-4 py-2 text-xs text-[#2D231D]"
          required
        ></textarea>
      </div>

      <!-- 5. Reference Image URL -->
      <div>
        <label class="block text-xs font-bold text-[#65554B] mb-1">Reference Image Link (Optional)</label>
        <input
          v-model="referenceImageUrl"
          type="url"
          placeholder="https://images.unsplash.com/..."
          class="w-full bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-4 py-2.5 text-xs text-[#2D231D]"
        />
      </div>

      <div class="p-4 bg-[#FCFAF7] border border-[#EFE8DF] rounded-xl text-xs text-[#7A6B60] leading-relaxed">
        <strong>Deposit Policy:</strong> Once our bakery manager quotes a price, a 50% deposit confirms your date. Deposit becomes non-refundable once custom baking work commences.
      </div>

      <button
        type="submit"
        :disabled="submitting"
        class="w-full py-3.5 bg-[#BA5420] hover:bg-[#9E4314] text-white font-bold text-sm rounded-xl transition shadow-md disabled:opacity-50"
      >
        {{ submitting ? 'Submitting Design Request...' : 'Submit Custom Cake Request' }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useBranchStore } from '../../stores/branch';
import api from '../../api';

const branchStore = useBranchStore();
const branches = ref([]);

const tomorrow = new Date();
tomorrow.setDate(tomorrow.getDate() + 1);
const minDate = tomorrow.toISOString().split('T')[0];

const form = ref({
  branch_id: 2, // Bole
  cake_size: 'Medium',
  flavor: 'Chocolate',
  message: 'Happy 30th Birthday Aster!',
  needed_by: minDate,
  design: 'Two tiers with rustic textured buttercream, edible 24k gold leaf flakes, and fresh maroon roses.',
});

const referenceImageUrl = ref('https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=600&auto=format&fit=crop&q=80');
const submitting = ref(false);
const successMsg = ref('');

const loadBranches = async () => {
  try {
    const res = await api.get('/branches');
    branches.value = res.data;
    if (branchStore.selectedBranchId) {
      form.value.branch_id = branchStore.selectedBranchId;
    }
  } catch (e) {}
};

const submitCakeRequest = async () => {
  submitting.value = true;
  successMsg.value = '';

  try {
    const payload = {
      ...form.value,
      image_urls: referenceImageUrl.value ? [referenceImageUrl.value] : [],
    };
    const res = await api.post('/custom-cakes', payload);
    successMsg.value = `Custom Cake Request ${res.data.request_number} submitted successfully!`;
  } catch (err) {
    alert(err.response?.data?.message || 'Failed to submit cake request');
  } finally {
    submitting.value = false;
  }
};

onMounted(() => {
  loadBranches();
});
</script>
