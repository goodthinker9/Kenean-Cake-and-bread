<template>
  <div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 font-sans">
    <div class="max-w-md w-full bg-white rounded-3xl border border-[#F3E8E8] shadow-xl p-8 sm:p-10 space-y-6">
      <!-- Header with Logo -->
      <div class="text-center space-y-2">
        <div class="w-14 h-14 rounded-full bg-[#FFF1F2] border border-[#FECDD3] flex items-center justify-center mx-auto text-2xl shadow-xs">
          🧁
        </div>
        <h2 class="text-2xl sm:text-3xl font-serif font-black text-[#4A1521]">
          Join SweetBakes!
        </h2>
        <p class="text-xs text-[#7A6B60]">
          Create your account for fast checkout, custom cake quotations, and exclusive weekend discounts.
        </p>
      </div>

      <!-- Error Alert -->
      <div v-if="errorMessage" class="p-3 bg-[#FFF1F2] border border-[#FECDD3] rounded-2xl text-xs text-[#BE123C] font-semibold flex items-center space-x-2">
        <span>⚠️</span>
        <span>{{ errorMessage }}</span>
      </div>

      <!-- Registration Form -->
      <form @submit.prevent="handleRegister" class="space-y-4">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">
            Full Name
          </label>
          <input
            v-model="form.full_name"
            type="text"
            placeholder="Sara Ahmed"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] focus:border-[#E11D48] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:outline-none focus:ring-1 focus:ring-[#E11D48]"
            required
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">
            Email Address
          </label>
          <input
            v-model="form.email"
            type="email"
            placeholder="sara@example.com"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] focus:border-[#E11D48] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:outline-none focus:ring-1 focus:ring-[#E11D48]"
            required
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">
            Phone Number
          </label>
          <input
            v-model="form.phone"
            type="text"
            placeholder="+251 911 123 456"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] focus:border-[#E11D48] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:outline-none focus:ring-1 focus:ring-[#E11D48]"
            required
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">
            Password (min 6 characters)
          </label>
          <input
            v-model="form.password"
            type="password"
            placeholder="••••••••"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] focus:border-[#E11D48] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:outline-none focus:ring-1 focus:ring-[#E11D48]"
            required
            minlength="6"
          />
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-[#38121A] mb-1">
            Confirm Password
          </label>
          <input
            v-model="confirmPassword"
            type="password"
            placeholder="••••••••"
            class="w-full bg-[#FAF7F5] border border-[#F2D7DC] focus:border-[#E11D48] rounded-xl px-4 py-2.5 text-xs text-[#261814] focus:outline-none focus:ring-1 focus:ring-[#E11D48]"
            required
          />
        </div>

        <div class="flex items-start pt-1">
          <input
            id="terms"
            type="checkbox"
            required
            class="mt-0.5 rounded text-[#E11D48] focus:ring-[#E11D48]"
          />
          <label for="terms" class="ml-2 text-xs text-[#5B2934] leading-relaxed">
            I agree to SweetBakes <a href="#" class="text-[#E11D48] underline">Terms of Service</a> and <a href="#" class="text-[#E11D48] underline">Privacy Policy</a>.
          </label>
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full py-3.5 bg-[#E11D48] hover:bg-[#BE123C] disabled:opacity-50 text-white text-xs font-bold uppercase tracking-wider rounded-2xl shadow-md hover:shadow-lg transition active:scale-98"
        >
          {{ loading ? 'Creating Account...' : 'Create Account →' }}
        </button>
      </form>

      <!-- Login Link -->
      <div class="text-center text-xs text-[#7A6B60] pt-2">
        Already have an account?
        <router-link to="/login" class="text-[#E11D48] font-bold hover:underline ml-1">
          Sign in here
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../../stores/auth';
import api from '../../api';

const router = useRouter();
const authStore = useAuthStore();

const form = ref({
  full_name: '',
  email: '',
  phone: '',
  password: '',
});

const confirmPassword = ref('');
const loading = ref(false);
const errorMessage = ref('');

async function handleRegister() {
  if (form.value.password !== confirmPassword.value) {
    errorMessage.value = 'Passwords do not match.';
    return;
  }

  loading.value = true;
  errorMessage.value = '';

  try {
    const res = await api.post('/auth/register', form.value);
    authStore.setUser(res.data.user, res.data.token);
    router.push('/account');
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Registration failed. Please check your information.';
  } finally {
    loading.value = false;
  }
}
</script>
