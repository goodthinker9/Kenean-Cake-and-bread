<template>
  <div
    v-if="feedback.confirmation"
    class="fixed inset-0 z-[110] flex items-center justify-center bg-black/50 p-4"
    @click.self="resolveConfirmation(false)"
  >
    <section
      class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl"
      role="alertdialog"
      aria-modal="true"
      aria-labelledby="confirm-dialog-title"
      aria-describedby="confirm-dialog-description"
      @keydown="handleKeydown"
    >
      <h2 id="confirm-dialog-title" class="text-xl font-serif font-bold text-[#38121A]">
        {{ feedback.confirmation.title }}
      </h2>
      <p id="confirm-dialog-description" class="mt-3 text-sm text-[#5B2934]">
        {{ feedback.confirmation.message }}
      </p>
      <div class="mt-6 flex justify-end gap-3">
        <button
          ref="cancelButton"
          type="button"
          class="rounded-xl border border-[#F2D7DC] px-4 py-2 text-sm font-semibold text-[#38121A] hover:bg-[#FFF1F2] focus:outline-none focus:ring-2 focus:ring-[#E11D48]"
          @click="resolveConfirmation(false)"
        >
          {{ feedback.confirmation.cancelText }}
        </button>
        <button
          ref="confirmButton"
          type="button"
          class="rounded-xl px-4 py-2 text-sm font-semibold text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#E11D48]"
          :class="feedback.confirmation.tone === 'danger' ? 'bg-[#BE123C] hover:bg-[#9F1239]' : 'bg-[#38121A] hover:bg-[#4A1521]'"
          @click="resolveConfirmation(true)"
        >
          {{ feedback.confirmation.confirmText }}
        </button>
      </div>
    </section>
  </div>
</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { feedback, resolveConfirmation } from '../services/feedback';

const cancelButton = ref(null);
const confirmButton = ref(null);
let previouslyFocusedElement = null;

watch(
  () => feedback.confirmation,
  async (confirmation) => {
    if (confirmation) {
      previouslyFocusedElement = document.activeElement;
      await nextTick();
      cancelButton.value?.focus();
    } else {
      previouslyFocusedElement?.focus?.();
      previouslyFocusedElement = null;
    }
  },
);

function handleKeydown(event) {
  if (event.key === 'Escape') {
    event.preventDefault();
    resolveConfirmation(false);
    return;
  }

  if (event.key !== 'Tab') return;

  const firstButton = cancelButton.value;
  const lastButton = confirmButton.value;
  if (event.shiftKey && document.activeElement === firstButton) {
    event.preventDefault();
    lastButton?.focus();
  } else if (!event.shiftKey && document.activeElement === lastButton) {
    event.preventDefault();
    firstButton?.focus();
  }
}

function handleGlobalKeydown(event) {
  if (feedback.confirmation && event.key === 'Escape') handleKeydown(event);
}

onMounted(() => window.addEventListener('keydown', handleGlobalKeydown));
onBeforeUnmount(() => window.removeEventListener('keydown', handleGlobalKeydown));
</script>
