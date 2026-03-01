<template>
  <div class="space-y-6">
    <div v-for="test in tests" :key="test.id" class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-8 relative overflow-hidden hover:shadow-2xl hover:shadow-black/5 transition-all duration-300 group">
      <!-- Status Badge -->
      <div class="absolute top-8 right-8">
        <span 
          v-if="test.status === 'upcoming'" 
          class="px-4 py-1.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 border border-yellow-100 flex items-center gap-1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
          Locked
        </span>
        <span 
          v-else-if="test.status === 'ongoing'" 
          class="px-4 py-1.5 rounded-full text-xs font-semibold bg-[#9DB359]/10 text-[#9DB359] border border-[#9DB359]/20 flex items-center gap-1"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
          Available
        </span>
        <span 
          v-else 
          class="px-4 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 border border-gray-200"
        >
          Ended
        </span>
      </div>

      <div class="pr-0 md:pr-32">
        <h3 class="text-xl font-bold text-[#1A1A1A] group-hover:text-[#9DB359] transition-colors">{{ test.name }}</h3>
        <p v-if="test.description" class="text-sm text-gray-500 mt-2 leading-relaxed max-w-2xl">{{ test.description }}</p>
        
        <div class="mt-6 flex flex-wrap gap-6 text-sm">
          <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg">
            <span class="text-gray-400">Category:</span>
            <span class="font-semibold capitalize text-[#1A1A1A]">{{ test.category }}</span>
          </div>
          <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg">
            <span class="text-gray-400">Duration:</span>
            <span class="font-semibold text-[#1A1A1A]">{{ test.duration }} min</span>
          </div>
          <div class="flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-1.5 rounded-lg">
            <span class="text-gray-400">Questions:</span>
            <span class="font-semibold text-[#1A1A1A]">{{ test.question_ids?.length || 0 }}</span>
          </div>
        </div>

        <!-- Time Information -->
        <div class="mt-6 p-4 rounded-xl bg-gray-50/50 border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <div class="text-xs text-gray-400 mb-1 font-medium uppercase tracking-wider">Start Time</div>
              <div class="text-sm font-semibold text-[#1A1A1A]">{{ formatDateTime(test.start_time) }}</div>
            </div>
            <div class="text-right">
              <div class="text-xs text-gray-400 mb-1 font-medium uppercase tracking-wider">End Time</div>
              <div class="text-sm font-semibold text-[#1A1A1A]">{{ formatDateTime(test.end_time) }}</div>
            </div>
          </div>

          <!-- Countdown Timer -->
          <div v-if="test.status === 'upcoming' && countdown[test.id]" class="mt-4 pt-4 border-t border-gray-200/50">
            <div class="text-center">
              <div class="text-xs text-gray-400 mb-1">Time until start</div>
              <div class="text-2xl font-bold text-[#9DB359] font-mono">
                {{ countdown[test.id] }}
              </div>
            </div>
          </div>

          <!-- Ongoing message -->
          <div v-else-if="test.status === 'ongoing'" class="mt-4 pt-4 border-t border-gray-200/50">
            <div class="text-center">
              <div class="text-sm font-semibold text-[#9DB359] flex items-center justify-center gap-2">
                <span class="relative flex h-2 w-2">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#9DB359] opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2 w-2 bg-[#9DB359]"></span>
                </span>
                Test is now available!
              </div>
            </div>
          </div>
        </div>

        <!-- Action Button -->
        <div class="mt-6">
          <button 
            v-if="test.status === 'upcoming'"
            disabled
            class="w-full px-6 py-3 rounded-full bg-gray-100 text-gray-400 font-medium cursor-not-allowed border border-gray-200"
          >
            Locked
          </button>
          <router-link
            v-else-if="test.status === 'ongoing'"
            :to="{ name: 'quick-test', params: { id: test.id } }"
            class="block w-full px-6 py-3 rounded-full bg-[#9DB359] text-white text-center font-semibold hover:bg-[#8ca34b] transition-all shadow-lg shadow-[#9DB359]/20 hover:scale-[1.01] active:scale-[0.99]"
          >
            Start Test Now
          </router-link>
          <router-link
            v-else
            :to="{ name: 'quick-test', params: { id: test.id } }"
            class="block w-full px-6 py-3 rounded-full bg-[#1A1A1A] text-white text-center font-medium hover:bg-black transition-all shadow-lg shadow-black/10"
          >
            Review Test (View Only)
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const tests = ref([])
const countdown = ref({})
const timerInterval = ref(null)

const fetchTests = async () => {
  try {
    const { data } = await window.axios.get('/api/incoming-tests')
    tests.value = data.data
    updateCountdowns()
  } catch (error) {
    console.error('Failed to fetch tests', error)
  }
}

const formatDateTime = (dateStr) => {
  if (!dateStr) return '-'
  return new Date(dateStr).toLocaleString('id-ID', {
    weekday: 'short',
    day: 'numeric',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  })
}

const updateCountdowns = () => {
  const now = new Date().getTime()
  
  tests.value.forEach(test => {
    if (test.status === 'upcoming') {
      const startTime = new Date(test.start_time).getTime()
      const distance = startTime - now
      
      if (distance < 0) {
        test.status = 'ongoing'
        delete countdown.value[test.id]
      } else {
        const days = Math.floor(distance / (1000 * 60 * 60 * 24))
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))
        const seconds = Math.floor((distance % (1000 * 60)) / 1000)
        
        countdown.value[test.id] = `${days}d ${hours}h ${minutes}m ${seconds}s`
      }
    }
  })
}

onMounted(() => {
  fetchTests()
  timerInterval.value = setInterval(updateCountdowns, 1000)
})

onUnmounted(() => {
  if (timerInterval.value) clearInterval(timerInterval.value)
})

defineExpose({ fetchTests })
</script>
