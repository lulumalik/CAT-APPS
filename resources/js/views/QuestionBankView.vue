<template>
  <main class="container-main py-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-semibold">Question Bank</h1>
        <p class="text-muted">Manage your test questions library</p>
      </div>
      <button class="px-4 py-2 rounded-md bg-navy text-white cursor-pointer" @click="openAdd">Add Question</button>
    </div>

    <!-- Skeleton Loader -->
    <div v-if="loading" class="mt-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="n in 4" :key="n" class="ui-card flex flex-col items-center justify-center animate-pulse">
          <div class="h-8 w-16 bg-gray-200 rounded mb-2"></div>
          <div class="h-4 w-24 bg-gray-200 rounded"></div>
        </div>
      </div>
      <div class="mt-6 ui-card animate-pulse">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="h-10 bg-gray-200 rounded"></div>
          <div class="h-10 bg-gray-200 rounded"></div>
          <div class="h-10 bg-gray-200 rounded"></div>
        </div>
      </div>
      <div class="mt-6 space-y-6">
        <div v-for="n in 3" :key="n" class="ui-card animate-pulse">
          <div class="flex justify-between mb-4">
            <div class="flex gap-2">
              <div class="h-6 w-20 bg-gray-200 rounded-full"></div>
              <div class="h-6 w-16 bg-gray-200 rounded-full"></div>
            </div>
            <div class="flex gap-2">
              <div class="h-8 w-16 bg-gray-200 rounded"></div>
              <div class="h-8 w-16 bg-gray-200 rounded"></div>
            </div>
          </div>
          <div class="h-6 w-3/4 bg-gray-200 rounded mb-4"></div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="h-12 bg-gray-200 rounded-xl"></div>
            <div class="h-12 bg-gray-200 rounded-xl"></div>
            <div class="h-12 bg-gray-200 rounded-xl"></div>
            <div class="h-12 bg-gray-200 rounded-xl"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ total }}</div><div class="text-muted">Total Questions</div></div>
        <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ easy }}</div><div class="text-muted">Easy Questions</div></div>
        <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ medium }}</div><div class="text-muted">Medium Questions</div></div>
        <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ hard }}</div><div class="text-muted">Hard Questions</div></div>
      </div>

      <div class="ui-card mt-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <input v-model="search" type="text" placeholder="Search questions..." class="rounded-md border border-gray-200 bg-white px-3 py-2" />
          <select v-model="filterCategory" class="rounded-md border border-gray-200 bg-white px-3 py-2">
            <option value="">All Categories</option>
            <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
          </select>
          <select v-model="filterDifficulty" class="rounded-md border border-gray-200 bg-white px-3 py-2">
            <option value="">All Difficulties</option>
            <option value="Easy">Easy</option>
            <option value="Medium">Medium</option>
            <option value="Hard">Hard</option>
          </select>
        </div>
      </div>

      <div class="mt-6 space-y-6">
        <div v-for="(q,i) in filtered" :key="i" class="ui-card">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 rounded-full text-sm bg-gray-100">{{ q.category }}</span>
              <span class="px-3 py-1 rounded-full text-sm" :class="diffBadge(q.difficulty)">{{ q.difficulty }}</span>
            </div>
            <div class="flex items-center gap-2">
              <button class="px-3 py-1 rounded-md border border-gray-200 cursor-pointer" @click="edit(i)">Edit</button>
              <button class="px-3 py-1 rounded-md border border-red-200 text-red-600 cursor-pointer" @click="remove(i)">Delete</button>
            </div>
          </div>
          <h2 class="mt-3 text-xl font-medium">{{ q.question }}</h2>
          <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="opt in q.options" :key="opt.key" class="rounded-xl border px-4 py-3" :class="opt.key===q.correct ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50'">
              <span class="font-medium mr-2">{{ opt.key }}.</span> {{ opt.label }}
              <span v-if="opt.key===q.correct" class="ml-2 px-2 py-0.5 rounded-full bg-green-200 text-xs">Correct</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <QuestionModal v-if="showModal" :initial="editingItem" @close="closeModal" @submit="onSubmit" />
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import QuestionModal from '@/components/QuestionModal.vue'
import { useModal, useToast } from '@/composables/useNotification'

const { confirm } = useModal()
const toast = useToast()

const categories = ['Geography','Math','Science','History','IT']
const items = ref([])
const loading = ref(false)
const stats = ref({ total: 0, easy: 0, medium: 0, hard: 0 })
const load = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/questions', { params: {
      search: search.value, category: filterCategory.value, difficulty: filterDifficulty.value
    }})
    items.value = data.items
    stats.value = data.stats
  } catch (error) {
    toast.error('Error', 'Failed to load questions. Please refresh the page.')
  } finally {
    loading.value = false
  }
}

const total = computed(() => stats.value.total)
const easy = computed(() => stats.value.easy)
const medium = computed(() => stats.value.medium)
const hard = computed(() => stats.value.hard)

const search = ref('')
const filterCategory = ref('')
const filterDifficulty = ref('')
const filtered = computed(() => items.value)

const diffBadge = (d) => ({
  'Easy': 'bg-green-100',
  'Medium': 'bg-orange-100',
  'Hard': 'bg-red-100',
}[d] || 'bg-gray-100')

const showModal = ref(false)
const editingIndex = ref(null)
const editingItem = computed(() => editingIndex.value!==null ? items.value[editingIndex.value] : null)
const openAdd = () => { editingIndex.value = null; showModal.value = true }
const edit = (i) => { editingIndex.value = i; showModal.value = true }
const closeModal = () => { showModal.value = false }
const onSubmit = async (payload) => {
  try {
    if (editingIndex.value!==null) {
      const id = items.value[editingIndex.value].id
      await window.axios.put(`/api/questions/${id}`, payload)
      toast.success('Question Updated', 'The question has been updated successfully.')
    } else {
      await window.axios.post('/api/questions', payload)
      toast.success('Question Added', 'New question has been added to the bank.')
    }
    showModal.value = false
    await load()
  } catch (error) {
    toast.error('Error', 'Failed to save question. Please try again.')
  }
}
const remove = async (i) => {
  const questionText = items.value[i].question
  const confirmed = await confirm(
    'Delete Question',
    `Are you sure you want to delete this question? This action cannot be undone.`,
    'Delete',
    'Cancel'
  )
  
  if (confirmed) {
    try {
      const id = items.value[i].id
      await window.axios.delete(`/api/questions/${id}`)
      toast.success('Question Deleted', 'The question has been removed from the bank.')
      await load()
    } catch (error) {
      toast.error('Error', 'Failed to delete question. Please try again.')
    }
  }
}

onMounted(load)
</script>

<style scoped>
</style>