<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-[#1A1A1A]">Test Management</h1>
        <p class="text-gray-500 mt-1">Create and manage your tests</p>
      </div>
      <button class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2" @click="openCreate">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Create Test
      </button>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-[#9DB359]/30 transition-colors">
        <div class="text-4xl font-bold text-[#1A1A1A] mb-1 group-hover:text-[#9DB359] transition-colors">{{ tests.length }}</div>
        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Tests</div>
      </div>
      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-green-500/30 transition-colors">
        <div class="text-4xl font-bold text-green-600 mb-1">{{ activeCount }}</div>
        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Active Tests</div>
      </div>
      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-blue-500/30 transition-colors">
        <div class="text-4xl font-bold text-blue-600 mb-1">{{ questions.length }}</div>
        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Available Questions</div>
      </div>
      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-purple-500/30 transition-colors">
        <div class="text-4xl font-bold text-purple-600 mb-1">{{ assignments }}</div>
        <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Assignments</div>
      </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 mt-8">
      <div class="relative">
        <input v-model="search" type="text" placeholder="Search tests..." class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 pl-10 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors" />
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      </div>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Skeleton Loader -->
      <div v-if="loading" class="lg:col-span-2 space-y-4">
        <div v-for="n in 3" :key="n" class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm animate-pulse">
          <div class="flex justify-between mb-4">
            <div class="space-y-2">
              <div class="h-6 w-48 bg-gray-100 rounded"></div>
              <div class="h-4 w-64 bg-gray-100 rounded"></div>
            </div>
            <div class="space-y-1">
              <div class="h-4 w-24 bg-gray-100 rounded ml-auto"></div>
              <div class="h-4 w-20 bg-gray-100 rounded ml-auto"></div>
            </div>
          </div>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-4">
            <div class="h-8 bg-gray-100 rounded"></div>
            <div class="h-8 bg-gray-100 rounded"></div>
            <div class="h-8 bg-gray-100 rounded"></div>
            <div class="h-8 bg-gray-100 rounded"></div>
          </div>
        </div>
      </div>

      <div v-else class="lg:col-span-2 space-y-6">
        <div v-if="filtered.length===0" class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-12 text-center">
          <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-4 mx-auto grayscale opacity-50">🗎</div>
          <div class="font-bold text-lg text-[#1A1A1A]">No tests found</div>
          <div class="text-gray-500 mt-2 text-sm">Start by creating your first test</div>
          <button class="mt-6 px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors cursor-pointer" @click="openCreate">+ Create Test</button>
        </div>

        <div v-for="(t,i) in filtered" :key="i" class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-md transition-shadow group">
          <div class="flex items-start justify-between mb-4">
            <div>
              <h2 class="text-xl font-bold text-[#1A1A1A] group-hover:text-[#9DB359] transition-colors">{{ t.name }}</h2>
              <p class="text-sm text-gray-500 mt-1 leading-relaxed">{{ t.description }}</p>
            </div>
            <div class="text-right">
              <div v-if="t.start_time" class="text-sm">
                <span class="text-gray-400 text-xs uppercase tracking-wide block">Start</span> 
                <span class="font-medium text-[#1A1A1A]">{{ formatDate(t.start_time) }}</span>
              </div>
              <div v-if="t.end_time" class="text-sm mt-1">
                <span class="text-gray-400 text-xs uppercase tracking-wide block">End</span> 
                <span class="font-medium text-[#1A1A1A]">{{ formatDate(t.end_time) }}</span>
              </div>
              <div v-else class="text-sm">
                <span class="text-gray-400 text-xs uppercase tracking-wide block">Scheduled</span>
                <span class="font-medium text-[#1A1A1A]">{{ formatDate(t.schedule_at) }}</span>
              </div>
            </div>
          </div>
          
          <div class="mt-6 flex flex-wrap gap-3">
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
              <span class="text-xs text-gray-400 uppercase font-bold">Category</span>
              <span class="text-sm font-medium text-gray-700 capitalize">{{ t.category }}</span>
            </div>
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
              <span class="text-xs text-gray-400 uppercase font-bold">Duration</span>
              <span class="text-sm font-medium text-gray-700">{{ t.duration }} min</span>
            </div>
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
              <span class="text-xs text-gray-400 uppercase font-bold">Questions</span>
              <span class="text-sm font-medium text-gray-700">{{ t.question_ids?.length || 0 }}</span>
            </div>
            <div class="flex items-center gap-2 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">
              <span class="text-xs text-gray-400 uppercase font-bold">Status</span>
              <span v-if="t.status === 'upcoming'" class="text-sm font-medium text-yellow-600 flex items-center gap-1">🔒 Upcoming</span>
              <span v-else-if="t.status === 'ongoing'" class="text-sm font-medium text-green-600 flex items-center gap-1">✓ Ongoing</span>
              <span v-else-if="t.status === 'ended'" class="text-sm font-medium text-gray-600">Ended</span>
              <span v-else class="text-sm font-medium text-gray-700">{{ isActive(t) ? 'Active' : 'Scheduled' }}</span>
            </div>
          </div>

          <div class="mt-6 flex items-center justify-end gap-3 pt-6 border-t border-gray-50">
            <button class="px-4 py-2 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-50 transition-colors text-gray-600" @click="viewSubmissions(t)" :disabled="deletingId === t.id">Submissions</button>
            <button class="px-4 py-2 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-50 transition-colors text-gray-600" @click="edit(i)" :disabled="deletingId === t.id">Edit</button>
            <button class="px-4 py-2 rounded-full border border-red-100 text-red-600 text-sm font-medium hover:bg-red-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" @click="remove(i)" :disabled="deletingId === t.id">
              <span v-if="deletingId === t.id" class="flex items-center gap-2">
                <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Deleting...
              </span>
              <span v-else>Delete</span>
            </button>
          </div>
        </div>
      </div>

      <aside>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-8 sticky top-24">
          <h3 class="font-bold text-lg text-[#1A1A1A] mb-4 flex items-center gap-2">
            <span class="w-1.5 h-6 rounded-full bg-[#9DB359]"></span>
            Upcoming Tests
          </h3>
          <ul class="space-y-4">
            <li v-if="upcoming.length === 0" class="text-gray-400 text-sm italic">No upcoming tests scheduled.</li>
            <li v-for="u in upcoming" :key="u.name" class="flex items-start justify-between pb-4 border-b border-gray-50 last:border-0 last:pb-0">
              <div>
                <div class="font-medium text-[#1A1A1A] text-sm">{{ u.name }}</div>
                <div class="text-xs text-gray-400 mt-0.5 capitalize">{{ u.category }}</div>
              </div>
              <span class="text-xs font-medium bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ formatDateShort(u.schedule_at) }}</span>
            </li>
          </ul>
        </div>
      </aside>
    </div>

    <TestCreateModal v-if="showCreateModal" :categories="categories" :initial="selectedTest" @close="closeCreate" @submit="createTest" />
    <TestAssignQuestionsModal v-if="showAssignModal" :test="selectedTest" :questions="questions" @close="closeAssign" @submit="assignQuestions" />
    <SubmissionsModal v-if="showSubmissionsModal" :test="selectedTest" @close="closeSubmissions" />
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TestCreateModal from '@/components/TestCreateModal.vue'
import TestAssignQuestionsModal from '@/components/TestAssignQuestionsModal.vue'
import SubmissionsModal from '@/components/SubmissionsModal.vue'
import { useModal, useToast } from '@/composables/useNotification'

const { confirm } = useModal()
const toast = useToast()

const tests = ref([])
const questions = ref([]) // For stats
const assignments = ref(0) // Mock stats
const loading = ref(false)
const search = ref('')

const showCreateModal = ref(false)
const showAssignModal = ref(false)
const showSubmissionsModal = ref(false)
const selectedTest = ref(null)
const editingIndex = ref(-1)
const deletingId = ref(null)

const categories = ['Science', 'Math', 'IT', 'Geography', 'History', 'General Knowledge']

const filtered = computed(() => {
  if (!search.value) return tests.value
  const s = search.value.toLowerCase()
  return tests.value.filter(t => t.name.toLowerCase().includes(s))
})

const activeCount = computed(() => tests.value.filter(t => isActive(t)).length)
const upcoming = computed(() => {
  return tests.value
    .filter(t => new Date(t.schedule_at || t.start_time) > new Date())
    .sort((a,b) => new Date(a.schedule_at || a.start_time) - new Date(b.schedule_at || b.start_time))
    .slice(0, 5)
})

const isActive = (t) => {
  const now = new Date()
  const start = new Date(t.start_time || t.schedule_at)
  const end = new Date(t.end_time)
  return now >= start && now <= end
}

const formatDate = (d) => {
  if (!d) return '-'
  return new Date(d).toLocaleString('id-ID', { 
    month: 'short', day: 'numeric', year: 'numeric', 
    hour: '2-digit', minute: '2-digit',
    hour12: false
  })
}

const formatDateShort = (d) => {
  if (!d) return '-'
  return new Date(d).toLocaleDateString('id-ID', { month: 'short', day: 'numeric' })
}

const loadTests = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/tests')
    tests.value = data
    
    // Also load questions count for stats
    const qRes = await window.axios.get('/api/questions')
    questions.value = qRes.data.items || []
  } catch (e) {
    toast.error('Error', 'Failed to load tests')
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  selectedTest.value = null
  editingIndex.value = -1
  showCreateModal.value = true
}

const edit = (i) => {
  editingIndex.value = i
  // Map fields for modal
  const t = filtered.value[i]
  selectedTest.value = { ...t }
  showCreateModal.value = true
}

const createTest = async (formData) => {
  try {
    // Map back to API expected format
    const payload = {
      name: formData.name,
      description: formData.description,
      category: formData.category,
      duration: formData.duration,
      schedule_at: formData.scheduleAt,
      start_time: formData.startTime,
      end_time: formData.endTime
    }

    let savedTest = null;

    if (editingIndex.value > -1) {
      const id = filtered.value[editingIndex.value].id
      const { data } = await window.axios.put(`/api/tests/${id}`, payload)
      savedTest = data
      const idx = tests.value.findIndex(t => t.id === id)
      if (idx !== -1) tests.value[idx] = data
      toast.success('Success', 'Test updated')
    } else {
      const { data } = await window.axios.post('/api/tests', payload)
      savedTest = data
      tests.value.unshift(data)
      toast.success('Success', 'Test created')
      
      // Prompt to assign questions
      const assignNow = await confirm({
        title: 'Assign Questions?',
        message: 'Do you want to add questions to this test now?',
        confirmText: 'Yes, add questions',
        cancelText: 'Later'
      })
      
      if (assignNow) {
        selectedTest.value = savedTest
        showAssignModal.value = true
      }
    }
    if (!showAssignModal.value) {
      closeCreate()
    } else {
      // If showing assign modal, we still need to close create modal
      showCreateModal.value = false
      // But keep selectedTest for the assign modal
    }
  } catch (e) {
    console.error(e)
    toast.error('Error', 'Failed to save test')
  }
}

const remove = async (i) => {
  const t = filtered.value[i]
  const confirmed = await confirm({
    title: 'Delete Test',
    message: 'Are you sure? This will delete all associated data.',
    confirmText: 'Delete',
    type: 'danger'
  })
  
  if (confirmed) {
    deletingId.value = t.id
    try {
      await window.axios.delete(`/api/tests/${t.id}`)
      tests.value = tests.value.filter(item => item.id !== t.id)
      toast.success('Success', 'Test deleted')
    } catch (e) {
      toast.error('Error', 'Failed to delete test')
    } finally {
      deletingId.value = null
    }
  }
}

const closeCreate = () => {
  showCreateModal.value = false
  selectedTest.value = null
}

const viewSubmissions = (test) => {
  selectedTest.value = test
  showSubmissionsModal.value = true
}

const closeSubmissions = () => {
  showSubmissionsModal.value = false
  selectedTest.value = null
}

const closeAssign = () => {
  showAssignModal.value = false
  selectedTest.value = null
  loadTests() // Reload to get updated question counts
}

const assignQuestions = async (updatedTest) => {
  if (!updatedTest?.id) {
    toast.error('Error', 'Test ID is missing')
    return
  }
  
  try {
    const payload = {
      ...updatedTest,
      question_ids: updatedTest.questionIds,
      is_active: updatedTest.isActive
    }
    
    const { data } = await window.axios.put(`/api/tests/${updatedTest.id}`, payload)
    const idx = tests.value.findIndex(t => t.id === updatedTest.id)
    if (idx !== -1) tests.value[idx] = data
    
    toast.success('Success', 'Questions assigned')
    closeAssign()
  } catch (e) {
    toast.error('Error', 'Failed to assign questions')
  }
}

onMounted(() => {
  loadTests()
})
</script>

<style scoped>
</style>
