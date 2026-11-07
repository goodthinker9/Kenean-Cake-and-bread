<template>
  <div
    class="fixed right-4 top-4 z-[100] flex w-[calc(100%-2rem)] flex-col gap-3 sm:w-96"
    aria-live="polite"
    aria-relevant="additions"
  >
    <div
      v-for="item in feedback.toasts"
      :key="item.id"
      class="flex items-start gap-3 rounded-xl border bg-white p-4 text-sm shadow-lg"
      :class="toastClasses[item.type]"
      :role="item.type === 'error' ? 'alert' : 'status'"
    >
      <p class="min-w-0 flex-1">{{ item.message }}</p>
      <button
        type="button"
        class="shrink-0 rounded px-1 text-lg leading-none text-[#6B5A53] hover:text-[#261814] focus:outline-none focus:ring-2 focus:ring-[#E11D48]"
        :aria-label="`Dismiss notification: ${item.message}`"
        @click="dismissToast(item.id)"
      >
        &times;
      </button>
    </div>
  </div>
</template>

<script setup>
import { feedback, dismissToast } from '../services/feedback';

const toastClasses = {
  success: 'border-emerald-200 text-emerald-900',
  error: 'border-rose-200 text-rose-900',
  info: 'border-[#F2D7DC] text-[#38121A]',
};
</script>
