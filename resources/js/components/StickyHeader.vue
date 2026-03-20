<template>
  <header class="sticky top-0 z-50 rounded-lg shadow-md w-full bg-white/80 backdrop-blur-md border-b border-gray-100/50 supports-[backdrop-filter]:bg-white/60">
    <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2 cursor-pointer" @click="navigateToHome">
        <div class="w-8 h-8 flex items-center justify-center">
          <img :src="logoUrl" alt="CAT Apps" class="w-full h-full object-contain" />
        </div>
        <span class="font-bold tracking-tight text-[#1A1A1A]">{{ t('app.name') }}</span>
      </div>

      <!-- Mobile Menu Button -->
      <button @click="isMenuOpen = !isMenuOpen" class="md:hidden p-2 text-gray-600 hover:text-gray-900 focus:outline-none rounded-full hover:bg-gray-100 transition-colors">
        <span class="text-xl">☰</span>
      </button>

      <!-- Desktop Nav -->
      <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">{{ t('nav.dashboard') }}</router-link>
          <template v-if="['admin', 'mentor'].includes(role)">
            <router-link to="/question-bank" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">{{ t('nav.questionBank') }}</router-link>
            <router-link to="/tests" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">{{ t('nav.tests') }}</router-link>
            <router-link to="/materials" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">{{ t('nav.manageMaterials') }}</router-link>
          </template>
          <template v-if="role==='admin'">
            <router-link to="/users" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">{{ t('nav.users') }}</router-link>
          </template>
          <router-link to="/rankings" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">{{ t('nav.rankings') }}</router-link>
          <router-link to="/blog" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">{{ t('nav.materials') }}</router-link>
          <div class="flex items-center rounded-full border border-gray-200 bg-white shadow-sm overflow-hidden">
            <button
              type="button"
              class="px-3 py-2 text-xs font-semibold transition-colors"
              :class="locale === 'id' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
              @click="setLocale('id')"
            >
              ID
            </button>
            <button
              type="button"
              class="px-3 py-2 text-xs font-semibold transition-colors"
              :class="locale === 'en' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
              @click="setLocale('en')"
            >
              EN
            </button>
          </div>
          
          <div class="flex items-center gap-4 ml-2 pl-4 border-l border-gray-200">
            <div class="flex flex-col items-end leading-tight">
              <span class="text-sm font-medium text-[#1A1A1A]">{{ user?.name || user?.email }}</span>
              <span class="text-xs text-gray-500 capitalize">{{ user?.role }}</span>
            </div>
            <button @click="handleLogout" class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium transition-all hover:border-gray-300">
              {{ t('nav.logout') }}
            </button>
          </div>
        </template>
        <template v-else>
          <router-link to="/rankings" class="text-gray-600 hover:text-[#1A1A1A] transition-colors">{{ t('nav.rankings') }}</router-link>
          <router-link to="/blog" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">Materi</router-link>
          <div class="flex items-center rounded-full border border-gray-200 bg-white shadow-sm overflow-hidden">
            <button
              type="button"
              class="px-3 py-2 text-xs font-semibold transition-colors"
              :class="locale === 'id' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
              @click="setLocale('id')"
            >
              ID
            </button>
            <button
              type="button"
              class="px-3 py-2 text-xs font-semibold transition-colors"
              :class="locale === 'en' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
              @click="setLocale('en')"
            >
              EN
            </button>
          </div>
          <router-link to="/login" class="px-5 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium transition-all">{{ t('nav.login') }}</router-link>
          <router-link to="/" class="px-5 py-2 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 text-sm font-medium transition-all shadow-lg shadow-black/10">{{ t('nav.getStarted') }}</router-link>
        </template>
      </nav>
    </div>

    <!-- Mobile Nav Dropdown -->
    <div v-if="isMenuOpen" class="md:hidden border-t border-gray-100 bg-white/95 backdrop-blur-xl shadow-2xl rounded-b-[2rem] border-b border-gray-100 transform transition-all">
      <nav class="flex flex-col p-6 space-y-4">
        <div class="flex items-center justify-end">
          <div class="flex items-center rounded-full border border-gray-200 bg-white shadow-sm overflow-hidden">
            <button
              type="button"
              class="px-3 py-2 text-xs font-semibold transition-colors"
              :class="locale === 'id' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
              @click="setLocale('id')"
            >
              ID
            </button>
            <button
              type="button"
              class="px-3 py-2 text-xs font-semibold transition-colors"
              :class="locale === 'en' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
              @click="setLocale('en')"
            >
              EN
            </button>
          </div>
        </div>
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">{{ t('nav.dashboard') }}</router-link>
          <template v-if="['admin', 'mentor'].includes(role)">
            <router-link to="/question-bank" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">{{ t('nav.questionBank') }}</router-link>
            <router-link to="/tests" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">{{ t('nav.tests') }}</router-link>
            <router-link to="/materials" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">{{ t('nav.manageMaterials') }}</router-link>
          </template>
          <template v-if="role==='admin'">
            <router-link to="/users" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">{{ t('nav.users') }}</router-link>
          </template>
          <router-link to="/rankings" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">{{ t('nav.rankings') }}</router-link>
          <router-link to="/blog" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">{{ t('nav.materials') }}</router-link>
          <div class="pt-4 mt-2 border-t border-gray-100">
            <div class="px-4 mb-4 text-sm font-bold text-[#1A1A1A]">{{ user?.name || user?.email }}</div>
            <button @click="handleLogout" class="w-full px-6 py-3 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-left text-sm font-bold text-gray-700 shadow-sm transition-all">
              {{ t('nav.logout') }}
            </button>
          </div>
        </template>
        <template v-else>
          <router-link to="/rankings" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" @click="isMenuOpen = false">{{ t('nav.rankings') }}</router-link>
          <router-link to="/blog" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" @click="isMenuOpen = false">{{ t('nav.materials') }}</router-link>
          <div class="grid grid-cols-2 gap-4 mt-4">
            <router-link to="/login" class="block w-full text-center px-6 py-3 rounded-full border border-gray-200 bg-white hover:bg-gray-50 font-bold text-gray-700 transition-all" @click="isMenuOpen = false">{{ t('nav.login') }}</router-link>
            <router-link to="/" class="block w-full text-center px-6 py-3 rounded-full bg-[#1A1A1A] text-white hover:bg-black font-bold shadow-lg shadow-black/20 transition-all" @click="isMenuOpen = false">{{ t('nav.getStarted') }}</router-link>
          </div>
        </template>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { useI18n } from '@/composables/useI18n'

const store = useAppStore()
const router = useRouter()
const { role, user, isAuthenticated } = storeToRefs(store)
const { t, locale, setLocale } = useI18n()
const logoUrl = new URL('../../assets/logo.png', import.meta.url).href
const isMenuOpen = ref(false)

const handleLogout = async () => {
  isMenuOpen.value = false
  await store.logout()
  router.push('/')
}

const navigateToHome = () => {
  router.push('/')
}
</script>

<style scoped>
</style>
