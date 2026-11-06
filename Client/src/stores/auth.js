import { defineStore } from 'pinia';
import api from '../api';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: JSON.parse(localStorage.getItem('user')) || {
      user_id: 1,
      full_name: 'Abebe Kebede',
      email: 'admin@goldencrust.com',
      role: 'Super Admin',
      branch_id: null,
      branch: null,
    },
    token: localStorage.getItem('token') || '',
    availableRoles: [
      { role: 'Super Admin', label: 'Super Admin (All branches)', email: 'admin@goldencrust.com' },
      { role: 'Main Branch Manager', label: 'Main Manager (Main Branch)', email: 'main@goldencrust.com', branch_code: 'MAIN01' },
      { role: 'Branch Admin', label: 'Branch Admin (Bole)', email: 'bole.admin@goldencrust.com', branch_code: 'BR001' },
      { role: 'Branch Admin', label: 'Branch Admin (Megenagna)', email: 'megenagna.admin@goldencrust.com', branch_code: 'BR002' },
      { role: 'Branch Admin', label: 'Branch Admin (Piassa)', email: 'piassa.admin@goldencrust.com', branch_code: 'BR003' },
      { role: 'Branch Staff', label: 'Branch Staff (Bole)', email: 'bole.staff@goldencrust.com', branch_code: 'BR001' },
      { role: 'Customer', label: 'Customer (Sara Mengistu)', email: 'customer@gmail.com' },
    ],
  }),

  getters: {
    isSuperAdmin: (state) => state.user?.role === 'Super Admin',
    isMainManager: (state) => state.user?.role === 'Main Branch Manager',
    isBranchAdmin: (state) => state.user?.role === 'Branch Admin',
    isBranchStaff: (state) => state.user?.role === 'Branch Staff',
    isCustomer: (state) => state.user?.role === 'Customer',
  },

  actions: {
    async switchRole(roleItem) {
      try {
        const res = await api.post('/auth/switch-role', {
          role: roleItem.role,
          branch_code: roleItem.branch_code || null,
        });
        this.token = res.data.token;
        this.user = res.data.user;
        localStorage.setItem('token', this.token);
        localStorage.setItem('user', JSON.stringify(this.user));
      } catch (err) {
        console.error('Role switch error', err);
      }
    },

    setUser(user, token) {
      this.user = user;
      this.token = token;
      localStorage.setItem('token', token);
      localStorage.setItem('user', JSON.stringify(user));
    },

    logout() {
      this.user = null;
      this.token = '';
      localStorage.removeItem('token');
      localStorage.removeItem('user');
    }
  }
});
