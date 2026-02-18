<template>
  <main class="min-h-screen bg-bg py-6">
    <div class="mx-auto max-w-7xl px-4">
      <!-- Header -->
      <div class="flex items-center justify-between mb-6">
        <div>
          <h1 class="text-xl font-semibold">{{ testData?.name || 'Computer Assisted Test' }}</h1>
          <p class="text-muted">{{ testData?.description || testData?.category || 'Assessment' }}</p>
        </div>
        <div class="flex items-center gap-4 pr-4">
          <div class="flex items-center gap-2 text-navy">
            <span class="w-5 h-5 grid place-items-center">⏱</span>
            <span class="font-medium text-lg">{{ mm }}:{{ ss }}</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Question Area - Wider -->
        <div class="lg:col-span-3">
          <!-- Progress Bar -->
          <div class="ui-card">
            <div class="flex items-center justify-between">
              <span class="text-sm">Progress</span>
              <span class="text-sm font-medium">{{ answeredCount }} / {{ questions.length }} answered</span>
            </div>
            <div class="mt-3 h-3 rounded-full bg-gray-200">
              <div class="h-3 rounded-full bg-brand transition-all" :style="{ width: progressPct + '%' }"></div>
            </div>
          </div>

          <!-- Question Card -->
          <div class="ui-card mt-6">
            <div class="flex items-center justify-between mb-6">
              <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-700 border border-blue-200">
                {{ current.category }}
              </span>
              <button 
                @click="toggleFlag(index)" 
                class="px-4 py-1.5 rounded-md border transition-colors cursor-pointer"
                :class="flags[index] ? 'border-yellow-500 bg-yellow-50 text-yellow-700' : 'border-gray-200 hover:border-yellow-300'"
              >
                {{ flags[index] ? '🚩 Flagged' : '⚐ Flag' }}
              </button>
            </div>

            <div class="mb-6">
              <h2 class="text-sm text-muted mb-1">Question {{ index + 1 }} of {{ questions.length }}</h2>
              <p class="text-xl font-medium leading-relaxed">{{ current.question }}</p>
            </div>

            <!-- Options -->
            <div class="space-y-3">
              <div 
                v-for="opt in current.options" 
                :key="opt.key" 
                @click="canSubmit && selectOption(opt.key)" 
                class="rounded-lg border-2 bg-white px-5 py-4 flex items-center gap-4 transition-all"
                :class="[
                  selected(index) === opt.key 
                    ? 'border-brand bg-brand/5' 
                    : 'border-gray-200 hover:border-brand/50',
                  canSubmit ? 'cursor-pointer' : 'cursor-not-allowed opacity-60'
                ]"
              >
                <span 
                  class="w-8 h-8 rounded-full border-2 grid place-items-center font-semibold flex-shrink-0"
                  :class="selected(index) === opt.key 
                    ? 'border-brand bg-brand text-white' 
                    : 'border-gray-300 text-gray-600'"
                >
                  {{ opt.key }}
                </span>
                <span class="text-base">{{ opt.label }}</span>
              </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-8 flex items-center justify-between">
              <button 
                class="px-6 py-2.5 rounded-md border border-gray-300 hover:bg-gray-50 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer" 
                @click="prev" 
                :disabled="index === 0"
              >
                ← Previous
              </button>
              <button 
                class="px-6 py-2.5 rounded-md bg-navy text-white hover:bg-navy/90 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer" 
                @click="next" 
                :disabled="index === questions.length - 1"
              >
                Next →
              </button>
            </div>
          </div>

          <!-- Warning if expired -->
          <div v-if="!canSubmit" class="mt-4 ui-card bg-yellow-50 border-yellow-200">
            <div class="flex items-center gap-3">
              <span class="text-2xl">⚠️</span>
              <div>
                <h3 class="font-semibold text-yellow-800">Test Time Expired</h3>
                <p class="text-sm text-yellow-700">You can review the questions but cannot submit answers.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar Navigator -->
        <aside class="lg:col-span-1">
          <div class="ui-card sticky top-6">
            <h3 class="font-semibold mb-4">Question Navigator</h3>
            <div class="grid grid-cols-4 sm:grid-cols-5 md:grid-cols-6 gap-2">
              <button 
                v-for="(q, i) in questions" 
                :key="i" 
                @click="go(i)" 
                class="aspect-square rounded-md text-sm font-medium transition-all relative cursor-pointer"
                :class="navClass(i)"
              >
                {{ i + 1 }}
                <span v-if="flags[i]" class="absolute -top-1 -right-1 text-xs">🚩</span>
              </button>
            </div>

            <!-- Legend -->
            <div class="mt-6 pt-6 border-t border-gray-200 space-y-3 text-sm">
              <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-green-100 border border-green-300"></span> 
                <span>Answered ({{ answeredCount }})</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-gray-100 border border-gray-300"></span> 
                <span>Not Answered ({{ questions.length - answeredCount }})</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="w-5 h-5 rounded bg-brand border border-brand"></span> 
                <span>Current</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-base">🚩</span> 
                <span>Flagged ({{ flaggedCount }})</span>
              </div>
            </div>

            <button 
              @click="submitTest" 
              :disabled="!canSubmit"
              class="mt-6 w-full px-4 py-2.5 rounded-md text-white font-medium transition-colors"
              :class="canSubmit ? 'bg-red-600 hover:bg-red-700 cursor-pointer' : 'bg-gray-400 cursor-not-allowed'"
            >
              {{ canSubmit ? 'Submit Test' : 'Expired' }}
            </button>
          </div>
        </aside>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useModal, useToast } from '@/composables/useNotification'
import axios from 'axios'

const router = useRouter()
const route = useRoute()
const { confirm, success, alert } = useModal()
const toast = useToast()

const testId = route.params.id
const testData = ref(null)
const questions = ref([])
const canSubmit = ref(true)

const index = ref(0)
const answers = ref([])
const flags = ref([])

const current = computed(() => questions.value[index.value] || {})
const answeredCount = computed(() => answers.value.filter(Boolean).length)
const flaggedCount = computed(() => flags.value.filter(Boolean).length)
const progressPct = computed(() => questions.value.length ? Math.round(answeredCount.value / questions.value.length * 100) : 0)

const selected = (i) => answers.value[i]
const selectOption = (key) => {
  if (canSubmit.value) {
    answers.value[index.value] = key
  }
}

const toggleFlag = (i) => {
  flags.value[i] = !flags.value[i]
}

const go = (i) => index.value = i
const next = () => index.value = Math.min(index.value + 1, questions.value.length - 1)
const prev = () => index.value = Math.max(index.value - 1, 0)

const navClass = (i) => {
  if (i === index.value) return 'bg-brand text-white border-2 border-brand'
  return answers.value[i] ? 'bg-green-100 border-2 border-green-300 text-green-700' : 'bg-gray-100 border-2 border-gray-300'
}

const submitTest = async () => {
  if (!canSubmit.value) return
  
  const unanswered = questions.value.length - answeredCount.value
  let message = `You have answered ${answeredCount.value} out of ${questions.value.length} questions.`
  
  if (unanswered > 0) {
    message += ` ${unanswered} questions are still unanswered.`
  }
  
  message += ' Are you sure you want to submit your test?'

  const confirmed = await confirm(
    'Submit Test',
    message,
    'Submit Now',
    'Continue Test'
  )
  
  if (confirmed) {
    try {
      // Format answers sebagai object { questionId: answer }
      const formattedAnswers = {}
      questions.value.forEach((q, idx) => {
        if (answers.value[idx]) {
          formattedAnswers[q.id] = answers.value[idx]
        }
      })

      const response = await axios.post(`/api/tests/${testId}/submit`, {
        answers: formattedAnswers
      })

      await success(
        'Test Submitted!',
        `Your score: ${response.data.score} / ${response.data.total}`,
        'View Results'
      )
      router.push('/dashboard')
    } catch (error) {
      toast.error(error.response?.data?.message || 'Failed to submit test')
    }
  }
}

// Timer - calculated from server time
const totalSeconds = ref(0)
const mm = computed(() => String(Math.floor(totalSeconds.value / 60)).padStart(2, '0'))
const ss = computed(() => String(totalSeconds.value % 60).padStart(2, '0'))

let timer
const updateRemainingTime = () => {
  if (!testData.value?.end_time) return
  
  const now = new Date()
  const endTime = new Date(testData.value.end_time)
  const diffInSeconds = Math.floor((endTime - now) / 1000)
  
  if (diffInSeconds <= 0) {
    totalSeconds.value = 0
    canSubmit.value = false
  } else {
    totalSeconds.value = diffInSeconds
  }
}

// Load test data
const loadTest = async () => {
  try {
    const response = await axios.get(`/api/tests/${testId}`)
    testData.value = response.data
    
    // Check if user already submitted
    if (response.data.has_submitted) {
      await alert(
        'Already Submitted',
        'You have already completed this test.',
        'Back to Dashboard'
      )
      router.push('/dashboard')
      return
    }

    // Check if test is locked or expired
    const now = new Date()
    const startTime = new Date(response.data.start_time)
    const endTime = new Date(response.data.end_time)

    if (now < startTime) {
      await alert(
        'Test Not Started',
        'This test has not started yet.',
        'Back to Dashboard'
      )
      router.push('/dashboard')
      return
    }

    if (now > endTime) {
      canSubmit.value = false
      toast.warning('This test has expired. You can view questions but cannot submit.')
    }

    // Setup questions
    questions.value = response.data.questions || []
    answers.value = Array(questions.value.length).fill(null)
    flags.value = Array(questions.value.length).fill(false)

    // Initialize timer
    updateRemainingTime()
    
    // Update timer every second
    timer = setInterval(() => { 
      updateRemainingTime()
    }, 1000)

  } catch (error) {
    toast.error(error.response?.data?.message || 'Failed to load test')
    router.push('/dashboard')
  }
}

onMounted(() => { 
  loadTest()
})

onBeforeUnmount(() => { 
  if (timer) clearInterval(timer) 
})
</script>

<style scoped>
</style>
