<template>
  <div class="space-y-4">
    <div v-for="test in tests" :key="test.id" class="ui-card relative overflow-hidden">
      <!-- Status Badge -->
      <div class="absolute top-4 right-4">
        <span 
          v-if="test.status === 'upcoming'" 
          class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200"
        >
          🔒 Locked
        </span>
        <span 
          v-else-if="test.status === 'ongoing'" 
          class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200"
        >
          ✓ Available
        </span>
        <span 
          v-else 
          class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200"
        >
          Ended
        </span>
      </div>

      <div class="pr-24">
        <h3 class="text-lg font-semibold text-text">{{ test.name }}</h3>
        <p v-if="test.description" class="text-sm text-muted mt-1">{{ test.description }}</p>
        
        <div class="mt-4 flex flex-wrap gap-4 text-sm">
          <div class="flex items-center gap-2">
            <span class="text-muted">Category:</span>
            <span class="font-medium capitalize">{{ test.category }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-muted">Duration:</span>
            <span class="font-medium">{{ test.duration }} min</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-muted">Questions:</span>
            <span class="font-medium">{{ test.question_ids?.length || 0 }}</span>
          </div>
        </div>

        <!-- Time Information -->
        <div class="mt-4 p-3 rounded-lg bg-gray-50 border border-gray-200">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-xs text-muted mb-1">Start Time</div>
              <div class="text-sm font-medium">{{ formatDateTime(test.start_time) }}</div>
            </div>
            <div class="text-right">
              <div class="text-xs text-muted mb-1">End Time</div>
              <div class="text-sm font-medium">{{ formatDateTime(test.end_time) }}</div>
            </div>
          </div>

          <!-- Countdown Timer -->
          <div v-if="test.status === 'upcoming' && countdown[test.id]" class="mt-3 pt-3 border-t border-gray-200">
            <div class="text-center">
              <div class="text-xs text-muted mb-1">Time until start</div>
              <div class="text-2xl font-bold text-brand">
                {{ countdown[test.id] }}
              </div>
            </div>
          </div>

          <!-- Ongoing message -->
          <div v-else-if="test.status === 'ongoing'" class="mt-3 pt-3 border-t border-gray-200">
            <div class="text-center">
              <div class="text-sm font-medium text-green-700">Test is now available!</div>
            </div>
          </div>
        </div>

        <!-- Action Button -->
        <div class="mt-4">
          <button 
            v-if="test.status === 'upcoming'"
            disabled
            class="w-full px-4 py-2 rounded-md bg-gray-300 text-gray-600 cursor-not-allowed"
          >
            🔒 Test Locked - {{ countdown[test.id] }}
          </button>
          <router-link
            v-else-if="test.status === 'ongoing'"
            :to="{ name: 'quick-test', params: { id: test.id } }"
            class="block w-full px-4 py-2 rounded-md bg-green-600 text-white text-center font-medium hover:bg-green-700 transition-colors cursor-pointer"
          >
            ✓ Start Test Now
          </router-link>
          <router-link
            v-else
            :to="{ name: 'quick-test', params: { id: test.id } }"
            class="block w-full px-4 py-2 rounded-md bg-blue-600 text-white text-center font-medium hover:bg-blue-700 transition-colors cursor-pointer"
          >
            👁️ Review Test (View Only)
          </router-link>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="tests.length === 0" class="ui-card text-center py-12">
      <div class="text-4xl mb-4">📅</div>
      <h3 class="text-lg font-semibold text-text mb-2">No Tests Available</h3>
      <p class="text-muted">There are currently no scheduled tests.</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import axios from 'axios'
import { useToast } from '@/composables/useNotification'

const tests = ref([])
const countdown = ref({})
const toast = useToast()
let intervalId = null

const fetchTests = async () => {
  try {
    const response = await axios.get('/api/incoming-tests')
    tests.value = response.data
    updateCountdowns()
  } catch (error) {
    console.error('Failed to fetch tests:', error)
    toast.error('Failed to Load Tests', 'Could not fetch test data. Please try again.')
  }
}

const formatDateTime = (dateTime) => {
  if (!dateTime) return 'Not set'
  const date = new Date(dateTime)
  return date.toLocaleString('id-ID', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatCountdown = (seconds) => {
  if (seconds <= 0) return 'Starting...'
  
  const days = Math.floor(seconds / (24 * 3600))
  const hours = Math.floor((seconds % (24 * 3600)) / 3600)
  const minutes = Math.floor((seconds % 3600) / 60)
  const secs = seconds % 60

  const parts = []
  if (days > 0) parts.push(`${days}d`)
  if (hours > 0) parts.push(`${hours}h`)
  if (minutes > 0) parts.push(`${minutes}m`)
  if (secs > 0 || parts.length === 0) parts.push(`${secs}s`)

  return parts.join(' ')
}

const updateCountdowns = () => {
  const now = new Date()
  
  tests.value.forEach(test => {
    if (test.status === 'upcoming' && test.start_time) {
      const startTime = new Date(test.start_time)
      const diff = Math.floor((startTime - now) / 1000)
      countdown.value[test.id] = formatCountdown(Math.max(0, diff))
      
      // If countdown reached zero, refresh tests
      if (diff <= 0) {
        fetchTests()
      }
    }
  })
}

// Expose fetchTests so parent can call it
defineExpose({
  fetchTests
})

onMounted(() => {
  fetchTests()
  
  // Update countdown every second
  intervalId = setInterval(() => {
    updateCountdowns()
  }, 1000)
  
  // Refresh tests every 30 seconds
  const refreshInterval = setInterval(fetchTests, 30000)
  
  onUnmounted(() => {
    if (intervalId) clearInterval(intervalId)
    if (refreshInterval) clearInterval(refreshInterval)
  })
})
</script>

<style scoped>
</style>
