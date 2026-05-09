<template>
  <template v-if="isAuthenticated">
    <button type="button" class="md:hidden fixed top-4 left-4 z-[60] rounded-full bg-[#1A1A1A] text-white w-10 h-10 grid place-items-center" @click="isMenuOpen = true">
      <Menu class="h-5 w-5" />
    </button>

    <aside class="hidden md:flex fixed left-0 top-0 h-screen w-72 bg-gradient-to-b from-[#FFFDE4] via-[#eef2f3] to-[#eef2f3] border-r border-gray-100 shadow-xl shadow-black/5 z-40 flex-col">
      <div class="px-6 py-6 border-b border-gray-100">
        <div class="flex items-center gap-3 cursor-pointer" @click="navigateToHome">
          <img :src="logoUrl" alt="CAT Apps" class="w-9 h-9 object-contain" />
          <div>
            <div class="font-bold text-[#1A1A1A] leading-tight">{{ t('app.name') }}</div>
            <div class="text-xs text-gray-500">Navigator</div>
          </div>
        </div>
      </div>
      <nav class="flex-1 overflow-y-auto p-4 space-y-2">
        <router-link v-for="item in navItems" :key="item.to" :to="item.to" class="block">
          <div
            class="px-3 py-2.5 rounded-xl text-sm transition-colors flex items-center justify-between gap-2"
            :class="route.path === item.to ? 'bg-[#9DB359]/15 text-[#1A1A1A] font-semibold' : 'text-gray-600 hover:bg-gray-50 hover:text-[#1A1A1A]'"
          >
            <span>{{ item.label }}</span>
            <Lock v-if="item.locked" class="h-4 w-4 text-gray-400" />
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
        <button @click="handleLogout" class="w-full px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium">
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
        <nav class="flex-1 overflow-y-auto space-y-2">
          <router-link v-for="item in navItems" :key="item.to" :to="item.to" class="block px-3 py-2 rounded-lg text-sm" @click="isMenuOpen = false">
            <div class="flex items-center justify-between gap-2">
              <span>{{ item.label }}</span>
              <Lock v-if="item.locked" class="h-4 w-4 text-gray-400" />
            </div>
          </router-link>
        </nav>
        <button @click="handleLogout" class="w-full px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium">
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
import { Bell, Lock, Menu, X } from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'
import { useI18n } from '@/composables/useI18n'
import { getProgramBadge, registrationCompleted } from '@/utils/userMeta'

const store = useAppStore()
const route = useRoute()
const router = useRouter()
const { role, user, isAuthenticated } = storeToRefs(store)
const { t, locale, setLocale } = useI18n()
const logoUrl = new URL('../../assets/logo.png', import.meta.url).href
const isMenuOpen = ref(false)
const unreadCount = ref(0)
const isAdmin = computed(() => role.value === 'admin')
const isStudent = computed(() => role.value === 'user')
const onboardingDone = computed(() => registrationCompleted(user.value))
const programBadge = computed(() => getProgramBadge(user.value))

const navItems = computed(() => {
  if (!isAuthenticated.value) {
    return [
      { to: '/', label: t('nav.home') },
      { to: '/rankings', label: t('nav.rankings') },
      { to: '/blog', label: t('nav.materials') },
    ]
  }

  if (isStudent.value) {
    return [
      { to: '/dashboard', label: t('nav.dashboard'), locked: !onboardingDone.value },
      { to: '/my-classes', label: t('nav.myClasses'), locked: !onboardingDone.value },
      { to: '/profile', label: 'Profil' },
      { to: '/registration', label: t('nav.registrationWizard') },
      { to: '/announcements', label: 'Announcement' },
      { to: '/notifications', label: 'Notifikasi' },
    ]
  }

  const items = [
    { to: '/', label: t('nav.home') },
    { to: '/dashboard', label: t('nav.dashboard') },
    { to: '/my-classes', label: t('nav.myClasses') },
    { to: '/bimble-classes', label: t('nav.bimbleClasses') },
    { to: '/materials', label: t('nav.manageMaterials') },
    { to: '/tests', label: t('nav.tests') },
    { to: '/question-bank', label: t('nav.questionBank') },
    { to: '/blog', label: t('nav.materials') },
    { to: '/rankings', label: t('nav.rankings') },
    { to: '/announcements', label: 'Announcement' },
    { to: '/notifications', label: 'Notifikasi' },
  ]

  if (isAdmin.value) {
    items.push({ to: '/users', label: t('nav.users') })
    items.push({ to: '/admin/registration', label: t('nav.adminRegistration') })
    items.push({ to: '/admin/announcements', label: 'Announcement management' })
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
