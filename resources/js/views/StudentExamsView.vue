<template>
  <main class="max-w-6xl mx-auto px-4 md:px-12 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-[#1A1A1A]">Ujian</h1>
      <p class="text-gray-500 mt-1">Daftar ujian yang bisa dikerjakan (gabungan lintas mata pelajaran).</p>
    </div>

    <div v-if="loading" class="py-16 text-center text-gray-500">Memuat ujian...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">
      {{ errorMessage }}
    </div>

    <div v-else-if="!items.length" class="rounded-[2rem] border border-gray-100 bg-white p-10 text-center text-gray-500">
      Belum ada ujian yang tersedia.
    </div>

    <div v-else class="grid gap-4">
      <article
        v-for="test in items"
        :key="test.id"
        class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm flex flex-wrap items-center justify-between gap-4"
      >
        <div>
          <h2 class="text-lg font-semibold text-[#1A1A1A]">{{ test.name }}</h2>
          <p class="text-sm text-gray-500">{{ test.category }} · {{ test.duration }} menit</p>
        </div>
        <router-link
          :to="{ name: 'quick-test', params: { id: test.id } }"
          class="inline-flex rounded-full bg-[#1A1A1A] px-5 py-2.5 text-sm font-semibold text-white hover:bg-black"
        >
          Mulai Ujian
        </router-link>
      </article>
    </div>
  </main>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import axios from 'axios'

const loading = ref(true)
const errorMessage = ref('')
const items = ref([])

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/available-tests')
    items.value = Array.isArray(data) ? data : []
    errorMessage.value = ''
  } catch (error) {
    items.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat data ujian.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>
