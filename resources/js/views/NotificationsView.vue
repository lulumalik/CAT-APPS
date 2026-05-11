<template>
  <main class="max-w-5xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between gap-3 mb-6">
      <h1 class="text-2xl font-bold text-[#1A1A1A]">Notifikasi</h1>
      <button type="button" class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm" @click="markAllRead">
        Tandai semua dibaca
      </button>
    </div>

    <div v-if="loading" class="py-12 text-center text-gray-500">Memuat notifikasi...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-5 text-sm text-red-700">
      {{ errorMessage }}
    </div>
    <div v-else-if="!items.length" class="rounded-2xl border border-gray-100 bg-white p-8 text-sm text-gray-500">
      Belum ada notifikasi.
    </div>
    <div v-else class="space-y-3">
      <article
        v-for="item in items"
        :key="item.id"
        class="rounded-2xl border p-5 transition-colors"
        :class="item.is_read ? 'border-gray-100 bg-white' : 'border-[#9DB359]/40 bg-[#f8fbe9]'"
      >
        <div class="flex items-center justify-between gap-3 mb-1">
          <h2 class="font-semibold text-[#1A1A1A]">{{ item.title }}</h2>
          <button
            v-if="!item.is_read"
            type="button"
            class="text-xs font-medium text-[#6c7c3f]"
            @click="markRead(item.id)"
          >
            Tandai dibaca
          </button>
        </div>
        <p class="text-sm text-gray-700">{{ item.message }}</p>
        <a
          v-if="downloadUrl(item)"
          :href="downloadUrl(item)"
          class="inline-flex mt-3 rounded-full border border-[#9DB359]/40 bg-[#f4f9e6] px-3 py-1.5 text-xs font-semibold text-[#6c7c3f] hover:bg-[#edf5d7]"
        >
          Download Sertifikat
        </a>
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

const downloadUrl = (item) => {
  return item?.payload?.download_url || ''
}

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/notifications')
    items.value = Array.isArray(data?.items) ? data.items : []
    errorMessage.value = ''
  } catch (error) {
    items.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat notifikasi.'
  } finally {
    loading.value = false
  }
}

async function markRead(id) {
  try {
    await axios.patch(`/api/notifications/${id}/read`)
    await load()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menandai notifikasi.'
  }
}

async function markAllRead() {
  try {
    await axios.patch('/api/notifications/read-all')
    await load()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menandai semua notifikasi.'
  }
}

onMounted(load)
</script>
