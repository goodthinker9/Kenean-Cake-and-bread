import { defineStore } from 'pinia';
import api from '../api';
import { useCartStore } from './cart';

export const useBranchStore = defineStore('branch', {
  state: () => ({
    branches: [],
    selectedBranchId: parseInt(localStorage.getItem('selectedBranchId')) || 2, // Default to Bole
    loading: false,
  }),

  getters: {
    selectedBranch: (state) => {
      return state.branches.find((b) => b.branch_id === state.selectedBranchId) || state.branches[0] || null;
    },
  },

  actions: {
    async fetchBranches() {
      this.loading = true;
      try {
        const res = await api.get('/branches');
        this.branches = res.data;
        if (!this.selectedBranchId && this.branches.length > 0) {
          this.selectedBranchId = this.branches[0].branch_id;
        }
      } catch (err) {
        console.error('Failed to load branches', err);
      } finally {
        this.loading = false;
      }
    },

    setSelectedBranch(branchId) {
      this.selectedBranchId = branchId;
      localStorage.setItem('selectedBranchId', branchId);

      // Revalidate cart items and prices for the newly selected branch
      const cartStore = useCartStore();
      cartStore.revalidateForBranch(branchId);
    },
  },
});
