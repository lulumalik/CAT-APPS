<template>
  <main class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
      <h1 class="text-2xl font-bold text-[#1A1A1A]">Announcement Management</h1>
      <button type="button" class="rounded-full bg-[#1A1A1A] px-4 py-2 text-sm font-medium text-white" @click="openCreate">
        Buat Announcement
      </button>
    </div>

    <div v-if="loading" class="py-12 text-center text-gray-500">Memuat announcement...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-5 text-sm text-red-700">
      {{ errorMessage }}
    </div>
    <div v-else class="space-y-3">
      <div
        v-for="item in items"
        :key="item.id"
        class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm"
      >
        <div class="flex items-center justify-between gap-3">
          <div>
            <h2 class="font-semibold text-[#1A1A1A]">{{ item.title }}</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ item.target_role }} · {{ item.is_published ? 'published' : 'draft' }}</p>
          </div>
          <div class="flex items-center gap-2">
            <button class="text-sm text-[#9DB359] font-medium" @click="openEdit(item)">Edit</button>
            <button class="text-sm text-red-500 font-medium" @click="remove(item.id)">Hapus</button>
          </div>
        </div>
        <p class="text-sm text-gray-700 mt-2 whitespace-pre-line">{{ item.body }}</p>
      </div>
    </div>

    <div v-if="modal.open" class="fixed inset-0 z-50 bg-black/40 flex items-center justify-center p-4" @click.self="modal.open = false">
      <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
        <h3 class="text-lg font-bold mb-4">{{ modal.id ? 'Edit' : 'Buat' }} Announcement</h3>
        <form class="space-y-3" @submit.prevent="submit">
          <input v-model="modal.title" type="text" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" placeholder="Judul" required />
          <textarea v-model="modal.body" rows="5" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" placeholder="Isi announcement" required />
          <select v-model="modal.target_role" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
            <option value="all">all</option>
            <option value="user">user</option>
            <option value="mentor">mentor</option>
            <option value="admin">admin</option>
          </select>
          <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input v-model="modal.is_published" type="checkbox" />
            Publish sekarang
          </label>
          <div class="flex justify-end gap-2 pt-1">
            <button type="button" class="rounded-full border border-gray-200 px-4 py-2 text-sm" @click="modal.open = false">Batal</button>
            <button type="submit" class="rounded-full bg-[#9DB359] px-4 py-2 text-sm font-semibold text-white" :disabled="modal.saving">
              {{ modal.saving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'

const loading = ref(true)
const items = ref([])
const errorMessage = ref('')

const modal = reactive({
  open: false,
  id: null,
  title: '',
  body: '',
  target_role: 'all',
  is_published: true,
  saving: false,
})

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/announcements', { params: { manage: 1 } })
    items.value = Array.isArray(data) ? data : []
    errorMessage.value = ''
  } catch (error) {
    items.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat announcement.'
  } finally {
    loading.value = false
  }
}

function openCreate() {
  modal.open = true
  modal.id = null
  modal.title = ''
  modal.body = ''
  modal.target_role = 'all'
  modal.is_published = true
}

function openEdit(item) {
  modal.open = true
  modal.id = item.id
  modal.title = item.title
  modal.body = item.body
  modal.target_role = item.target_role || 'all'
  modal.is_published = Boolean(item.is_published)
}

async function submit() {
  modal.saving = true
  try {
    const payload = {
      title: modal.title,
      body: modal.body,
      target_role: modal.target_role,
      is_published: modal.is_published,
    }
    if (modal.id) {
      await axios.put(`/api/admin/announcements/${modal.id}`, payload)
    } else {
      await axios.post('/api/admin/announcements', payload)
    }
    modal.open = false
    await load()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menyimpan announcement.'
  } finally {
    modal.saving = false
  }
}

async function remove(id) {
  try {
    await axios.delete(`/api/admin/announcements/${id}`)
    await load()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menghapus announcement.'
  }
}

onMounted(load)
</script>
