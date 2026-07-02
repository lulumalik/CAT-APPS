<template>
  <main class="max-w-5xl mx-auto px-4 md:px-12 py-8">
    <h1 class="text-3xl font-bold text-[#1A1A1A]">Riwayat Aktivitas</h1>
    <p class="text-gray-500 mt-1">Aktivitas kelas yang pernah Anda ikuti.</p>

    <div v-if="loading" class="py-20 text-center text-gray-500">Memuat riwayat aktivitas...</div>
    <div v-else-if="errorMessage" class="mt-6 rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">
      {{ errorMessage }}
    </div>
    <template v-else>
      <section class="mt-8 bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <h2 class="font-bold text-lg mb-4">Kelas Saya</h2>
        <div v-if="!classes.length" class="text-sm text-gray-500">Belum ada kelas.</div>
        <div v-else class="space-y-3">
          <div v-for="c in classes" :key="c.id" class="rounded-xl border border-gray-100 p-3">
            <div class="font-semibold">{{ c.name }}</div>
            <div class="text-xs text-gray-500">{{ c.class_code }}</div>
            <div class="text-xs text-gray-600 mt-1">
              Aktivitas terakhir:
              <span class="font-medium">{{ c.latest_activity?.title || 'Belum ada aktivitas' }}</span>
            </div>
          </div>
        </div>
      </section>

      <section class="mt-6 bg-white border border-gray-100 rounded-2xl p-5 shadow-sm">
        <h2 class="font-bold text-lg mb-4">Riwayat Aktivitas</h2>
        <div v-if="!activities.length" class="text-sm text-gray-500">Belum ada aktivitas.</div>
        <div v-else class="space-y-3">
          <div v-for="a in activities" :key="a.id" class="rounded-xl border border-gray-100 p-3">
            <div class="flex items-start justify-between gap-3">
              <div class="font-semibold">{{ a.title }}</div>
              <span
                v-if="activityTypeLabel(a.activity_type)"
                class="shrink-0 text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full"
                :class="activityTypeClass(a.activity_type)"
              >
                {{ activityTypeLabel(a.activity_type) }}
              </span>
            </div>
            <div class="text-xs text-gray-500">
              <template v-if="a.activity_type === 'class'">
                {{ a.bimble_class?.name }} · {{ a.creator?.name }} ·
              </template>
              {{ formatDate(a.happened_at || a.created_at) }}
            </div>
            <p v-if="a.description" class="text-sm text-gray-600 mt-2">{{ a.description }}</p>
          </div>
        </div>
      </section>
    </template>
  </main>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

const loading = ref(true)
const errorMessage = ref('')
const classes = ref([])
const activities = ref([])

const formatDate = (d) => {
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

const activityTypeLabel = (type) => {
  if (type === 'exam') return 'Ujian'
  if (type === 'quiz') return 'Quiz'
  if (type === 'class') return 'Kelas'
  return ''
}

const activityTypeClass = (type) => {
  if (type === 'exam') return 'bg-blue-100 text-blue-800'
  if (type === 'quiz') return 'bg-emerald-100 text-emerald-800'
  if (type === 'class') return 'bg-gray-100 text-gray-600'
  return 'bg-gray-100 text-gray-600'
}

onMounted(async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/my-activity-history')
    classes.value = data.classes || []
    activities.value = data.class_activities || []
    errorMessage.value = ''
  } catch (error) {
    classes.value = []
    activities.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat riwayat aktivitas.'
  } finally {
    loading.value = false
  }
})
</script>
