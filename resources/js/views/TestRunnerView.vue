<template>
  <main class="min-h-screen bg-[#F9F9F7] py-8 font-sans text-[#1A1A1A]">
    <div class="mx-auto max-w-7xl px-4 md:px-12">
      <!-- Header -->
      <div class="flex items-center justify-between mb-8">
        <div>
          <h1 class="text-2xl font-bold text-[#1A1A1A]">{{ testData?.name || 'Computer Assisted Test' }}</h1>
          <p class="text-gray-500 mt-1 flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-[#9DB359]"></span>
            {{ testData?.description || testData?.category || 'Assessment' }}
          </p>
        </div>
        <div class="flex items-center gap-4">
          <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
            <div class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <span class="font-bold text-xl text-[#1A1A1A] font-mono">{{ mm }}:{{ ss }}</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Question Area - Wider -->
        <div class="lg:col-span-3 space-y-6">
          <!-- Progress Bar -->
          <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">Progress</span>
              <span class="text-sm font-bold text-[#1A1A1A]">{{ answeredCount }} <span class="text-gray-400 font-normal">/</span> {{ questions.length }} <span class="text-gray-400 font-normal">answered</span></span>
            </div>
            <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
              <div class="h-full rounded-full bg-[#9DB359] transition-all duration-500 ease-out" :style="{ width: progressPct + '%' }"></div>
            </div>
          </div>

          <!-- Question Card -->
          <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
              <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wide">
                {{ current.category }}
              </span>
              <button 
                @click="toggleFlag(index)" 
                class="px-4 py-1.5 rounded-full border transition-all cursor-pointer flex items-center gap-2 text-sm font-medium"
                :class="flags[index] ? 'border-yellow-200 bg-yellow-50 text-yellow-700' : 'border-gray-200 text-gray-500 hover:border-yellow-300 hover:text-yellow-600'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :fill="flags[index] ? 'currentColor' : 'none'"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                {{ flags[index] ? 'Flagged' : 'Flag' }}
              </button>
            </div>

            <div class="mb-8">
              <h2 class="text-sm font-medium text-gray-400 mb-2 uppercase tracking-wide">Question {{ index + 1 }}</h2>
              <div v-if="current.image" class="mb-6">
                  <img :src="current.image" class="max-h-80 object-contain rounded-2xl border border-gray-200 shadow-sm" />
              </div>
              <p class="text-2xl font-medium leading-relaxed text-[#1A1A1A]">{{ current.question }}</p>
            </div>

            <!-- Options -->
            <div v-if="current.type === 'multiple_choice' || !current.type" class="space-y-4">
              <div 
                v-for="opt in current.options" 
                :key="opt.key" 
                @click="canSubmit && selectOption(opt.key)" 
                class="group rounded-xl border-2 px-6 py-5 flex items-center gap-5 transition-all relative overflow-hidden"
                :class="[
                  selected(index) === opt.key 
                    ? 'border-[#9DB359] bg-[#9DB359]/5' 
                    : 'border-gray-100 bg-white hover:border-[#9DB359]/50 hover:bg-gray-50',
                  canSubmit ? 'cursor-pointer' : 'cursor-not-allowed opacity-60'
                ]"
              >
                <div 
                  class="w-10 h-10 rounded-full border-2 flex items-center justify-center font-bold text-sm flex-shrink-0 transition-colors"
                  :class="selected(index) === opt.key 
                    ? 'border-[#9DB359] bg-[#9DB359] text-white' 
                    : 'border-gray-200 text-gray-400 group-hover:border-[#9DB359]/50 group-hover:text-gray-600'"
                >
                  {{ opt.key }}
                </div>
                <span class="text-lg text-[#1A1A1A]" :class="{'font-medium': selected(index) === opt.key}">{{ opt.label }}</span>
                
                <!-- Checkmark for selected -->
                <div v-if="selected(index) === opt.key" class="absolute right-6 top-1/2 -translate-y-1/2 text-[#9DB359]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
              </div>
            </div>

            <!-- Essay Input -->
            <div v-else-if="current.type === 'essay'" class="space-y-3">
                <textarea 
                    v-model="answers[index]" 
                    rows="8" 
                    placeholder="Type your answer here..."
                    class="w-full rounded-xl border border-gray-200 p-5 focus:border-[#9DB359] focus:ring-1 focus:ring-[#9DB359] outline-none text-lg resize-none shadow-sm transition-colors"
                    :disabled="!canSubmit"
                ></textarea>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-10 flex items-center justify-between pt-8 border-t border-gray-50">
              <button 
                class="px-8 py-3 rounded-full border border-gray-200 hover:bg-gray-50 transition-colors font-medium text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2" 
                @click="prev" 
                :disabled="index === 0"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Previous
              </button>
              
              <button 
                v-if="index < questions.length - 1" 
                class="px-8 py-3 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors font-medium cursor-pointer shadow-lg shadow-black/10 flex items-center gap-2" 
                @click="next"
              >
                Next
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
              </button>
              
              <button 
                v-else 
                class="px-8 py-3 rounded-full bg-[#9DB359] text-white hover:bg-[#8ca34b] transition-colors font-bold cursor-pointer shadow-lg shadow-[#9DB359]/20 flex items-center gap-2" 
                @click="finishTest"
                :disabled="!canSubmit"
              >
                Submit Test
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
            </div>
          </div>
        </div>

        <!-- Sidebar - Question Navigator -->
        <aside class="lg:col-span-1">
          <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 sticky top-24">
            <h3 class="font-bold text-[#1A1A1A] mb-4 flex items-center gap-2">
              <span class="w-1.5 h-6 rounded-full bg-[#9DB359]"></span>
              Question Navigator
            </h3>
            
            <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-4 gap-3">
              <button 
                v-for="(q, i) in questions" 
                :key="i"
                @click="jumpTo(i)"
                class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all border-2 cursor-pointer relative"
                :class="[
                  index === i 
                    ? 'border-[#1A1A1A] bg-[#1A1A1A] text-white scale-110 shadow-lg' 
                    : answers[i] 
                      ? 'border-[#9DB359] bg-[#9DB359]/10 text-[#9DB359]' 
                      : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-gray-300'
                ]"
              >
                {{ i + 1 }}
                <span v-if="flags[i]" class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-400 rounded-full border-2 border-white"></span>
              </button>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-50 space-y-3">
              <div class="flex items-center gap-3 text-xs font-medium text-gray-500">
                <span class="w-3 h-3 rounded-full bg-[#1A1A1A]"></span>
                Current
              </div>
              <div class="flex items-center gap-3 text-xs font-medium text-gray-500">
                <span class="w-3 h-3 rounded-full bg-[#9DB359]/10 border border-[#9DB359]"></span>
                Answered
              </div>
              <div class="flex items-center gap-3 text-xs font-medium text-gray-500">
                <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                Flagged
              </div>
              <div class="flex items-center gap-3 text-xs font-medium text-gray-500">
                <span class="w-3 h-3 rounded-full bg-gray-50 border border-gray-200"></span>
                Not Answered
              </div>
            </div>
          </div>
        </aside>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast, useModal } from '@/composables/useNotification'
import { useAppStore } from '@/stores/app'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { confirm } = useModal()
const store = useAppStore()

const testId = route.params.id
const questions = ref([])
const testData = ref(null)
const index = ref(0)
const answers = ref({})
const flags = ref({})
const timeLeft = ref(0)
const timer = ref(null)
const canSubmit = ref(true)

const current = computed(() => questions.value[index.value] || {})
const selected = (idx) => answers.value[idx]

const answeredCount = computed(() => Object.keys(answers.value).length)
const progressPct = computed(() => {
  if (questions.value.length === 0) return 0
  return Math.round((answeredCount.value / questions.value.length) * 100)
})

const mm = computed(() => String(Math.floor(timeLeft.value / 60)).padStart(2, '0'))
const ss = computed(() => String(timeLeft.value % 60).padStart(2, '0'))

const fetchTest = async () => {
  try {
    const { data } = await window.axios.get(`/api/tests/${testId}/start`)
    testData.value = data.test
    questions.value = data.questions
    timeLeft.value = data.remaining_time * 60 // minutes to seconds
    startTimer()
  } catch (error) {
    toast.error('Error', 'Failed to start test or test not found')
    router.push('/dashboard')
  }
}

const startTimer = () => {
  timer.value = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      finishTest(true)
    }
  }, 1000)
}

const selectOption = (key) => {
  answers.value[index.value] = key
}

const next = () => {
  if (index.value < questions.value.length - 1) index.value++
}

const prev = () => {
  if (index.value > 0) index.value--
}

const jumpTo = (i) => {
  index.value = i
}

const toggleFlag = (i) => {
  flags.value[i] = !flags.value[i]
}

const finishTest = async (force = false) => {
  if (!force) {
    const confirmed = await confirm({
      title: 'Submit Test',
      message: `You have answered ${answeredCount.value} of ${questions.value.length} questions. Are you sure you want to submit?`,
      confirmText: 'Submit Now',
      cancelText: 'Review'
    })
    if (!confirmed) return
  }

  clearInterval(timer.value)
  canSubmit.value = false

  try {
    await window.axios.post(`/api/tests/${testId}/submit`, {
      answers: answers.value
    })
    toast.success('Test Submitted', 'Your answers have been recorded.')
    router.push('/dashboard')
  } catch (error) {
    toast.error('Error', 'Failed to submit test')
    canSubmit.value = true // Allow retry
  }
}

onMounted(() => {
  fetchTest()
})

onUnmounted(() => {
  if (timer.value) clearInterval(timer.value)
})

// Prevent accidental navigation
window.onbeforeunload = () => {
  if (canSubmit.value) return "Are you sure you want to leave? Your progress may be lost."
}
</script>

<style scoped>
</style>
