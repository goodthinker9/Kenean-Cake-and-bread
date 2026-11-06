<template>
  <div class="min-h-screen bg-[#FAF7F5] text-[#261814] flex flex-col font-sans">
    <!-- Top Announcement Bar matching Image 1, 3, 4 -->
    <div class="bg-[#38121A] text-white text-[11px] py-2 px-4 sm:px-8 border-b border-[#4A1521]">
      <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2">
        <div class="flex items-center space-x-4 text-[#FDE8EC]">
          <span class="flex items-center space-x-1">
            <span>🚚</span>
            <span>Free Delivery on Orders Over ETB 1,000</span>
          </span>
          <span class="hidden md:inline text-[#7A3644]">|</span>
          <span class="hidden md:flex items-center space-x-1">
            <span>📞</span>
            <span>+251 911 123 456</span>
          </span>
          <span class="hidden lg:inline text-[#7A3644]">|</span>
          <span class="hidden lg:flex items-center space-x-1">
            <span>✉️</span>
            <span>hello@sweetbakes.com</span>
          </span>
        </div>

        <div class="flex items-center space-x-4 text-[#FDE8EC]">
          <div class="flex items-center space-x-2">
            <span class="text-[#F9A8D4]">Follow Us:</span>
            <a href="#" class="hover:text-white transition">📘</a>
            <a href="#" class="hover:text-white transition">📷</a>
            <a href="#" class="hover:text-white transition">▶️</a>
            <a href="#" class="hover:text-white transition">🎵</a>
          </div>
          <span class="text-[#7A3644]">|</span>
          <router-link to="/track-order" class="hover:text-white transition">Track Order</router-link>
          <router-link to="/contact" class="hover:text-white transition">Help</router-link>
          <router-link to="/contact" class="hover:text-white transition">Contact</router-link>
        </div>
      </div>
    </div>

    <!-- Main Navigation Header matching Image 1, 3, 4 -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-[#F3E8E8] shadow-2xs">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
        <!-- Logo -->
        <router-link to="/" class="flex items-center space-x-2.5 shrink-0 group">
          <!-- SweetBakes Cupcake Logo SVG -->
          <div class="w-10 h-10 rounded-full bg-[#FFF1F2] border border-[#FECDD3] flex items-center justify-center shadow-xs group-hover:scale-105 transition">
            <svg class="w-6 h-6" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
              <!-- Cherry -->
              <circle cx="20" cy="8" r="3" fill="#BE123C" />
              <!-- Icing -->
              <path d="M12 20C12 15 15 12 20 12C25 12 28 15 28 20C29.5 20 31 21 31 22.5C31 24 29 25 20 25C11 25 9 24 9 22.5C9 21 10.5 20 12 20Z" fill="#F43F5E" />
              <path d="M14 19C16 16 24 16 26 19" stroke="#FDA4AF" stroke-width="1.5" stroke-linecap="round" />
              <!-- Liner -->
              <path d="M11 25L14 34C14.5 35 15.5 35.5 16.5 35.5H23.5C24.5 35.5 25.5 35 26 34L29 25H11Z" fill="#881337" />
              <line x1="16" y1="26" x2="17" y2="34" stroke="#BE123C" stroke-width="1" />
              <line x1="20" y1="26" x2="20" y2="34" stroke="#BE123C" stroke-width="1" />
              <line x1="24" y1="26" x2="23" y2="34" stroke="#BE123C" stroke-width="1" />
            </svg>
          </div>
          <div>
            <div class="text-2xl font-serif font-black tracking-tight text-[#881337] leading-none">
              Sweet<span class="text-[#E11D48]">Bakes</span>
            </div>
            <div class="text-[10px] tracking-widest text-[#9F1239] font-medium uppercase mt-0.5">
              Cakes &middot; Bread &middot; Happiness
            </div>
          </div>
        </router-link>

        <!-- Navigation Links -->
        <nav class="hidden lg:flex items-center space-x-8 text-sm font-semibold text-[#4B1A24]">
          <router-link
            to="/"
            class="transition-colors hover:text-[#E11D48] relative py-1"
            :class="$route.path === '/' || $route.path === '/home' ? 'text-[#E11D48] font-bold border-b-2 border-[#E11D48]' : ''"
          >
            Home
          </router-link>

          <router-link
            to="/shop"
            class="transition-colors hover:text-[#E11D48] relative py-1 flex items-center space-x-1"
            :class="$route.path.startsWith('/shop') || $route.path.startsWith('/cakes-and-bread') ? 'text-[#E11D48] font-bold border-b-2 border-[#E11D48]' : ''"
          >
            <span>Shop</span>
            <span class="text-xs">▾</span>
          </router-link>

          <router-link
            to="/custom-cake"
            class="transition-colors hover:text-[#E11D48] relative py-1"
            :class="$route.path.startsWith('/custom-cake') ? 'text-[#E11D48] font-bold border-b-2 border-[#E11D48]' : ''"
          >
            Custom Cake
          </router-link>

          <router-link
            to="/about"
            class="transition-colors hover:text-[#E11D48] relative py-1"
            :class="$route.path === '/about' ? 'text-[#E11D48] font-bold border-b-2 border-[#E11D48]' : ''"
          >
            About
          </router-link>

          <router-link
            to="/contact"
            class="transition-colors hover:text-[#E11D48] relative py-1"
            :class="$route.path === '/contact' ? 'text-[#E11D48] font-bold border-b-2 border-[#E11D48]' : ''"
          >
            Contact
          </router-link>
        </nav>

        <!-- Search Bar Input matching Image 1, 3, 4 -->
        <div class="hidden md:flex items-center relative flex-1 max-w-xs">
          <input
            v-model="searchKeyword"
            @keyup.enter="handleSearch"
            type="text"
            placeholder="Search for cakes, bread..."
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] focus:border-[#E11D48] focus:bg-white rounded-full py-2 pl-4 pr-10 text-xs text-[#261814] focus:outline-none transition shadow-2xs"
          />
          <button @click="handleSearch" class="absolute right-3 text-[#E11D48] hover:text-[#BE123C]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </button>
        </div>

        <!-- Right Side: Branch Selector, Account & Cart -->
        <div class="flex items-center space-x-3">
          <!-- Active Branch Selector Pill with Quick Switch Dropdown -->
          <div class="relative">
            <button
              @click="branchMenuOpen = !branchMenuOpen"
              title="Change active bakery branch"
              class="flex items-center space-x-1.5 bg-[#FFF1F2] border border-[#FECDD3] px-3 py-1.5 rounded-full text-xs font-semibold text-[#881337] hover:bg-[#FFE4E6] transition shadow-2xs"
            >
              <span class="w-2 h-2 rounded-full bg-[#10B981] animate-pulse"></span>
              <span class="font-bold">{{ branchName }}</span>
              <span class="text-[10px] text-[#E11D48] underline ml-1">Change</span>
              <span class="text-[10px]">▾</span>
            </button>

            <!-- Quick Branch Switcher Dropdown -->
            <div
              v-if="branchMenuOpen"
              class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-[#F2D7DC] p-3 z-50 animate-in fade-in zoom-in-95"
            >
              <div class="flex items-center justify-between pb-2 border-b border-[#F8E7EA] mb-2">
                <span class="text-xs font-bold text-[#38121A]">Choose Your Branch</span>
                <router-link to="/branches" @click="branchMenuOpen = false" class="text-[11px] font-bold text-[#E11D48] hover:underline">
                  View Map & Areas →
                </router-link>
              </div>

              <div class="space-y-1.5 max-h-64 overflow-y-auto">
                <button
                  v-for="b in branchStore.branches"
                  :key="b.branch_id"
                  @click="selectHeaderBranch(b)"
                  class="w-full text-left p-2.5 rounded-xl transition flex items-start justify-between border"
                  :class="branchStore.selectedBranchId === b.branch_id ? 'border-[#E11D48] bg-[#FFF1F2]' : 'border-transparent hover:bg-[#FAF7F5]'"
                >
                  <div class="space-y-0.5">
                    <div class="text-xs font-bold text-[#38121A] flex items-center space-x-1.5">
                      <span>{{ b.branch_name }}</span>
                      <span v-if="b.branch_type === 'Main'" class="text-[9px] px-1.5 py-0.2 bg-[#FEE2E2] text-[#991B1B] rounded font-semibold">Main</span>
                    </div>
                    <div class="text-[10px] text-[#7A6B60]">{{ b.address || b.city }}</div>
                    <div class="text-[9px] text-[#059669]">🕒 {{ b.opening_hours || '07:00 - 20:00' }}</div>
                  </div>
                  <span v-if="branchStore.selectedBranchId === b.branch_id" class="text-[#E11D48] font-bold text-xs">✓</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Account Dropdown Button -->
          <div class="relative" ref="accountDropdownRef">
            <button
              @click="accountMenuOpen = !accountMenuOpen"
              class="flex items-center space-x-1.5 px-3 py-1.5 rounded-full border border-[#F2D7DC] hover:border-[#E11D48] bg-white text-xs font-semibold text-[#4B1A24] transition shadow-2xs"
            >
              <svg class="w-4 h-4 text-[#E11D48]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              <span>{{ authStore.user?.full_name ? authStore.user.full_name.split(' ')[0] : 'Account' }}</span>
              <span class="text-[10px]">▾</span>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="accountMenuOpen"
              class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-[#F2D7DC] py-2 z-50 animate-in fade-in zoom-in-95"
            >
              <div v-if="authStore.user" class="px-4 py-2 border-b border-[#F8E7EA]">
                <div class="font-bold text-xs text-[#261814] truncate">{{ authStore.user.full_name }}</div>
                <div class="text-[10px] text-[#881337] font-semibold">{{ authStore.user.role }}</div>
              </div>

              <div class="py-1 text-xs">
                <router-link
                  v-if="authStore.user"
                  to="/account"
                  @click="accountMenuOpen = false"
                  class="flex items-center px-4 py-2 text-[#4B1A24] hover:bg-[#FFF1F2] hover:text-[#E11D48]"
                >
                  <span>👤 My Account & Orders</span>
                </router-link>

                <router-link
                  v-if="!authStore.user"
                  to="/login"
                  @click="accountMenuOpen = false"
                  class="flex items-center px-4 py-2 text-[#4B1A24] hover:bg-[#FFF1F2] hover:text-[#E11D48]"
                >
                  <span>🔑 Sign In</span>
                </router-link>

                <router-link
                  v-if="!authStore.user"
                  to="/register"
                  @click="accountMenuOpen = false"
                  class="flex items-center px-4 py-2 text-[#4B1A24] hover:bg-[#FFF1F2] hover:text-[#E11D48]"
                >
                  <span>✨ Create Account</span>
                </router-link>

                <router-link
                  to="/admin/overview"
                  @click="accountMenuOpen = false"
                  class="flex items-center px-4 py-2 text-[#881337] font-bold hover:bg-[#FFF1F2] border-t border-[#F8E7EA] mt-1 pt-2"
                >
                  <span>⚙️ Admin Dashboard</span>
                </router-link>

                <button
                  v-if="authStore.user"
                  @click="handleLogout"
                  class="w-full text-left flex items-center px-4 py-2 text-[#BE123C] hover:bg-[#FFF1F2]"
                >
                  <span>🚪 Sign Out</span>
                </button>
              </div>
            </div>
          </div>

          <!-- Shopping Cart Pill matching Image 1, 3, 4 -->
          <router-link
            to="/cart"
            class="relative flex items-center space-x-1.5 px-3.5 py-2 bg-[#E11D48] hover:bg-[#BE123C] text-white rounded-full text-xs font-bold transition shadow-xs group"
          >
            <svg class="w-4 h-4 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="hidden sm:inline">Cart</span>
            <span class="w-5 h-5 rounded-full bg-white text-[#E11D48] text-[10px] font-black flex items-center justify-center">
              {{ cartStore.totalItems }}
            </span>
          </router-link>
        </div>
      </div>
    </header>

    <!-- Main View Slot -->
    <main class="flex-1 pb-16 md:pb-0">
      <router-view />
    </main>

    <!-- Cute Footer Banner matching Image 4 -->
    <div class="bg-[#FFF1F2] border-t border-[#FECDD3] py-4 text-center">
      <div class="max-w-7xl mx-auto px-4 flex items-center justify-center space-x-2 text-sm font-serif font-bold text-[#881337]">
        <span class="text-lg">💖</span>
        <span class="text-lg font-black text-[#E11D48]">SweetBakes</span>
        <span>— Because life is sweeter with cake!</span>
        <span class="text-lg">💖</span>
      </div>
    </div>

    <!-- Main SweetBakes Footer -->
    <footer class="bg-[#38121A] text-[#FDE8EC] pt-12 pb-8 border-t border-[#4A1521]">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-12 gap-8">
        <!-- Brand Summary (4 cols) -->
        <div class="md:col-span-4 space-y-4">
          <div class="flex items-center space-x-2.5">
            <div class="w-9 h-9 rounded-full bg-[#FFF1F2] flex items-center justify-center text-lg">
              🧁
            </div>
            <span class="text-2xl font-serif font-black text-white tracking-wide">
              Sweet<span class="text-[#F43F5E]">Bakes</span>
            </span>
          </div>
          <p class="text-xs text-[#FBCFE8] leading-relaxed max-w-sm">
            Handcrafted with organic wheat, rich Belgian chocolate, and genuine passion. Freshly baked everyday and delivered to your doorstep in Addis Ababa.
          </p>
          <div class="flex items-center space-x-3 text-lg pt-1">
            <a href="#" class="w-8 h-8 rounded-full bg-[#4A1521] hover:bg-[#E11D48] flex items-center justify-center transition">📘</a>
            <a href="#" class="w-8 h-8 rounded-full bg-[#4A1521] hover:bg-[#E11D48] flex items-center justify-center transition">📷</a>
            <a href="#" class="w-8 h-8 rounded-full bg-[#4A1521] hover:bg-[#E11D48] flex items-center justify-center transition">▶️</a>
            <a href="#" class="w-8 h-8 rounded-full bg-[#4A1521] hover:bg-[#E11D48] flex items-center justify-center transition">🎵</a>
          </div>
        </div>

        <!-- Quick Links (2 cols) -->
        <div class="md:col-span-2 space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-widest text-[#F9A8D4]">Quick Links</h4>
          <ul class="space-y-2 text-xs text-[#FDE8EC]">
            <li><router-link to="/" class="hover:text-white transition">Home</router-link></li>
            <li><router-link to="/shop" class="hover:text-white transition">Shop Cakes & Bread</router-link></li>
            <li><router-link to="/custom-cake" class="hover:text-white transition">Design Custom Cake</router-link></li>
            <li><router-link to="/about" class="hover:text-white transition">About Our Bakery</router-link></li>
            <li><router-link to="/contact" class="hover:text-white transition">Contact Us</router-link></li>
          </ul>
        </div>

        <!-- Categories (3 cols) -->
        <div class="md:col-span-3 space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-widest text-[#F9A8D4]">Categories</h4>
          <ul class="space-y-2 text-xs text-[#FDE8EC]">
            <li><router-link to="/shop?category=Cakes" class="hover:text-white transition">Artisan Birthday Cakes</router-link></li>
            <li><router-link to="/shop?category=Cakes" class="hover:text-white transition">Wedding Celebrations</router-link></li>
            <li><router-link to="/shop?category=Bread" class="hover:text-white transition">Fresh Oven Breads</router-link></li>
            <li><router-link to="/shop?category=Pastries" class="hover:text-white transition">French Butter Croissants</router-link></li>
            <li><router-link to="/shop?category=Pastries" class="hover:text-white transition">Frosted Cupcakes</router-link></li>
          </ul>
        </div>

        <!-- Contact & Hours (3 cols) -->
        <div class="md:col-span-3 space-y-3">
          <h4 class="text-xs font-bold uppercase tracking-widest text-[#F9A8D4]">Bakery Branches</h4>
          <div class="space-y-2 text-xs text-[#FBCFE8]">
            <p><strong>Bole Branch:</strong> Bole Medhanialem, Addis Ababa</p>
            <p><strong>Megenagna Branch:</strong> Near Megenagna Square</p>
            <p><strong>Piassa Branch:</strong> Churchill Avenue</p>
            <p class="pt-1 text-[#FDE8EC]"><strong>Hours:</strong> Mon - Sun: 7:00 AM - 9:00 PM</p>
            <p class="text-[#FDE8EC]"><strong>Phone:</strong> +251 911 123 456</p>
          </div>
        </div>
      </div>

      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10 pt-6 border-t border-[#4A1521] flex flex-col sm:flex-row items-center justify-between text-[11px] text-[#F9A8D4] gap-2">
        <p>&copy; 2026 SweetBakes Bakery & Confectionery. All rights reserved.</p>
        <div class="flex items-center space-x-4">
          <router-link to="/about" class="hover:text-white">Privacy Policy</router-link>
          <router-link to="/about" class="hover:text-white">Terms of Service</router-link>
          <router-link to="/admin/overview" class="hover:text-white font-bold underline">Staff Portal →</router-link>
        </div>
      </div>
    </footer>

    <!-- Mobile Bottom Navigation Bar -->
    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-[#F3E8E8] py-2 px-6 flex justify-around items-center z-40 text-[11px] font-semibold text-[#6B21A8]">
      <router-link to="/" class="flex flex-col items-center hover:text-[#E11D48]" :class="$route.path === '/' ? 'text-[#E11D48]' : 'text-[#4B1A24]'">
        <span class="text-lg">🏠</span>
        <span>Home</span>
      </router-link>
      <router-link to="/shop" class="flex flex-col items-center hover:text-[#E11D48]" :class="$route.path.startsWith('/shop') ? 'text-[#E11D48]' : 'text-[#4B1A24]'">
        <span class="text-lg">🥐</span>
        <span>Shop</span>
      </router-link>
      <router-link to="/custom-cake" class="flex flex-col items-center hover:text-[#E11D48]" :class="$route.path.startsWith('/custom-cake') ? 'text-[#E11D48]' : 'text-[#4B1A24]'">
        <span class="text-lg">🎂</span>
        <span>Custom</span>
      </router-link>
      <router-link to="/account" class="flex flex-col items-center hover:text-[#E11D48]" :class="$route.path.startsWith('/account') ? 'text-[#E11D48]' : 'text-[#4B1A24]'">
        <span class="text-lg">📦</span>
        <span>Orders</span>
      </router-link>
      <router-link to="/cart" class="flex flex-col items-center hover:text-[#E11D48]" :class="$route.path.startsWith('/cart') ? 'text-[#E11D48]' : 'text-[#4B1A24]'">
        <span class="text-lg relative">
          🛒
          <span v-if="cartStore.totalItems > 0" class="absolute -top-1 -right-2 w-4 h-4 bg-[#E11D48] text-white rounded-full text-[9px] flex items-center justify-center font-bold">
            {{ cartStore.totalItems }}
          </span>
        </span>
        <span>Cart</span>
      </router-link>
    </nav>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useBranchStore } from '../stores/branch';
import { useCartStore } from '../stores/cart';
import { useAuthStore } from '../stores/auth';

const router = useRouter();
const branchStore = useBranchStore();
const cartStore = useCartStore();
const authStore = useAuthStore();

const searchKeyword = ref('');
const accountMenuOpen = ref(false);
const branchMenuOpen = ref(false);

const branchName = computed(() => {
  return branchStore.selectedBranch?.branch_name || 'Bole';
});

function selectHeaderBranch(b) {
  branchStore.setSelectedBranch(b.branch_id);
  branchMenuOpen.value = false;
}

onMounted(() => {
  branchStore.fetchBranches();
});

function handleSearch() {
  if (searchKeyword.value.trim()) {
    router.push({ path: '/shop', query: { search: searchKeyword.value.trim() } });
  }
}

function handleLogout() {
  authStore.logout();
  accountMenuOpen.value = false;
  router.push('/login');
}
</script>
