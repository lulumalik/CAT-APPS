<template>
  <main class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-[#1A1A1A] mb-6">Announcement</h1>
    <div v-if="loading" class="py-12 text-center text-gray-500">Memuat announcement...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-5 text-sm text-red-700">
      {{ errorMessage }}
    </div>
    <div v-else-if="!items.length" class="rounded-2xl border border-gray-100 bg-white p-8 text-sm text-gray-500">
      Belum ada announcement.
    </div>
    <div v-else class="space-y-4">
      <article
        v-for="item in items"
        :key="item.id"
        class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm"
      >
        <div class="flex items-center justify-between gap-3 mb-2">
          <h2 class="text-lg font-semibold text-[#1A1A1A]">{{ item.title }}</h2>
          <span class="text-xs rounded-full px-2 py-1 bg-gray-100 text-gray-600">
            {{ item.target_role }}
          </span>
        </div>
        <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ item.body }}</p>
      </article>
    </div>
  </main>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

const loading = ref(true)
const items = ref([])
const errorMessage = ref('')

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/announcements')
    items.value = Array.isArray(data) ? data : []
    errorMessage.value = ''
  } catch (error) {
    items.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat announcement.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>
