<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="$emit('close')"></div>
    <div class="relative w-full max-w-6xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-6 md:p-8 shadow-2xl border border-gray-100">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Hasil Submission Tryout Gratis</h2>
          <p class="text-sm text-gray-500 mt-1">{{ test?.name }}</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div v-if="loading" class="text-sm text-gray-500 py-12 text-center">Memuat hasil tryout...</div>
      <div v-else-if="rows.length === 0" class="text-sm text-gray-500 py-12 text-center">Belum ada hasil tryout gratis.</div>
      <div v-else class="space-y-6">
        <div class="grid gap-4 md:grid-cols-3">
          <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4">
            <p class="text-xs text-gray-400 uppercase font-semibold">Total Peserta</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ rows.length }}</p>
          </div>
          <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4">
            <p class="text-xs text-gray-400 uppercase font-semibold">Rata-rata Skor</p>
            <p class="text-2xl font-bold text-[#9DB359] mt-1">{{ averageScore }}</p>
          </div>
          <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-4">
            <p class="text-xs text-gray-400 uppercase font-semibold">Skor Tertinggi</p>
            <p class="text-2xl font-bold text-[#9DB359] mt-1">{{ highestScore }}</p>
          </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-gray-100">
          <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Nama Lengkap</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Gender</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Kota / Alamat</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Tgl Lahir</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">No. WA</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Email</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Skor</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Submitted</th>
                <th class="px-4 py-3 text-right text-xs font-bold uppercase text-gray-500">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr
                v-for="row in rows"
                :key="row.id"
                class="hover:bg-gray-50/80 transition-colors"
                :class="selectedRow?.id === row.id ? 'bg-[#9DB359]/5' : ''"
              >
                <td class="px-4 py-3 font-semibold text-gray-900 whitespace-nowrap">{{ row.full_name }}</td>
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ normalizeGender(row.gender) }}</td>
                <td class="px-4 py-3 text-gray-600 max-w-xs truncate" :title="row.city">{{ row.city }}</td>
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ formatDate(row.birth_date) }}</td>
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap font-mono text-xs">{{ row.phone }}</td>
                <td class="px-4 py-3 text-gray-600 whitespace-nowrap">{{ row.email || '—' }}</td>
                <td class="px-4 py-3 whitespace-nowrap font-bold text-gray-900">
                  {{ getRowScore(row) }}
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">{{ formatDateTime(row.submitted_at) }}</td>
                <td class="px-4 py-3 text-right whitespace-nowrap">
                  <button
                    type="button"
                    class="text-xs font-bold text-[#9DB359] hover:text-[#7a9247] px-3 py-1.5 rounded-lg bg-[#9DB359]/10 hover:bg-[#9DB359]/20 transition-colors"
                    @click="selectedRow = selectedRow?.id === row.id ? null : row"
                  >
                    {{ selectedRow?.id === row.id ? 'Tutup Detail' : 'Lihat Detail & Jawaban' }}
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Detail & Answers Review Section -->
        <div v-if="selectedRow" class="rounded-2xl border border-[#9DB359]/30 bg-white p-6 shadow-sm space-y-6">
          <div class="flex items-center justify-between border-b border-gray-100 pb-4">
            <div>
              <h3 class="text-lg font-bold text-gray-900">Detail Form & Jawaban Peserta</h3>
              <p class="text-xs text-gray-500 mt-0.5">{{ selectedRow.full_name }} — {{ formatDateTime(selectedRow.submitted_at) }}</p>
            </div>
            <button type="button" class="text-xs text-gray-500 hover:text-gray-800 font-semibold" @click="selectedRow = null">
              Tutup
            </button>
          </div>

          <!-- Form Input Details Grid -->
          <div class="grid grid-cols-2 md:grid-cols-3 gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100 text-sm">
            <div>
              <span class="text-xs font-bold text-gray-400 uppercase block">Nama Lengkap</span>
              <span class="font-medium text-gray-900">{{ selectedRow.full_name }}</span>
            </div>
            <div>
              <span class="text-xs font-bold text-gray-400 uppercase block">Jenis Kelamin</span>
              <span class="font-medium text-gray-900">{{ normalizeGender(selectedRow.gender) }}</span>
            </div>
            <div>
              <span class="text-xs font-bold text-gray-400 uppercase block">Kota / Alamat</span>
              <span class="font-medium text-gray-900">{{ selectedRow.city }}</span>
            </div>
            <div>
              <span class="text-xs font-bold text-gray-400 uppercase block">Tanggal Lahir</span>
              <span class="font-medium text-gray-900">{{ formatDate(selectedRow.birth_date) }}</span>
            </div>
            <div>
              <span class="text-xs font-bold text-gray-400 uppercase block">No. WhatsApp / Telepon</span>
              <span class="font-medium text-gray-900 font-mono">{{ selectedRow.phone }}</span>
            </div>
            <div>
              <span class="text-xs font-bold text-gray-400 uppercase block">Email</span>
              <span class="font-medium text-gray-900">{{ selectedRow.email || '—' }}</span>
            </div>
          </div>

          <!-- Answers List -->
          <div>
            <h4 class="text-sm font-bold text-gray-900 mb-3">Review Jawaban Soal</h4>
            <div class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
              <div
                v-for="(answer, qId) in selectedRow.answers || {}"
                :key="qId"
                class="rounded-xl border border-gray-100 bg-gray-50/60 p-4"
              >
                <div class="flex items-start justify-between gap-2 mb-2">
                  <div class="text-sm font-medium text-gray-900 flex-1">
                    <span class="text-xs font-bold text-gray-400 uppercase mr-2">Soal #{{ qId }}</span>
                    {{ getQuestionText(qId) }}
                  </div>
                  <span
                    class="shrink-0 px-2.5 py-0.5 rounded-full text-xs font-semibold"
                    :class="isCorrectAnswer(qId, answer) ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                  >
                    {{ isCorrectAnswer(qId, answer) ? 'Benar' : 'Salah' }}
                  </span>
                </div>
                <div class="grid grid-cols-2 gap-3 text-xs mt-2">
                  <div class="p-2.5 bg-white rounded-lg border border-gray-100">
                    <span class="font-bold text-gray-400 uppercase block mb-1">Jawaban Peserta</span>
                    <span class="font-semibold text-gray-800">{{ formatAnswer(qId, answer) }}</span>
                  </div>
                  <div class="p-2.5 bg-white rounded-lg border border-gray-100">
                    <span class="font-bold text-gray-400 uppercase block mb-1">Kunci Jawaban</span>
                    <span class="font-semibold text-gray-800">{{ formatAnswer(qId, getQuestionCorrect(qId)) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useToast } from '@/composables/useNotification'

const props = defineProps({
  test: { type: Object, required: true },
})

const rows = ref([])
const questions = ref([])
const selectedRow = ref(null)
const loading = ref(true)
const toast = useToast()

const getRowScore = (row) => {
  const total = Number(row.total_questions || questions.value.length || 0)
  const raw = Number(row.score || 0)
  if (!total) return 0
  return Math.round((raw / total) * 100)
}

const averageScore = computed(() => {
  if (!rows.value.length) return 0
  const avg = rows.value.reduce((sum, row) => sum + getRowScore(row), 0) / rows.value.length
  return avg.toFixed(1)
})

const highestScore = computed(() => {
  if (!rows.value.length) return 0
  return rows.value.reduce((max, row) => Math.max(max, getRowScore(row)), 0)
})

const loadRows = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get(`/api/tests/${props.test.id}/free-tryout-submissions`)
    if (Array.isArray(data)) {
      rows.value = data
    } else {
      rows.value = data?.items || []
      questions.value = data?.questions || []
    }
  } catch (error) {
    toast.error('Error', 'Gagal memuat tryout result')
  } finally {
    loading.value = false
  }
}

const getQuestionText = (qId) => {
  const q = questions.value.find(item => item.id == qId)
  if (!q) return `Soal ID #${qId}`
  return q.question || (q.image ? '[Soal Gambar]' : `Soal #${qId}`)
}

const getQuestionCorrect = (qId) => {
  const q = questions.value.find(item => item.id == qId)
  return q ? q.correct : ''
}

const isCorrectAnswer = (qId, userAnswer) => {
  const correct = getQuestionCorrect(qId)
  if (!correct) return false
  return String(userAnswer).trim().toUpperCase() === String(correct).trim().toUpperCase()
}

const formatAnswer = (qId, val) => {
  if (val == null || val === '') return '— Tidak Dijawab —'
  const q = questions.value.find(item => item.id == qId)
  if (q && Array.isArray(q.options)) {
    const opt = q.options.find(o => o.key === val)
    if (opt) return `${opt.key}. ${opt.label}`
  }
  return String(val)
}

const normalizeGender = (value) => {
  if (value === 'L' || value === 'Laki-laki') return 'Laki-laki'
  if (value === 'P' || value === 'Perempuan') return 'Perempuan'
  return value || '-'
}

const formatDate = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleDateString('id-ID')
}

const formatDateTime = (value) => {
  if (!value) return '-'
  return new Date(value).toLocaleString('id-ID')
}

onMounted(loadRows)
</script>
