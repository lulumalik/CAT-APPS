<template>
  <main class="h-screen bg-[#F9F9F7] py-4 md:py-6 font-sans text-[#1A1A1A] anti-cheat-mode overflow-hidden">
    <div class="mx-auto max-w-7xl px-4 md:px-8 h-full flex flex-col min-h-0">
      <div
        v-if="limitReached"
        class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900 font-medium"
      >
        Kamu sudah terlalu banyak hal yang melanggar aturan anti-cheat. Tetap di halaman ujian dan lanjutkan mengerjakan soal.
      </div>
      <div
        v-else-if="antiCheatMessage"
        class="mb-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900"
      >
        {{ antiCheatMessage }} ({{ violations }}/{{ maxViolations }})
      </div>

      <div class="flex items-center justify-between mb-4 md:mb-6 shrink-0">
        <div>
          <h1 class="text-2xl font-bold text-[#1A1A1A]">{{ testData?.name || t('testRunner.defaultTitle') }}</h1>
          <p class="text-gray-500 mt-1 flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full bg-[#9DB359]"></span>
            {{ testData?.description || testData?.category || t('testRunner.defaultSubtitle') }}
          </p>
        </div>
        <div class="flex items-center gap-4">
          <div class="rounded-full border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-800">
            Anti-cheat: {{ violations }}/{{ maxViolations }}
          </div>
          <div class="flex items-center gap-3 bg-white px-4 py-2 rounded-full shadow-sm border border-gray-100">
            <div class="w-8 h-8 rounded-full bg-red-50 text-red-500 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            </div>
            <span class="font-bold text-xl text-[#1A1A1A] font-mono">{{ mm }}:{{ ss }}</span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 gap-4 md:gap-6 flex-1 min-h-0" :class="isExam ? '' : 'lg:grid-cols-4'">
        <div class="space-y-4 min-h-0 flex flex-col" :class="isExam ? '' : 'lg:col-span-3'">
          <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-4 md:p-5 shrink-0">
            <div class="flex items-center justify-between mb-3">
              <span class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ t('testRunner.progress') }}</span>
              <span class="text-sm font-bold text-[#1A1A1A]">{{ answeredCount }} <span class="text-gray-400 font-normal">/</span> {{ questions.length }} <span class="text-gray-400 font-normal">{{ t('testRunner.answered') }}</span></span>
            </div>
            <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
              <div class="h-full rounded-full bg-[#9DB359] transition-all duration-500 ease-out" :style="{ width: progressPct + '%' }"></div>
            </div>
          </div>

          <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-4 md:p-6 flex flex-col min-h-0 flex-1">
            <div class="flex items-center justify-between mb-4 md:mb-6 shrink-0">
              <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wide">
                {{ current.category }}
              </span>
              <button
                @click="toggleFlag(index)"
                class="px-4 py-1.5 rounded-full border transition-all cursor-pointer flex items-center justify-center gap-2 text-sm font-medium"
                :class="flags[index] ? 'border-yellow-200 bg-yellow-50 text-yellow-700' : 'border-gray-200 text-gray-500 hover:border-yellow-300 hover:text-yellow-600'"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :fill="flags[index] ? 'currentColor' : 'none'"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path><line x1="4" y1="22" x2="4" y2="15"></line></svg>
                {{ flags[index] ? t('testRunner.flagged') : t('testRunner.flag') }}
              </button>
            </div>

            <div class="mb-4 md:mb-6 shrink-0">
              <h2 class="text-sm font-medium text-gray-400 mb-2 uppercase tracking-wide">{{ t('testRunner.question', { n: index + 1 }) }}</h2>
              <div v-if="current.image" class="mb-6">
                <img :src="current.image" class="max-h-80 object-contain rounded-2xl border border-gray-200 shadow-sm" />
              </div>
              <p class="text-xl md:text-2xl font-medium leading-relaxed text-[#1A1A1A]">{{ current.question }}</p>
            </div>

            <div v-if="current.type === 'multiple_choice' || !current.type" class="space-y-3 overflow-y-auto pr-1 min-h-0">
              <div
                v-for="opt in current.options"
                :key="opt.key"
                @click="canSubmit && !submitting && selectOption(opt.key)"
                class="group rounded-xl border-2 px-4 md:px-6 py-4 flex items-center gap-4 transition-all relative overflow-hidden"
                :class="[
                  selected(index) === opt.key
                    ? 'border-[#9DB359] bg-[#9DB359]/5'
                    : 'border-gray-100 bg-white hover:border-[#9DB359]/50 hover:bg-gray-50',
                  canSubmit && !submitting ? 'cursor-pointer' : 'cursor-not-allowed opacity-60',
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
                <span class="text-base md:text-lg text-[#1A1A1A]" :class="{ 'font-medium': selected(index) === opt.key }">{{ opt.label }}</span>
                <div v-if="selected(index) === opt.key" class="absolute right-6 top-1/2 -translate-y-1/2 text-[#9DB359]">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
              </div>
            </div>

            <div v-else-if="current.type === 'essay'" class="space-y-3 min-h-0">
              <textarea
                v-model="answers[current.id]"
                rows="8"
                :placeholder="t('testRunner.answerPlaceholder')"
                class="w-full rounded-xl border border-gray-200 p-5 focus:border-[#9DB359] focus:ring-1 focus:ring-[#9DB359] outline-none text-lg resize-none shadow-sm transition-colors"
                :disabled="!canSubmit || submitting"
              ></textarea>
            </div>

            <div class="mt-4 md:mt-6 flex flex-wrap items-center justify-between gap-3 pt-4 md:pt-6 border-t border-gray-50 shrink-0">
              <div v-if="isExam" class="text-sm font-medium text-gray-500">
                Soal {{ index + 1 }} / {{ questions.length }}
              </div>
              <div v-else class="hidden sm:block"></div>
              <button
                class="px-5 md:px-8 py-2.5 md:py-3 rounded-full border border-gray-200 hover:bg-gray-50 transition-colors font-medium text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed cursor-pointer flex items-center gap-2"
                @click="prev"
                :disabled="index === 0"
              >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                {{ t('testRunner.previous') }}
              </button>

              <button
                v-if="index < questions.length - 1"
                class="px-5 md:px-8 py-2.5 md:py-3 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors font-medium cursor-pointer shadow-lg shadow-black/10 flex items-center gap-2"
                @click="next"
              >
                {{ t('testRunner.next') }}
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
              </button>

              <button
                v-else
                class="px-5 md:px-8 py-2.5 md:py-3 rounded-full bg-[#9DB359] text-white hover:bg-[#8ca34b] transition-colors font-bold cursor-pointer shadow-lg shadow-[#9DB359]/20 flex items-center gap-2"
                @click="finishTest"
                :disabled="!canSubmit || submitting"
              >
                {{ submitting ? '...' : t('testRunner.submit') }}
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
              </button>
            </div>
          </div>
        </div>

        <aside v-if="!isExam" class="lg:col-span-1 min-h-0">
          <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-5 sticky top-4">
            <h3 class="font-bold text-[#1A1A1A] mb-4 flex items-center gap-2">
              <span class="w-1.5 h-6 rounded-full bg-[#9DB359]"></span>
              {{ t('testRunner.questionNavigator') }}
            </h3>

            <div class="grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-4 gap-3">
              <button
                v-for="(q, i) in questions"
                :key="q.id ?? i"
                @click="jumpTo(i)"
                class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-bold transition-all border-2 cursor-pointer relative"
                :class="[
                  index === i
                    ? 'border-[#1A1A1A] bg-[#1A1A1A] text-white scale-110 shadow-lg'
                    : answers[q.id]
                      ? 'border-[#9DB359] bg-[#9DB359]/10 text-[#9DB359]'
                      : 'border-gray-100 bg-gray-50 text-gray-400 hover:border-gray-300',
                ]"
              >
                {{ i + 1 }}
                <span v-if="flags[i]" class="absolute -top-1 -right-1 w-3 h-3 bg-yellow-400 rounded-full border-2 border-white"></span>
              </button>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-50 space-y-3">
              <div class="flex items-center gap-3 text-xs font-medium text-gray-500">
                <span class="w-3 h-3 rounded-full bg-[#1A1A1A]"></span>
                {{ t('testRunner.legendCurrent') }}
              </div>
              <div class="flex items-center gap-3 text-xs font-medium text-gray-500">
                <span class="w-3 h-3 rounded-full bg-[#9DB359]/10 border border-[#9DB359]"></span>
                {{ t('testRunner.legendAnswered') }}
              </div>
              <div class="flex items-center gap-3 text-xs font-medium text-gray-500">
                <span class="w-3 h-3 rounded-full bg-yellow-400"></span>
                {{ t('testRunner.legendFlagged') }}
              </div>
              <div class="flex items-center gap-3 text-xs font-medium text-gray-500">
                <span class="w-3 h-3 rounded-full bg-gray-50 border border-gray-200"></span>
                {{ t('testRunner.legendUnanswered') }}
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
import { useModal } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'
import { useAntiCheat } from '@/composables/useAntiCheat'

const props = defineProps({
  testData: { type: Object, required: true },
  questions: { type: Array, required: true },
  submitting: { type: Boolean, default: false },
  isExam: { type: Boolean, default: false },
})

const emit = defineEmits(['submit'])

const { confirm } = useModal()
const { t } = useI18n()

const index = ref(0)
const answers = ref({})
const flags = ref({})
const timeLeft = ref(0)
const timer = ref(null)
const canSubmit = ref(true)

const { violations, antiCheatMessage, maxViolations, limitReached, attach, detach, requestFullscreen } = useAntiCheat({
  maxViolations: 5,
  onMaxViolations: () => {
    requestFullscreen()
  },
  isActive: () => canSubmit.value && !props.submitting,
})

const progressStorageKey = computed(() =>
  props.isExam && props.testData?.id ? `exam-progress-${props.testData.id}` : null,
)

function saveProgress() {
  const key = progressStorageKey.value
  if (!key) return
  sessionStorage.setItem(key, JSON.stringify({
    index: index.value,
    answers: answers.value,
    flags: flags.value,
  }))
}

function restoreProgress() {
  const key = progressStorageKey.value
  if (!key) return
  try {
    const raw = sessionStorage.getItem(key)
    if (!raw) return
    const saved = JSON.parse(raw)
    if (Number.isInteger(saved.index) && saved.index >= 0 && saved.index < props.questions.length) {
      index.value = saved.index
    }
    if (saved.answers && typeof saved.answers === 'object') {
      answers.value = { ...saved.answers }
    }
    if (saved.flags && typeof saved.flags === 'object') {
      flags.value = { ...saved.flags }
    }
  } catch {
    // ignore corrupt storage
  }
}

function clearProgress() {
  const key = progressStorageKey.value
  if (key) sessionStorage.removeItem(key)
}

const current = computed(() => props.questions[index.value] || {})
const selected = (idx) => {
  const qId = props.questions[idx]?.id
  return qId ? answers.value[qId] : null
}

const answeredCount = computed(() => Object.keys(answers.value).length)
const progressPct = computed(() => {
  if (props.questions.length === 0) return 0
  return Math.round((answeredCount.value / props.questions.length) * 100)
})

const mm = computed(() => String(Math.floor(timeLeft.value / 60)).padStart(2, '0'))
const ss = computed(() => String(timeLeft.value % 60).padStart(2, '0'))

function initTimer() {
  const data = props.testData
  if (!data) return

  const now = new Date()
  const endTime = data.end_time ? new Date(data.end_time) : null
  let remainingSeconds = endTime ? Math.floor((endTime - now) / 1000) : 0
  if (remainingSeconds < 0) remainingSeconds = 0

  const durationSeconds = (data.duration || 60) * 60
  timeLeft.value = endTime ? Math.min(remainingSeconds, durationSeconds) : durationSeconds

  if (timer.value) clearInterval(timer.value)
  timer.value = setInterval(() => {
    if (timeLeft.value > 0) {
      timeLeft.value--
    } else {
      clearInterval(timer.value)
      finishTest(true)
    }
  }, 1000)
}

const selectOption = (key) => {
  const qId = props.questions[index.value]?.id
  if (qId) answers.value[qId] = key
}

const next = () => {
  if (index.value < props.questions.length - 1) index.value++
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
  if (props.submitting) return
  if (!force) {
    const confirmed = await confirm({
      title: t('testRunner.confirmSubmitTitle'),
      message: `${t('testRunner.confirmSubmitMessage')} (${answeredCount.value}/${props.questions.length})`,
      confirmText: t('testRunner.confirmSubmitConfirm'),
      cancelText: t('testRunner.review'),
    })
    if (!confirmed) return
  }

  emit('submit', { answers: { ...answers.value }, force })
  clearProgress()
  canSubmit.value = false
}

watch(
  () => [props.testData, props.questions.length],
  () => {
    if (props.testData && props.questions.length) {
      restoreProgress()
      initTimer()
    }
  },
  { immediate: true },
)

watch([index, answers, flags], () => {
  saveProgress()
}, { deep: true })

onMounted(() => {
  attach()
  requestFullscreen()
  window.onbeforeunload = () => {
    if (canSubmit.value) return t('testRunner.leavePrompt')
  }
})

onUnmounted(() => {
  if (timer.value) clearInterval(timer.value)
  window.onbeforeunload = null
  detach()
})
</script>

<style scoped>
.anti-cheat-mode {
  user-select: none;
  -webkit-user-select: none;
}

.anti-cheat-mode input,
.anti-cheat-mode textarea {
  user-select: text;
  -webkit-user-select: text;
}
</style>
