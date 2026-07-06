<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      title="Undang Orang Tua"
      subtitle="Hubungkan orang tua/wali dengan peserta yang sudah menyelesaikan registrasi."
      theme="rose"
      :icon="HeartHandshake"
    />

    <div class="space-y-6">
      <!-- Create invite -->
      <section class="bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-6">
        <h2 class="font-bold text-lg mb-4">Buat Undangan</h2>

        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Peserta (sudah registrasi)</label>
        <div class="relative mb-2">
          <input v-model="studentSearch" type="text" placeholder="Nama / email / username"
            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 pr-10 text-sm outline-none focus:border-[#9DB359]"
            @input="onStudentSearchInput" @focus="openStudentDropdown" @blur="closeStudentDropdown" />
          <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
          </svg>
          <ul v-if="showStudentDropdown && (searchingStudents || studentOptions.length)"
            class="absolute z-10 mt-1 w-full rounded-xl border border-gray-100 bg-white shadow-lg max-h-48 overflow-y-auto">
            <li v-if="searchingStudents" class="px-4 py-2.5 text-xs text-gray-400">Mencari peserta...</li>
            <li v-for="s in studentOptions" :key="s.id">
              <button type="button"
                class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50"
                :class="form.student_user_id === s.id ? 'bg-[#9DB359]/10 font-semibold' : ''"
                @mousedown.prevent="selectStudent(s)">
                {{ s.name }} <span class="text-xs text-gray-400">· {{ s.username || s.email }}</span>
              </button>
            </li>
          </ul>
        </div>

        <p v-if="!searchingStudents && showStudentDropdown && studentSearch && !studentOptions.length"
          class="text-xs text-amber-600 mb-2">
          Tidak ada peserta yang cocok. Hanya peserta dengan registrasi selesai yang bisa diundang.
        </p>
        <p v-else-if="!searchingStudents && showStudentDropdown && !studentSearch && !studentOptions.length"
          class="text-xs text-gray-400 mb-2">
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
      <section class="bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-bold text-lg">Daftar Undangan</h2>
          <button type="button" class="text-sm text-gray-500 hover:text-[#1A1A1A]" @click="loadInvites(currentPage)">Muat ulang</button>
        </div>

        <div v-if="loadingInvites" class="text-sm text-gray-500 py-8 text-center">Memuat undangan...</div>
        <div v-else-if="!invites.length" class="text-sm text-gray-500 py-8 text-center">Belum ada undangan.</div>
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

        <div v-if="!loadingInvites && lastPage > 0" class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-t border-gray-100 pt-4">
          <p class="text-sm text-gray-500">
            Menampilkan {{ rangeFrom }}–{{ rangeTo }} dari {{ totalInvites }} undangan
          </p>
          <div class="flex items-center justify-end gap-3">
            <span class="text-xs text-gray-500 px-1">
              Halaman {{ currentPage }} dari {{ lastPage }}
            </span>
            <button
              type="button"
              @click="goToPage(currentPage - 1)"
              :disabled="currentPage <= 1"
              class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium"
            >
              Sebelumnya
            </button>
            <button
              type="button"
              @click="goToPage(currentPage + 1)"
              :disabled="currentPage >= lastPage"
              class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium"
            >
              Berikutnya
            </button>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { HeartHandshake } from 'lucide-vue-next'
import axios from 'axios'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useToast, useModal } from '@/composables/useNotification'

const toast = useToast()
const { confirm } = useModal()

const studentSearch = ref('')
const studentOptions = ref([])
const selectedStudent = ref(null)
const showStudentDropdown = ref(false)
const invites = ref([])
const saving = ref(false)
const searchingStudents = ref(false)
const loadingInvites = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)
const totalInvites = ref(0)
const perPage = 10
let searchTimer = null

const rangeFrom = computed(() => {
  if (totalInvites.value === 0) return 0
  return (currentPage.value - 1) * perPage + 1
})
const rangeTo = computed(() => {
  if (totalInvites.value === 0) return 0
  return Math.min(currentPage.value * perPage, totalInvites.value)
})

const form = reactive({
  student_user_id: null,
  guardian_name: '',
  relationship: 'ayah',
  phone: '',
})

const selectedStudentName = computed(() => selectedStudent.value?.name || '')

function studentLabel(s) {
  return `${s.name} · ${s.username || s.email}`
}

function onStudentSearchInput() {
  form.student_user_id = null
  selectedStudent.value = null
  showStudentDropdown.value = true
  searchStudents()
}

function openStudentDropdown() {
  showStudentDropdown.value = true
  searchStudents()
}

function closeStudentDropdown() {
  setTimeout(() => {
    showStudentDropdown.value = false
  }, 150)
}

function selectStudent(s) {
  form.student_user_id = s.id
  selectedStudent.value = s
  studentSearch.value = studentLabel(s)
  showStudentDropdown.value = false
}

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

async function loadInvites(page = 1) {
  loadingInvites.value = true
  try {
    const { data } = await axios.get('/api/guardians', {
      params: { page, per_page: perPage },
    })
    invites.value = data.data || []
    currentPage.value = data.current_page || 1
    lastPage.value = data.last_page || 1
    totalInvites.value = data.total || 0
  } catch (e) {
    invites.value = []
    currentPage.value = 1
    lastPage.value = 1
    totalInvites.value = 0
  } finally {
    loadingInvites.value = false
  }
}

function goToPage(page) {
  if (page < 1 || page > lastPage.value) return
  loadInvites(page)
}

async function createInvite() {
  saving.value = true
  try {
    await axios.post('/api/guardians', { ...form })
    toast.success('OK', 'Undangan dibuat. Salin link / pesan WA untuk dikirim.')
    form.guardian_name = ''
    form.phone = ''
    await loadInvites(1)
  } catch (e) {
    toast.error('Gagal', e?.response?.data?.message || 'Tidak bisa membuat undangan.')
  } finally {
    saving.value = false
  }
}

async function markSent(inv) {
  try {
    await axios.patch(`/api/guardians/${inv.id}/sent`)
    await loadInvites(currentPage.value)
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
    const nextPage = invites.value.length === 1 && currentPage.value > 1
      ? currentPage.value - 1
      : currentPage.value
    await loadInvites(nextPage)
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
  await Promise.all([loadInvites(1), fetchStudents()])
})
</script>
