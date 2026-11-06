import { defineStore } from 'pinia';
import api from '../api';

export const useCartStore = defineStore('cart', {
  state: () => ({
    items: JSON.parse(localStorage.getItem('cart_items')) || [],
    branchId: parseInt(localStorage.getItem('cart_branch_id')) || 2,
    fulfillmentMethod: localStorage.getItem('cart_fulfillment') || 'Delivery', // 'Delivery' or 'Pickup'
    selectedZoneId: parseInt(localStorage.getItem('cart_zone_id')) || null,
    deliveryFee: parseFloat(localStorage.getItem('cart_delivery_fee')) || 60,
    revalidationWarnings: [],
    isRevalidating: false,
  }),

  getters: {
    totalItems: (state) => state.items.reduce((sum, item) => sum + item.quantity, 0),
    subtotal: (state) => state.items.reduce((sum, item) => sum + (item.price * item.quantity), 0),
    total: (state) => {
      const delivery = state.fulfillmentMethod === 'Delivery' ? state.deliveryFee : 0;
      return state.subtotal + delivery;
    },
    hasSoldOutItems: (state) => state.items.some((item) => (item.available || 0) <= 0),
  },

  actions: {
    addItem(product, branchId, requestedQty = 1) {
      if (this.branchId !== branchId) {
        this.branchId = branchId;
      }

      const available = typeof product.available === 'number' ? product.available : 0;
      if (available <= 0) {
        return {
          success: false,
          reason: 'sold_out',
          message: `${product.product_name} is sold out at this branch.`,
        };
      }

      const existing = this.items.find((i) => i.product_id === product.product_id);
      if (existing) {
        if (existing.quantity + requestedQty > available) {
          const added = Math.max(0, available - existing.quantity);
          existing.quantity = available;
          this.save();
          return {
            success: false,
            reason: 'capped',
            message: `Only ${available} available in stock. Quantity adjusted to ${available}.`,
          };
        }
        existing.quantity += requestedQty;
      } else {
        const initialQty = Math.min(requestedQty, available);
        this.items.push({
          product_id: product.product_id,
          product_name: product.product_name,
          price: product.resolved_price || product.base_price,
          quantity: initialQty,
          image_url: product.image_url,
          available: available,
          isSoldOut: available <= 0,
        });
      }

      this.save();
      return { success: true };
    },

    updateQuantity(productId, quantity) {
      const item = this.items.find((i) => i.product_id === productId);
      if (item) {
        if (quantity <= 0) {
          this.removeItem(productId);
          return { success: true, removed: true };
        }

        if (quantity > (item.available || 0)) {
          item.quantity = item.available || 1;
          this.save();
          return {
            success: false,
            message: `Requested quantity exceeds available stock (${item.available} available). Adjusted to maximum available.`,
          };
        }

        item.quantity = quantity;
        this.save();
        return { success: true };
      }
      return { success: false };
    },

    removeItem(productId) {
      this.items = this.items.filter((i) => i.product_id !== productId);
      this.save();
    },

    clearCart() {
      this.items = [];
      this.revalidationWarnings = [];
      this.save();
    },

    setFulfillment(method, fee = 0, zoneId = null) {
      this.fulfillmentMethod = method;
      this.deliveryFee = method === 'Delivery' ? fee : 0;
      this.selectedZoneId = zoneId;
      localStorage.setItem('cart_fulfillment', method);
      localStorage.setItem('cart_delivery_fee', this.deliveryFee);
      if (zoneId) {
        localStorage.setItem('cart_zone_id', zoneId);
      } else {
        localStorage.removeItem('cart_zone_id');
      }
    },

    dismissWarning(index) {
      this.revalidationWarnings.splice(index, 1);
    },

    clearWarnings() {
      this.revalidationWarnings = [];
    },

    async revalidateForBranch(newBranchId) {
      this.branchId = newBranchId;
      this.save();

      if (this.items.length === 0) return;

      this.isRevalidating = true;
      const warnings = [];

      try {
        const res = await api.get(`/catalog/products?branch_id=${newBranchId}`);
        const branchProducts = res.data || [];

        for (const item of this.items) {
          const match = branchProducts.find((p) => p.product_id === item.product_id);

          if (!match) {
            item.available = 0;
            item.isSoldOut = true;
            warnings.push({
              product_id: item.product_id,
              product_name: item.product_name,
              message: `${item.product_name} is not available at the selected branch.`,
              available: 0,
              requested: item.quantity,
              suggestions: await this.fetchAlternativeBranches(item.product_id, item.quantity, newBranchId),
            });
            continue;
          }

          // Update price according to new branch
          item.price = match.resolved_price || match.base_price;
          item.available = match.available || 0;
          item.isSoldOut = item.available <= 0;

          // Check if requested quantity exceeds availability
          if (item.available <= 0) {
            warnings.push({
              product_id: item.product_id,
              product_name: item.product_name,
              message: `${item.product_name} is sold out at this branch.`,
              available: 0,
              requested: item.quantity,
              suggestions: await this.fetchAlternativeBranches(item.product_id, item.quantity, newBranchId),
            });
          } else if (item.quantity > item.available) {
            const requested = item.quantity;
            item.quantity = item.available; // Cap to maximum available stock
            warnings.push({
              product_id: item.product_id,
              product_name: item.product_name,
              message: `Requested ${requested} units of ${item.product_name}, but only ${item.available} available. Quantity adjusted to maximum available.`,
              available: item.available,
              requested: requested,
              suggestions: await this.fetchAlternativeBranches(item.product_id, requested, newBranchId),
            });
          }
        }

        this.revalidationWarnings = warnings;
        this.save();
      } catch (err) {
        console.error('Error revalidating cart for branch', err);
      } finally {
        this.isRevalidating = false;
      }
    },

    async fetchAlternativeBranches(productId, neededQty, currentBranchId) {
      try {
        const res = await api.get(`/catalog/products/${productId}/branch-availability`);
        const branches = res.data?.branches || [];
        return branches
          .filter((b) => b.branch_id !== currentBranchId && b.available > 0)
          .map((b) => ({
            branch_id: b.branch_id,
            branch_name: b.branch_name,
            city: b.city,
            available: b.available,
            price: b.price,
            canFulfillFully: b.available >= neededQty,
          }));
      } catch {
        return [];
      }
    },

    save() {
      localStorage.setItem('cart_items', JSON.stringify(this.items));
      localStorage.setItem('cart_branch_id', this.branchId);
    },
  },
});
