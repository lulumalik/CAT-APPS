<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <router-link
      :to="backRoute"
      class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#1A1A1A] mb-6"
    >
      <ArrowLeft class="h-4 w-4" />
      Kembali ke {{ isExam ? 'Manajemen Ujian' : 'Manajemen Quiz/Test' }}
    </router-link>

    <div v-if="loading" class="py-20 text-center text-gray-500">Memuat submisi...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">
      {{ errorMessage }}
    </div>

    <template v-else>
      <PageHeroHeader
        :title="t('submissionsPage.title')"
        :theme="isExam ? 'blue' : 'green'"
        :icon="ListChecks"
      >
        <template #subtitle>
          {{ assessment?.name || '—' }}
          <span v-if="assessment?.category" class="text-gray-400"> · {{ assessment.category }}</span>
        </template>
      </PageHeroHeader>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total submisi</p>
          <p class="text-3xl font-bold text-[#1A1A1A] mt-1">{{ submissions.length }}</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Rata-rata skor</p>
          <p class="text-3xl font-bold text-[#9DB359] mt-1">{{ averageScore }}%</p>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
          <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Total soal</p>
          <p class="text-3xl font-bold text-[#1A1A1A] mt-1">{{ totalQuestions }}</p>
        </div>
      </div>

      <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ t('modals.submissions.tableUser') }}</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ t('modals.submissions.tableSubmittedAt') }}</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Skor</th>
                <th class="px-5 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Persentase</th>
                <th class="px-5 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ t('modals.submissions.tableActions') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr
                v-for="(sub, idx) in submissions"
                :key="sub.id"
                class="hover:bg-gray-50/80 transition-colors"
                :class="selectedSubmission?.id === sub.id ? 'bg-[#9DB359]/5' : ''"
              >
                <td class="px-5 py-4 text-sm text-gray-500">{{ idx + 1 }}</td>
                <td class="px-5 py-4">
                  <div class="font-medium text-gray-900">{{ sub.user?.name || '—' }}</div>
                  <div class="text-sm text-gray-500">{{ sub.user?.email || sub.user?.username || '—' }}</div>
                </td>
                <td class="px-5 py-4 text-sm text-gray-600 whitespace-nowrap">{{ formatDateTime(sub.submitted_at) }}</td>
                <td class="px-5 py-4 whitespace-nowrap">
                  <span class="font-semibold text-gray-900">{{ sub.score ?? 0 }}</span>
                  <span class="text-gray-400 text-sm"> / {{ totalQuestions }}</span>
                </td>
                <td class="px-5 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                    :class="scorePercent(sub.score) >= 70 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'"
                  >
                    {{ scorePercent(sub.score) }}%
                  </span>
                </td>
                <td class="px-5 py-4 text-right whitespace-nowrap">
                  <button
                    type="button"
                    class="text-sm font-semibold text-[#9DB359] hover:text-[#7a9247]"
                    @click="selectSubmission(sub)"
                  >
                    {{ selectedSubmission?.id === sub.id ? 'Tutup detail' : t('modals.submissions.review') }}
                  </button>
                </td>
              </tr>
              <tr v-if="!submissions.length">
                <td colspan="6" class="px-5 py-12 text-center text-gray-500">{{ t('modals.submissions.noneFound') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <section
        v-if="selectedSubmission"
        ref="detailSection"
        class="mt-8 rounded-2xl border border-[#9DB359]/30 bg-white shadow-sm overflow-hidden"
      >
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-6 py-4 bg-[#9DB359]/5">
          <div>
            <h2 class="text-lg font-bold text-[#1A1A1A]">
              {{ t('modals.submissions.reviewTitle', { name: selectedSubmission.user?.name || 'Peserta' }) }}
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ formatDateTime(selectedSubmission.submitted_at) }}</p>
          </div>
          <button type="button" class="text-sm text-gray-500 hover:text-gray-800" @click="selectedSubmission = null">
            {{ t('modals.submissions.closeReview') }}
          </button>
        </div>

        <div class="p-6 space-y-4 max-h-[560px] overflow-y-auto">
          <div
            v-for="(answer, qId) in selectedSubmission.answers || {}"
            :key="qId"
            class="rounded-xl border border-gray-100 bg-gray-50/60 p-5"
          >
            <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
              <div class="font-medium text-gray-900 flex-1 min-w-0">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2">Soal {{ questionIndex(qId) }}</span>
                {{ getQuestionText(qId) }}
              </div>
              <span
                v-if="getQuestionType(qId) === 'multiple_choice'"
                class="shrink-0 px-2 py-1 rounded-md text-xs font-semibold"
                :class="isCorrectAnswer(qId, answer) ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
              >
                {{ isCorrectAnswer(qId, answer) ? 'Benar' : 'Salah' }}
              </span>
              <span v-else class="shrink-0 px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-semibold">
                {{ t('modals.submissions.essay') }}
              </span>
            </div>
            <div class="grid gap-3 sm:grid-cols-2">
              <div class="p-3 bg-white rounded-lg border border-gray-100">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ t('modals.submissions.answer') }}</span>
                <div class="text-gray-800">{{ formatAnswer(qId, answer) }}</div>
              </div>
              <div v-if="getQuestionType(qId) === 'multiple_choice'" class="p-3 bg-white rounded-lg border border-gray-100">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Kunci jawaban</span>
                <div class="text-gray-800">{{ formatAnswer(qId, getQuestionCorrect(qId)) }}</div>
              </div>
            </div>
          </div>
        </div>

        <div
          v-if="!isExam && hasEssayQuestions"
          class="border-t border-gray-100 px-6 py-4 bg-white flex flex-wrap items-center justify-between gap-4"
        >
          <div class="font-bold text-gray-900">{{ t('modals.submissions.finalScore') }}</div>
          <div class="flex items-center gap-3">
            <input
              v-model.number="selectedSubmission.score"
              type="number"
              min="0"
              :max="totalQuestions"
              class="w-24 rounded-xl border border-gray-200 px-3 py-2 text-sm focus:border-[#9DB359] focus:ring-[#9DB359]"
            />
            <button
              type="button"
              class="rounded-full bg-[#1A1A1A] text-white px-5 py-2.5 text-sm font-semibold hover:bg-black"
              :disabled="updatingScore"
              @click="updateScore(selectedSubmission)"
            >
              {{ updatingScore ? '…' : t('modals.submissions.updateScore') }}
            </button>
          </div>
        </div>
      </section>
    </template>
  </main>
</template>

<script setup>
import { computed, nextTick, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { ArrowLeft, ListChecks } from 'lucide-vue-next'
import axios from 'axios'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const route = useRoute()
const toast = useToast()
const { t } = useI18n()

const isExam = computed(() => route.name === 'exam-submissions')
const itemId = computed(() => route.params.id)
const backRoute = computed(() => ({ name: isExam.value ? 'exams' : 'tests' }))
const submissionsApi = computed(() =>
  isExam.value ? `/api/exams/${itemId.value}/submissions` : `/api/tests/${itemId.value}/submissions`,
)

const loading = ref(true)
const errorMessage = ref('')
const assessment = ref(null)
const submissions = ref([])
const questions = ref([])
const selectedSubmission = ref(null)
const updatingScore = ref(false)
const detailSection = ref(null)

const totalQuestions = computed(() => assessment.value?.total_questions || assessment.value?.question_ids?.length || 0)

const averageScore = computed(() => {
  if (!submissions.value.length || !totalQuestions.value) return 0
  const sum = submissions.value.reduce((acc, s) => acc + scorePercent(s.score), 0)
  return Math.round((sum / submissions.value.length) * 10) / 10
})

const hasEssayQuestions = computed(() => questions.value.some((q) => q.type === 'essay'))

function scorePercent(score) {
  if (!totalQuestions.value) return 0
  return Math.round(((Number(score) || 0) / totalQuestions.value) * 1000) / 10
}

async function load() {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data } = await axios.get(submissionsApi.value)
    assessment.value = data.assessment || null
    submissions.value = Array.isArray(data.submissions) ? data.submissions : (Array.isArray(data) ? data : [])

    const ids = new Set(assessment.value?.question_ids || [])
    if (ids.size) {
      const qRes = await axios.get('/api/questions')
      const items = qRes.data?.items || qRes.data || []
      questions.value = items.filter((q) => ids.has(q.id))
    } else {
      questions.value = []
    }
  } catch (error) {
    assessment.value = null
    submissions.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat data submisi.'
  } finally {
    loading.value = false
  }
}

function getQuestion(id) {
  return questions.value.find((q) => String(q.id) === String(id))
}

function getQuestionText(id) {
  return getQuestion(id)?.question || t('modals.submissions.questionNotFound')
}

function getQuestionType(id) {
  return getQuestion(id)?.type || 'multiple_choice'
}

function getQuestionCorrect(id) {
  return getQuestion(id)?.correct ?? ''
}

function questionIndex(id) {
  const ids = assessment.value?.question_ids || []
  const idx = ids.findIndex((qid) => String(qid) === String(id))
  return idx >= 0 ? idx + 1 : id
}

function formatAnswer(qId, key) {
  const q = getQuestion(qId)
  if (!q || q.type === 'essay') return key ?? '—'
  const opt = (q.options || []).find((o) => o.key === key)
  return opt ? `${opt.key}. ${opt.label}` : (key ?? '—')
}

function isCorrectAnswer(qId, answer) {
  const q = getQuestion(qId)
  if (!q || q.type === 'essay') return null
  return String(q.correct) === String(answer)
}

async function selectSubmission(sub) {
  if (selectedSubmission.value?.id === sub.id) {
    selectedSubmission.value = null
    return
  }
  selectedSubmission.value = { ...sub, score: sub.score ?? 0 }
  await nextTick()
  detailSection.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

async function updateScore(sub) {
  if (isExam.value) return
  updatingScore.value = true
  try {
    const { data } = await axios.put(`/api/submissions/${sub.id}`, { score: sub.score })
    const idx = submissions.value.findIndex((s) => s.id === sub.id)
    if (idx !== -1) submissions.value[idx] = { ...submissions.value[idx], score: data.score }
    toast.success('Success', t('modals.submissions.updateScore'))
  } catch {
    toast.error('Error', 'Gagal memperbarui skor')
  } finally {
    updatingScore.value = false
  }
}

function formatDateTime(dateStr) {
  if (!dateStr) return '—'
  try {
    return new Date(dateStr).toLocaleString('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  } catch {
    return dateStr
  }
}

onMounted(load)
</script>
