import { reactive } from 'vue';

export const feedback = reactive({
  toasts: [],
  confirmation: null,
});

let nextToastId = 0;

function showToast(type, message, duration = 5000) {
  const id = ++nextToastId;
  const toast = { id, type, message };

  feedback.toasts.push(toast);
  window.setTimeout(() => dismissToast(id), duration);
}

export function dismissToast(id) {
  const index = feedback.toasts.findIndex((toast) => toast.id === id);
  if (index !== -1) feedback.toasts.splice(index, 1);
}

export const toast = {
  success: (message, duration) => showToast('success', message, duration),
  error: (message, duration) => showToast('error', message, duration),
  info: (message, duration) => showToast('info', message, duration),
};

export function confirmAction({
  title = 'Please confirm',
  message,
  confirmText = 'Continue',
  cancelText = 'Cancel',
  tone = 'danger',
}) {
  if (feedback.confirmation) {
    return Promise.reject(new Error('A confirmation dialog is already open.'));
  }

  return new Promise((resolve) => {
    feedback.confirmation = {
      title,
      message,
      confirmText,
      cancelText,
      tone,
      resolve,
    };
  });
}

export function resolveConfirmation(confirmed) {
  const confirmation = feedback.confirmation;
  if (!confirmation) return;

  feedback.confirmation = null;
  confirmation.resolve(confirmed);
}
