<template>
  <header class="sticky top-0 z-50 bg-white/80 backdrop-blur border-b border-gray-200 w-screen">
    <div class="mx-auto max-w-7xl px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <div class="w-6 h-6">
          <img :src="logoUrl" alt="CAT Apps" class="w-6 h-6 rounded-full object-cover" />
        </div>
        <span class="font-semibold">CAT Platform</span>
      </div>
      <nav class="flex items-center gap-4">
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="text-sm text-muted hover:text-text">Dashboard</router-link>
          <template v-if="role==='admin'">
            <router-link to="/question-bank" class="text-sm text-muted hover:text-text">Question Bank</router-link>
            <router-link to="/tests" class="text-sm text-muted hover:text-text">Tests</router-link>
          </template>
          <router-link to="/rankings" class="text-sm text-muted hover:text-text">Rankings</router-link>
          <div class="flex items-center gap-3 ml-2 pl-3 border-l border-gray-200">
            <span class="text-sm text-muted">{{ user?.name || user?.email }}</span>
            <button @click="handleLogout" class="px-4 py-1.5 rounded-md border border-gray-200 bg-white text-sm hover:bg-gray-50">
              Logout
            </button>
          </div>
        </template>
        <template v-else>
          <router-link to="/rankings" class="text-sm text-muted hover:text-text">Rankings</router-link>
          <router-link to="/login" class="px-4 py-1.5 rounded-md border border-gray-200 bg-white text-sm">Login</router-link>
          <router-link to="/" class="px-4 py-1.5 rounded-md bg-navy text-white text-sm">Get Started</router-link>
        </template>
      </nav>
    </div>
  </header>
</template>

<script setup>
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'

const store = useAppStore()
const router = useRouter()
const { role, user, isAuthenticated } = storeToRefs(store)
const logoUrl = new URL('../../assets/logo.png', import.meta.url).href

const handleLogout = async () => {
  await store.logout()
  router.push('/')
}
</script>

<style scoped>
</style>
