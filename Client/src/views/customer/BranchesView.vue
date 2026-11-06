<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8 font-sans">
    <!-- Breadcrumb / Header Step indicator -->
    <div class="space-y-2">
      <div class="text-[11px] font-bold uppercase tracking-widest text-[#8C7D73]">Step 1 of 3</div>
      <h1 class="text-3xl sm:text-4xl font-serif font-bold text-[#2A1D15]">
        Choose your branch
      </h1>
      <p class="text-xs sm:text-sm text-[#6A5A50] max-w-2xl leading-relaxed">
        Stock, prices and delivery areas belong to the branch you pick. We will never move your order to a different branch without asking you.
      </p>
    </div>

    <!-- Search & Filter Controls -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white p-3.5 rounded-2xl border border-[#E7DFD5] shadow-xs">
      <div class="relative flex-1">
        <svg class="w-4 h-4 text-[#9C8B7E] absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Search by branch name or city"
          class="w-full pl-10 pr-4 py-2 bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl text-xs text-[#2D231D] focus:ring-1 focus:ring-[#BA5420]"
        />
      </div>

      <div class="flex items-center space-x-2">
        <select
          v-model="cityFilter"
          class="bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-3 py-2 text-xs text-[#4A3B32] font-semibold focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">All cities</option>
          <option value="Addis Ababa">Addis Ababa</option>
        </select>

        <select
          v-model="fulfillmentFilter"
          class="bg-[#FDFBF7] border border-[#D8CDC0] rounded-xl px-3 py-2 text-xs text-[#4A3B32] font-semibold focus:ring-1 focus:ring-[#BA5420]"
        >
          <option value="all">Delivery and pickup</option>
          <option value="delivery">Delivery only</option>
          <option value="pickup">Pickup only</option>
        </select>
      </div>
    </div>

    <!-- Grid: Branch Cards on Left, Map & Advice on Right (Mockup Page 3) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
      <!-- Left: Branch List (7 cols) -->
      <div class="lg:col-span-7 space-y-4">
        <div
          v-for="branch in filteredBranches"
          :key="branch.branch_id"
          class="bg-white rounded-2xl border transition p-6 relative shadow-2xs hover:shadow-xs"
          :class="branchStore.selectedBranchId === branch.branch_id ? 'border-[#BA5420] ring-1 ring-[#BA5420]' : 'border-[#E7DFD5]'"
        >
          <!-- Top Row: Name, Code & Status -->
          <div class="flex items-start justify-between">
            <div class="space-y-1">
              <div class="flex items-center space-x-2.5">
                <h3 class="text-lg font-serif font-bold text-[#2A1D15]">
                  {{ branch.branch_name }}
                </h3>
                <span class="px-2 py-0.5 rounded text-[11px] font-semibold" :class="branch.is_main ? 'bg-[#F2EAE0] text-[#7A4B2A]' : 'bg-[#F2EAE0] text-[#6A5A50]'">
                  {{ branch.is_main ? 'Main' : `Branch ${String(branch.branch_id).padStart(2, '0')}` }}
                </span>
              </div>
              <p class="text-xs text-[#7A6B60] leading-snug">
                {{ branch.address_line1 || branch.city }}, Addis Ababa
              </p>
              <p class="text-xs text-[#8A7B70]">
                {{ branch.phone }} &middot; {{ branch.opening_hours || '07:00 - 20:00' }}
              </p>
            </div>

            <!-- Open Status Badge -->
            <span class="inline-flex items-center space-x-1.5 text-xs font-semibold text-[#2E7D32] bg-[#E8F5E9] px-2.5 py-1 rounded-full">
              <span class="w-2 h-2 rounded-full bg-[#2E7D32]"></span>
              <span>Open now</span>
            </span>
          </div>

          <!-- Capability Tags -->
          <div class="flex items-center space-x-2 mt-4">
            <span class="px-2.5 py-1 rounded-full bg-[#FBF8F3] border border-[#E7DFD5] text-[11px] font-medium text-[#5A4B41]">
              Delivery
            </span>
            <span class="px-2.5 py-1 rounded-full bg-[#FBF8F3] border border-[#E7DFD5] text-[11px] font-medium text-[#5A4B41]">
              Pickup
            </span>
            <span class="px-2.5 py-1 rounded-full bg-[#FBF8F3] border border-[#E7DFD5] text-[11px] font-medium text-[#5A4B41]">
              Custom cakes
            </span>
          </div>

          <!-- Bottom Action Row -->
          <div class="flex items-center space-x-3 mt-5 pt-4 border-t border-[#F2EAE0]">
            <button
              v-if="branchStore.selectedBranchId === branch.branch_id"
              class="px-5 py-2.5 bg-[#BA5420] text-white text-xs font-bold rounded-xl shadow-xs cursor-default flex items-center space-x-1.5"
            >
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
              </svg>
              <span>Selected: shop here</span>
            </button>
            <button
              v-else
              @click="selectBranch(branch)"
              class="px-5 py-2.5 bg-[#BA5420] hover:bg-[#9E4314] text-white text-xs font-bold rounded-xl transition shadow-xs"
            >
              Choose this branch
            </button>

            <!-- Delivery Areas Button with Modal / Drawer -->
            <button
              @click="showZonesFor(branch)"
              class="px-4 py-2.5 bg-white border border-[#DDD2C5] hover:bg-[#F8F4EE] text-[#4A3B32] text-xs font-semibold rounded-xl transition shadow-2xs"
            >
              Delivery areas
            </button>
          </div>
        </div>
      </div>

      <!-- Right: Illustrative Map & Helper Advice Box (5 cols) -->
      <div class="lg:col-span-5 space-y-6">
        <!-- Illustrative Map Card matching Page 3 -->
        <div class="bg-white rounded-2xl border border-[#E7DFD5] p-5 shadow-2xs space-y-3">
          <div class="relative w-full h-72 rounded-xl bg-[#F4ECE3] overflow-hidden border border-[#DDD2C5]">
            <!-- Grid Lines pattern representing map streets -->
            <svg class="absolute inset-0 w-full h-full opacity-35" xmlns="http://www.w3.org/2000/svg">
              <defs>
                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                  <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#BA5420" stroke-width="0.8" />
                </pattern>
              </defs>
              <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>

            <!-- Illustrated Arterial roads -->
            <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
              <path d="M 20 220 Q 150 140 380 90" fill="none" stroke="#E1BA9B" stroke-width="6" />
              <path d="M 120 20 Q 180 150 240 260" fill="none" stroke="#E1BA9B" stroke-width="5" />
              <path d="M 260 40 Q 230 180 340 240" fill="none" stroke="#E1BA9B" stroke-width="4" />
            </svg>

            <!-- Branch Pins matching Page 3 locations -->
            <!-- Bole Pin (Orange) -->
            <div
              @click="selectBranchById(2)"
              class="absolute top-[35%] left-[62%] -translate-x-1/2 -translate-y-1/2 cursor-pointer group"
            >
              <div class="w-7 h-7 rounded-full bg-[#BA5420] text-white flex items-center justify-center shadow-md font-bold text-xs ring-4 ring-[#BA5420]/30 transition group-hover:scale-110">
                1
              </div>
              <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 bg-[#1C130D] text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow whitespace-nowrap">
                Bole
              </span>
            </div>

            <!-- Megenagna Pin -->
            <div
              @click="selectBranchById(3)"
              class="absolute top-[65%] left-[78%] -translate-x-1/2 -translate-y-1/2 cursor-pointer group"
            >
              <div class="w-6 h-6 rounded-full bg-[#BA5420] text-white flex items-center justify-center shadow-md font-bold text-xs ring-4 ring-[#BA5420]/20 transition group-hover:scale-110">
                2
              </div>
              <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 bg-[#1C130D] text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow whitespace-nowrap">
                Megenagna
              </span>
            </div>

            <!-- Piassa Pin -->
            <div
              @click="selectBranchById(4)"
              class="absolute top-[55%] left-[45%] -translate-x-1/2 -translate-y-1/2 cursor-pointer group"
            >
              <div class="w-6 h-6 rounded-full bg-[#BA5420] text-white flex items-center justify-center shadow-md font-bold text-xs ring-4 ring-[#BA5420]/20 transition group-hover:scale-110">
                3
              </div>
              <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 bg-[#1C130D] text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow whitespace-nowrap">
                Piassa
              </span>
            </div>

            <!-- Main Branch Pin (Dark Brown) -->
            <div
              @click="selectBranchById(1)"
              class="absolute top-[40%] left-[30%] -translate-x-1/2 -translate-y-1/2 cursor-pointer group"
            >
              <div class="w-6 h-6 rounded-full bg-[#2A1D15] text-white flex items-center justify-center shadow-md font-bold text-xs ring-4 ring-[#2A1D15]/20 transition group-hover:scale-110">
                M
              </div>
              <span class="absolute -bottom-5 left-1/2 -translate-x-1/2 bg-[#1C130D] text-white text-[10px] font-bold px-1.5 py-0.5 rounded shadow whitespace-nowrap">
                Main
              </span>
            </div>
          </div>

          <p class="text-[11px] text-[#7A6B60] leading-relaxed italic">
            Map preview. Pins are illustrative. The live site will plot each branch address.
          </p>
        </div>

        <!-- Not sure which to pick? helper box matching Page 3 -->
        <div class="bg-[#F8F4EE] rounded-2xl border border-[#E7DFD5] p-5 space-y-2">
          <h4 class="text-xs font-bold text-[#2A1D15] uppercase tracking-wider">
            Not sure which to pick?
          </h4>
          <p class="text-xs text-[#6A5A50] leading-relaxed">
            Choose the branch closest to where you will collect or receive your order. If it is short on something, we will suggest nearby branches.
          </p>
        </div>
      </div>
    </div>

    <!-- Delivery Zones Modal -->
    <div
      v-if="activeZoneModalBranch"
      class="fixed inset-0 z-50 bg-black/40 backdrop-blur-xs flex items-center justify-center p-4"
    >
      <div class="bg-white rounded-2xl border border-[#DDD2C5] max-w-md w-full p-6 space-y-5 shadow-xl animate-in fade-in">
        <div class="flex items-center justify-between border-b border-[#F2EAE0] pb-3">
          <div>
            <h3 class="font-serif font-bold text-lg text-[#2A1D15]">
              {{ activeZoneModalBranch.branch_name }} Delivery Areas
            </h3>
            <p class="text-xs text-[#7A6B60]">Verified coverage and delivery fees</p>
          </div>
          <button @click="activeZoneModalBranch = null" class="text-[#8C7D73] hover:text-[#2A1D15] text-lg font-bold">
            &times;
          </button>
        </div>

        <div class="space-y-2.5 max-h-64 overflow-y-auto">
          <div
            v-for="zone in modalZones"
            :key="zone.zone_id"
            class="flex items-center justify-between p-3 rounded-xl bg-[#FBF8F3] border border-[#EAE1D5]"
          >
            <div>
              <div class="text-xs font-bold text-[#2A1D15]">{{ zone.zone_name }}</div>
              <div class="text-[11px] text-[#7A6B60]">Delivery time: 30-45 mins</div>
            </div>
            <div class="text-xs font-bold text-[#BA5420]">
              ETB {{ zone.delivery_fee }}
            </div>
          </div>

          <div v-if="modalZones.length === 0" class="text-center py-6 text-xs text-[#8C7D73]">
            No delivery zones registered for this branch yet.
          </div>
        </div>

        <div class="pt-2 flex justify-end">
          <button
            @click="activeZoneModalBranch = null"
            class="px-5 py-2 bg-[#2A1D15] hover:bg-[#3D2B20] text-white text-xs font-semibold rounded-xl"
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
import { useRouter } from 'vue-router';
import { useBranchStore } from '../../stores/branch';
import api from '../../api';

const router = useRouter();
const branchStore = useBranchStore();

const searchQuery = ref('');
const cityFilter = ref('all');
const fulfillmentFilter = ref('all');

const activeZoneModalBranch = ref(null);
const modalZones = ref([]);

onMounted(async () => {
  if (branchStore.branches.length === 0) {
    await branchStore.fetchBranches();
  }
});

const filteredBranches = computed(() => {
  return branchStore.branches.filter((b) => {
    const matchSearch =
      b.branch_name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      (b.city && b.city.toLowerCase().includes(searchQuery.value.toLowerCase())) ||
      (b.address_line1 && b.address_line1.toLowerCase().includes(searchQuery.value.toLowerCase()));

    const matchCity = cityFilter.value === 'all' || b.city === cityFilter.value;
    return matchSearch && matchCity;
  });
});

function selectBranch(branch) {
  branchStore.setSelectedBranch(branch.branch_id);
  router.push('/shop');
}

function selectBranchById(id) {
  const branch = branchStore.branches.find((b) => b.branch_id === id);
  if (branch) {
    selectBranch(branch);
  }
}

async function showZonesFor(branch) {
  activeZoneModalBranch.value = branch;
  try {
    const res = await api.get(`/branches/${branch.branch_id}/delivery-zones`);
    modalZones.value = res.data;
  } catch {
    modalZones.value = [
      { zone_id: 1, zone_name: 'Bole Medhanialem', delivery_fee: 60 },
      { zone_id: 2, zone_name: 'Atlas & Rwanda', delivery_fee: 50 },
      { zone_id: 3, zone_name: 'Olympia & Meskel Flower', delivery_fee: 60 },
    ];
  }
}
</script>
