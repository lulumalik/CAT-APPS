<template>
  <main class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
      <h1 class="text-2xl font-bold text-[#1A1A1A]">{{ t('bimble.manageTitle') }}</h1>
      <button type="button" class="rounded-full bg-[#9DB359] text-white px-5 py-2.5 text-sm font-semibold shadow-md" @click="showCreate = true">
        {{ t('bimble.createClass') }}
      </button>
    </div>

    <div v-if="loading" class="py-16 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="errorMessage"
      class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>

    <div v-else class="grid gap-4 md:grid-cols-2">
      <div
        v-for="c in classes"
        :key="c.id"
        class="rounded-[2rem] border border-gray-100 bg-white p-6 shadow-lg shadow-black/5 flex flex-col gap-3"
      >
        <div class="flex justify-between items-start gap-2">
          <div>
            <h2 class="text-lg font-bold text-[#1A1A1A]">{{ c.name }}</h2>
            <p class="text-sm text-gray-500">{{ t('bimble.code') }}: {{ c.class_code }} · {{ formatProgram(c.program_type) }}</p>
          </div>
          <router-link
            v-if="c?.id"
            :to="{ name: 'bimble-class-room', params: { id: c.id } }"
            class="text-sm font-semibold text-[#9DB359] hover:underline shrink-0"
          >
            {{ t('bimble.openRoom') }}
          </router-link>
          <span v-else class="text-xs text-gray-400">class id missing</span>
        </div>
        <p class="text-sm text-gray-600">Pengajar: {{ c.instructor?.name || c.instructor_name || 'Belum dipilih' }}</p>
        <p class="text-xs text-gray-500">Periode: {{ formatPeriod(c) }}</p>
        <p class="text-xs text-gray-400">{{ t('bimble.students') }}: {{ c.students_count ?? 0 }}</p>
        <button
          type="button"
          class="self-start mt-1 text-xs px-3 py-1.5 rounded-full border border-gray-200 hover:bg-gray-50"
          @click="openManage(c)"
        >
          Kelola kelas
        </button>
      </div>
    </div>

    <div v-if="showCreate" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="showCreate = false">
      <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <h3 class="font-bold text-lg mb-4">{{ t('bimble.createClass') }}</h3>
        <form class="space-y-3" @submit.prevent="createClass">
          <div>
            <label class="text-sm font-medium text-gray-700">{{ t('bimble.className') }}</label>
            <input v-model="form.name" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700">{{ t('bimble.programType') }}</label>
            <select v-model="form.program_type" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
              <option v-for="p in programTypes" :key="p" :value="p">{{ p }}</option>
            </select>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700">{{ t('bimble.instructor') }}</label>
            <select v-model="form.instructor_id" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
              <option :value="null">Pilih pengajar</option>
              <option v-for="instructor in instructorOptions" :key="instructor.id" :value="instructor.id">
                {{ instructor.name }} ({{ instructor.role }})
              </option>
            </select>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700">{{ t('bimble.period') }}</label>
            <div class="mt-1 grid grid-cols-2 gap-2">
              <input v-model="form.academic_period_start" type="date" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
              <input v-model="form.academic_period_end" type="date" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-full border border-gray-200 text-sm" @click="showCreate = false">{{ t('common.cancel') }}</button>
            <button type="submit" class="px-4 py-2 rounded-full bg-[#1A1A1A] text-white text-sm font-semibold" :disabled="creating">
              {{ creating ? '…' : t('common.create') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <div v-if="showManage && managedClass" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="closeManage">
      <div class="bg-white rounded-2xl w-full max-w-5xl p-6 shadow-2xl max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
          <div>
            <h3 class="font-bold text-xl text-[#1A1A1A]">Kelola {{ managedClass.name }}</h3>
            <p class="text-xs text-gray-500">{{ managedClass.class_code }} · {{ formatProgram(managedClass.program_type) }}</p>
          </div>
          <button type="button" class="px-3 py-1.5 rounded-full border border-gray-200 text-sm" @click="closeManage">Tutup</button>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
          <section class="rounded-xl border border-gray-100 p-4">
            <h4 class="font-semibold mb-3">Tambah Peserta</h4>
            <input v-model="studentSearch" @input="searchStudents" placeholder="Cari nama/email" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm mb-2" />
            <select v-model="forms.student_id" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm mb-2">
              <option :value="null">Pilih siswa</option>
              <option v-for="s in studentOptions" :key="s.id" :value="s.id">{{ s.name }} ({{ s.email }})</option>
            </select>
            <button type="button" class="w-full rounded-lg bg-[#1A1A1A] text-white py-2 text-sm" @click="attachStudent">Tambahkan</button>
            <ul class="mt-3 space-y-2">
              <li v-for="s in managedClass.students || []" :key="s.id" class="text-xs flex justify-between gap-2">
                <span class="truncate">{{ s.name }}</span>
                <button type="button" class="text-red-500" @click="detachStudent(s.id)">hapus</button>
              </li>
            </ul>
          </section>

          <section class="rounded-xl border border-gray-100 p-4">
            <h4 class="font-semibold mb-3">Assign Materi</h4>
            <select v-model="forms.material_id" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm mb-2">
              <option :value="null">Pilih materi</option>
              <option v-for="m in materialOptions" :key="m.id" :value="m.id">{{ m.title }}</option>
            </select>
            <input v-model.number="forms.session_number" type="number" min="1" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm mb-2" placeholder="Sesi" />
            <button type="button" class="w-full rounded-lg bg-[#1A1A1A] text-white py-2 text-sm" @click="attachMaterial">Assign materi</button>
            <ul class="mt-3 space-y-2">
              <li v-for="m in managedClass.materials || []" :key="m.id" class="text-xs flex justify-between gap-2">
                <span class="truncate">{{ m.title }}</span>
                <button type="button" class="text-red-500" @click="detachMaterial(m.id)">hapus</button>
              </li>
            </ul>
          </section>

          <section class="rounded-xl border border-gray-100 p-4">
            <h4 class="font-semibold mb-3">Assign Tes / Kuis</h4>
            <select v-model="forms.test_definition_id" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm mb-2">
              <option :value="null">Pilih tes</option>
              <option v-for="x in testOptions" :key="x.id" :value="x.id">{{ x.name }}</option>
            </select>
            <select v-model="forms.kind" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm mb-2">
              <option value="cbt">CBT</option>
              <option value="quiz">Quiz</option>
            </select>
            <button type="button" class="w-full rounded-lg bg-[#1A1A1A] text-white py-2 text-sm" @click="attachTest">Assign test</button>
            <ul class="mt-3 space-y-2">
              <li v-for="x in managedClass.test_definitions || []" :key="x.id" class="text-xs flex justify-between gap-2">
                <span class="truncate">{{ x.name }}</span>
                <button type="button" class="text-red-500" @click="detachTest(x.id)">hapus</button>
              </li>
            </ul>
          </section>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { useI18n } from '@/composables/useI18n'
import { programCategoryLabel } from '@/utils/userMeta'

const { t } = useI18n()

const loading = ref(true)
const creating = ref(false)
const classes = ref([])
const showCreate = ref(false)
const errorMessage = ref('')
const showManage = ref(false)
const managedClass = ref(null)
const materialOptions = ref([])
const testOptions = ref([])
const studentOptions = ref([])
const instructorOptions = ref([])
const studentSearch = ref('')

const programTypes = [
  'vip',
  'regular',
  'bimbingan_online',
  'try_out',
]

const form = reactive({
  name: '',
  program_type: 'regular',
  instructor_id: null,
  academic_period_start: '',
  academic_period_end: '',
})

const forms = reactive({
  student_id: null,
  material_id: null,
  test_definition_id: null,
  kind: 'cbt',
  session_number: 1,
})

function formatProgram(programType) {
  return programCategoryLabel(programType)
}

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/bimble-classes')
    classes.value = (Array.isArray(data) ? data : []).filter((item) => item && item.id)
    errorMessage.value = ''
  } catch (error) {
    classes.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat data kelas bimbingan.'
  } finally {
    loading.value = false
  }
}

async function createClass() {
  creating.value = true
  try {
    await axios.post('/api/bimble-classes', {
      name: form.name,
      program_type: form.program_type,
      instructor_id: form.instructor_id || null,
      academic_period_start: form.academic_period_start || null,
      academic_period_end: form.academic_period_end || null,
    })
    showCreate.value = false
    form.name = ''
    form.instructor_id = null
    form.academic_period_start = ''
    form.academic_period_end = ''
    await load()
    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal membuat kelas baru.'
  } finally {
    creating.value = false
  }
}

async function loadInstructors() {
  try {
    const { data } = await axios.get('/api/bimble-class-instructors')
    instructorOptions.value = Array.isArray(data) ? data : []
    if (!form.instructor_id && instructorOptions.value.length === 1) {
      form.instructor_id = instructorOptions.value[0].id
    }
  } catch (error) {
    instructorOptions.value = []
  }
}

async function openManage(c) {
  showManage.value = true
  managedClass.value = null
  try {
    const [detail, mats, tests] = await Promise.all([
      axios.get(`/api/bimble-classes/${c.id}`),
      axios.get('/api/materials'),
      axios.get('/api/tests'),
    ])
    managedClass.value = detail.data
    materialOptions.value = Array.isArray(mats.data) ? mats.data : (mats.data.data || [])
    testOptions.value = Array.isArray(tests.data) ? tests.data : (tests.data.data || [])
    await searchStudents()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal membuka panel kelola kelas.'
    showManage.value = false
  }
}

function closeManage() {
  showManage.value = false
  managedClass.value = null
}

async function reloadManagedClass() {
  if (!managedClass.value?.id) return
  const { data } = await axios.get(`/api/bimble-classes/${managedClass.value.id}`)
  managedClass.value = data
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

async function attachStudent() {
  if (!managedClass.value?.id || !forms.student_id) return
  try {
    await axios.post(`/api/bimble-classes/${managedClass.value.id}/students`, { user_id: forms.student_id })
    forms.student_id = null
    await reloadManagedClass()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menambahkan peserta.'
  }
}

async function detachStudent(userId) {
  if (!managedClass.value?.id) return
  try {
    await axios.delete(`/api/bimble-classes/${managedClass.value.id}/students/${userId}`)
    await reloadManagedClass()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menghapus peserta.'
  }
}

async function attachMaterial() {
  if (!managedClass.value?.id || !forms.material_id) return
  try {
    await axios.post(`/api/bimble-classes/${managedClass.value.id}/materials`, {
      material_id: forms.material_id,
      session_number: forms.session_number || 1,
    })
    forms.material_id = null
    await reloadManagedClass()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal assign materi.'
  }
}

async function detachMaterial(materialId) {
  if (!managedClass.value?.id) return
  try {
    await axios.delete(`/api/bimble-classes/${managedClass.value.id}/materials/${materialId}`)
    await reloadManagedClass()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menghapus materi dari kelas.'
  }
}

async function attachTest() {
  if (!managedClass.value?.id || !forms.test_definition_id) return
  try {
    await axios.post(`/api/bimble-classes/${managedClass.value.id}/tests`, {
      test_definition_id: forms.test_definition_id,
      kind: forms.kind,
    })
    forms.test_definition_id = null
    await reloadManagedClass()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal assign test.'
  }
}

async function detachTest(testId) {
  if (!managedClass.value?.id) return
  try {
    await axios.delete(`/api/bimble-classes/${managedClass.value.id}/tests/${testId}`)
    await reloadManagedClass()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menghapus test dari kelas.'
  }
}

function formatPeriod(c) {
  const start = c?.academic_period_start
  const end = c?.academic_period_end
  if (start && end) {
    return `${start} s/d ${end}`
  }

  return c?.academic_period || 'Belum diatur'
}

onMounted(async () => {
  await Promise.all([load(), loadInstructors()])
})
</script>
