<template>
  <main class="container-main py-8">
    <div v-if="user" class="mb-6">
      <h1 class="text-2xl font-semibold">Welcome back, {{ user.name }}!</h1>
      <p class="text-muted mt-1">{{ user.email }} - <span class="capitalize">{{ user.role }}</span></p>
    </div>
    <div v-else class="mb-6">
      <h1 class="text-2xl font-semibold">Welcome back</h1>
    </div>

    <!-- Stats Cards -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="ui-card">
        <div class="text-3xl font-bold text-brand">85.2%</div>
        <div class="text-sm text-muted mt-1">Average Score</div>
      </div>
      <div class="ui-card">
        <div class="text-3xl font-bold text-brand">24</div>
        <div class="text-sm text-muted mt-1">Tests Completed</div>
      </div>
      <div class="ui-card">
        <div class="text-3xl font-bold text-brand">#127</div>
        <div class="text-sm text-muted mt-1">Global Rank</div>
      </div>
      <div class="ui-card">
        <div class="text-3xl font-bold text-brand">12.5h</div>
        <div class="text-sm text-muted mt-1">Study Time</div>
      </div>
    </div>

    <!-- Incoming Tests Section -->
    <div class="mt-8">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Incoming Tests</h2>
        <button @click="refreshTests" class="text-sm text-brand hover:underline cursor-pointer">
          🔄 Refresh
        </button>
      </div>
      <IncomingTests ref="incomingTestsRef" />
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useAppStore } from '@/stores/app'
import IncomingTests from '@/components/IncomingTests.vue'

const store = useAppStore()
const { user } = storeToRefs(store)
const incomingTestsRef = ref(null)

const refreshTests = () => {
  if (incomingTestsRef.value && incomingTestsRef.value.fetchTests) {
    incomingTestsRef.value.fetchTests()
  }
}
</script>

<style scoped>
</style>