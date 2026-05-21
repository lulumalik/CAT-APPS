<template>
  <main class="max-w-7xl mx-auto px-4 md:px-10 py-8 space-y-6">
    <div class="flex items-center justify-between gap-3">
      <div>
        <h1 class="text-2xl font-bold text-[#1A1A1A]">Kelola Sertifikat</h1>
        <p class="text-sm text-gray-500 mt-1">Template sertifikat per program dan distribusi ke siswa melalui notifikasi.</p>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
      <div class="flex flex-wrap gap-2">
        <button
          v-for="program in programs"
          :key="program.value"
          type="button"
          class="px-4 py-2 rounded-full text-sm border transition-colors"
          :class="activeProgram === program.value ? 'bg-[#1A1A1A] text-white border-[#1A1A1A]' : 'border-gray-200 hover:bg-gray-50'"
          @click="activeProgram = program.value"
        >
          {{ program.label }}
        </button>
      </div>

      <div class="mt-5">
        <form class="grid md:grid-cols-2 gap-4" @submit.prevent="saveTemplate">
          <div>
            <label class="text-sm text-gray-600">Judul</label>
            <input v-model="form.title" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" required />
          </div>
          <div>
            <label class="text-sm text-gray-600">Subjudul</label>
            <input v-model="form.subtitle" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          </div>
          <div class="md:col-span-2">
            <label class="text-sm text-gray-600">Isi sertifikat</label>
            <textarea v-model="form.body_text" rows="4" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
            <p class="text-xs text-gray-500 mt-1">Gunakan placeholder: <code>{name}</code>, <code>{program}</code>, <code>{date}</code>.</p>
          </div>
          <div>
            <label class="text-sm text-gray-600">Penanda tangan</label>
            <input v-model="form.sign_name" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-sm text-gray-600">Jabatan</label>
            <input v-model="form.sign_position" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-sm text-gray-600">Warna tema</label>
            <input v-model="form.theme_color" type="color" class="mt-1 h-10 w-24 rounded-lg border border-gray-200 px-1 py-1" />
          </div>
          <div class="md:col-span-2 flex justify-end">
            <button type="submit" class="rounded-full bg-[#1A1A1A] text-white px-5 py-2.5 text-sm font-semibold" :disabled="savingTemplate">
              {{ savingTemplate ? 'Menyimpan...' : 'Simpan Template' }}
            </button>
          </div>
        </form>

        <section class="rounded-2xl border border-gray-200 bg-[#fafafa] p-4">
          <h3 class="font-semibold text-[#1A1A1A]">Preview Sertifikat</h3>
          <p class="text-xs text-gray-500 mt-1">Preview ini contoh yang akan dikirim ke user.</p>
          <div class="mt-4 rounded-xl border-2 border-[#0e2f88] bg-white p-2">
            <div class="relative rounded-[10px] border border-[#7b6a67] overflow-hidden">
              <div class="pointer-events-none absolute -top-2 -right-28 z-20 rotate-[40deg] opacity-95">
                <div class="h-11 w-72 rounded-full bg-gradient-to-r from-[#1339a6] to-[#0e2f88]"></div>
                <div class="mt-3 ml-5 h-8 w-60 rounded-full bg-gradient-to-r from-[#1f5fe5] to-[#0f47cf]"></div>
                <div class="mt-3 ml-12 h-6 w-40 rounded-full bg-gradient-to-r from-[#44b9ff] to-[#1e86f7]"></div>
              </div>
              <div class="pointer-events-none absolute -bottom-2 -left-28 z-0 rotate-[-140deg] opacity-95">
                <div class="h-11 w-72 rounded-full bg-gradient-to-r from-[#1339a6] to-[#0e2f88]"></div>
                <div class="mt-3 ml-5 h-8 w-60 rounded-full bg-gradient-to-r from-[#1f5fe5] to-[#0f47cf]"></div>
                <div class="mt-3 ml-12 h-6 w-40 rounded-full bg-gradient-to-r from-[#44b9ff] to-[#1e86f7]"></div>
              </div>
              <div class="relative z-10 bg-gradient-to-r from-[#1339a6] to-[#0e2f88] text-white text-center px-4 py-4">
                <div class="font-serif text-xl font-bold">NAMA LEMBAGA PENYELENGGARA KEGIATAN</div>
                <div class="text-xs mt-1">Alamat 123 Anywhere St., Any City, ST 12345</div>
                <div class="text-[11px] opacity-90">E-mail hello@reallygreatsite.com | Telepon +123-456-7890</div>
              </div>

              <div class="relative z-10 px-5 py-5 text-[#403735]">
                <h4 class="text-3xl font-serif font-bold text-center" :style="{ color: form.theme_color || '#1A1A1A' }">{{ form.title || 'SERTIFIKAT' }}</h4>
                <p class="text-center text-xs mt-1">NOMOR : CERT/0001/DUMMY</p>
                <p class="text-center text-sm mt-4">Diberikan Kepada</p>
                <p class="text-center text-4xl text-black font-serif font-bold mt-1">{{ previewUserName }}</p>
                <div class="border-t-2 border-[#000] mt-2"></div>
                <p class="text-center text-sm mt-4 leading-relaxed whitespace-pre-line">{{ previewBodyText }}</p>
                <p class="text-center mt-3">
                  <span class="inline-block rounded bg-[#e6ca75] px-4 py-1 text-sm font-semibold text-[#5d4a2e]">Jakarta, {{ todayLabel }}</span>
                </p>
                <p class="text-center text-[11px] text-gray-500 mt-2">Program: {{ readableProgram(activeProgram) }}</p>

                <div class="flex justify-center items-center gap-44 mt-8 text-center">
                  <div>
                    <p class="text-sm font-semibold">Penanggung Jawab</p>
                    <div class="border-t w-56 border-gray-400 mt-10"></div>
                    <p class="text-base font-semibold mt-2">{{ form.sign_name || 'Admin Program' }}</p>
                    <p class="text-xs text-gray-500">({{ form.sign_position || 'Penyelenggara' }})</p>
                  </div>
                  <div>
                    <p class="text-sm font-semibold">Ketua Panitia</p>
                    <div class="border-t w-56 border-gray-400 mt-10"></div>
                    <p class="text-base font-semibold mt-2">Admin Panitia</p>
                    <p class="text-xs text-gray-500">(Jabatan)</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
      <h2 class="text-lg font-bold text-[#1A1A1A]">Terbitkan Sertifikat</h2>
      <p class="text-sm text-gray-500 mt-1">Admin menerbitkan sertifikat ke siswa, lalu link unduh dikirim via notifikasi.</p>
      <div class="grid md:grid-cols-3 gap-3 mt-4">
        <input
          v-model="studentSearch"
          type="text"
          placeholder="Cari nama/email siswa..."
          class="rounded-xl border border-gray-200 px-3 py-2 text-sm"
          @input="searchStudents"
        />
        <select v-model="selectedUserId" class="rounded-xl border border-gray-200 px-3 py-2 text-sm">
          <option :value="null">Pilih siswa</option>
          <option v-for="s in studentOptions" :key="s.id" :value="s.id">
            {{ s.name }} ({{ readableProgram(s.program_category) }})
          </option>
        </select>
        <button
          type="button"
          class="rounded-full bg-[#9DB359] text-white px-5 py-2.5 text-sm font-semibold"
          :disabled="issuing || !selectedUserId"
          @click="issueCertificate"
        >
          {{ issuing ? 'Menerbitkan...' : 'Terbitkan & Kirim Notifikasi' }}
        </button>
      </div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
      <h2 class="text-lg font-bold text-[#1A1A1A]">Riwayat Penerbitan</h2>
      <div v-if="loadingIssues" class="text-sm text-gray-500 py-6">Memuat riwayat...</div>
      <div v-else-if="!issues.length" class="text-sm text-gray-500 py-6">Belum ada sertifikat diterbitkan.</div>
      <div v-else class="space-y-2 mt-3">
        <div v-for="item in issues" :key="item.id" class="rounded-xl border border-gray-100 p-3 text-sm flex items-center justify-between gap-3">
          <div>
            <div class="font-semibold text-[#1A1A1A]">{{ item.user?.name }} - {{ readableProgram(item.program_category) }}</div>
            <div class="text-xs text-gray-500">No: {{ item.certificate_number }}</div>
          </div>
          <a :href="`/api/certificates/${item.id}/download`" class="text-[#6c7c3f] text-xs font-semibold hover:underline">
            Download
          </a>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import axios from 'axios'
import { ONLINE_PROGRAMS, programSignupOptionLabel } from '@/constants/onlinePrograms'
import { programCategoryLabel } from '@/utils/userMeta'
import { useToast } from '@/composables/useNotification'

const toast = useToast()
const programs = ONLINE_PROGRAMS.map((p) => ({
  value: p.value,
  label: programSignupOptionLabel(p),
}))

const activeProgram = ref('vip')
const templates = ref([])
const savingTemplate = ref(false)
const loadingIssues = ref(false)
const issues = ref([])

const form = reactive({
  title: '',
  subtitle: '',
  body_text: '',
  sign_name: '',
  sign_position: '',
  theme_color: '#1A1A1A',
})

const studentSearch = ref('')
const studentOptions = ref([])
const selectedUserId = ref(null)
const issuing = ref(false)

const readableProgram = (program) => programCategoryLabel(program)
const todayLabel = computed(() => new Date().toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' }))
const selectedUser = computed(() => studentOptions.value.find((x) => Number(x.id) === Number(selectedUserId.value)) || null)
const previewUserName = computed(() => selectedUser.value?.name || 'Nama Peserta')
const previewBodyText = computed(() => {
  const raw = String(form.body_text || 'Dengan ini menyatakan bahwa peserta telah menyelesaikan program dengan baik.')
  return raw
    .replaceAll('{name}', previewUserName.value)
    .replaceAll('{program}', readableProgram(activeProgram.value))
    .replaceAll('{date}', todayLabel.value)
})

const hydrateForm = () => {
  const tpl = templates.value.find((x) => x.program_category === activeProgram.value)
  if (!tpl) return
  form.title = tpl.title || ''
  form.subtitle = tpl.subtitle || ''
  form.body_text = tpl.body_text || ''
  form.sign_name = tpl.sign_name || ''
  form.sign_position = tpl.sign_position || ''
  form.theme_color = tpl.theme_color || '#1A1A1A'
}

async function loadTemplates() {
  const { data } = await axios.get('/api/admin/certificate-templates')
  templates.value = Array.isArray(data) ? data : []
  hydrateForm()
}

async function saveTemplate() {
  savingTemplate.value = true
  try {
    const { data } = await axios.put(`/api/admin/certificate-templates/${activeProgram.value}`, { ...form })
    const idx = templates.value.findIndex((x) => x.program_category === activeProgram.value)
    if (idx >= 0) templates.value[idx] = data
    else templates.value.push(data)
    toast.success('Success', 'Template sertifikat disimpan.')
  } catch (error) {
    toast.error('Error', error?.response?.data?.message || 'Gagal menyimpan template.')
  } finally {
    savingTemplate.value = false
  }
}

async function searchStudents() {
  try {
    const { data } = await axios.get('/api/students/search', {
      params: { search: studentSearch.value || undefined },
    })
    studentOptions.value = Array.isArray(data) ? data : []
  } catch (error) {
    studentOptions.value = []
  }
}

async function issueCertificate() {
  if (!selectedUserId.value) return
  issuing.value = true
  try {
    await axios.post('/api/admin/certificates/issue', { user_id: selectedUserId.value })
    toast.success('Success', 'Sertifikat diterbitkan dan notifikasi terkirim ke siswa.')
    selectedUserId.value = null
    await loadIssues()
  } catch (error) {
    toast.error('Error', error?.response?.data?.message || 'Gagal menerbitkan sertifikat.')
  } finally {
    issuing.value = false
  }
}

async function loadIssues() {
  loadingIssues.value = true
  try {
    const { data } = await axios.get('/api/admin/certificates/issues')
    issues.value = Array.isArray(data) ? data : []
  } catch (error) {
    issues.value = []
  } finally {
    loadingIssues.value = false
  }
}

watch(activeProgram, hydrateForm)
onMounted(async () => {
  await Promise.all([loadTemplates(), loadIssues(), searchStudents()])
})
</script>
