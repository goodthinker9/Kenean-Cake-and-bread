import { createRouter, createWebHistory } from 'vue-router';

import AdminLayout from '../layouts/AdminLayout.vue';
import CustomerLayout from '../layouts/CustomerLayout.vue';

// Admin views
import OverviewView from '../views/admin/OverviewView.vue';
import BranchManagementView from '../views/admin/BranchManagementView.vue';
import UsersRolesView from '../views/admin/UsersRolesView.vue';
import ProductsPricesView from '../views/admin/ProductsPricesView.vue';
import InventoryView from '../views/admin/InventoryView.vue';
import DistributionView from '../views/admin/DistributionView.vue';
import NewTransferView from '../views/admin/NewTransferView.vue';
import OrdersView from '../views/admin/OrdersView.vue';
import CustomCakesView from '../views/admin/CustomCakesView.vue';
import PaymentsView from '../views/admin/PaymentsView.vue';
import ReportsView from '../views/admin/ReportsView.vue';
import NotificationsView from '../views/admin/NotificationsView.vue';
import SettingsView from '../views/admin/SettingsView.vue';
import AuditLogView from '../views/admin/AuditLogView.vue';

// Customer views (SweetBakes theme)
import CustomerHomeView from '../views/customer/CustomerHomeView.vue';
import ShopView from '../views/customer/ShopView.vue';
import ProductDetailView from '../views/customer/ProductDetailView.vue';
import CartView from '../views/customer/CartView.vue';
import CheckoutView from '../views/customer/CheckoutView.vue';
import CustomCakeBuilderView from '../views/customer/CustomCakeBuilderView.vue';
import BranchesView from '../views/customer/BranchesView.vue';
import OrderStatusView from '../views/customer/OrderStatusView.vue';
import OrderTrackingView from '../views/customer/OrderTrackingView.vue';
import LoginView from '../views/customer/LoginView.vue';
import RegisterView from '../views/customer/RegisterView.vue';
import ContactView from '../views/customer/ContactView.vue';
import AboutView from '../views/customer/AboutView.vue';
import AccountView from '../views/customer/AccountView.vue';

const routes = [
  // Admin Portal Routes
  {
    path: '/admin',
    component: AdminLayout,
    redirect: '/admin/overview',
    children: [
      { path: 'overview', component: OverviewView, meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin'] } },
      { path: 'branches', component: BranchManagementView, meta: { roles: ['Super Admin'] } },
      { path: 'users', component: UsersRolesView, meta: { roles: ['Super Admin', 'Main Branch Manager'] } },
      { path: 'products', component: ProductsPricesView, meta: { roles: ['Super Admin'] } },
      { path: 'inventory', component: InventoryView, meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff'] } },
      { path: 'distribution', component: DistributionView, meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff'] } },
      { path: 'transfers/new', component: NewTransferView, meta: { roles: ['Super Admin', 'Main Branch Manager'] } },
      { path: 'orders', component: OrdersView, meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff'] } },
      { path: 'custom-cakes', component: CustomCakesView, meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin'] } },
      { path: 'payments', component: PaymentsView, meta: { roles: ['Super Admin', 'Main Branch Manager'] } },
      { path: 'reports', component: ReportsView, meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin'] } },
      { path: 'notifications', component: NotificationsView, meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff'] } },
      { path: 'settings', component: SettingsView, meta: { roles: ['Super Admin'] } },
      { path: 'audit-logs', component: AuditLogView, meta: { roles: ['Super Admin'] } },
    ],
  },

  // Customer SweetBakes Storefront Routes
  {
    path: '/',
    component: CustomerLayout,
    children: [
      { path: '', component: CustomerHomeView },
      { path: 'home', component: CustomerHomeView },
      { path: 'shop', component: ShopView },
      { path: 'cakes-and-bread', redirect: '/shop' },
      { path: 'product/:id', component: ProductDetailView },
      { path: 'cart', component: CartView },
      { path: 'checkout', component: CheckoutView },
      { path: 'custom-cake', component: CustomCakeBuilderView },
      { path: 'custom-cakes-builder', redirect: '/custom-cake' },
      { path: 'branches', component: BranchesView },
      { path: 'orders/:order_number', component: OrderStatusView },
      { path: 'track-order', component: OrderTrackingView },
      { path: 'login', component: LoginView },
      { path: 'register', component: RegisterView },
      { path: 'contact', component: ContactView },
      { path: 'about', component: AboutView },
      { path: 'account', component: AccountView },
      { path: 'my-orders', redirect: '/account' },
    ],
  },

  // Legacy fallback
  { path: '/store', redirect: '/' },

  // Catch-all
  {
    path: '/:pathMatch(.*)*',
    redirect: '/',
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

// Navigation Guard for Role Enforcement
router.beforeEach((to, from, next) => {
  if (to.path.startsWith('/admin')) {
    const rawUser = localStorage.getItem('user');
    const user = rawUser ? JSON.parse(rawUser) : null;
    const role = user?.role || 'Super Admin';

    if (role === 'Customer') {
      alert('Access denied: Customer accounts cannot access the bakery admin portal.');
      return next('/');
    }

    if (to.meta?.roles && !to.meta.roles.includes(role)) {
      alert(`Access restricted: The "${to.path.replace('/admin/', '')}" page is only accessible to: ${to.meta.roles.join(', ')}.`);
      if (role === 'Branch Staff') {
        return next('/admin/orders');
      }
      return next('/admin/overview');
    }
  }

  next();
});

export default router;
