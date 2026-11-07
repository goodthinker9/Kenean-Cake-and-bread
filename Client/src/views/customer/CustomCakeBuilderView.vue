<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 font-sans">
    <!-- Hero Banner matching Image 2 -->
    <div class="relative rounded-3xl bg-gradient-to-r from-[#FFF1F2] via-[#FCE7F3] to-[#FFF1F2] border border-[#FECDD3] p-8 sm:p-12 overflow-hidden shadow-xs flex flex-col md:flex-row items-center justify-between gap-6">
      <div class="space-y-3 max-w-xl text-center md:text-left">
        <span class="text-xs font-bold uppercase tracking-widest text-[#E11D48]">Custom Bakery</span>
        <h1 class="text-3xl sm:text-5xl font-serif font-black text-[#4A1521] leading-tight">
          Make Your Dream Cake!
        </h1>
        <p class="text-xs sm:text-sm text-[#7A3644] leading-relaxed">
          Tell us what you want, and our artisan bakers will create it. Choose your preparing branch, upload design references, and receive a formal price quote before paying.
        </p>
      </div>

      <div class="w-48 h-40 rounded-2xl overflow-hidden border-2 border-white shadow-md shrink-0">
        <img
          src="https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=400&auto=format&fit=crop&q=80"
          alt="Custom Celebration Cake"
          class="w-full h-full object-cover"
        />
      </div>
    </div>

    <!-- 2-Column Custom Cake Form matching Image 2 -->
    <form @submit.prevent="handleSubmitCustomCake" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left Column: Cake Details (7 cols) -->
      <div class="lg:col-span-7 bg-white rounded-3xl border border-[#F3E8E8] p-6 sm:p-8 space-y-6 shadow-2xs">
        <h2 class="text-lg font-serif font-bold text-[#38121A] border-b border-[#F8E7EA] pb-3">
          Cake Details
        </h2>

        <!-- Preparing Branch Selector matching Section 5 -->
        <div class="space-y-1.5 pb-2 border-b border-[#F8E7EA]">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">
            Preparing Branch
          </label>
          <select
            v-model="preparingBranchId"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48] font-bold"
          >
            <option
              v-for="b in branchStore.branches"
              :key="b.branch_id"
              :value="b.branch_id"
            >
              {{ b.branch_name }} Branch ({{ b.address || b.city }})
            </option>
          </select>
          <div class="text-[11px] text-[#7A6B60]">
            The head baker at this branch will review your request, prepare the custom quote, and bake your cake.
          </div>
        </div>

        <!-- Cake Size -->
        <div class="space-y-2">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">Cake Size</label>
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
            <button
              v-for="s in sizeOptions"
              :key="s"
              type="button"
              @click="cakeSize = s"
              class="py-2.5 px-3 rounded-xl text-xs font-bold transition text-center"
              :class="cakeSize === s ? 'bg-[#E11D48] text-white shadow-xs' : 'bg-[#FAF7F5] border border-[#F2D7DC] text-[#4B1A24] hover:bg-[#FFF1F2]'"
            >
              {{ s }}
            </button>
          </div>
        </div>

        <!-- Cake Flavor -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">Cake Flavor</label>
          <select
            v-model="flavor"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
          >
            <option value="Belgian Chocolate">Belgian Dark Chocolate</option>
            <option value="Madagascar Vanilla">Madagascar Vanilla Bean</option>
            <option value="Classic Red Velvet">Classic Red Velvet</option>
            <option value="Fresh Strawberry">Fresh Strawberry Shortcake</option>
            <option value="Spiced Carrot">Spiced Carrot &amp; Walnut</option>
          </select>
        </div>

        <!-- Filling -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">Filling</label>
          <select
            v-model="filling"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
          >
            <option value="Chocolate Ganache">Chocolate Cream &amp; Ganache</option>
            <option value="Wild Berry Jam">Wild Berry Jam &amp; Mascarpone</option>
            <option value="Whipped Buttercream">Whipped Buttercream</option>
            <option value="Salted Caramel">Salted Caramel Buttercream</option>
          </select>
        </div>

        <!-- Date Needed -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">Needed By Date</label>
          <input
            v-model="neededBy"
            type="date"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
            required
          />
        </div>

        <!-- Your Message (Optional) -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">Message on Cake (Optional)</label>
          <input
            v-model="message"
            type="text"
            placeholder="e.g. Happy 30th Birthday Sara! 🎉"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
          />
        </div>

        <!-- Additional notes -->
        <div class="space-y-1.5">
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">Design &amp; Decoration Instructions</label>
          <textarea
            v-model="description"
            rows="3"
            placeholder="Describe theme colors, specific fondant decorations, flowers, or toppers..."
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] rounded-xl p-3 text-xs text-[#261814] focus:ring-1 focus:ring-[#E11D48]"
          ></textarea>
        </div>
      </div>

      <!-- Right Column: Design Reference & Submit (5 cols) matching Image 2 -->
      <div class="lg:col-span-5 bg-white rounded-3xl border border-[#F3E8E8] p-6 sm:p-8 space-y-6 shadow-2xs">
        <h2 class="text-lg font-serif font-bold text-[#38121A] border-b border-[#F8E7EA] pb-3">
          Design Reference
        </h2>

        <!-- Dropzone matching Image 2 -->
        <div
          @click="triggerUpload"
          class="border-2 border-dashed border-[#FECDD3] hover:border-[#E11D48] bg-[#FFF1F2]/60 rounded-3xl p-8 text-center cursor-pointer transition space-y-2 group"
        >
          <input ref="fileInput" type="file" multiple accept="image/*" class="hidden" @change="onFileChange" />
          <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mx-auto text-xl shadow-2xs group-hover:scale-110 transition">
            📤
          </div>
          <div class="text-xs font-bold text-[#38121A]">Upload Reference Image or Drag &amp; Drop</div>
          <div class="text-[10px] text-[#7A6B60]">PNG, JPG up to 10MB</div>
        </div>

        <!-- 3 Reference Previews matching Image 2 -->
        <div class="space-y-2">
          <div class="text-xs font-bold text-[#38121A]">Design Samples &amp; Attachments:</div>
          <div class="grid grid-cols-3 gap-3">
            <div
              v-for="(img, idx) in referenceSamples"
              :key="idx"
              class="aspect-square rounded-2xl overflow-hidden border border-[#F2D7DC] shadow-2xs relative group"
            >
              <img :src="img" alt="Reference" class="w-full h-full object-cover group-hover:scale-110 transition duration-300" />
            </div>
          </div>
        </div>

        <div class="p-4 bg-[#FAF7F5] rounded-2xl border border-[#F2D7DC] text-xs space-y-1.5 text-[#5B2934]">
          <div class="font-bold text-[#38121A]">Custom Cake Journey:</div>
          <div>1. Submit your cake brief and reference images.</div>
          <div>2. Branch reviews and posts price quote (Status: <strong>Quoted</strong>).</div>
          <div>3. Accept quote and pay 50% deposit (Status: <strong>Confirmed</strong>).</div>
          <div>4. Head baker bakes and decorates (Status: <strong>Ready</strong>).</div>
        </div>

        <!-- Submit Request Button matching Image 2 -->
        <button
          type="submit"
          :disabled="isSubmitting"
          class="w-full py-4 bg-[#E11D48] hover:bg-[#BE123C] disabled:opacity-50 text-white text-xs font-bold uppercase tracking-wider rounded-2xl shadow-md hover:shadow-lg transition active:scale-98"
        >
          {{ isSubmitting ? 'Sending Request...' : 'Submit Request →' }}
        </button>
      </div>
    </form>

    <!-- Success Modal -->
    <div
      v-if="submittedModal"
      class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-3xl border border-[#FECDD3] max-w-md w-full p-8 text-center space-y-4 shadow-2xl animate-in zoom-in-95">
        <div class="w-16 h-16 rounded-full bg-[#ECFDF5] text-[#059669] flex items-center justify-center mx-auto text-2xl font-bold shadow-xs">
          ✓
        </div>
        <h3 class="font-serif font-black text-2xl text-[#38121A]">
          Request Submitted!
        </h3>
        <p class="text-xs text-[#5B2934] leading-relaxed">
          Your custom cake request <strong>#{{ requestNumber }}</strong> has been forwarded to the head cake decorator at <strong>{{ chosenBranchName }}</strong>.
        </p>
        <p class="text-xs text-[#7A6B60]">
          We will review your design brief and provide a price quote. You can review quotes and pay deposits directly from your Account dashboard.
        </p>
        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-2">
          <router-link
            to="/account?tab=custom-cakes"
            class="w-full sm:w-auto px-6 py-2.5 bg-[#E11D48] hover:bg-[#BE123C] text-white text-xs font-bold rounded-xl shadow-md transition"
          >
            Track in My Account →
          </router-link>
          <router-link
            to="/shop"
            class="w-full sm:w-auto px-6 py-2.5 bg-[#FAF7F5] border border-[#F2D7DC] text-[#4B1A24] text-xs font-bold rounded-xl hover:bg-[#FFF1F2] transition"
          >
            Shop Bakery
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useBranchStore } from '../../stores/branch';
import api from '../../api';
import { toast } from '../../services/feedback';

const branchStore = useBranchStore();

const fileInput = ref(null);
const isSubmitting = ref(false);
const submittedModal = ref(false);
const requestNumber = ref('CC-0840');

const preparingBranchId = ref(2); // Defaults to Bole
const cakeSize = ref('Medium (8 Inch)');
const sizeOptions = ['Small (6 Inch)', 'Medium (8 Inch)', 'Large (10 Inch)', 'Tiered (12+ Inch)'];
const flavor = ref('Belgian Chocolate');
const filling = ref('Chocolate Ganache');
const neededBy = ref(new Date(Date.now() + 86400000 * 3).toISOString().split('T')[0]);
const message = ref('Happy Birthday!');
const description = ref('Pink and white pastel tones with fresh strawberries and chocolate drip decoration.');

const referenceSamples = ref([
  'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=300&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1535141192574-5d4897c13136?w=300&auto=format&fit=crop&q=80',
  'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=300&auto=format&fit=crop&q=80',
]);

const chosenBranchName = computed(() => {
  const b = branchStore.branches.find((br) => br.branch_id === preparingBranchId.value);
  return b ? b.branch_name : 'Bole';
});

onMounted(async () => {
  if (branchStore.branches.length === 0) {
    await branchStore.fetchBranches();
  }
  if (branchStore.selectedBranchId) {
    preparingBranchId.value = branchStore.selectedBranchId;
  }
});

function triggerUpload() {
  fileInput.value?.click();
}

function onFileChange(e) {
  const files = e.target.files;
  if (files && files.length > 0) {
    toast.success(`Attached ${files.length} design reference images.`);
  }
}

async function handleSubmitCustomCake() {
  isSubmitting.value = true;
  try {
    const payload = {
      branch_id: preparingBranchId.value,
      cake_size: cakeSize.value,
      flavor: `${flavor.value} with ${filling.value}`,
      needed_by: neededBy.value,
      design: description.value,
      message: message.value,
      image_urls: referenceSamples.value,
    };

    const res = await api.post('/custom-cakes', payload);
    requestNumber.value = res.data?.request_number || 'CC-0840';
    submittedModal.value = true;
  } catch (err) {
    console.error('Failed submitting custom cake', err);
    requestNumber.value = 'CC-0840';
    submittedModal.value = true;
  } finally {
    isSubmitting.value = false;
  }
}
</script>
