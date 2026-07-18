<template>
  <template v-if="isAuthenticated">
    <!-- Admin mobile top bar -->
    <header v-if="isAdmin" class="admin-mobile-topbar md:hidden">
      <button type="button" class="admin-mobile-topbar__icon-btn" aria-label="Menu" @click="isMenuOpen = true">
        <Menu class="h-5 w-5" />
      </button>
      <button type="button" class="admin-mobile-topbar__brand" @click="navigateToHome">
        <img :src="logoUrl" alt="CATLab" class="admin-mobile-topbar__logo" />
        <span>{{ t('app.name') }}</span>
      </button>
      <router-link to="/notifications" class="admin-mobile-topbar__icon-btn relative" aria-label="Notifikasi">
        <Bell class="h-5 w-5" />
        <span v-if="unreadCount" class="admin-mobile-topbar__dot" />
      </router-link>
    </header>

    <!-- Non-admin mobile hamburger -->
    <button
      v-else
      type="button"
      class="md:hidden fixed top-4 left-4 z-[60] rounded-full bg-[#1A1A1A] text-white w-10 h-10 grid place-items-center"
      @click="isMenuOpen = true"
    >
      <Menu class="h-5 w-5" />
    </button>

    <aside class="hidden md:flex fixed left-0 top-0 h-screen w-72 bg-gradient-to-b from-background via-background to-background border-r border-gray-100 shadow-xl shadow-black/5 z-40 flex-col">
      <div class="px-6 py-6 border-b border-gray-100">
        <div class="flex items-center gap-3 cursor-pointer" @click="navigateToHome">
          <img :src="logoUrl" alt="CAT Apps" class="w-9 h-9 object-contain" />
          <div>
            <div class="font-bold text-[#1A1A1A] leading-tight">{{ t('app.name') }}</div>
            <div class="text-xs text-gray-500">Navigator</div>
          </div>
        </div>
      </div>
      <nav class="flex-1 overflow-y-auto p-4 space-y-1.5">
        <router-link v-for="item in navItems" :key="item.to" :to="item.to" class="block">
          <div
            class="px-3 py-2.5 rounded-xl text-sm transition-colors flex items-center justify-between gap-2"
            :class="isActive(item.to) ? 'bg-primary/15 text-[#1A1A1A] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#1A1A1A]'"
          >
            <span class="inline-flex items-center gap-2.5 min-w-0">
              <component
                :is="item.icon"
                class="h-4 w-4 shrink-0"
                :class="isActive(item.to) ? 'text-[#1A1A1A]' : 'text-gray-400'"
              />
              <span class="truncate">{{ item.label }}</span>
            </span>
            <Lock v-if="item.locked" class="h-4 w-4 text-gray-400 shrink-0" />
          </div>
        </router-link>
      </nav>
      <div class="p-4 border-t border-gray-100 space-y-3">
        <div class="p-1 rounded-full border border-gray-200 bg-white shadow-sm flex gap-1">
          <button type="button" class="flex-1 px-4 py-2 text-xs font-semibold rounded-full transition-all" :class="locale === 'id' ? 'bg-black text-white shadow' : 'text-gray-700 hover:bg-gray-50'" @click="setLocale('id')">ID</button>
          <button type="button" class="flex-1 px-4 py-2 text-xs font-semibold rounded-full transition-all" :class="locale === 'en' ? 'bg-black text-white shadow' : 'text-gray-700 hover:bg-gray-50'" @click="setLocale('en')">EN</button>
        </div>
        <div class="px-1 text-sm">
          <div class="font-semibold text-[#1A1A1A] truncate">{{ user?.name || user?.email }}</div>
          <div class="mt-1 flex items-center gap-2">
            <span class="text-xs rounded-full px-2 py-0.5 capitalize bg-gray-100 text-gray-600">{{ user?.role }}</span>
            <span class="text-xs rounded-full px-2 py-0.5" :class="programBadge.className">{{ programBadge.label }}</span>
          </div>
        </div>
        <router-link to="/notifications" class="w-full inline-flex items-center justify-between px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm">
          <span class="inline-flex items-center gap-2">
            <Bell class="h-4 w-4" />
            Notifikasi
          </span>
          <span v-if="unreadCount" class="text-xs bg-red-500 text-white rounded-full px-2 py-0.5">{{ unreadCount }}</span>
        </router-link>
        <button
          type="button"
          @click="handleLogout"
          class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium"
        >
          <LogOut class="h-4 w-4" />
          {{ t('nav.logout') }}
        </button>
      </div>
    </aside>

    <div v-if="isMenuOpen" class="md:hidden fixed inset-0 z-[70]">
      <div class="absolute inset-0 bg-black/40" @click="isMenuOpen = false"></div>
      <aside class="absolute left-0 top-0 h-full w-72 bg-white border-r border-gray-100 shadow-2xl p-4 flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <div class="font-semibold">{{ t('app.name') }}</div>
          <button class="w-8 h-8 rounded-full hover:bg-gray-100 grid place-items-center" @click="isMenuOpen = false">
            <X class="h-4 w-4" />
          </button>
        </div>
        <div class="p-1 mb-3 rounded-full border border-gray-200 bg-white shadow-sm flex gap-1">
          <button type="button" class="flex-1 px-4 py-2 text-xs font-semibold rounded-full transition-all" :class="locale === 'id' ? 'bg-black text-white shadow' : 'text-gray-700 hover:bg-gray-50'" @click="setLocale('id')">ID</button>
          <button type="button" class="flex-1 px-4 py-2 text-xs font-semibold rounded-full transition-all" :class="locale === 'en' ? 'bg-black text-white shadow' : 'text-gray-700 hover:bg-gray-50'" @click="setLocale('en')">EN</button>
        </div>
        <nav class="flex-1 overflow-y-auto space-y-1.5">
          <router-link v-for="item in navItems" :key="item.to" :to="item.to" class="block" @click="isMenuOpen = false">
            <div
              class="px-3 py-2.5 rounded-xl text-sm flex items-center justify-between gap-2"
              :class="isActive(item.to) ? 'bg-primary/15 text-[#1A1A1A] font-semibold' : 'text-gray-600'"
            >
              <span class="inline-flex items-center gap-2.5 min-w-0">
                <component :is="item.icon" class="h-4 w-4 shrink-0 text-gray-400" />
                <span class="truncate">{{ item.label }}</span>
              </span>
              <Lock v-if="item.locked" class="h-4 w-4 text-gray-400 shrink-0" />
            </div>
          </router-link>
        </nav>
        <button
          type="button"
          @click="handleLogout"
          class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium"
        >
          <LogOut class="h-4 w-4" />
          {{ t('nav.logout') }}
        </button>
      </aside>
    </div>
  </template>

  <header v-else class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 md:px-8 py-3 flex items-center justify-between gap-4">
      <div class="flex items-center gap-3 cursor-pointer" @click="navigateToHome">
        <img :src="logoUrl" alt="CAT Apps" class="w-8 h-8 object-contain" />
        <span class="font-bold tracking-tight">{{ t('app.name') }}</span>
      </div>
      <nav class="hidden md:flex items-center gap-5 text-sm text-gray-600">
        <button type="button" class="hover:text-[#1A1A1A]" @click="goPublicSection('hero')">{{ t('nav.home') }}</button>
        <button type="button" class="hover:text-[#1A1A1A]" @click="goPublicSection('services')">Layanan</button>
        <button type="button" class="hover:text-[#1A1A1A]" @click="goPublicSection('programs')">Program</button>
        <button type="button" class="hover:text-[#1A1A1A]" @click="goPublicSection('leaders')">Pembina</button>
      </nav>
      <div class="flex items-center gap-2">
        <div class="p-1 rounded-full border border-gray-200 bg-white shadow-sm flex gap-1">
          <button type="button" class="px-4 py-1.5 text-xs font-semibold rounded-full transition-all" :class="locale === 'id' ? 'bg-black text-white shadow' : 'text-gray-700 hover:bg-gray-50'" @click="setLocale('id')">ID</button>
          <button type="button" class="px-4 py-1.5 text-xs font-semibold rounded-full transition-all" :class="locale === 'en' ? 'bg-black text-white shadow' : 'text-gray-700 hover:bg-gray-50'" @click="setLocale('en')">EN</button>
        </div>
        <router-link to="/login" class="px-4 py-2 rounded-full bg-[#1A1A1A] text-white text-sm font-medium">{{ t('nav.login') }}</router-link>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import {
  Bell,
  BookOpen,
  ClipboardCheck,
  ClipboardList,
  FileStack,
  GraduationCap,
  History,
  Home,
  Layers,
  LayoutDashboard,
  LibraryBig,
  Lock,
  LogOut,
  Menu,
  Newspaper,
  PencilLine,
  UserCircle2,
  Users,
  X,
} from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'
import { useI18n } from '@/composables/useI18n'
import { getProgramBadge, registrationCompleted, isAppExpired, isExamOnlyProgram } from '@/utils/userMeta'

const store = useAppStore()
const route = useRoute()
const router = useRouter()
const { role, user, isAuthenticated } = storeToRefs(store)
const { t, locale, setLocale } = useI18n()
const logoUrl = new URL('../../assets/favicon_io/android-chrome-192x192.png', import.meta.url).href
const isMenuOpen = ref(false)
const unreadCount = ref(0)
const isAdmin = computed(() => role.value === 'admin')
const isStudent = computed(() => role.value === 'user')
const isExamOnlyStudent = computed(() => isStudent.value && isExamOnlyProgram(user.value))
const onboardingDone = computed(() => registrationCompleted(user.value))
const appExpired = computed(() => isAppExpired(user.value))
const programBadge = computed(() => getProgramBadge(user.value))

const isActive = (to) => {
  if (to === '/') return route.path === '/'
  return route.path === to || route.path.startsWith(`${to}/`)
}

const navItems = computed(() => {
  if (!isAuthenticated.value) {
    return [
      { to: '/', label: t('nav.home'), icon: Home },
      { to: '/blog', label: t('nav.materials'), icon: Newspaper },
    ]
  }

  if (isStudent.value) {
    if (appExpired.value) {
      return [
        { to: '/profile', label: 'Profil', icon: UserCircle2 },
        { to: '/activity-history', label: 'Riwayat Aktivitas', icon: History },
      ]
    }

    const studentItems = [
      { to: '/dashboard', label: t('nav.dashboard'), icon: LayoutDashboard, locked: !onboardingDone.value },
      { to: '/ujian', label: 'Ujian', icon: ClipboardCheck, locked: !onboardingDone.value },
    ]

    if (!isExamOnlyStudent.value) {
      studentItems.push({ to: '/my-classes', label: t('nav.myClasses'), icon: GraduationCap, locked: !onboardingDone.value })
    }

    studentItems.push(
      { to: '/profile', label: 'Profil', icon: UserCircle2 },
      { to: '/activity-history', label: 'Riwayat Aktivitas', icon: History },
      { to: '/registration', label: t('nav.registrationWizard'), icon: PencilLine },
      { to: '/notifications', label: t('nav.notifications'), icon: Bell },
    )

    return studentItems
  }

  // Admin / mentor
  const items = [
    { to: '/dashboard', label: t('nav.dashboard'), icon: LayoutDashboard },
    { to: '/exams', label: 'Ujian', icon: ClipboardCheck },
    { to: '/bimble-classes', label: t('nav.bimbleClasses'), icon: GraduationCap },
    { to: '/materials', label: t('nav.manageMaterials'), icon: BookOpen },
    { to: '/tests', label: t('nav.tests'), icon: ClipboardList },
    { to: '/question-bank', label: t('nav.questionBank'), icon: LibraryBig },
  ]

  if (isAdmin.value) {
    items.push({ to: '/admin/exam-categories', label: 'Kategori Ujian', icon: Layers })
    items.push({ to: '/batches', label: t('nav.batches'), icon: FileStack })
    items.push({ to: '/users', label: t('nav.users'), icon: Users })
    items.push({ to: '/profile', label: 'Pengaturan', icon: UserCircle2 })
  }

  return items
})

const handleLogout = async () => {
  isMenuOpen.value = false
  await store.logout()
  router.push('/')
}

const navigateToHome = () => {
  router.push('/')
}

const goPublicSection = async (id) => {
  if (route.name !== 'home') {
    await router.push({ path: '/', hash: `#${id}` })
    return
  }
  const el = document.getElementById(id)
  if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

async function loadUnreadCount() {
  if (!isAuthenticated.value) {
    unreadCount.value = 0
    return
  }
  try {
    const { data } = await axios.get('/api/notifications')
    unreadCount.value = Number(data?.unread_count || 0)
  } catch (error) {
    unreadCount.value = 0
  }
}

watch(isAuthenticated, loadUnreadCount)
onMounted(loadUnreadCount)
</script>

<style scoped>
</style>
