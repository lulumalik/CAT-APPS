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
import MaterialsManageView from '@/views/MaterialsManageView.vue';
import BlogView from '@/views/BlogView.vue';
import BlogDetailView from '@/views/BlogDetailView.vue';
import RegistrationWizardView from '@/views/RegistrationWizardView.vue';
import AdminRegistrationView from '@/views/AdminRegistrationView.vue';
import BimbleClassesManageView from '@/views/BimbleClassesManageView.vue';
import BimbleClassRoomView from '@/views/BimbleClassRoomView.vue';
import MyBimbleClassesView from '@/views/MyBimbleClassesView.vue';
import ProfileView from '@/views/ProfileView.vue';
import AnnouncementsView from '@/views/AnnouncementsView.vue';
import AdminAnnouncementsManageView from '@/views/AdminAnnouncementsManageView.vue';
import NotificationsView from '@/views/NotificationsView.vue';
import CertificateManagementView from '@/views/CertificateManagementView.vue';
import { useAppStore } from '@/stores/app';

const routes = [
  { path: '/', name: 'home', component: HomeView },
  { path: '/login', name: 'login', component: LoginView },
  { path: '/signup', name: 'signup', component: SignupView },
  { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
  { path: '/question-bank', name: 'question-bank', component: QuestionBankView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/tests', name: 'tests', component: TestsView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/users', name: 'users', component: UserManagementView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/materials', name: 'materials', component: MaterialsManageView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/blog', name: 'blog', component: BlogView },
  { path: '/blog/:slug', name: 'blog-detail', component: BlogDetailView },
  { path: '/rankings', name: 'rankings', component: RankingsView },
  { path: '/quick-test/:id', name: 'quick-test', component: TestRunnerView, meta: { requiresAuth: true } },
  { path: '/registration', name: 'registration', component: RegistrationWizardView, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: ProfileView, meta: { requiresAuth: true } },
  { path: '/announcements', name: 'announcements', component: AnnouncementsView, meta: { requiresAuth: true } },
  { path: '/notifications', name: 'notifications', component: NotificationsView, meta: { requiresAuth: true } },
  { path: '/admin/registration', name: 'admin-registration', component: AdminRegistrationView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/admin/announcements', name: 'admin-announcements', component: AdminAnnouncementsManageView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/admin/certificates', name: 'admin-certificates', component: CertificateManagementView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/bimble-classes', name: 'bimble-classes', component: BimbleClassesManageView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/my-classes', name: 'my-classes', component: MyBimbleClassesView, meta: { requiresAuth: true } },
  { path: '/class/:id', name: 'bimble-class-room', component: BimbleClassRoomView, meta: { requiresAuth: true } },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    }
    return { top: 0, left: 0 };
  },
});

router.beforeEach(async (to, from, next) => {
  const store = useAppStore();
  
  // Try to fetch user on first load if not authenticated
  if (!store.isAuthenticated && !store.isAuthChecked) {
    await store.fetchUser();
  }
  
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

  if (to.meta.requiresStaff && !['admin', 'mentor'].includes(store.role)) {
    next({ name: 'dashboard' });
    return;
  }

  // Registered students can only access participant flows.
  if (store.role === 'user') {
    const allowedForStudent = [
      'profile',
      'registration',
      'dashboard',
      'my-classes',
      'bimble-class-room',
      'quick-test',
      'blog-detail',
      'announcements',
      'notifications',
    ];
    if (!allowedForStudent.includes(String(to.name))) {
      next({ name: 'dashboard' });
      return;
    }
  }
  
  // If user is authenticated and trying to access login page, redirect to dashboard
  if ((to.name === 'login' || to.name === 'signup') && store.isAuthenticated) {
    next({ name: 'dashboard' });
    return;
  }
  
  next();
});

export default router;
