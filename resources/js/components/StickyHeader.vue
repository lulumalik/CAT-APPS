<template>
  <header class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur-md border-b border-gray-100/50 supports-[backdrop-filter]:bg-white/60">
    <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 flex items-center justify-center">
          <img :src="logoUrl" alt="CAT Apps" class="w-full h-full object-contain" />
        </div>
        <span class="font-bold tracking-tight text-[#1A1A1A]">CAT Platform</span>
      </div>

      <!-- Mobile Menu Button -->
      <button @click="isMenuOpen = !isMenuOpen" class="md:hidden p-2 text-gray-600 hover:text-gray-900 focus:outline-none rounded-full hover:bg-gray-100 transition-colors">
        <span class="text-xl">☰</span>
      </button>

      <!-- Desktop Nav -->
      <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">Dashboard</router-link>
          <template v-if="['admin', 'mentor'].includes(role)">
            <router-link to="/question-bank" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">Question Bank</router-link>
            <router-link to="/tests" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">Tests</router-link>
          </template>
          <template v-if="role==='admin'">
            <router-link to="/users" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">Users</router-link>
          </template>
          <router-link to="/rankings" class="text-gray-600 hover:text-[#1A1A1A] transition-colors" active-class="text-[#1A1A1A] font-semibold">Rankings</router-link>
          
          <div class="flex items-center gap-4 ml-2 pl-4 border-l border-gray-200">
            <div class="flex flex-col items-end leading-tight">
              <span class="text-sm font-medium text-[#1A1A1A]">{{ user?.name || user?.email }}</span>
              <span class="text-xs text-gray-500 capitalize">{{ user?.role }}</span>
            </div>
            <button @click="handleLogout" class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium transition-all hover:border-gray-300">
              Logout
            </button>
          </div>
        </template>
        <template v-else>
          <router-link to="/rankings" class="text-gray-600 hover:text-[#1A1A1A] transition-colors">Rankings</router-link>
          <router-link to="/login" class="px-5 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm font-medium transition-all">Login</router-link>
          <router-link to="/" class="px-5 py-2 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 text-sm font-medium transition-all shadow-lg shadow-black/10">Get Started</router-link>
        </template>
      </nav>
    </div>

    <!-- Mobile Nav Dropdown -->
    <div v-if="isMenuOpen" class="md:hidden border-t border-gray-100 bg-white/95 backdrop-blur-xl shadow-2xl rounded-b-[2rem] border-b border-gray-100 transform transition-all">
      <nav class="flex flex-col p-6 space-y-4">
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">Dashboard</router-link>
          <template v-if="['admin', 'mentor'].includes(role)">
            <router-link to="/question-bank" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">Question Bank</router-link>
            <router-link to="/tests" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">Tests</router-link>
          </template>
          <template v-if="role==='admin'">
            <router-link to="/users" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">Users</router-link>
          </template>
          <router-link to="/rankings" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" active-class="bg-gray-50 text-[#1A1A1A] font-bold" @click="isMenuOpen = false">Rankings</router-link>
          <div class="pt-4 mt-2 border-t border-gray-100">
            <div class="px-4 mb-4 text-sm font-bold text-[#1A1A1A]">{{ user?.name || user?.email }}</div>
            <button @click="handleLogout" class="w-full px-6 py-3 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-left text-sm font-bold text-gray-700 shadow-sm transition-all">
              Logout
            </button>
          </div>
        </template>
        <template v-else>
          <router-link to="/rankings" class="block py-3 px-4 rounded-xl hover:bg-gray-50 text-gray-600 hover:text-[#1A1A1A] font-medium transition-all" @click="isMenuOpen = false">Rankings</router-link>
          <div class="grid grid-cols-2 gap-4 mt-4">
            <router-link to="/login" class="block w-full text-center px-6 py-3 rounded-full border border-gray-200 bg-white hover:bg-gray-50 font-bold text-gray-700 transition-all" @click="isMenuOpen = false">Login</router-link>
            <router-link to="/" class="block w-full text-center px-6 py-3 rounded-full bg-[#1A1A1A] text-white hover:bg-black font-bold shadow-lg shadow-black/20 transition-all" @click="isMenuOpen = false">Get Started</router-link>
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

const store = useAppStore()
const router = useRouter()
const { role, user, isAuthenticated } = storeToRefs(store)
const logoUrl = new URL('../../assets/logo.png', import.meta.url).href
const isMenuOpen = ref(false)

const handleLogout = async () => {
  isMenuOpen.value = false
  await store.logout()
  router.push('/')
}
</script>

<style scoped>
</style>
