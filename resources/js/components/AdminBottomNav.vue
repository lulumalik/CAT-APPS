<template>
  <nav class="admin-bottom-nav md:hidden" aria-label="Navigasi admin">
    <router-link
      v-for="item in items"
      :key="item.to"
      :to="item.to"
      class="admin-bottom-nav__item"
      :class="{ 'is-active': isActive(item.to) }"
    >
      <span class="admin-bottom-nav__indicator" aria-hidden="true" />
      <component :is="item.icon" class="admin-bottom-nav__icon" stroke-width="2" />
      <span class="admin-bottom-nav__label">{{ item.label }}</span>
    </router-link>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import {
  ClipboardList,
  GraduationCap,
  Home,
  Settings,
  Users,
} from 'lucide-vue-next'

const route = useRoute()

const items = computed(() => [
  { to: '/dashboard', label: 'Dashboard', icon: Home },
  { to: '/bimble-classes', label: 'Kelas', icon: GraduationCap },
  { to: '/exams', label: 'Tryout', icon: ClipboardList },
  { to: '/users', label: 'Peserta', icon: Users },
  { to: '/profile', label: 'Pengaturan', icon: Settings },
])

const isActive = (to) => {
  if (to === '/dashboard') return route.path === '/dashboard'
  return route.path === to || route.path.startsWith(`${to}/`)
}
</script>
