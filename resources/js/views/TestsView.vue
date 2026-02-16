<template>
  <main class="container-main py-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-semibold">Test Management</h1>
        <p class="text-muted">Create and manage your tests</p>
      </div>
      <button class="px-4 py-2 rounded-md bg-navy text-white cursor-pointer" @click="openCreate">Create Test</button>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ tests.length }}</div><div class="text-muted">Total Tests</div></div>
      <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ activeCount }}</div><div class="text-muted">Active Tests</div></div>
      <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ questions.length }}</div><div class="text-muted">Available Questions</div></div>
      <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ assignments }}</div><div class="text-muted">Total Assignments</div></div>
    </div>

    <div class="ui-card mt-6">
      <input v-model="search" type="text" placeholder="Search tests..." class="rounded-md border border-gray-200 bg-white px-3 py-2 w-full" />
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <div v-if="filtered.length===0" class="ui-card text-center">
          <div class="text-6xl">🗎</div>
          <div class="mt-2 font-medium">No tests found</div>
          <div class="text-muted">Start by creating your first test</div>
          <button class="mt-4 w-full px-4 py-2 rounded-md bg-navy text-white cursor-pointer" @click="openCreate">+ Create Test</button>
        </div>

        <div v-for="(t,i) in filtered" :key="i" class="ui-card">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold">{{ t.name }}</h2>
              <p class="text-sm text-muted">{{ t.description }}</p>
            </div>
            <div class="text-right">
              <div v-if="t.startTime" class="text-sm">
                <span class="text-muted">Start:</span> <span class="font-medium">{{ formatDate(t.startTime) }}</span>
              </div>
              <div v-if="t.endTime" class="text-sm">
                <span class="text-muted">End:</span> <span class="font-medium">{{ formatDate(t.endTime) }}</span>
              </div>
              <div v-else class="text-sm">Scheduled: <span class="font-medium">{{ formatDate(t.scheduleAt) }}</span></div>
              <div class="text-sm">Duration: {{ t.duration }} min</div>
            </div>
          </div>
          <div class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">Category: {{ t.category }}</div>
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">Questions: {{ t.questionIds.length }}</div>
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
              Status: 
              <span v-if="t.status === 'upcoming'" class="text-yellow-600">🔒 Upcoming</span>
              <span v-else-if="t.status === 'ongoing'" class="text-green-600">✓ Ongoing</span>
              <span v-else-if="t.status === 'ended'" class="text-gray-600">Ended</span>
              <span v-else>{{ isActive(t) ? 'Active' : 'Scheduled' }}</span>
            </div>
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
              <span v-if="t.isActive">✓ Active</span>
              <span v-else class="text-gray-500">Inactive</span>
            </div>
          </div>
          <div class="mt-4 flex items-center justify-end gap-2">
            <button class="px-3 py-1 rounded-md border border-gray-200 cursor-pointer" @click="edit(i)">Edit</button>
            <button class="px-3 py-1 rounded-md border border-red-200 text-red-600 cursor-pointer" @click="remove(i)">Delete</button>
          </div>
        </div>
      </div>

      <aside>
        <div class="ui-card">
          <h3 class="font-medium">Upcoming Tests</h3>
          <ul class="mt-3 space-y-2">
            <li v-for="u in upcoming" :key="u.name" class="flex items-center justify-between">
              <span>{{ u.name }}</span>
              <span class="text-sm">{{ formatDate(u.scheduleAt) }}</span>
            </li>
          </ul>
        </div>
      </aside>
    </div>

    <TestCreateModal v-if="showCreate" :questions="questions" :categories="categories" :initial="editingItem" @close="closeCreate" @submit="saveTest" />
    <TestAssignQuestionsModal v-if="showAssign" :questions="questions" :test="assignTarget" @close="closeAssign" @submit="saveAssign" />
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TestCreateModal from '@/components/TestCreateModal.vue'
import TestAssignQuestionsModal from '@/components/TestAssignQuestionsModal.vue'
import { useModal, useToast } from '@/composables/useNotification'

const { confirm } = useModal()
const toast = useToast()

const getApiErrorMessage = (error, fallbackMessage) => {
  const data = error?.response?.data
  if (!data) return fallbackMessage
  if (typeof data === 'string') return data
  if (typeof data?.message === 'string' && data.message.trim()) return data.message
  if (typeof data?.error === 'string' && data.error.trim()) return data.error
  if (data?.errors && typeof data.errors === 'object') {
    const firstKey = Object.keys(data.errors)[0]
    const firstVal = firstKey ? data.errors[firstKey] : null
    if (Array.isArray(firstVal) && typeof firstVal[0] === 'string' && firstVal[0].trim()) return firstVal[0]
    if (typeof firstVal === 'string' && firstVal.trim()) return firstVal
  }
  return fallbackMessage
}

const categories = ['Geography','Math','Science','History','IT']
const questions = ref([])
const tests = ref([])
const assignments = 0
const search = ref('')
const filtered = computed(() => tests.value.filter(t => !search.value || t.name.toLowerCase().includes(search.value.toLowerCase())))
const isActive = (t) => new Date(t.scheduleAt) <= new Date()
const activeCount = computed(() => tests.value.filter(isActive).length)
const upcoming = computed(() => tests.value.filter(t => !isActive(t)).sort((a,b)=> new Date(a.scheduleAt)-new Date(b.scheduleAt)))

const showCreate = ref(false)
const showAssign = ref(false)
const assignTarget = ref(null)
const editingIndex = ref(null)
const editingItem = computed(() => editingIndex.value!==null ? tests.value[editingIndex.value] : null)
const openCreate = () => { editingIndex.value=null; showCreate.value=true }
const closeCreate = () => { showCreate.value=false }
const toApiDateTime = (value) => {
  if (!value || typeof value !== 'string') return value
  if (/[zZ]$/.test(value) || /[+-]\d{2}:\d{2}$/.test(value)) return value
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toISOString()
}
const toSnake = (obj) => {
  const scheduleAt = obj?.scheduleAt ?? obj?.schedule_at
  const startTime = obj?.startTime ?? obj?.start_time
  const endTime = obj?.endTime ?? obj?.end_time
  const isActive = obj?.isActive ?? obj?.is_active
  const questionIds = obj?.questionIds ?? obj?.question_ids

  return {
    name: obj?.name,
    description: obj?.description ?? null,
    category: obj?.category,
    duration: obj?.duration,
    schedule_at: toApiDateTime(scheduleAt),
    start_time: toApiDateTime(startTime),
    end_time: toApiDateTime(endTime),
    is_active: isActive,
    question_ids: questionIds,
  }
}
const mapTest = (item) => ({
  ...item,
  scheduleAt: item.schedule_at,
  startTime: item.start_time,
  endTime: item.end_time,
  isActive: !!item.is_active,
  questionIds: Array.isArray(item.question_ids) ? item.question_ids : [],
})
const saveTest = async (payload) => {
  try {
    if (editingIndex.value!==null) {
      const id = tests.value[editingIndex.value].id
      await window.axios.put(`/api/tests/${id}`, toSnake(payload))
      toast.success('Test Updated', 'The test has been updated successfully.')
    } else {
      const res = await window.axios.post('/api/tests', toSnake(payload))
      toast.success('Test Created', 'Test dibuat. Silakan tambahkan pertanyaan.')
      assignTarget.value = res.data
      showAssign.value = true
    }
    showCreate.value=false
    await load()
  } catch (error) {
    toast.error('Error', getApiErrorMessage(error, 'Failed to save test. Please try again.'))
  }
}
const edit = (i) => { 
  editingIndex.value=i; 
  assignTarget.value = tests.value[i]
  showAssign.value = true 
}
const remove = async (i) => { 
  const testName = tests.value[i].name
  const confirmed = await confirm(
    'Delete Test',
    `Are you sure you want to delete "${testName}"? This action cannot be undone.`,
    'Delete',
    'Cancel'
  )
  
  if (confirmed) {
    try {
      const id = tests.value[i].id
      await window.axios.delete(`/api/tests/${id}`)
      toast.success('Test Deleted', 'The test has been deleted successfully.')
      await load()
    } catch (error) {
      toast.error('Error', getApiErrorMessage(error, 'Failed to delete test. Please try again.'))
    }
  }
}

const formatDate = (d) => {
  const date = new Date(d)
  if (Number.isNaN(date.getTime())) return ''
  return new Intl.DateTimeFormat('id-ID', {
    year: 'numeric',
    month: '2-digit',
    day: '2-digit',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  }).format(date)
}

const load = async () => {
  try {
    const qRes = await window.axios.get('/api/questions')
    questions.value = qRes.data.items
    const tRes = await window.axios.get('/api/tests')
    tests.value = Array.isArray(tRes.data) ? tRes.data.map(mapTest) : []
  } catch (error) {
    toast.error('Error', getApiErrorMessage(error, 'Failed to load data. Please refresh the page.'))
  }
}

onMounted(load)

const closeAssign = () => { showAssign.value=false; assignTarget.value=null }
const saveAssign = async (payload) => {
  try {
    const id = payload.id
    await window.axios.put(`/api/tests/${id}`, toSnake(payload))
    toast.success('Assigned', 'Pertanyaan telah ditambahkan ke test.')
    showAssign.value=false
    await load()
  } catch (error) {
    toast.error('Error', getApiErrorMessage(error, 'Gagal menyimpan assignment.'))
  }
}
</script>

<style scoped>
</style>
