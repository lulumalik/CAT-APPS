<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      title="Laporan Perkembangan Peserta"
      subtitle="Catat aktivitas & perkembangan harian peserta. Orang tua akan melihatnya di dashboard."
      theme="blue"
      :icon="LineChart"
    />

    <div class="grid lg:grid-cols-12 gap-6">
      <section class="lg:col-span-5 bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-6">
        <h2 class="font-bold text-lg mb-4">Tulis Laporan Harian</h2>

        <label class="block text-sm font-medium text-gray-700 mb-1">Cari Peserta</label>
        <input v-model="studentSearch" type="text" placeholder="Nama / email / username"
          class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm mb-2 outline-none focus:border-[#9DB359]"
          @input="searchStudents" />
        <div v-if="students.length" class="border border-gray-100 rounded-xl divide-y divide-gray-50 mb-4 max-h-40 overflow-y-auto">
          <button v-for="s in students" :key="s.id" type="button"
            class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50"
            :class="form.student_user_id === s.id ? 'bg-[#9DB359]/10 font-semibold' : ''"
            @click="selectStudent(s)">
            {{ s.name }} <span class="text-xs text-gray-400">· {{ s.username || s.email }}</span>
          </button>
        </div>

        <form @submit.prevent="createReport" class="space-y-3">
          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
              <input v-model="form.title" type="text" required placeholder="mis. Latihan Fisik Pagi"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#9DB359]" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
              <input v-model="form.report_date" type="date"
                class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#9DB359]" />
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ringkasan</label>
            <textarea v-model="form.summary" rows="2"
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#9DB359]"></textarea>
          </div>
          <div v-for="cat in categoryKeys" :key="cat.key">
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ cat.label }}</label>
            <input v-model="form.categories[cat.key]" type="text"
              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm outline-none focus:border-[#9DB359]" />
          </div>

          <p v-if="selectedStudentName" class="text-xs text-gray-500">
            Peserta: <span class="font-semibold">{{ selectedStudentName }}</span>
          </p>
          <button type="submit" :disabled="!form.student_user_id || saving"
            class="w-full rounded-full bg-[#1A1A1A] text-white py-2.5 text-sm font-semibold disabled:opacity-50">
            {{ saving ? 'Menyimpan...' : 'Simpan Laporan Harian' }}
          </button>
        </form>
      </section>

      <section class="lg:col-span-7 bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-6">
        <div class="flex items-center justify-between mb-4">
          <h2 class="font-bold text-lg">Laporan Tersimpan</h2>
          <button
            type="button"
            class="inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-500 hover:text-[#1A1A1A] hover:bg-gray-100 transition-colors"
            title="Muat ulang"
            aria-label="Muat ulang"
            @click="loadReports"
          >
            <RefreshCw class="h-4 w-4" />
          </button>
        </div>
        <p v-if="!form.student_user_id" class="text-sm text-gray-400 py-8 text-center">Pilih peserta untuk melihat laporannya.</p>
        <div v-else-if="!reports.length" class="text-sm text-gray-400 py-8 text-center">Belum ada laporan.</div>
        <div v-else class="space-y-3">
          <article v-for="r in reports" :key="r.id" class="rounded-xl border border-gray-100 p-4">
            <div class="flex items-start justify-between gap-2">
              <div>
                <div class="font-semibold text-sm">{{ r.title }}
                  <span class="text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full ml-1"
                    :class="r.type === 'weekly_summary' ? 'bg-[#9DB359]/15 text-[#6f8235]' : 'bg-gray-100 text-gray-500'">
                    {{ r.type === 'weekly_summary' ? 'Mingguan' : 'Harian' }}
                  </span>
                </div>
                <div class="text-xs text-gray-500">{{ r.report_date }} · {{ r.created_by }}</div>
              </div>
              <button
                type="button"
                class="inline-flex items-center justify-center w-8 h-8 rounded-full text-red-600 hover:bg-red-50 transition-colors"
                title="Hapus"
                @click="remove(r)"
              >
                <Trash2 class="h-3.5 w-3.5" />
              </button>
            </div>
            <p v-if="r.summary" class="text-sm text-gray-600 mt-1">{{ r.summary }}</p>
          </article>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { LineChart, RefreshCw, Trash2 } from 'lucide-vue-next'
import axios from 'axios'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useToast, useModal } from '@/composables/useNotification'

const toast = useToast()
const { confirm } = useModal()

const categoryKeys = [
  { key: 'akademik', label: 'Catatan Akademik' },
]

const studentSearch = ref('')
const students = ref([])
const reports = ref([])
const saving = ref(false)
let searchTimer = null

const form = reactive({
  student_user_id: null,
  title: '',
  report_date: new Date().toISOString().slice(0, 10),
  summary: '',
  categories: {},
})

const selectedStudentName = computed(() => students.value.find((s) => s.id === form.student_user_id)?.name || '')

function searchStudents() {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(async () => {
    try {
      const { data } = await axios.get('/api/students/search', { params: { search: studentSearch.value } })
      students.value = Array.isArray(data) ? data : (data.items || [])
    } catch (e) {
      students.value = []
    }
  }, 300)
}

function selectStudent(s) {
  form.student_user_id = s.id
  loadReports()
}

function cleanCategories() {
  const out = {}
  for (const [k, v] of Object.entries(form.categories)) {
    if (v && String(v).trim()) out[k] = v
  }
  return out
}

async function createReport() {
  saving.value = true
  try {
    await axios.post('/api/student-reports', {
      student_user_id: form.student_user_id,
      title: form.title,
      report_date: form.report_date,
      summary: form.summary,
      categories: cleanCategories(),
    })
    toast.success('OK', 'Laporan harian tersimpan.')
    form.title = ''
    form.summary = ''
    form.categories = {}
    await loadReports()
  } catch (e) {
    toast.error('Gagal', e?.response?.data?.message || 'Tidak bisa menyimpan laporan.')
  } finally {
    saving.value = false
  }
}

async function loadReports() {
  if (!form.student_user_id) return
  try {
    const { data } = await axios.get('/api/student-reports', { params: { student_id: form.student_user_id } })
    reports.value = data.items || []
  } catch (e) {
    reports.value = []
  }
}

async function remove(r) {
  const ok = await confirm({ title: 'Hapus laporan?', message: r.title, confirmText: 'Hapus', type: 'danger' })
  if (!ok) return
  try {
    await axios.delete(`/api/student-reports/${r.id}`)
    await loadReports()
  } catch (e) {
    toast.error('Gagal', 'Tidak bisa menghapus.')
  }
}
</script>
