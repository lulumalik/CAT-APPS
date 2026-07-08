<template>
  <main class="max-w-7xl mx-auto px-4 py-8">
    <PageHeroHeader
      :title="t('bimble.classListTitle')"
      :subtitle="t('bimble.classListSubtitle')"
      theme="green"
      :icon="GraduationCap"
    >
      <template #actions>
        <button type="button" class="inline-flex items-center gap-2 rounded-full border border-[#9DB359]/40 bg-[#9DB359]/10 px-5 py-2.5 text-sm font-semibold text-[#5a6b2e] hover:bg-[#9DB359]/20" @click="openFirstRoom" :disabled="!classes.length">
          <BookOpen class="h-4 w-4" />
          {{ t('bimble.openRoom') }}
        </button>
        <button type="button" class="rounded-full bg-[#9DB359] text-white px-5 py-2.5 text-sm font-semibold shadow-md" @click="showCreate = true">
          {{ t('bimble.createClass') }}
        </button>
      </template>
    </PageHeroHeader>

    <div v-if="loading" class="py-16 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="errorMessage"
      class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>

    <div v-else-if="!classes.length" class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 text-sm">
      Belum ada kelas. Buat kelas baru untuk memulai.
    </div>

    <div v-else class="grid gap-6 md:grid-cols-2">
      <article
        v-for="(c, idx) in classes"
        :key="c.id"
        class="rounded-[1.75rem] border border-gray-100 bg-white shadow-lg shadow-black/5 overflow-hidden flex flex-col hover:shadow-xl transition-shadow"
        :class="cardTheme(idx).topBorder"
      >
        <div class="p-6 flex-1 flex flex-col gap-4">
          <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0" :class="cardTheme(idx).iconWrap">
              <component :is="cardTheme(idx).icon" class="h-6 w-6" :class="cardTheme(idx).iconColor" />
            </div>
            <div class="min-w-0">
              <h2 class="text-xl font-bold text-[#1A1A1A] leading-tight">{{ c.name }}</h2>
              <span class="inline-block mt-2 rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-[11px] font-semibold text-gray-600">
                {{ t('bimble.code') }}: {{ c.class_code }}
              </span>
            </div>
          </div>

          <p class="text-sm font-medium text-gray-700">{{ formatProgram(c.program_type) }}</p>

          <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-center gap-2.5">
              <UserRound class="h-4 w-4 text-gray-400 shrink-0" />
              <span>{{ c.instructor?.name || c.instructor_name || 'Belum dipilih' }}</span>
            </li>
            <li class="flex items-center gap-2.5">
              <CalendarRange class="h-4 w-4 text-gray-400 shrink-0" />
              <span>{{ formatPeriod(c) }}</span>
            </li>
            <li class="flex items-center gap-2.5">
              <Users class="h-4 w-4 text-gray-400 shrink-0" />
              <span>{{ c.students_count ?? 0 }} peserta</span>
            </li>
            <li v-if="(c.batches || []).length" class="flex items-start gap-2.5">
              <Layers class="h-4 w-4 text-gray-400 shrink-0 mt-0.5" />
              <span class="text-xs leading-relaxed">{{ (c.batches || []).map((b) => b.name).join(', ') }}</span>
            </li>
          </ul>
        </div>

        <div class="px-6 pb-6 flex flex-col sm:flex-row gap-2">
          <router-link
            v-if="c?.id"
            :to="{ name: 'bimble-class-room', params: { id: c.id } }"
            class="inline-flex flex-1 items-center justify-center rounded-full border-2 px-4 py-2.5 text-sm font-semibold transition-colors hover:bg-gray-50"
            :class="cardTheme(idx).manageBtn"
          >
            {{ t('bimble.openRoom') }} &gt;
          </router-link>
          <button
            type="button"
            class="inline-flex flex-1 items-center justify-center rounded-full border-2 border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50"
            @click="openManage(c)"
          >
            Kelola kelas
          </button>
        </div>
      </article>
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
              <option v-for="p in ONLINE_PROGRAMS" :key="p.value" :value="p.value">{{ programSignupOptionLabel(p) }}</option>
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
          <div>
            <label class="text-sm font-medium text-gray-700">{{ t('batches.linkBatches') }}</label>
            <p class="mt-0.5 text-[11px] text-gray-500">{{ t('batches.linkBatchesHint') }}</p>
            <div class="mt-2 max-h-36 space-y-1.5 overflow-y-auto rounded-xl border border-gray-200 bg-gray-50 p-2">
              <label
                v-for="b in batchOptions"
                :key="b.id"
                class="flex items-center gap-2 rounded-lg px-2 py-1.5 text-sm hover:bg-white"
              >
              <input
                type="checkbox"
                class="rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359]"
                :value="b.id"
                :checked="form.batch_ids.map(Number).includes(Number(b.id))"
                @change="toggleCreateBatch(b.id, $event.target.checked)"
              />
              <span class="truncate">{{ b.name }}</span>
              </label>
              <p v-if="!batchOptions.length" class="px-2 py-1 text-xs text-gray-400">{{ t('batches.noBatchesYet') }}</p>
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
      <div class="bg-white rounded-3xl w-full max-w-6xl p-6 shadow-2xl max-h-[92vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
          <div>
            <h3 class="font-bold text-xl text-[#1A1A1A]">Kelola {{ managedClass.name }}</h3>
            <p class="text-xs text-gray-500">{{ managedClass.class_code }} · {{ formatProgram(managedClass.program_type) }}</p>
          </div>
          <button
            type="button"
            class="inline-flex items-center justify-center w-9 h-9 rounded-full text-gray-400 hover:bg-gray-100 hover:text-gray-700 transition-colors"
            :title="t('common.close')"
            :aria-label="t('common.close')"
            @click="closeManage"
          >
            <X class="h-5 w-5" />
          </button>
        </div>

        <section class="mb-4 rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
          <div class="mb-3 flex items-start justify-between gap-3">
            <div>
              <h4 class="font-semibold text-[#1A1A1A]">{{ t('batches.linkBatches') }}</h4>
              <p class="mt-1 text-xs text-gray-500">{{ t('batches.linkBatchesManageHint') }}</p>
            </div>
            <button
              v-if="manageBatchIds.length"
              type="button"
              class="shrink-0 text-xs font-medium text-gray-500 hover:text-gray-800"
              @click="clearClassBatches"
            >
              {{ t('batches.clearBatches') }}
            </button>
          </div>
          <div class="mb-3 flex flex-wrap gap-2">
            <label
              v-for="b in batchOptions"
              :key="b.id"
              class="inline-flex cursor-pointer items-center gap-2 rounded-full border px-3 py-1.5 text-xs transition-colors"
              :class="isBatchChecked(b.id) ? 'border-[#9DB359] bg-[#9DB359]/10 text-[#5a6b2e]' : 'border-gray-200 bg-white text-gray-700'"
            >
              <input
                type="checkbox"
                class="rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359]"
                :checked="isBatchChecked(b.id)"
                @change="toggleClassBatch(b.id, $event.target.checked)"
              />
              {{ b.name }}
            </label>
            <span v-if="!batchOptions.length" class="text-xs text-gray-400">{{ t('batches.noBatchesYet') }}</span>
          </div>
          <button
            type="button"
            class="rounded-xl bg-[#9DB359] px-4 py-2 text-sm font-semibold text-white disabled:opacity-50"
            :disabled="savingBatches"
            @click="saveClassBatches"
          >
            {{ savingBatches ? '…' : t('batches.saveAndAssign') }}
          </button>
        </section>

        <div class="grid gap-4 lg:grid-cols-3">
          <section class="rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
            <h4 class="font-semibold text-[#1A1A1A]">1) Tambah Peserta</h4>
            <p class="mt-1 mb-3 text-xs text-gray-500">Cari siswa lalu tambahkan ke kelas. Preferensi: manage lewat Batch.</p>
            <input v-model="studentSearch" @input="searchStudents" placeholder="Cari nama/email" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm mb-2" />
            <select v-model="forms.student_id" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm mb-2">
              <option :value="null">Pilih siswa</option>
              <option v-for="s in studentOptions" :key="s.id" :value="s.id">{{ s.name }} ({{ s.email }})</option>
            </select>
            <button type="button" class="w-full rounded-xl bg-[#1A1A1A] text-white py-2 text-sm font-medium" @click="attachStudent">Tambahkan Peserta</button>
            <ul class="mt-3 space-y-2 rounded-xl border border-gray-100 bg-white p-3">
              <li v-for="s in managedClass.students || []" :key="s.id" class="text-xs flex justify-between items-center gap-2 border-b border-gray-50 pb-2 last:border-b-0 last:pb-0">
                <span class="truncate">{{ s.name }}</span>
                <button
                  type="button"
                  class="inline-flex items-center justify-center w-7 h-7 rounded-full text-red-500 hover:bg-red-50"
                  title="Hapus"
                  @click="detachStudent(s.id)"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                </button>
              </li>
              <li v-if="!(managedClass.students || []).length" class="text-xs text-gray-400">Belum ada peserta.</li>
            </ul>
          </section>

          <section class="rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
            <h4 class="font-semibold text-[#1A1A1A]">2) Assign Materi</h4>
            <p class="mt-1 mb-3 text-xs text-gray-500">Pilih materi lalu tentukan sesi pembelajaran.</p>
            <select v-model="forms.material_id" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm mb-2">
              <option :value="null">Pilih materi</option>
              <option v-for="m in materialOptions" :key="m.id" :value="m.id">{{ m.title }}</option>
            </select>
            <input v-model.number="forms.session_number" type="number" min="1" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm mb-2" placeholder="Sesi" />
            <button type="button" class="w-full rounded-xl bg-[#1A1A1A] text-white py-2 text-sm font-medium" @click="attachMaterial">Assign Materi</button>
            <ul class="mt-3 space-y-2 rounded-xl border border-gray-100 bg-white p-3">
              <li v-for="m in managedClass.materials || []" :key="m.id" class="text-xs flex justify-between items-center gap-2 border-b border-gray-50 pb-2 last:border-b-0 last:pb-0">
                <span class="truncate">{{ m.title }}</span>
                <button
                  type="button"
                  class="inline-flex items-center justify-center w-7 h-7 rounded-full text-red-500 hover:bg-red-50"
                  title="Hapus"
                  @click="detachMaterial(m.id)"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                </button>
              </li>
              <li v-if="!(managedClass.materials || []).length" class="text-xs text-gray-400">Belum ada materi.</li>
            </ul>
          </section>

          <section class="rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
            <h4 class="font-semibold text-[#1A1A1A]">3) Assign Quiz</h4>
            <p class="mt-1 mb-3 text-xs text-gray-500">Pilih tes yang akan ditautkan ke kelas.</p>
            <select v-model="forms.test_definition_id" class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm mb-2">
              <option :value="null">{{ testOptions.length ? 'Pilih tes' : 'Tidak ada quiz aktif' }}</option>
              <option v-for="x in testOptions" :key="x.id" :value="x.id">{{ x.name }}</option>
            </select>
            <div class="mb-2 rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700">
              Jenis: <span class="font-semibold">Quiz</span>
            </div>
            <button type="button" class="w-full rounded-xl bg-[#1A1A1A] text-white py-2 text-sm font-medium" @click="attachTest">Assign Quiz</button>
            <ul class="mt-3 space-y-2 rounded-xl border border-gray-100 bg-white p-3">
              <li v-for="x in managedClass.test_definitions || []" :key="x.id" class="text-xs flex justify-between items-center gap-2 border-b border-gray-50 pb-2 last:border-b-0 last:pb-0">
                <span class="truncate">{{ x.name }}</span>
                <button
                  type="button"
                  class="inline-flex items-center justify-center w-7 h-7 rounded-full text-red-500 hover:bg-red-50"
                  title="Hapus"
                  @click="detachTest(x.id)"
                >
                  <Trash2 class="h-3.5 w-3.5" />
                </button>
              </li>
              <li v-if="!(managedClass.test_definitions || []).length" class="text-xs text-gray-400">Belum ada quiz.</li>
            </ul>
          </section>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import { BookOpen, Calculator, CalendarRange, Globe, GraduationCap, Layers, Trash2, UserRound, Users, X } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useI18n } from '@/composables/useI18n'
import { ONLINE_PROGRAMS, programSignupOptionLabel } from '@/constants/onlinePrograms'
import { programCategoryLabel } from '@/utils/userMeta'

const { t } = useI18n()
const router = useRouter()

const cardThemes = [
  {
    topBorder: 'border-t-4 border-t-[#9DB359]',
    iconWrap: 'bg-[#9DB359]/15',
    iconColor: 'text-[#5a6b2e]',
    manageBtn: 'border-[#9DB359] text-[#5a6b2e]',
    icon: GraduationCap,
  },
  {
    topBorder: 'border-t-4 border-t-blue-500',
    iconWrap: 'bg-blue-50',
    iconColor: 'text-blue-600',
    manageBtn: 'border-blue-500 text-blue-600',
    icon: Calculator,
  },
  {
    topBorder: 'border-t-4 border-t-purple-500',
    iconWrap: 'bg-purple-50',
    iconColor: 'text-purple-600',
    manageBtn: 'border-purple-500 text-purple-600',
    icon: Globe,
  },
]

function cardTheme(index) {
  return cardThemes[index % cardThemes.length]
}

function openFirstRoom() {
  const first = classes.value[0]
  if (first?.id) {
    router.push({ name: 'bimble-class-room', params: { id: first.id } })
  }
}

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
const batchOptions = ref([])
const studentSearch = ref('')
const manageBatchIds = ref([])
const savingBatches = ref(false)

const form = reactive({
  name: '',
  program_type: 'regular',
  instructor_id: null,
  academic_period_start: '',
  academic_period_end: '',
  batch_ids: [],
})

const forms = reactive({
  student_id: null,
  material_id: null,
  test_definition_id: null,
  kind: 'quiz',
  session_number: 1,
})

function formatProgram(programType) {
  return programCategoryLabel(programType)
}

function isTestExpired(test) {
  if (test?.status === 'ended') return true
  if (!test?.end_time) return false
  const end = new Date(test.end_time)
  return !Number.isNaN(end.getTime()) && end < new Date()
}

function filterAssignableTests(tests) {
  const attachedIds = new Set((managedClass.value?.test_definitions || []).map((t) => t.id))
  return (Array.isArray(tests) ? tests : []).filter((t) => !isTestExpired(t) && !attachedIds.has(t.id))
}

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/bimble-classes')
    classes.value = (Array.isArray(data) ? data : []).filter((item) => item && item.id)
    errorMessage.value = ''
  } catch (error) {
    classes.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat data kelas kursus.'
  } finally {
    loading.value = false
  }
}

async function createClass() {
  creating.value = true
  try {
    const { data } = await axios.post('/api/bimble-classes', {
      name: form.name,
      program_type: form.program_type,
      instructor_id: form.instructor_id || null,
      academic_period_start: form.academic_period_start || null,
      academic_period_end: form.academic_period_end || null,
      batch_ids: form.batch_ids || [],
    })
    showCreate.value = false
    form.name = ''
    form.instructor_id = null
    form.academic_period_start = ''
    form.academic_period_end = ''
    form.batch_ids = []
    await load()
    const attached = data?.auto_assigned?.attached || 0
    errorMessage.value = attached
      ? `Kelas dibuat. ${attached} peserta dari batch otomatis di-assign.`
      : ''
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

async function loadBatches() {
  try {
    const { data } = await axios.get('/api/batches', { params: { active_only: 1 } })
    batchOptions.value = Array.isArray(data) ? data : []
  } catch {
    batchOptions.value = []
  }
}

function isBatchChecked(id) {
  return manageBatchIds.value.map(Number).includes(Number(id))
}

function toggleClassBatch(id, checked) {
  const numId = Number(id)
  if (checked) {
    if (!isBatchChecked(numId)) manageBatchIds.value = [...manageBatchIds.value, numId]
  } else {
    manageBatchIds.value = manageBatchIds.value.filter((x) => Number(x) !== numId)
  }
}

function clearClassBatches() {
  manageBatchIds.value = []
}

function toggleCreateBatch(id, checked) {
  const numId = Number(id)
  const current = form.batch_ids.map(Number)
  if (checked) {
    if (!current.includes(numId)) form.batch_ids = [...form.batch_ids, numId]
  } else {
    form.batch_ids = form.batch_ids.filter((x) => Number(x) !== numId)
  }
}

async function openManage(c) {
  showManage.value = true
  managedClass.value = null
  manageBatchIds.value = []
  try {
    const [detail, mats, tests] = await Promise.all([
      axios.get(`/api/bimble-classes/${c.id}`),
      axios.get('/api/materials'),
      axios.get('/api/tests'),
    ])
    managedClass.value = detail.data
    manageBatchIds.value = (detail.data.batches || []).map((b) => Number(b.id))
    materialOptions.value = Array.isArray(mats.data) ? mats.data : (mats.data.data || [])
    testOptions.value = filterAssignableTests(Array.isArray(tests.data) ? tests.data : (tests.data?.data || []))
    if (forms.test_definition_id && !testOptions.value.some((t) => t.id === forms.test_definition_id)) {
      forms.test_definition_id = null
    }
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
  manageBatchIds.value = (data.batches || []).map((b) => Number(b.id))
}

async function saveClassBatches() {
  if (!managedClass.value?.id) return
  savingBatches.value = true
  try {
    const { data } = await axios.post(`/api/bimble-classes/${managedClass.value.id}/batches`, {
      batch_ids: manageBatchIds.value,
    })
    managedClass.value = data.class
    manageBatchIds.value = (data.class?.batches || []).map((b) => Number(b.id))
    const attached = data.auto_assigned?.attached || 0
    errorMessage.value = attached
      ? `${attached} peserta dari batch otomatis di-assign ke kelas.`
      : ''
    await load()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menyimpan batch kelas.'
  } finally {
    savingBatches.value = false
  }
}

async function searchStudents() {
  try {
    const { data } = await axios.get('/api/students/search', {
      params: {
        search: studentSearch.value || undefined,
        batch_eligible: 1,
      },
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

async function refreshTestOptions() {
  const { data: tests } = await axios.get('/api/tests')
  testOptions.value = filterAssignableTests(Array.isArray(tests) ? tests : (tests.data || []))
}

async function attachTest() {
  if (!managedClass.value?.id || !forms.test_definition_id) return
  try {
    await axios.post(`/api/bimble-classes/${managedClass.value.id}/tests`, {
      test_definition_id: forms.test_definition_id,
      kind: 'quiz',
    })
    forms.test_definition_id = null
    await reloadManagedClass()
    await refreshTestOptions()
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal assign test.'
  }
}

async function detachTest(testId) {
  if (!managedClass.value?.id) return
  try {
    await axios.delete(`/api/bimble-classes/${managedClass.value.id}/tests/${testId}`)
    await reloadManagedClass()
    await refreshTestOptions()
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
  await Promise.all([load(), loadInstructors(), loadBatches()])
})
</script>
