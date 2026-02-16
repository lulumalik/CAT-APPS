<template>
  <main class="container-main py-8">
    <div v-if="user" class="mb-6">
      <h1 class="text-2xl font-semibold">Welcome back, {{ user.name }}!</h1>
      <p class="text-muted mt-1">{{ user.email }} - <span class="capitalize">{{ user.role }}</span></p>
    </div>
    <div v-else class="mb-6">
      <h1 class="text-2xl font-semibold">Welcome back</h1>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="ui-card">
        <div class="text-3xl font-bold text-brand">{{ stats.average }}%</div>
        <div class="text-sm text-muted mt-1">Average Score</div>
      </div>
      <div class="ui-card">
        <div class="text-3xl font-bold text-brand">{{ stats.completed }}</div>
        <div class="text-sm text-muted mt-1">Tests Completed</div>
      </div>
    </div>

    <div class="mt-8">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">My Test History</h2>
        <button @click="fetchHistory" class="text-sm text-brand hover:underline cursor-pointer">
          🔄 Refresh
        </button>
      </div>
      <div v-if="history.length===0" class="ui-card text-center">
        <div class="text-6xl">🗎</div>
        <div class="mt-2 font-medium">Belum ada hasil</div>
        <div class="text-muted">Ikuti test untuk melihat riwayat skor di sini</div>
      </div>
      <div v-else class="space-y-4">
        <div v-for="item in history" :key="item.test_id" class="ui-card flex items-center justify-between">
          <div>
            <div class="font-semibold">{{ item.name }}</div>
            <div class="text-sm text-muted">{{ item.category }}</div>
          </div>
          <div class="text-right">
            <div class="text-lg font-semibold text-brand">{{ item.score }} / {{ item.total }} ({{ item.percentage }}%)</div>
            <div class="text-sm text-muted">{{ formatDate(item.submitted_at) }}</div>
          </div>
        </div>
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
import { ref, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import { useAppStore } from '@/stores/app'
import IncomingTests from '@/components/IncomingTests.vue'

const store = useAppStore()
const { user } = storeToRefs(store)
const incomingTestsRef = ref(null)
const history = ref([])
const stats = ref({ completed: 0, average: 0 })

const refreshTests = () => {
  if (incomingTestsRef.value && incomingTestsRef.value.fetchTests) {
    incomingTestsRef.value.fetchTests()
  }
}

const formatDate = (d) => {
  if (!d) return ''
  const dt = new Date(d)
  return dt.toLocaleString()
}

const fetchHistory = async () => {
  try {
    const { data } = await window.axios.get('/api/my-tests')
    history.value = data.items || []
    stats.value = data.stats || { completed: 0, average: 0 }
  } catch (error) {
  }
}

onMounted(() => {
  fetchHistory()
})
</script>

<style scoped>
</style>
