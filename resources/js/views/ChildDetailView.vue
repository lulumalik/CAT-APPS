<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <router-link to="/dashboard" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#1A1A1A] mb-6">
      <ArrowLeft class="h-4 w-4" /> Kembali ke Dashboard
    </router-link>

    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
      <div>
        <h1 class="text-2xl md:text-3xl font-bold text-[#1A1A1A]">Perkembangan {{ childName || 'Ananda' }}</h1>
        <p class="text-gray-500 mt-1 text-sm">Laporan & progress peserta — bersifat privat untuk Anda.</p>
      </div>
      <div class="flex flex-wrap items-end gap-2">
        <label class="flex flex-col gap-1 text-xs text-gray-500">
          <span>Tanggal laporan</span>
          <input
            v-model="reportDate"
            type="date"
            class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm text-gray-700 focus:border-[#9DB359] focus:ring-[#9DB359]"
            :disabled="exportingPdf"
          />
        </label>
        <button
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#9DB359]/40 bg-[#9DB359]/10 hover:bg-[#9DB359]/20 text-sm font-medium text-[#5a6b2e] disabled:opacity-50"
          :disabled="exportingPdf"
          @click="downloadPdf"
        >
          <Download class="h-4 w-4" />
          {{ exportingPdf ? 'Menyiapkan PDF...' : 'Download PDF' }}
        </button>
      </div>
    </div>

    <StudentProgressPanel
      v-model:report-date="reportDate"
      :student-id="studentId"
    />
  </main>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ArrowLeft, Download } from 'lucide-vue-next'
import axios from 'axios'
import StudentProgressPanel from '@/components/StudentProgressPanel.vue'

const route = useRoute()
const studentId = computed(() => route.params.id)
const childName = ref('')
const exportingPdf = ref(false)

function todayIso() {
  try {
    return new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(new Date())
  } catch {
    return new Date().toISOString().slice(0, 10)
  }
}

const reportDate = ref(todayIso())

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/parent/children')
    const match = (data.items || []).find((c) => String(c.student.id) === String(studentId.value))
    childName.value = match?.student?.name || ''
  } catch (e) {
    childName.value = ''
  }
})

const downloadPdf = async () => {
  if (exportingPdf.value) return

  exportingPdf.value = true

  try {
    const response = await window.axios.get(`/api/students/${studentId.value}/pdf`, {
      responseType: 'blob',
      params: { date: reportDate.value },
    })
    const dateLabel = reportDate.value || todayIso()
    const safeName = String(childName.value || 'peserta').replace(/[^\w\-]+/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '') || 'peserta'
    const filename = `Laporan-Perkembangan-${safeName}-${dateLabel}.pdf`

    const blobUrl = URL.createObjectURL(response.data)
    const link = document.createElement('a')
    link.href = blobUrl
    link.download = filename
    link.click()
    URL.revokeObjectURL(blobUrl)
  } catch (error) {
    console.error('Gagal membuat PDF:', error)
    window.alert('Gagal membuat PDF. Silakan coba lagi.')
  } finally {
    exportingPdf.value = false
  }
}
</script>
