<template>
  <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-md w-full bg-white rounded-3xl border border-[#F3E8E8] shadow-xl p-8 sm:p-10 space-y-6">
      <!-- Header with Logo -->
      <div class="text-center space-y-2">
        <div class="w-14 h-14 rounded-full bg-[#FFF1F2] border border-[#FECDD3] flex items-center justify-center mx-auto text-2xl shadow-xs">
          🧁
        </div>
        <h2 class="text-2xl sm:text-3xl font-serif font-black text-[#4A1521]">
          Welcome Back!
        </h2>
        <p class="text-xs text-[#7A6B60]">
          Sign in to your {{ appConfig.brandName }} account to track orders and save your favorites.
        </p>
      </div>

      <!-- Error Alert -->
      <div v-if="errorMessage" class="p-3 bg-[#FFF1F2] border border-[#FECDD3] rounded-2xl text-xs text-[#BE123C] font-semibold flex items-center space-x-2">
        <span>⚠️</span>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="handleLogin" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">
            Email Address
          </label>
          <input
            v-model="email"
            type="email"
            placeholder="you@example.com"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] focus:border-[#E11D48] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:outline-none focus:ring-1 focus:ring-[#E11D48]"
            required
          />
        </div>

        <div>
          <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A]">
              Password
            </label>
            <a href="#" class="text-[11px] text-[#E11D48] hover:underline font-semibold">
              Forgot password?
            </a>
          </div>
          <div class="relative">
            <input
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="••••••••"
              class="w-full bg-[#FAF7F5] border border-[#F2D7DC] focus:border-[#E11D48] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:outline-none focus:ring-1 focus:ring-[#E11D48]"
              required
            />
            <button
              type="button"
              @click="showPassword = !showPassword"
              class="absolute right-3 top-2.5 text-[#9CA3AF] hover:text-[#38121A] text-xs"
            >
              {{ showPassword ? '🙈' : '👁️' }}
            </button>
          </div>
        </div>

        <div class="flex items-center">
          <input
            id="remember"
            type="checkbox"
            v-model="rememberMe"
            class="rounded text-[#E11D48] focus:ring-[#E11D48]"
          />
          <label for="remember" class="ml-2 text-xs text-[#5B2934]">
            Remember me on this browser
          </label>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3.5 bg-[#E11D48] hover:bg-[#BE123C] disabled:opacity-50 text-white text-xs font-bold uppercase tracking-wider rounded-2xl shadow-md hover:shadow-lg transition active:scale-98"
        >
          {{ loading ? 'Signing in...' : 'Sign In →' }}
        </button>
      </form>

      <!-- Quick Demo Login Helpers -->
      <div class="pt-4 border-t border-[#F8E7EA] space-y-2.5">
        <div class="text-center text-[10px] uppercase font-bold tracking-widest text-[#9CA3AF]">
          Or Quick Sign-In for Testing
        </div>
        <div class="grid grid-cols-2 gap-2 text-xs">
          <button
            type="button"
            @click="fillDemo('customer@gmail.com', 'password123')"
            class="p-2.5 bg-[#FFF1F2] border border-[#FECDD3] hover:bg-[#FFE4E6] rounded-xl text-left transition"
          >
            <div class="font-bold text-[#881337]">Sara Ahmed</div>
            <div class="text-[10px] text-[#E11D48]">Customer Account</div>
          </button>
          <button
            type="button"
            @click="fillDemo('admin@goldencrust.com', 'password123')"
            class="p-2.5 bg-[#FDF2F8] border border-[#FBCFE8] hover:bg-[#FCE7F3] rounded-xl text-left transition"
          >
            <div class="font-bold text-[#831843]">Super Admin</div>
            <div class="text-[10px] text-[#DB2777]">Admin Portal</div>
          </button>
        </div>
      </div>

      <!-- Register Link -->
      <div class="text-center text-xs text-[#7A6B60] pt-2">
        Don't have an account yet?
        <router-link to="/register" class="text-[#E11D48] font-bold hover:underline ml-1">
          Sign up now
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { appConfig } from '../../config';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../api';

const router = useRouter();
const authStore = useAuthStore();

const email = ref('customer@gmail.com');
const password = ref('password123');
const rememberMe = ref(true);
const showPassword = ref(false);
const loading = ref(false);
const errorMessage = ref('');

function fillDemo(e, p) {
  email.value = e;
  password.value = p;
  handleLogin();
}

async function handleLogin() {
  loading.value = true;
  errorMessage.value = '';
  try {
    const res = await api.post('/auth/login', {
      email: email.value,
      password: password.value,
    });

    authStore.setUser(res.data.user, res.data.token);

    if (res.data.user?.role === 'Customer') {
      router.push('/account');
    } else {
      router.push('/admin/overview');
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Invalid email or password. Please try again.';
  } finally {
    loading.value = false;
  }
}
</script>
