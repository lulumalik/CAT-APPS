<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="$emit('close')"></div>
    <div class="relative w-full max-w-5xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl border border-gray-100">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-text">Tryout Result</h2>
          <p class="text-sm text-muted mt-1">{{ test?.name }}</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100" @click="$emit('close')">
          <span class="text-gray-500">x</span>
        </button>
      </div>

      <div v-if="loading" class="text-sm text-muted py-8 text-center">Memuat hasil tryout...</div>
      <div v-else-if="rows.length === 0" class="text-sm text-muted py-8 text-center">Belum ada hasil tryout gratis.</div>
      <div v-else>
        <div class="grid gap-4 md:grid-cols-3 mb-5">
          <div class="rounded-xl border border-border bg-background px-4 py-3">
            <p class="text-xs text-muted uppercase font-semibold">Total Peserta</p>
            <p class="text-2xl font-bold text-text mt-1">{{ rows.length }}</p>
          </div>
          <div class="rounded-xl border border-border bg-background px-4 py-3">
            <p class="text-xs text-muted uppercase font-semibold">Rata-rata Skor</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ averageScore }}</p>
          </div>
          <div class="rounded-xl border border-border bg-background px-4 py-3">
            <p class="text-xs text-muted uppercase font-semibold">Skor Tertinggi</p>
            <p class="text-2xl font-bold text-primary mt-1">{{ highestScore }}</p>
          </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100">
          <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Nama</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Gender</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Kota</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Tgl Lahir</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">No. WA</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Skor</th>
                <th class="px-4 py-3 text-left text-xs font-bold uppercase text-gray-500">Submitted</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
              <tr v-for="row in rows" :key="row.id">
                <td class="px-4 py-3 text-sm text-text font-medium">{{ row.full_name }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ normalizeGender(row.gender) }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ row.city }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ formatDate(row.birth_date) }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ row.phone }}</td>
                <td class="px-4 py-3 text-sm font-semibold text-primary">{{ row.score }} / {{ row.total_questions }}</td>
                <td class="px-4 py-3 text-sm text-gray-700">{{ formatDateTime(row.submitted_at) }}</td>
              </tr>
            </tbody>
          </table>
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
const loading = ref(true)
const toast = useToast()

const averageScore = computed(() => {
  if (!rows.value.length) return 0
  const avg = rows.value.reduce((sum, row) => sum + Number(row.score || 0), 0) / rows.value.length
  return avg.toFixed(1)
})

const highestScore = computed(() => {
  if (!rows.value.length) return 0
  return rows.value.reduce((max, row) => Math.max(max, Number(row.score || 0)), 0)
})

const loadRows = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get(`/api/tests/${props.test.id}/free-tryout-submissions`)
    rows.value = Array.isArray(data) ? data : []
  } catch (error) {
    toast.error('Error', 'Gagal memuat tryout result')
  } finally {
    loading.value = false
  }
}

const normalizeGender = (value) => {
  if (value === 'L') return 'Laki-laki'
  if (value === 'P') return 'Perempuan'
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
