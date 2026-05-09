<template>
  <main class="max-w-4xl mx-auto px-4 pb-8 space-y-6">
    <h1 class="text-2xl font-bold text-[#1A1A1A]">Profil Pendaftar</h1>

    <div v-if="loading" class="py-20 text-center text-gray-500">Memuat profil...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">
      {{ errorMessage }}
    </div>

    <template v-else>
      <section class="rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-6">
        <h2 class="font-semibold text-lg mb-4">Data Akun</h2>
        <div v-if="approvedPhotoUrl" class="mb-4 flex items-center gap-3">
          <img :src="approvedPhotoUrl" alt="Foto profil" class="w-16 h-16 rounded-full object-cover border border-gray-200" />
          <div class="text-sm text-gray-600">Foto profil aktif (administrasi disetujui)</div>
        </div>
        <dl class="grid sm:grid-cols-2 gap-3 text-sm">
          <div><dt class="text-gray-500">Nama</dt><dd class="font-medium text-[#1A1A1A]">{{ user?.name || '-' }}</dd></div>
          <div><dt class="text-gray-500">Email</dt><dd class="font-medium text-[#1A1A1A]">{{ user?.email || '-' }}</dd></div>
          <div><dt class="text-gray-500">Role</dt><dd class="font-medium capitalize text-[#1A1A1A]">{{ user?.role || '-' }}</dd></div>
          <div><dt class="text-gray-500">Tier</dt><dd><span class="text-xs rounded-full px-2 py-1" :class="programBadge.className">{{ programBadge.label }}</span></dd></div>
          <div><dt class="text-gray-500">Program</dt><dd class="font-medium text-[#1A1A1A]">{{ user?.program_category || '-' }}</dd></div>
          <div><dt class="text-gray-500">Karantina</dt><dd class="font-medium text-[#1A1A1A]">{{ user?.in_quarantine ? 'Ya' : 'Tidak' }}</dd></div>
        </dl>
      </section>

      <section class="rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-6">
        <h2 class="font-semibold text-lg mb-4">Progress Onboarding</h2>
        <ol class="space-y-3">
          <li v-for="s in steps" :key="s.key" class="flex items-center justify-between rounded-xl border border-gray-100 px-4 py-3">
            <div class="font-medium text-[#1A1A1A]">{{ s.label }}</div>
            <span
              class="text-xs px-2 py-1 rounded-full capitalize"
              :class="statusClass(progress?.[s.statusKey])"
            >
              {{ statusLabel(progress?.[s.statusKey]) }}
            </span>
          </li>
        </ol>

        <router-link
          to="/registration"
          class="inline-flex mt-5 px-5 py-2.5 rounded-full bg-[#9DB359] text-white text-sm font-semibold"
        >
          Lanjutkan Onboarding
        </router-link>
      </section>
    </template>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import { useAppStore } from '@/stores/app'
import { storeToRefs } from 'pinia'
import { getProgramBadge } from '@/utils/userMeta'

const store = useAppStore()
const { user } = storeToRefs(store)

const loading = ref(true)
const errorMessage = ref('')
const progress = ref(null)
const programBadge = computed(() => getProgramBadge(user.value))

const steps = computed(() => [
  { key: 'administration', label: 'Administrasi', statusKey: 'administration_status' },
  { key: 'psychology', label: 'Psikologi', statusKey: 'psychology_status' },
  { key: 'health', label: 'Kesehatan', statusKey: 'health_status' },
  { key: 'physical', label: 'Fisik', statusKey: 'physical_status' },
])

const approvedPhotoUrl = computed(() => {
  if (progress.value?.administration_status !== 'approved') return ''
  return progress.value?.administration_data?.profile_photo_url || ''
})

const statusClass = (status) => {
  if (status === 'approved') return 'bg-emerald-50 text-emerald-700 border border-emerald-100'
  if (status === 'submitted') return 'bg-amber-50 text-amber-700 border border-amber-100'
  if (status === 'revision_requested') return 'bg-red-50 text-red-700 border border-red-100'
  if (status === 'not_started') return 'bg-red-50 text-red-700 border border-red-100'
  return 'bg-gray-100 text-gray-600 border border-gray-200'
}

const statusLabel = (status) => {
  if (status === 'approved') return 'complete'
  if (!status) return 'not started'
  return String(status).replaceAll('_', ' ')
}

onMounted(async () => {
  loading.value = true
  try {
    await store.fetchUser()
    const { data } = await axios.get('/api/my-registration')
    progress.value = data
    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat data profil.'
  } finally {
    loading.value = false
  }
})
</script>
