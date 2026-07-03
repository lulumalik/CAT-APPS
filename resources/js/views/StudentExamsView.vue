<template>
  <main class="max-w-5xl mx-auto px-4 md:px-8 py-8 md:py-10">
    <!-- Hero header -->
    <div class="flex flex-wrap items-stretch justify-between gap-5 mb-10">
      <div class="min-w-0 flex-1 flex flex-col justify-center py-0.5">
        <h1 class="text-4xl md:text-5xl font-bold text-[#1E3A8A] tracking-tight leading-tight">Ujian</h1>
        <p class="text-gray-500 mt-2 text-sm md:text-base max-w-xl leading-relaxed">
          Daftar ujian yang bisa dikerjakan (gabungan lintas mata pelajaran).
        </p>
      </div>
      <div class="hidden sm:block shrink-0 self-stretch">
        <div class="h-full aspect-square rounded-[1.75rem] bg-gradient-to-br from-blue-50 to-blue-100/80 relative overflow-hidden flex items-center justify-center">
          <div class="pointer-events-none absolute -top-4 -right-4 h-16 w-16 rounded-full bg-blue-200/35" aria-hidden="true" />
          <div class="relative flex items-center justify-center">
            <ClipboardList class="h-10 w-10 text-blue-500 drop-shadow-sm" stroke-width="1.5" />
            <Pencil class="h-5 w-5 text-amber-400 absolute -bottom-0.5 -right-2.5 rotate-[-24deg]" stroke-width="2" />
          </div>
        </div>
      </div>
    </div>

    <!-- Section title -->
    <div class="flex items-center gap-2.5 mb-5">
      <ClipboardList class="h-5 w-5 text-blue-500" stroke-width="2" />
      <h2 class="text-lg font-bold text-[#1E3A8A]">Daftar Ujian</h2>
    </div>

    <div v-if="loading" class="py-16 text-center text-gray-500">Memuat ujian...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">
      {{ errorMessage }}
    </div>

    <div v-else-if="!items.length" class="rounded-2xl border border-blue-100 bg-white p-10 text-center text-gray-500 shadow-sm">
      Belum ada ujian yang tersedia.
    </div>

    <div v-else class="space-y-4">
      <article
        v-for="test in items"
        :key="test.id"
        class="rounded-2xl border border-gray-100 bg-white p-5 md:p-6 shadow-md shadow-blue-900/5 flex flex-wrap items-center gap-4 md:gap-5"
      >
        <!-- Icon -->
        <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
          <FileText class="h-7 w-7 text-blue-500" stroke-width="1.75" />
        </div>

        <!-- Info -->
        <div class="min-w-0 flex-1">
          <h3 class="text-lg md:text-xl font-bold text-[#1E3A8A] leading-snug">{{ test.name }}</h3>
          <div class="flex flex-wrap items-center gap-2 mt-2.5">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
              <Users class="h-3.5 w-3.5" />
              {{ test.category || 'Gabungan' }}
            </span>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
              <Clock class="h-3.5 w-3.5" />
              {{ test.duration }} menit
            </span>
          </div>
          <p v-if="scheduleLabel(test)" class="text-xs text-gray-400 mt-2">{{ scheduleLabel(test) }}</p>
        </div>

        <!-- Action -->
        <div class="w-full sm:w-auto shrink-0 flex justify-end sm:justify-center">
          <div
            v-if="examState(test) === 'upcoming'"
            class="rounded-xl border border-blue-100 bg-blue-50 px-5 py-3 text-center min-w-[200px]"
          >
            <p class="text-[11px] font-semibold uppercase tracking-wide text-blue-700">Ujian dimulai dalam</p>
            <p class="text-xl font-bold font-mono text-[#1E3A8A] mt-1">{{ countdownFor(test) }}</p>
          </div>

          <span
            v-else-if="examState(test) === 'submitted'"
            class="inline-flex items-center gap-2 rounded-xl bg-emerald-50 border border-emerald-100 px-5 py-3 text-sm font-semibold text-emerald-700"
          >
            <CheckCircle2 class="h-4 w-4" />
            Sudah dikerjakan
          </span>

          <span
            v-else-if="examState(test) === 'ended'"
            class="inline-flex items-center rounded-xl bg-gray-50 border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-500"
          >
            Ujian berakhir
          </span>

          <router-link
            v-else-if="examState(test) === 'ongoing'"
            :to="{ name: 'quick-exam', params: { id: test.id } }"
            class="inline-flex items-center gap-2 rounded-xl bg-blue-500 hover:bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-md shadow-blue-500/25 transition-colors"
          >
            Mulai Ujian
            <ArrowRight class="h-4 w-4" />
          </router-link>

          <span
            v-else
            class="inline-flex items-center rounded-xl bg-gray-50 border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-500"
          >
            Belum tersedia
          </span>
        </div>
      </article>
    </div>
  </main>
</template>

<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import axios from 'axios'
import { ArrowRight, CheckCircle2, ClipboardList, Clock, FileText, Pencil, Users } from 'lucide-vue-next'

const loading = ref(true)
const errorMessage = ref('')
const items = ref([])
const now = ref(Date.now())
let tickTimer = null

function examState(test) {
  if (test?.has_submitted) return 'submitted'

  const start = test?.start_time ? new Date(test.start_time).getTime() : null
  const end = test?.end_time ? new Date(test.end_time).getTime() : null
  const current = now.value

  if (start && current < start) return 'upcoming'
  if (end && current > end) return 'ended'
  if (test?.can_submit || test?.status === 'ongoing') return 'ongoing'
  if (start && end && current >= start && current <= end) return 'ongoing'

  return 'unavailable'
}

function msUntilStart(test) {
  const start = test?.start_time ? new Date(test.start_time).getTime() : null
  if (!start) return 0
  return Math.max(0, start - now.value)
}

function countdownFor(test) {
  const ms = msUntilStart(test)
  const totalSec = Math.floor(ms / 1000)
  const days = Math.floor(totalSec / 86400)
  const hours = Math.floor((totalSec % 86400) / 3600)
  const minutes = Math.floor((totalSec % 3600) / 60)
  const seconds = totalSec % 60
  const pad = (n) => String(n).padStart(2, '0')

  if (days > 0) {
    return `${days}h ${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
  }
  return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`
}

function scheduleLabel(test) {
  const start = test?.start_time ? new Date(test.start_time) : null
  const end = test?.end_time ? new Date(test.end_time) : null
  if (!start) return ''

  const fmt = (d) => d.toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    timeZone: 'Asia/Jakarta',
  })

  if (end) {
    return `Jadwal: ${fmt(start)} – ${fmt(end)}`
  }
  return `Mulai: ${fmt(start)}`
}

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/available-exams')
    items.value = Array.isArray(data) ? data : []
    errorMessage.value = ''
  } catch (error) {
    items.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat data ujian.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  load()
  tickTimer = setInterval(() => {
    now.value = Date.now()
  }, 1000)
})

onUnmounted(() => {
  if (tickTimer) clearInterval(tickTimer)
})
</script>
