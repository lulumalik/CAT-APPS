<template>
  <main class="max-w-4xl mx-auto px-4 md:px-8 py-8 md:py-10">
    <div class="mb-6">
      <router-link
        to="/ujian"
        class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-[#1E3A8A] transition-colors"
      >
        <ArrowLeft class="h-4 w-4" />
        Kembali ke daftar ujian
      </router-link>
    </div>

    <div v-if="loading" class="py-16 text-center text-gray-500">Memuat tinjauan...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">
      {{ errorMessage }}
    </div>

    <template v-else-if="review">
      <header class="rounded-2xl border border-blue-100 bg-white p-6 md:p-8 shadow-sm mb-6">
        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 mb-1">Tinjau hasil ujian</p>
        <h1 class="text-2xl md:text-3xl font-bold text-[#1E3A8A]">{{ review.exam?.name }}</h1>
        <p v-if="review.exam?.category" class="text-sm text-gray-500 mt-1">{{ review.exam.category }}</p>

        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-3">
          <div class="rounded-xl bg-blue-50 px-4 py-3">
            <div class="text-[11px] font-semibold uppercase text-blue-600">Percobaan</div>
            <div class="text-xl font-bold text-[#1E3A8A] mt-0.5">#{{ review.submission?.attempt_number }}</div>
          </div>
          <div class="rounded-xl bg-emerald-50 px-4 py-3">
            <div class="text-[11px] font-semibold uppercase text-emerald-600">Benar</div>
            <div class="text-xl font-bold text-emerald-700 mt-0.5">{{ review.correct_count }}</div>
          </div>
          <div class="rounded-xl bg-red-50 px-4 py-3">
            <div class="text-[11px] font-semibold uppercase text-red-600">Salah</div>
            <div class="text-xl font-bold text-red-700 mt-0.5">{{ review.wrong_count }}</div>
          </div>
          <div class="rounded-xl bg-gray-50 px-4 py-3">
            <div class="text-[11px] font-semibold uppercase text-gray-500">Skor</div>
            <div class="text-xl font-bold text-gray-900 mt-0.5">
              {{ review.submission?.score }}/{{ review.submission?.total }}
              <span class="text-sm font-semibold text-gray-500">({{ review.submission?.percent }}%)</span>
            </div>
          </div>
        </div>

        <div class="mt-5 flex flex-wrap gap-3">
          <router-link
            v-if="review.exam?.can_retake"
            :to="{ name: 'quick-exam', params: { id: examId } }"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-500 hover:bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-blue-500/20 transition-colors"
          >
            <RotateCcw class="h-4 w-4" />
            Ulangi ujian
          </router-link>
          <button
            v-if="attempts.length > 1"
            type="button"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
            @click="showAttempts = !showAttempts"
          >
            Riwayat {{ attempts.length }} percobaan
          </button>
        </div>

        <div v-if="showAttempts && attempts.length" class="mt-4 rounded-xl border border-gray-100 overflow-hidden">
          <button
            v-for="a in attempts"
            :key="a.id"
            type="button"
            class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left text-sm border-b border-gray-50 last:border-0 hover:bg-blue-50/50 transition-colors"
            :class="{ 'bg-blue-50': String(a.id) === String(submissionId) }"
            @click="loadReview(a.id)"
          >
            <span class="font-medium text-gray-800">Percobaan #{{ a.attempt_number }}</span>
            <span class="text-gray-500">
              {{ a.score }}/{{ a.total }} · {{ formatDate(a.submitted_at) }}
            </span>
          </button>
        </div>
      </header>

      <section class="space-y-4">
        <article
          v-for="item in review.items"
          :key="item.question_id"
          class="rounded-2xl border bg-white p-5 md:p-6 shadow-sm"
          :class="item.is_correct === false ? 'border-red-200' : item.is_correct ? 'border-emerald-200' : 'border-gray-100'"
        >
          <div class="flex flex-wrap items-start justify-between gap-2 mb-3">
            <h2 class="font-semibold text-gray-900 flex-1 min-w-0">
              <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2">Soal {{ item.number }}</span>
              {{ item.question }}
            </h2>
            <span
              v-if="item.type === 'multiple_choice'"
              class="shrink-0 px-2.5 py-1 rounded-md text-xs font-semibold"
              :class="item.is_correct ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
            >
              {{ item.is_correct ? 'Benar' : 'Salah' }}
            </span>
            <span v-else class="shrink-0 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-semibold">
              Essay
            </span>
          </div>

          <img
            v-if="item.image_url"
            :src="item.image_url"
            alt=""
            class="mb-4 max-h-48 rounded-xl border border-gray-100 object-contain bg-gray-50"
          />

          <div v-if="item.type === 'multiple_choice'" class="space-y-2 mb-4">
            <div
              v-for="opt in item.options || []"
              :key="opt.key"
              class="rounded-xl border px-4 py-2.5 text-sm"
              :class="optionClass(item, opt.key)"
            >
              <span class="font-bold mr-2">{{ opt.key }}.</span>
              {{ opt.label }}
            </div>
          </div>

          <div class="grid gap-3 sm:grid-cols-2">
            <div class="p-3 rounded-xl bg-gray-50 border border-gray-100">
              <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Jawabanmu</span>
              <div class="text-gray-800 font-medium">{{ formatAnswer(item, item.user_answer) }}</div>
            </div>
            <div
              v-if="item.type === 'multiple_choice'"
              class="p-3 rounded-xl border"
              :class="item.is_correct ? 'bg-emerald-50 border-emerald-100' : 'bg-red-50 border-red-100'"
            >
              <span class="text-[11px] font-bold uppercase tracking-wider block mb-1"
                :class="item.is_correct ? 'text-emerald-600' : 'text-red-600'"
              >
                Kunci jawaban
              </span>
              <div class="text-gray-800 font-medium">{{ formatAnswer(item, item.correct_answer) }}</div>
            </div>
          </div>
        </article>
      </section>
    </template>
  </main>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { ArrowLeft, RotateCcw } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()

const examId = route.params.id
const loading = ref(true)
const errorMessage = ref('')
const review = ref(null)
const attempts = ref([])
const showAttempts = ref(false)
const submissionId = ref(route.params.submissionId || null)

function optionClass(item, key) {
  const isUser = String(item.user_answer) === String(key)
  const isCorrect = String(item.correct_answer) === String(key)
  if (isCorrect) return 'border-emerald-300 bg-emerald-50 text-emerald-900'
  if (isUser && !item.is_correct) return 'border-red-300 bg-red-50 text-red-900'
  return 'border-gray-100 bg-gray-50/80 text-gray-700'
}

function formatAnswer(item, key) {
  if (key == null || key === '') return '— (tidak dijawab)'
  if (item.type !== 'multiple_choice') return String(key)
  const opt = (item.options || []).find((o) => String(o.key) === String(key))
  return opt ? `${opt.key}. ${opt.label}` : String(key)
}

function formatDate(iso) {
  if (!iso) return '—'
  try {
    return new Date(iso).toLocaleString('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  } catch {
    return iso
  }
}

async function loadAttempts() {
  const { data } = await axios.get(`/api/exams/${examId}/my-submissions`)
  attempts.value = data.submissions || []
  if (!submissionId.value && attempts.value.length) {
    submissionId.value = attempts.value[0].id
  }
}

async function loadReview(id) {
  if (!id) {
    errorMessage.value = 'Belum ada hasil ujian untuk ditinjau.'
    review.value = null
    return
  }
  loading.value = true
  errorMessage.value = ''
  try {
    submissionId.value = id
    const { data } = await axios.get(`/api/exams/${examId}/my-submissions/${id}`)
    review.value = data
    if (String(route.params.submissionId) !== String(id)) {
      router.replace({ name: 'exam-review', params: { id: examId, submissionId: id } })
    }
  } catch (error) {
    review.value = null
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat tinjauan.'
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    await loadAttempts()
    await loadReview(submissionId.value || route.params.submissionId)
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat riwayat ujian.'
    loading.value = false
  }
})

watch(
  () => route.params.submissionId,
  (id) => {
    if (id && String(id) !== String(submissionId.value)) {
      loadReview(id)
    }
  },
)
</script>
