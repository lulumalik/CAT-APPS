<template>
  <header class="sticky top-0 z-50 w-full bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6">
          <img :src="logoUrl" alt="CAT Apps" class="w-6 h-6 rounded-full object-cover" />
        </div>
        <span class="font-semibold">CAT Platform</span>
      </div>

      <!-- Mobile Menu Button -->
      <button @click="isMenuOpen = !isMenuOpen" class="md:hidden p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
        <span class="text-xl">☰</span>
      </button>

      <!-- Desktop Nav -->
      <nav class="hidden md:flex items-center gap-4 text-sm">
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="text-muted hover:text-text">Dashboard</router-link>
          <template v-if="role==='admin'">
            <router-link to="/question-bank" class="text-muted hover:text-text">Question Bank</router-link>
            <router-link to="/tests" class="text-muted hover:text-text">Tests</router-link>
            <router-link to="/users" class="text-muted hover:text-text">Users</router-link>
          </template>
          <router-link to="/rankings" class="text-muted hover:text-text">Rankings</router-link>
          <div class="flex items-center gap-3 ml-2 pl-3 border-l border-gray-200">
            <span class="text-muted">{{ user?.name || user?.email }}</span>
            <button @click="handleLogout" class="px-4 py-1.5 rounded-md border border-gray-200 bg-white hover:bg-gray-50">
              Logout
            </button>
          </div>
        </template>
        <template v-else>
          <router-link to="/rankings" class="text-muted hover:text-text">Rankings</router-link>
          <router-link to="/login" class="px-4 py-1.5 rounded-md border border-gray-200 bg-white">Login</router-link>
          <router-link to="/" class="px-4 py-1.5 rounded-md bg-navy text-white">Get Started</router-link>
        </template>
      </nav>
    </div>

    <!-- Mobile Nav Dropdown -->
    <div v-if="isMenuOpen" class="md:hidden border-t border-gray-100 bg-white">
      <nav class="flex flex-col p-4 space-y-3">
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="block py-2 text-muted hover:text-text" @click="isMenuOpen = false">Dashboard</router-link>
          <template v-if="role==='admin'">
            <router-link to="/question-bank" class="block py-2 text-muted hover:text-text" @click="isMenuOpen = false">Question Bank</router-link>
            <router-link to="/tests" class="block py-2 text-muted hover:text-text" @click="isMenuOpen = false">Tests</router-link>
            <router-link to="/users" class="block py-2 text-muted hover:text-text" @click="isMenuOpen = false">Users</router-link>
          </template>
          <router-link to="/rankings" class="block py-2 text-muted hover:text-text" @click="isMenuOpen = false">Rankings</router-link>
          <div class="pt-3 mt-2 border-t border-gray-100">
            <div class="mb-3 text-sm text-muted">{{ user?.name || user?.email }}</div>
            <button @click="handleLogout" class="w-full px-4 py-2 rounded-md border border-gray-200 bg-white hover:bg-gray-50 text-left">
              Logout
            </button>
          </div>
        </template>
        <template v-else>
          <router-link to="/rankings" class="block py-2 text-muted hover:text-text" @click="isMenuOpen = false">Rankings</router-link>
          <router-link to="/login" class="block w-full text-center px-4 py-2 rounded-md border border-gray-200 bg-white" @click="isMenuOpen = false">Login</router-link>
          <router-link to="/" class="block w-full text-center px-4 py-2 rounded-md bg-navy text-white" @click="isMenuOpen = false">Get Started</router-link>
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
