<template>
  <router-view />
  <ToastHost />
  <ConfirmDialogHost />
</template>

<script setup>
import { onBeforeUnmount } from 'vue';
import { useRouter } from 'vue-router';
import ConfirmDialogHost from './components/ConfirmDialogHost.vue';
import ToastHost from './components/ToastHost.vue';
import { useAuthStore } from './stores/auth';

const router = useRouter();
const authStore = useAuthStore();

function handleSessionExpired() {
  authStore.logout();
  if (router.currentRoute.value.path !== '/login') {
    router.replace('/login');
  }
}

window.addEventListener('app:session-expired', handleSessionExpired);
onBeforeUnmount(() => window.removeEventListener('app:session-expired', handleSessionExpired));
</script>
