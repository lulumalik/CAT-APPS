import { createRouter, createWebHistory } from 'vue-router';
import HomeDemoView from '@/views/HomeDemoView.vue';
import LoginView from '@/views/LoginView.vue';
import SignupView from '@/views/SignupView.vue';
import DashboardView from '@/views/DashboardView.vue';
import QuestionBankView from '@/views/QuestionBankView.vue';
import TestsView from '@/views/TestsView.vue';
import UserManagementView from '@/views/UserManagementView.vue';
import BatchManagementView from '@/views/BatchManagementView.vue';
import BatchDetailView from '@/views/BatchDetailView.vue';
import TestRunnerView from '@/views/TestRunnerView.vue';
import MaterialsManageView from '@/views/MaterialsManageView.vue';
import BlogView from '@/views/BlogView.vue';
import BlogDetailView from '@/views/BlogDetailView.vue';
import FreeTryoutView from '@/views/FreeTryoutView.vue';
import RegistrationWizardView from '@/views/RegistrationWizardView.vue';
import BimbleClassesManageView from '@/views/BimbleClassesManageView.vue';
import BimbleClassRoomView from '@/views/BimbleClassRoomView.vue';
import MyBimbleClassesView from '@/views/MyBimbleClassesView.vue';
import ProfileView from '@/views/ProfileView.vue';
import ActivityHistoryView from '@/views/ActivityHistoryView.vue';
import EmailVerifiedView from '@/views/EmailVerifiedView.vue';
import NotificationsView from '@/views/NotificationsView.vue';
import CertificateManagementView from '@/views/CertificateManagementView.vue';
import StudentReportsManageView from '@/views/StudentReportsManageView.vue';
import TestSubmissionsView from '@/views/TestSubmissionsView.vue';
import StudentExamsView from '@/views/StudentExamsView.vue';
import ExamReviewView from '@/views/ExamReviewView.vue';
import InterestSelectView from '@/views/InterestSelectView.vue';
import ExamCategoryManageView from '@/views/ExamCategoryManageView.vue';
import { useAppStore } from '@/stores/app';
import { normalizeProgramCategory } from '@/utils/userMeta';

const routes = [
  { path: '/', name: 'home-demo', component: HomeDemoView },
  { path: '/about-us', redirect: '/' },
  { path: '/selayang-pandang', redirect: '/' },
  { path: '/login', name: 'login', component: LoginView },
  { path: '/signup', name: 'signup', component: SignupView },
  { path: '/dashboard', name: 'dashboard', component: DashboardView, meta: { requiresAuth: true } },
  { path: '/dashboard/student/:id', name: 'student-dashboard', component: DashboardView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/question-bank', name: 'question-bank', component: QuestionBankView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/tests', name: 'tests', component: TestsView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/tests/:id/submissions', name: 'test-submissions', component: TestSubmissionsView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/exams', name: 'exams', component: TestsView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/exams/:id/submissions', name: 'exam-submissions', component: TestSubmissionsView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/ujian', name: 'student-exams', component: StudentExamsView, meta: { requiresAuth: true } },
  { path: '/ujian/:id/tinjau/:submissionId?', name: 'exam-review', component: ExamReviewView, meta: { requiresAuth: true } },
  { path: '/pilih-minat', name: 'interest-select', component: InterestSelectView, meta: { requiresAuth: true } },
  { path: '/users', name: 'users', component: UserManagementView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/batches', name: 'batches', component: BatchManagementView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/batches/:id', name: 'batch-detail', component: BatchDetailView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/materials', name: 'materials', component: MaterialsManageView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/blog', name: 'blog', component: BlogView },
  { path: '/blog/:slug', name: 'blog-detail', component: BlogDetailView },
  { path: '/free-tryout', name: 'free-tryout', component: FreeTryoutView },
  { path: '/admin/exam-categories', name: 'admin-exam-categories', component: ExamCategoryManageView, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/admin/student-reports', name: 'admin-student-reports', component: StudentReportsManageView, meta: { requiresAuth: true, requiresStaff: true } },
  { path: '/quick-test/:id', name: 'quick-test', component: TestRunnerView, meta: { requiresAuth: true } },
  { path: '/quick-exam/:id', name: 'quick-exam', component: TestRunnerView, meta: { requiresAuth: true } },
  { path: '/registration', name: 'registration', component: RegistrationWizardView, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: ProfileView, meta: { requiresAuth: true } },
  { path: '/activity-history', name: 'activity-history', component: ActivityHistoryView, meta: { requiresAuth: true } },
  { path: '/email/verified', name: 'email-verified', component: EmailVerifiedView },
  { path: '/notifications', name: 'notifications', component: NotificationsView, meta: { requiresAuth: true } },
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
    next({ name: store.role === 'mentor' ? 'bimble-classes' : 'dashboard' });
    return;
  }

  if (to.meta.requiresStaff && !['admin', 'mentor'].includes(store.role)) {
    next({ name: 'dashboard' });
    return;
  }

  // Registered students can only access participant flows.
  if (store.role === 'user') {
    const expired = store.user?.app_expired === true
      || (store.user?.app_expires_at && new Date(store.user.app_expires_at) < new Date())

    const allowedForExpiredStudent = [
      'home-demo',
      'home',
      'profile',
      'activity-history',
      'email-verified',
      'blog-detail',
      'free-tryout',
      'interest-select',
    ]

    const allowedForStudent = expired
      ? allowedForExpiredStudent
      : [
        'home-demo',
        'home',
        'profile',
        'activity-history',
        'email-verified',
        'registration',
        'dashboard',
        'my-classes',
        'bimble-class-room',
        'quick-test',
        'quick-exam',
        'student-exams',
        'exam-review',
        'blog-detail',
        'free-tryout',
        'notifications',
        'interest-select',
      ]

    if (!allowedForStudent.includes(String(to.name))) {
      next({ name: expired ? 'profile' : 'dashboard' })
      return
    }

    // Peserta baru wajib memilih minat ujian dulu sebelum masuk dashboard/ujian.
    const needsInterest = !expired && !store.user?.exam_track_id
    const interestGatedPages = ['dashboard', 'student-exams', 'quick-exam', 'quick-test', 'my-classes', 'bimble-class-room']
    if (needsInterest && interestGatedPages.includes(String(to.name))) {
      next({ name: 'interest-select' })
      return
    }

    const examOnly = store.user?.is_exam_only_program === true
      || normalizeProgramCategory(store.user?.program_category) === 'try_out'
    if (examOnly && ['my-classes', 'bimble-class-room'].includes(String(to.name))) {
      next({ name: 'student-exams' })
      return
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
