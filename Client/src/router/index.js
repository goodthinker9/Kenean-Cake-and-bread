import { createRouter, createWebHistory } from 'vue-router';
import { toast } from '../services/feedback';

const routes = [
  {
    path: '/admin',
    component: () => import('../layouts/AdminLayout.vue'),
    redirect: '/admin/overview',
    children: [
      { path: 'overview', component: () => import('../views/admin/OverviewView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin'] } },
      { path: 'branches', component: () => import('../views/admin/BranchManagementView.vue'), meta: { roles: ['Super Admin'] } },
      { path: 'users', component: () => import('../views/admin/UsersRolesView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager'] } },
      { path: 'products', component: () => import('../views/admin/ProductsPricesView.vue'), meta: { roles: ['Super Admin'] } },
      { path: 'inventory', component: () => import('../views/admin/InventoryView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff'] } },
      { path: 'distribution', component: () => import('../views/admin/DistributionView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff'] } },
      { path: 'transfers/new', component: () => import('../views/admin/NewTransferView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager'] } },
      { path: 'orders', component: () => import('../views/admin/OrdersView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff'] } },
      { path: 'custom-cakes', component: () => import('../views/admin/CustomCakesView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin'] } },
      { path: 'payments', component: () => import('../views/admin/PaymentsView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager'] } },
      { path: 'reports', component: () => import('../views/admin/ReportsView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin'] } },
      { path: 'notifications', component: () => import('../views/admin/NotificationsView.vue'), meta: { roles: ['Super Admin', 'Main Branch Manager', 'Branch Admin', 'Branch Staff'] } },
      { path: 'settings', component: () => import('../views/admin/SettingsView.vue'), meta: { roles: ['Super Admin'] } },
      { path: 'audit-logs', component: () => import('../views/admin/AuditLogView.vue'), meta: { roles: ['Super Admin'] } },
    ],
  },
  {
    path: '/',
    component: () => import('../layouts/CustomerLayout.vue'),
    children: [
      { path: '', component: () => import('../views/customer/CustomerHomeView.vue') },
      { path: 'home', component: () => import('../views/customer/CustomerHomeView.vue') },
      { path: 'shop', component: () => import('../views/customer/ShopView.vue') },
      { path: 'cakes-and-bread', redirect: '/shop' },
      { path: 'product/:id', component: () => import('../views/customer/ProductDetailView.vue') },
      { path: 'cart', component: () => import('../views/customer/CartView.vue') },
      { path: 'checkout', component: () => import('../views/customer/CheckoutView.vue') },
      { path: 'custom-cake', component: () => import('../views/customer/CustomCakeBuilderView.vue') },
      { path: 'custom-cakes-builder', redirect: '/custom-cake' },
      { path: 'branches', component: () => import('../views/customer/BranchesView.vue') },
      { path: 'orders/:order_number', component: () => import('../views/customer/OrderStatusView.vue') },
      { path: 'track-order', component: () => import('../views/customer/OrderTrackingView.vue') },
      { path: 'login', component: () => import('../views/customer/LoginView.vue') },
      { path: 'register', component: () => import('../views/customer/RegisterView.vue') },
      { path: 'contact', component: () => import('../views/customer/ContactView.vue') },
      { path: 'about', component: () => import('../views/customer/AboutView.vue') },
      { path: 'account', component: () => import('../views/customer/AccountView.vue') },
      { path: 'my-orders', redirect: '/account' },
    ],
  },
  { path: '/store', redirect: '/' },
  { path: '/:pathMatch(.*)*', redirect: '/' },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

router.beforeEach((to) => {
  if (!to.path.startsWith('/admin')) return true;

  const rawUser = localStorage.getItem('user');
  const user = rawUser ? JSON.parse(rawUser) : null;
  const role = user?.role || 'Super Admin';

  if (role === 'Customer') {
    toast.error('Access denied: Customer accounts cannot access the bakery admin portal.');
    return '/';
  }

  if (to.meta?.roles && !to.meta.roles.includes(role)) {
    toast.error(`Access restricted: The "${to.path.replace('/admin/', '')}" page is only accessible to: ${to.meta.roles.join(', ')}.`);
    return role === 'Branch Staff' ? '/admin/orders' : '/admin/overview';
  }

  return true;
});

export default router;
