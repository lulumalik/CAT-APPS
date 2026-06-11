<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div class="mb-8">
      <h1 class="text-3xl font-bold text-[#1A1A1A]">Undang Orang Tua</h1>
      <p class="text-gray-500 mt-2">Hubungkan orang tua/wali dengan peserta yang sudah menyelesaikan registrasi.</p>
    </div>

    <div class="grid lg:grid-cols-12 gap-6">
      <!-- Create invite -->
      <section class="lg:col-span-5 bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-6">
        <h2 class="font-bold text-lg mb-4">Buat Undangan</h2>

        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Peserta (sudah registrasi)</label>
        <input v-model="studentSearch" type="text" placeholder="Nama / email / username"
          class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm mb-2 outline-none focus:border-[#9DB359]"
          @input="searchStudents" @focus="searchStudents" />

        <select v-model="form.student_user_id"
          class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm mb-2 outline-none focus:border-[#9DB359]">
          <option :value="null">Pilih peserta</option>
          <option v-for="s in studentOptions" :key="s.id" :value="s.id">
            {{ s.name }} · {{ s.username || s.email }}
          </option>
        </select>

        <p v-if="searchingStudents" class="text-xs text-gray-400 mb-2">Mencari peserta...</p>
        <p v-else-if="studentSearch && !studentOptions.length" class="text-xs text-amber-600 mb-2">
          Tidak ada peserta yang cocok. Hanya peserta dengan registrasi selesai yang bisa diundang.
        </p>
        <p v-else-if="!studentOptions.length" class="text-xs text-gray-400 mb-2">
          Belum ada peserta yang menyelesaikan registrasi.
        </p>

        <form @submit.prevent="createInvite" class="space-y-3">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Orang Tua / Wali</label>
            <input v-model="form.guardian_name" type="text" required
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#9DB359]" />
          </div>
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Hubungan</label>
              <select v-model="form.relationship"
                class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm outline-none focus:border-[#9DB359]">
                <option value="ayah">Ayah</option>
                <option value="ibu">Ibu</option>
                <option value="wali">Wali</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">No. WhatsApp</label>
              <input v-model="form.phone" type="text" placeholder="08xxx"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#9DB359]" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email (opsional)</label>
            <input v-model="form.email" type="email"
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#9DB359]" />
          </div>

          <p v-if="selectedStudentName" class="text-xs text-gray-500">
            Untuk peserta: <span class="font-semibold">{{ selectedStudentName }}</span>
          </p>
          <button type="submit" :disabled="!form.student_user_id || saving"
            class="w-full rounded-full bg-[#1A1A1A] text-white py-2.5 text-sm font-semibold disabled:opacity-50">
            {{ saving ? 'Menyimpan...' : 'Buat Undangan' }}
          </button>
        </form>
      </section>

      <!-- Invite list -->
      <section class="lg:col-span-7 bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-bold text-lg">Daftar Undangan</h2>
          <button type="button" class="text-sm text-gray-500 hover:text-[#1A1A1A]" @click="loadInvites">Muat ulang</button>
        </div>

        <div v-if="!invites.length" class="text-sm text-gray-500 py-8 text-center">Belum ada undangan.</div>
        <div v-else class="space-y-3">
          <article v-for="inv in invites" :key="inv.id" class="rounded-xl border border-gray-100 p-4">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="font-semibold text-sm">{{ inv.guardian_name }}
                  <span class="text-xs text-gray-400">({{ inv.relationship_label }})</span>
                </div>
                <div class="text-xs text-gray-500">Peserta: {{ inv.student?.name }}</div>
                <div v-if="inv.phone" class="text-xs text-gray-500">WA: {{ inv.phone }}</div>
              </div>
              <span class="text-[10px] uppercase tracking-wide px-2 py-1 rounded-full font-semibold shrink-0"
                :class="statusClass(inv.invite_status)">{{ statusLabel(inv.invite_status) }}</span>
            </div>

            <div class="flex flex-wrap gap-2 mt-3">
              <button v-if="inv.invite_url" type="button"
                class="text-xs px-3 py-1.5 rounded-full bg-gray-50 border border-gray-100 hover:bg-gray-100"
                @click="copy(inv.invite_url, 'Link disalin')">Salin Link</button>
              <button v-if="inv.whatsapp_message" type="button"
                class="text-xs px-3 py-1.5 rounded-full bg-[#25D366]/10 border border-[#25D366]/30 text-[#1c8a47] hover:bg-[#25D366]/20"
                @click="copy(inv.whatsapp_message, 'Pesan WhatsApp disalin')">Salin Pesan WA</button>
              <a v-if="inv.phone && inv.whatsapp_message" :href="waLink(inv)" target="_blank" rel="noopener"
                class="text-xs px-3 py-1.5 rounded-full bg-[#25D366] text-white hover:bg-[#1eb558]">Buka WhatsApp</a>
              <button v-if="inv.invite_status !== 'accepted'" type="button"
                class="text-xs px-3 py-1.5 rounded-full bg-gray-50 border border-gray-100 hover:bg-gray-100"
                @click="markSent(inv)">Tandai Terkirim</button>
              <button type="button"
                class="text-xs px-3 py-1.5 rounded-full text-red-600 hover:bg-red-50"
                @click="remove(inv)">Hapus</button>
            </div>
          </article>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import axios from 'axios'
import { useToast, useModal } from '@/composables/useNotification'

const toast = useToast()
const { confirm } = useModal()

const studentSearch = ref('')
const studentOptions = ref([])
const invites = ref([])
const saving = ref(false)
const searchingStudents = ref(false)
let searchTimer = null

const form = reactive({
  student_user_id: null,
  guardian_name: '',
  relationship: 'ayah',
  phone: '',
  email: '',
})

const selectedStudentName = computed(() => {
  const s = studentOptions.value.find((x) => x.id === form.student_user_id)
  return s?.name || ''
})

async function fetchStudents() {
  searchingStudents.value = true
  try {
    const { data } = await axios.get('/api/guardians/eligible-students', {
      params: { search: studentSearch.value || undefined },
    })
    studentOptions.value = data.items || []
  } catch (e) {
    studentOptions.value = []
    toast.error('Gagal', e?.response?.data?.message || 'Tidak bisa memuat daftar peserta.')
  } finally {
    searchingStudents.value = false
  }
}

function searchStudents() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(fetchStudents, 300)
}

async function loadInvites() {
  try {
    const { data } = await axios.get('/api/guardians')
    invites.value = data.items || []
  } catch (e) {
    invites.value = []
  }
}

async function createInvite() {
  saving.value = true
  try {
    await axios.post('/api/guardians', { ...form })
    toast.success('OK', 'Undangan dibuat. Salin link / pesan WA untuk dikirim.')
    form.guardian_name = ''
    form.phone = ''
    form.email = ''
    await loadInvites()
  } catch (e) {
    toast.error('Gagal', e?.response?.data?.message || 'Tidak bisa membuat undangan.')
  } finally {
    saving.value = false
  }
}

async function markSent(inv) {
  try {
    await axios.patch(`/api/guardians/${inv.id}/sent`)
    await loadInvites()
  } catch (e) {
    toast.error('Gagal', 'Tidak bisa memperbarui status.')
  }
}

async function remove(inv) {
  const ok = await confirm({
    title: 'Hapus undangan?',
    message: `Hapus undangan untuk ${inv.guardian_name}?`,
    confirmText: 'Hapus',
    type: 'danger',
  })
  if (!ok) return
  try {
    await axios.delete(`/api/guardians/${inv.id}`)
    await loadInvites()
  } catch (e) {
    toast.error('Gagal', 'Tidak bisa menghapus undangan.')
  }
}

async function copy(text, msg) {
  try {
    await navigator.clipboard.writeText(text)
    toast.success('Disalin', msg)
  } catch (e) {
    toast.error('Gagal', 'Tidak bisa menyalin.')
  }
}

function waLink(inv) {
  const phone = String(inv.phone || '').replace(/\D/g, '').replace(/^0/, '62')
  return `https://wa.me/${phone}?text=${encodeURIComponent(inv.whatsapp_message)}`
}

function statusLabel(s) {
  return { pending: 'Belum dikirim', sent: 'Terkirim', accepted: 'Diterima', expired: 'Kedaluwarsa' }[s] || s
}
function statusClass(s) {
  return {
    pending: 'bg-gray-100 text-gray-600',
    sent: 'bg-amber-100 text-amber-700',
    accepted: 'bg-emerald-100 text-emerald-700',
    expired: 'bg-red-100 text-red-700',
  }[s] || 'bg-gray-100 text-gray-600'
}

onMounted(async () => {
  await Promise.all([loadInvites(), fetchStudents()])
})
</script>
