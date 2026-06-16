<template>
  <div class="space-y-6">
    <div v-if="loadingProgress" class="py-10 text-center text-gray-500">Memuat perkembangan...</div>
    <template v-else>
      <!-- 1. Laporan Harian -->
      <section :class="pdfMode ? 'pdf-progress-section' : 'bg-white border border-gray-100 rounded-2xl p-5 shadow-sm'">
        <div class="flex flex-wrap items-end justify-between gap-3 mb-4">
          <div>
            <h3 :class="pdfMode ? 'pdf-subsection-title' : 'font-bold text-base'">Laporan Harian</h3>
            <p class="text-xs text-gray-500 mt-0.5">Diurutkan dari waktu terbaru</p>
          </div>
          <label v-if="!pdfMode" class="flex flex-col gap-1 text-xs text-gray-500">
            <span>Tanggal</span>
            <input
              v-model="dailyDate"
              type="date"
              class="rounded-xl border border-gray-100 bg-gray-50 px-3 py-2 text-sm text-gray-700 focus:bg-white focus:border-gray-200 focus:ring-0"
              @change="onDailyDateChange"
            />
          </label>
          <p v-else class="pdf-muted">Tanggal: {{ formatDate(dailyDate) }}</p>
        </div>

        <div v-if="loadingDaily" class="py-8 text-center text-sm text-gray-400">Memuat laporan harian...</div>
        <div v-else-if="!dailyReports.length" :class="pdfMode ? 'pdf-muted' : 'text-sm text-gray-400'">
          Belum ada laporan harian untuk {{ formatDate(dailyDate) }}.
        </div>
        <div v-else class="space-y-3">
          <article v-for="r in dailyReports" :key="r.id" :class="pdfMode ? 'pdf-card' : 'rounded-xl border border-gray-100 p-4'">
            <div class="flex items-center justify-between gap-2">
              <div class="font-semibold text-sm">{{ r.title }}</div>
              <span class="text-xs text-gray-400 shrink-0">{{ formatDateTime(r.created_at || r.report_date) }}</span>
            </div>
            <p v-if="r.summary" :class="pdfMode ? 'pdf-text-sm mt-1' : 'text-sm text-gray-600 mt-1'">{{ r.summary }}</p>
            <div v-if="Object.keys(r.categories || {}).length" class="mt-2 flex flex-wrap gap-2">
              <span
                v-for="(val, key) in r.categories"
                :key="key"
                :class="pdfMode ? 'pdf-tag' : 'text-[11px] rounded-full bg-gray-100 px-2.5 py-1 text-gray-600'"
              >
                <span class="font-semibold capitalize">{{ key }}</span>: {{ val }}
              </span>
            </div>
            <div class="text-[11px] text-gray-400 mt-2">
              <span v-if="r.class">{{ r.class.name }} · </span>{{ r.created_by || 'Sistem' }}
            </div>
          </article>
        </div>

        <div
          v-if="dailyMeta.total > dailyMeta.per_page && !pdfMode"
          class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-gray-100 pt-4"
        >
          <p class="text-xs text-gray-500">
            Menampilkan {{ dailyRangeLabel }} dari {{ dailyMeta.total }} laporan
          </p>
          <div class="flex items-center gap-2">
            <button
              type="button"
              class="rounded-full border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 disabled:opacity-40"
              :disabled="dailyPage <= 1 || loadingDaily"
              @click="goDailyPage(dailyPage - 1)"
            >
              Sebelumnya
            </button>
            <span class="text-xs text-gray-500">{{ dailyPage }} / {{ dailyMeta.last_page }}</span>
            <button
              type="button"
              class="rounded-full border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 disabled:opacity-40"
              :disabled="dailyPage >= dailyMeta.last_page || loadingDaily"
              @click="goDailyPage(dailyPage + 1)"
            >
              Berikutnya
            </button>
          </div>
        </div>
      </section>

      <!-- 2. Ringkasan Mingguan -->
      <section :class="pdfMode ? 'pdf-progress-section' : 'bg-white border border-gray-100 rounded-2xl p-5 shadow-sm'">
        <h3 :class="pdfMode ? 'pdf-subsection-title' : 'font-bold text-base mb-3'">Ringkasan Mingguan</h3>
        <div v-if="!reports.weekly?.length" :class="pdfMode ? 'pdf-muted' : 'text-sm text-gray-400'">Belum ada ringkasan mingguan.</div>
        <div v-else class="space-y-3">
          <article
            v-for="r in reports.weekly"
            :key="r.id"
            :class="pdfMode ? 'pdf-weekly-card' : 'rounded-xl border border-[#9DB359]/30 bg-[#9DB359]/5 p-4'"
          >
            <div class="font-semibold text-sm">{{ r.title }}</div>
            <p v-if="r.summary" :class="pdfMode ? 'pdf-text-sm mt-1' : 'text-sm text-gray-700 mt-1'">{{ r.summary }}</p>
            <div v-if="Object.keys(r.categories || {}).length" class="mt-2 grid sm:grid-cols-2 gap-2">
              <div v-for="(val, key) in r.categories" :key="key" class="text-xs">
                <span class="font-semibold capitalize text-gray-700">{{ key }}:</span>
                <span class="whitespace-pre-line text-gray-600"> {{ val }}</span>
              </div>
            </div>
          </article>
        </div>
      </section>

      <!-- 3. Materi Kelas -->
      <section :class="pdfMode ? 'pdf-progress-section' : 'bg-white border border-gray-100 rounded-2xl p-5 shadow-sm'">
        <h3 :class="pdfMode ? 'pdf-subsection-title' : 'font-bold text-base mb-1'">Materi Kelas</h3>
        <p :class="pdfMode ? 'pdf-muted mb-3' : 'text-xs text-gray-500 mb-3'">Jumlah materi & aktivitas per kelas</p>
        <div v-if="!(progress.materials || []).length" :class="pdfMode ? 'pdf-muted py-6 text-center' : 'text-sm text-gray-400 py-6 text-center'">
          Belum tergabung di kelas.
        </div>
        <div v-else class="space-y-3">
          <div v-for="c in progress.materials" :key="c.id" :class="pdfMode ? 'pdf-card' : 'rounded-xl border border-gray-100 p-3'">
            <div class="font-semibold text-sm">{{ c.name }}</div>
            <div class="flex flex-wrap gap-x-4 gap-y-1 mt-1" :class="pdfMode ? 'pdf-muted' : 'text-xs text-gray-500'">
              <span>{{ c.materials_count }} materi</span>
              <span>{{ c.sessions_count }} sesi</span>
              <span>{{ c.activities_count }} aktivitas</span>
            </div>
          </div>
        </div>
      </section>

      <!-- 4. Nilai per Mata Pelajaran -->
      <section :class="pdfMode ? 'pdf-progress-section flex flex-col min-h-[220px]' : 'bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col min-h-[220px]'">
        <h3 :class="pdfMode ? 'pdf-subsection-title' : 'font-bold text-base mb-1'">Nilai per Mata Pelajaran</h3>
        <p :class="pdfMode ? 'pdf-muted mb-3' : 'text-xs text-gray-500 mb-3'">Perkembangan nilai (%) tiap mata pelajaran dari waktu ke waktu</p>
        <div class="flex-1">
          <ProgressChart
            type="multiline"
            :series="progress.academic_subject_timeline || []"
            value-mode="percent"
            empty-text="Belum ada nilai akademik."
          />
        </div>
      </section>

      <!-- 5. Hasil Jasmani -->
      <section :class="pdfMode ? 'pdf-progress-section flex flex-col min-h-[220px]' : 'bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col min-h-[220px]'">
        <h3 :class="pdfMode ? 'pdf-subsection-title' : 'font-bold text-base mb-1'">Hasil Jasmani</h3>
        <p :class="pdfMode ? 'pdf-muted mb-3' : 'text-xs text-gray-500 mb-3'">Perkembangan nilai jasmani peserta dari waktu ke waktu</p>
        <div class="flex-1">
          <ProgressChart
            type="multiline"
            :series="progress.physical_timeline || []"
            value-mode="value"
            empty-text="Belum ada hasil jasmani."
          />
        </div>
      </section>

      <!-- 6. Nilai Tes -->
      <section :class="pdfMode ? 'pdf-progress-section flex flex-col min-h-[220px]' : 'bg-white border border-gray-100 rounded-2xl p-5 shadow-sm flex flex-col min-h-[220px]'">
        <h3 :class="pdfMode ? 'pdf-subsection-title' : 'font-bold text-base mb-1'">Nilai Tes</h3>
        <p :class="pdfMode ? 'pdf-muted mb-3' : 'text-xs text-gray-500 mb-3'">Perkembangan persentase nilai dari waktu ke waktu</p>
        <div class="flex-1">
          <ProgressChart
            type="line"
            :data="progress.academic_timeline || []"
            color="#2F6BFF"
            empty-text="Belum ada nilai tes."
          />
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import axios from 'axios'
import ProgressChart from '@/components/ProgressChart.vue'

const props = defineProps({
  studentId: { type: [Number, String], required: true },
  pdfMode: { type: Boolean, default: false },
  reportDate: { type: String, default: '' },
})

const emit = defineEmits(['update:reportDate'])

const DAILY_PER_PAGE = 10

const loadingProgress = ref(true)
const loadingDaily = ref(false)
const progress = ref({})
const reports = ref({ weekly: [] })
const dailyReports = ref([])
const dailyMeta = ref({
  current_page: 1,
  last_page: 1,
  total: 0,
  per_page: DAILY_PER_PAGE,
  date: todayIso(),
})
const dailyDate = ref(props.reportDate || todayIso())
const dailyPage = ref(1)

watch(
  () => props.reportDate,
  (value) => {
    if (!value || value === dailyDate.value) return
    dailyDate.value = value
    dailyPage.value = 1
    loadDailyReports()
  },
)

const dailyRangeLabel = computed(() => {
  if (!dailyMeta.value.total) return '0'
  const start = (dailyMeta.value.current_page - 1) * dailyMeta.value.per_page + 1
  const end = Math.min(dailyMeta.value.total, dailyMeta.value.current_page * dailyMeta.value.per_page)
  return `${start}-${end}`
})

function todayIso() {
  try {
    return new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(new Date())
  } catch {
    return new Date().toISOString().slice(0, 10)
  }
}

async function loadProgress() {
  if (!props.studentId) return
  loadingProgress.value = true
  try {
    const { data } = await axios.get(`/api/students/${props.studentId}/progress`)
    progress.value = data || {}
  } catch {
    progress.value = {}
  } finally {
    loadingProgress.value = false
  }
}

async function loadDailyReports() {
  if (!props.studentId) return
  loadingDaily.value = true
  try {
    const { data } = await axios.get(`/api/students/${props.studentId}/reports`, {
      params: {
        date: dailyDate.value,
        page: dailyPage.value,
        per_page: DAILY_PER_PAGE,
      },
    })
    dailyReports.value = data.daily?.data || []
    dailyMeta.value = {
      current_page: data.daily?.current_page || 1,
      last_page: data.daily?.last_page || 1,
      total: data.daily?.total || 0,
      per_page: data.daily?.per_page || DAILY_PER_PAGE,
      date: data.daily?.date || dailyDate.value,
    }
    reports.value.weekly = data.weekly || []
    if (data.daily?.date) {
      dailyDate.value = data.daily.date
    }
    dailyPage.value = dailyMeta.value.current_page
  } catch {
    dailyReports.value = []
    dailyMeta.value = {
      current_page: 1,
      last_page: 1,
      total: 0,
      per_page: DAILY_PER_PAGE,
      date: dailyDate.value,
    }
    reports.value.weekly = []
  } finally {
    loadingDaily.value = false
  }
}

async function loadAll() {
  await Promise.all([loadProgress(), loadDailyReports()])
}

function onDailyDateChange() {
  dailyPage.value = 1
  emit('update:reportDate', dailyDate.value)
  loadDailyReports()
}

function goDailyPage(page) {
  if (page < 1 || page > dailyMeta.value.last_page) return
  dailyPage.value = page
  loadDailyReports()
}

function formatDate(d) {
  if (!d) return '-'
  try {
    return new Date(`${d}T00:00:00`).toLocaleDateString('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
    })
  } catch {
    return d
  }
}

function formatDateTime(d) {
  if (!d) return '-'
  try {
    return new Date(d).toLocaleString('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  } catch {
    return d
  }
}

watch(
  () => props.studentId,
  () => {
    dailyDate.value = props.reportDate || todayIso()
    dailyPage.value = 1
    loadAll()
  },
  { immediate: true },
)

let savedExportState = null

async function prepareForPdfExport() {
  savedExportState = {
    dailyPage: dailyPage.value,
    dailyReports: [...dailyReports.value],
    dailyMeta: { ...dailyMeta.value },
  }

  const total = dailyMeta.value.total || 0
  if (total <= dailyReports.value.length) {
    return
  }

  loadingDaily.value = true
  try {
    const { data } = await axios.get(`/api/students/${props.studentId}/reports`, {
      params: {
        date: dailyDate.value,
        page: 1,
        per_page: Math.min(50, total),
      },
    })
    dailyReports.value = data.daily?.data || []
    dailyMeta.value = {
      current_page: data.daily?.current_page || 1,
      last_page: data.daily?.last_page || 1,
      total: data.daily?.total || 0,
      per_page: data.daily?.per_page || DAILY_PER_PAGE,
      date: data.daily?.date || dailyDate.value,
    }
    dailyPage.value = 1
  } finally {
    loadingDaily.value = false
  }
}

function restoreAfterPdfExport() {
  if (!savedExportState) return
  dailyPage.value = savedExportState.dailyPage
  dailyReports.value = savedExportState.dailyReports
  dailyMeta.value = savedExportState.dailyMeta
  savedExportState = null
}

defineExpose({ prepareForPdfExport, restoreAfterPdfExport })
</script>
