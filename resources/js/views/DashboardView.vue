<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div v-if="user" class="mb-10 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-[#1A1A1A]">Welcome back, {{ user.name }}!</h1>
        <p class="text-gray-500 mt-1 flex items-center gap-2">
          {{ user.email }} 
          <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
          <span class="capitalize px-2 py-0.5 rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ user.role }}</span>
        </p>
      </div>
      <div class="hidden md:block text-right">
        <div class="text-sm text-gray-500">Today is</div>
        <div class="font-medium text-[#1A1A1A]">{{ new Date().toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</div>
      </div>
    </div>
    <div v-else class="mb-10">
      <h1 class="text-3xl font-bold text-[#1A1A1A]">Welcome back</h1>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-8 flex items-center justify-between group hover:border-[#9DB359]/30 transition-colors">
        <div>
          <div class="text-5xl font-bold text-[#9DB359] mb-1">{{ stats.average }}%</div>
          <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Average Score</div>
        </div>
        <div class="w-16 h-16 rounded-full bg-[#9DB359]/10 flex items-center justify-center text-2xl text-[#9DB359]">
          📊
        </div>
      </div>
      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-8 flex items-center justify-between group hover:border-[#9DB359]/30 transition-colors">
        <div>
          <div class="text-5xl font-bold text-[#1A1A1A] mb-1">{{ stats.completed }}</div>
          <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Tests Completed</div>
        </div>
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-2xl text-gray-600">
          ✅
        </div>
      </div>
    </div>

    <div class="mt-12 grid grid-cols-1 lg:grid-cols-3 gap-10">
      <!-- Incoming Tests Section (Left, Wider) -->
      <div class="lg:col-span-2 space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-[#1A1A1A] flex items-center gap-2">
            <span class="w-2 h-8 rounded-full bg-[#9DB359]"></span>
            Incoming Tests
          </h2>
          <button @click="refreshTests" class="text-sm font-medium text-[#9DB359] hover:text-[#8ca34b] cursor-pointer flex items-center gap-1 transition-colors px-3 py-1.5 rounded-full hover:bg-[#9DB359]/10">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="animate-spin-slow"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38"/></svg>
            Refresh
          </button>
        </div>
        <IncomingTests ref="incomingTestsRef" />
      </div>

      <!-- Test History Section (Right, Narrower) -->
      <div class="space-y-6">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-[#1A1A1A] flex items-center gap-2">
            <span class="w-2 h-8 rounded-full bg-gray-800"></span>
            Recent History
          </h2>
          <button @click="fetchHistory" class="text-sm font-medium text-gray-500 hover:text-[#1A1A1A] cursor-pointer flex items-center gap-1 transition-colors px-3 py-1.5 rounded-full hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38"/></svg>
            Refresh
          </button>
        </div>

        <div v-if="history.length===0" class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-8 text-center min-h-[300px] flex flex-col items-center justify-center">
          <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-4 grayscale opacity-50">🗎</div>
          <div class="font-bold text-lg text-[#1A1A1A]">No History Yet</div>
          <div class="text-gray-500 mt-2 text-sm max-w-[200px]">Complete a test to see your score history here</div>
        </div>
        
        <div v-else class="space-y-4">
          <div v-for="item in history" :key="item.test_id" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow group">
            <div class="flex items-start justify-between mb-3">
              <div>
                <div class="font-bold text-[#1A1A1A] line-clamp-1 group-hover:text-[#9DB359] transition-colors">{{ item.name }}</div>
                <div class="text-xs text-gray-500 font-medium bg-gray-50 px-2 py-0.5 rounded inline-block mt-1 capitalize">{{ item.category }}</div>
              </div>
              <div class="text-right">
                <div class="text-lg font-bold" :class="getScoreColor(item.percentage)">{{ item.score }}</div>
                <div class="text-[10px] text-gray-400 uppercase tracking-wide">Score</div>
              </div>
            </div>
            
            <div class="w-full bg-gray-100 rounded-full h-1.5 mb-3">
              <div class="bg-[#9DB359] h-1.5 rounded-full transition-all duration-1000" :style="{ width: item.percentage + '%' }"></div>
            </div>

            <div class="flex items-center justify-between text-xs text-gray-400">
              <span>{{ formatDate(item.submitted_at) }}</span>
              <span class="font-medium text-gray-600">{{ item.percentage }}% Correct</span>
            </div>
          </div>
        </div>
      </div>
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
  return dt.toLocaleDateString('en-US', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
}

const getScoreColor = (percentage) => {
  if (percentage >= 80) return 'text-[#9DB359]'
  if (percentage >= 60) return 'text-yellow-500'
  return 'text-red-500'
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
.animate-spin-slow {
  animation: spin 3s linear infinite;
}
@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>
