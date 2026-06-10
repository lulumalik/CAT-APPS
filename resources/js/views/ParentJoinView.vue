<template>
  <main class="min-h-screen flex items-center justify-center px-4 py-12 bg-gray-50">
    <div class="w-full max-w-md">
      <div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl shadow-black/5 p-7">
        <div class="flex items-center gap-3 mb-6">
          <img :src="logoUrl" alt="Logo" class="w-10 h-10 object-contain" />
          <div>
            <div class="font-bold text-[#1A1A1A] leading-tight">Dashboard Orang Tua</div>
            <div class="text-xs text-gray-500">Pratistha Cendekia Prestasi</div>
          </div>
        </div>

        <div v-if="loading" class="py-10 text-center text-gray-500">Memeriksa undangan...</div>

        <div v-else-if="invalid" class="rounded-2xl border border-red-100 bg-red-50 p-5 text-red-700 text-sm">
          {{ invalid }}
        </div>

        <template v-else>
          <p class="text-sm text-gray-600 mb-1">
            Undangan untuk <span class="font-semibold text-[#1A1A1A]">{{ invite.guardian_name }}</span>
            ({{ invite.relationship }})
          </p>
          <p class="text-sm text-gray-600 mb-5">
            Memantau perkembangan ananda
            <span class="font-semibold text-[#1A1A1A]">{{ invite.student_name }}</span>.
          </p>

          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input v-model="form.name" type="text" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#9DB359]/40 focus:border-[#9DB359] outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="form.email" type="email" required
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#9DB359]/40 focus:border-[#9DB359] outline-none" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi</label>
              <input v-model="form.password" type="password" required minlength="6"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-[#9DB359]/40 focus:border-[#9DB359] outline-none" />
              <p class="text-xs text-gray-400 mt-1">Minimal 6 karakter. Pakai email & sandi yang sama jika Anda sudah punya akun orang tua.</p>
            </div>

            <p v-if="errorMessage" class="text-sm text-red-600">{{ errorMessage }}</p>

            <button type="submit" :disabled="saving"
              class="w-full rounded-full bg-[#1A1A1A] text-white py-3 text-sm font-semibold disabled:opacity-50">
              {{ saving ? 'Memproses...' : 'Buat Akun & Hubungkan' }}
            </button>
          </form>
        </template>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, onMounted, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'
import { useAppStore } from '@/stores/app'

const route = useRoute()
const router = useRouter()
const store = useAppStore()
const logoUrl = new URL('../../assets/favicon_io/android-chrome-192x192.png', import.meta.url).href

const loading = ref(true)
const saving = ref(false)
const invalid = ref('')
const errorMessage = ref('')
const invite = ref({})
const form = reactive({ name: '', email: '', password: '' })

onMounted(async () => {
  try {
    const { data } = await axios.get(`/api/guardian-invite/${route.params.token}`)
    invite.value = data
    form.name = data.guardian_name || ''
    form.email = data.email || ''
  } catch (e) {
    invalid.value = e?.response?.data?.message || 'Undangan tidak valid atau sudah kedaluwarsa.'
  } finally {
    loading.value = false
  }
})

async function submit() {
  saving.value = true
  errorMessage.value = ''
  try {
    const { data } = await axios.post(`/api/guardian-invite/${route.params.token}/accept`, { ...form })
    if (data.success) {
      store.setUser(data.user)
      store.isAuthChecked = true
      router.push('/dashboard')
    }
  } catch (e) {
    errorMessage.value = e?.response?.data?.message || 'Gagal membuat akun. Coba lagi.'
  } finally {
    saving.value = false
  }
}
</script>
