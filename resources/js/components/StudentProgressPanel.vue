<template>
  <div class="space-y-6">
    <div v-if="loading" class="py-10 text-center text-gray-500">Memuat perkembangan...</div>
    <template v-else>
      <!-- Charts -->
      <div class="grid lg:grid-cols-2 gap-6">
        <section class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
          <h3 class="font-bold text-base mb-1">Nilai Tes / Ujian Kelas</h3>
          <p class="text-xs text-gray-500 mb-3">Perkembangan persentase nilai dari waktu ke waktu</p>
          <ProgressChart type="line" :data="progress.academic_timeline || []" color="#2F6BFF"
            empty-text="Belum ada nilai tes." />
        </section>

        <section class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
          <h3 class="font-bold text-base mb-1">Nilai per Mata Pelajaran</h3>
          <p class="text-xs text-gray-500 mb-3">Nilai terbaik per bidang akademik</p>
          <ProgressChart type="hbars" :data="progress.academic_subjects || []" color="#2F6BFF"
            empty-text="Belum ada nilai akademik." />
        </section>

        <section class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
          <h3 class="font-bold text-base mb-1">Hasil Jasmani</h3>
          <p class="text-xs text-gray-500 mb-3">Hasil tes fisik terakhir</p>
          <ProgressChart type="bars" :data="progress.physical || []" color="#9DB359"
            empty-text="Belum ada hasil jasmani." />
        </section>

        <section class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
          <h3 class="font-bold text-base mb-1">Materi Kelas</h3>
          <p class="text-xs text-gray-500 mb-3">Jumlah materi & aktivitas per kelas</p>
          <div v-if="!(progress.materials || []).length" class="text-sm text-gray-400 py-6 text-center">
            Belum tergabung di kelas.
          </div>
          <div v-else class="space-y-3">
            <div v-for="c in progress.materials" :key="c.id" class="rounded-xl border border-gray-100 p-3">
              <div class="font-semibold text-sm">{{ c.name }}</div>
              <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 mt-1">
                <span>{{ c.materials_count }} materi</span>
                <span>{{ c.sessions_count }} sesi</span>
                <span>{{ c.activities_count }} aktivitas</span>
              </div>
            </div>
          </div>
        </section>
      </div>

      <!-- Weekly summaries -->
      <section class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <h3 class="font-bold text-base mb-3">Ringkasan Mingguan</h3>
        <div v-if="!reports.weekly?.length" class="text-sm text-gray-400">Belum ada ringkasan mingguan.</div>
        <div v-else class="space-y-3">
          <article v-for="r in reports.weekly" :key="r.id" class="rounded-xl border border-[#9DB359]/30 bg-[#9DB359]/5 p-4">
            <div class="font-semibold text-sm">{{ r.title }}</div>
            <p v-if="r.summary" class="text-sm text-gray-700 mt-1">{{ r.summary }}</p>
            <div v-if="Object.keys(r.categories || {}).length" class="mt-2 grid sm:grid-cols-2 gap-2">
              <div v-for="(val, key) in r.categories" :key="key" class="text-xs">
                <span class="font-semibold capitalize text-gray-700">{{ key }}:</span>
                <span class="whitespace-pre-line text-gray-600"> {{ val }}</span>
              </div>
            </div>
          </article>
        </div>
      </section>

      <!-- Daily reports -->
      <section class="bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <h3 class="font-bold text-base mb-3">Laporan Harian</h3>
        <div v-if="!reports.daily?.length" class="text-sm text-gray-400">Belum ada laporan harian.</div>
        <div v-else class="space-y-3">
          <article v-for="r in reports.daily" :key="r.id" class="rounded-xl border border-gray-100 p-4">
            <div class="flex items-center justify-between gap-2">
              <div class="font-semibold text-sm">{{ r.title }}</div>
              <span class="text-xs text-gray-400 shrink-0">{{ formatDate(r.report_date) }}</span>
            </div>
            <p v-if="r.summary" class="text-sm text-gray-600 mt-1">{{ r.summary }}</p>
            <div v-if="Object.keys(r.categories || {}).length" class="mt-2 flex flex-wrap gap-2">
              <span v-for="(val, key) in r.categories" :key="key"
                class="text-[11px] rounded-full bg-gray-100 px-2.5 py-1 text-gray-600">
                <span class="font-semibold capitalize">{{ key }}</span>: {{ val }}
              </span>
            </div>
            <div class="text-[11px] text-gray-400 mt-2">
              <span v-if="r.class">{{ r.class.name }} · </span>{{ r.created_by }}
            </div>
          </article>
        </div>
      </section>
    </template>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import ProgressChart from '@/components/ProgressChart.vue'

const props = defineProps({
  studentId: { type: [Number, String], required: true },
})

const loading = ref(true)
const progress = ref({})
const reports = ref({ daily: [], weekly: [] })

async function load() {
  if (!props.studentId) return
  loading.value = true
  try {
    const [p, r] = await Promise.all([
      axios.get(`/api/students/${props.studentId}/progress`),
      axios.get(`/api/students/${props.studentId}/reports`),
    ])
    progress.value = p.data || {}
    reports.value = r.data || { daily: [], weekly: [] }
  } catch (e) {
    progress.value = {}
    reports.value = { daily: [], weekly: [] }
  } finally {
    loading.value = false
  }
}

function formatDate(d) {
  if (!d) return '-'
  try {
    return new Date(d).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
  } catch (e) {
    return d
  }
}

watch(() => props.studentId, load, { immediate: true })
</script>
