import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '@/views/HomeView.vue';
import LoginView from '@/views/LoginView.vue';
import SignupView from '@/views/SignupView.vue';
import DashboardView from '@/views/DashboardView.vue';
import QuestionBankView from '@/views/QuestionBankView.vue';
import TestsView from '@/views/TestsView.vue';
import UserManagementView from '@/views/UserManagementView.vue';
import RankingsView from '@/views/RankingsView.vue';
import TestRunnerView from '@/views/TestRunnerView.vue';
import { useAppStore } from '@/stores/app';

const routes = [
  { path: '/', name: 'home', component: HomeView },
  { path: '/login', name: 'login', component: LoginView },
  { path: '/signup', name: 'signup', component: SignupView },
  { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
  { path: '/question-bank', name: 'question-bank', component: QuestionBankView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/tests', name: 'tests', component: TestsView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/users', name: 'users', component: UserManagementView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/rankings', name: 'rankings', component: RankingsView },
  { path: '/quick-test/:id', name: 'quick-test', component: TestRunnerView, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const store = useAppStore();
  
  // Check if route requires authentication
  if (to.meta.requiresAuth && !store.isAuthenticated) {
    next({ name: 'login' });
    return;
  }
  
  // Check if route requires admin role
  if (to.meta.requiresAdmin && store.role !== 'admin') {
    next({ name: 'dashboard' });
    return;
  }
  
  // If user is authenticated and trying to access login page, redirect to dashboard
  if ((to.name === 'login' || to.name === 'signup') && store.isAuthenticated) {
    next({ name: 'dashboard' });
    return;
  }
  
  next();
});

export default router;
