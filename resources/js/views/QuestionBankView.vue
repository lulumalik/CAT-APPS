<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-[#1A1A1A]">Question Bank</h1>
        <p class="text-gray-500 mt-1">Manage your test questions library</p>
      </div>
      <button class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2" @click="openAdd">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
        Add Question
      </button>
    </div>

    <!-- Skeleton Loader -->
    <div v-if="loading" class="mt-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="n in 4" :key="n" class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm animate-pulse">
          <div class="h-8 w-16 bg-gray-100 rounded mb-2 mx-auto"></div>
          <div class="h-4 w-24 bg-gray-100 rounded mx-auto"></div>
        </div>
      </div>
      <div class="mt-6 bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm animate-pulse">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="h-12 bg-gray-100 rounded-xl"></div>
          <div class="h-12 bg-gray-100 rounded-xl"></div>
          <div class="h-12 bg-gray-100 rounded-xl"></div>
        </div>
      </div>
      <div class="mt-6 space-y-4">
        <div v-for="n in 3" :key="n" class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm animate-pulse">
          <div class="flex justify-between mb-4">
            <div class="flex gap-2">
              <div class="h-6 w-20 bg-gray-100 rounded-full"></div>
              <div class="h-6 w-16 bg-gray-100 rounded-full"></div>
            </div>
            <div class="flex gap-2">
              <div class="h-8 w-16 bg-gray-100 rounded"></div>
              <div class="h-8 w-16 bg-gray-100 rounded"></div>
            </div>
          </div>
          <div class="h-6 w-3/4 bg-gray-100 rounded mb-4"></div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="h-12 bg-gray-100 rounded-xl"></div>
            <div class="h-12 bg-gray-100 rounded-xl"></div>
            <div class="h-12 bg-gray-100 rounded-xl"></div>
            <div class="h-12 bg-gray-100 rounded-xl"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-[#9DB359]/30 transition-colors">
          <div class="text-4xl font-bold text-[#1A1A1A] mb-1 group-hover:text-[#9DB359] transition-colors">{{ total }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Questions</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-green-500/30 transition-colors">
          <div class="text-4xl font-bold text-green-600 mb-1">{{ easy }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Easy Questions</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-yellow-500/30 transition-colors">
          <div class="text-4xl font-bold text-yellow-500 mb-1">{{ medium }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Medium Questions</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-red-500/30 transition-colors">
          <div class="text-4xl font-bold text-red-500 mb-1">{{ hard }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">Hard Questions</div>
        </div>
      </div>

      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="relative">
            <input v-model="search" type="text" placeholder="Search questions..." class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 pl-10 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors" />
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </div>
          <select v-model="filterCategory" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors appearance-none">
            <option value="">All Categories</option>
            <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
          </select>
          <select v-model="filterDifficulty" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors appearance-none">
            <option value="">All Difficulties</option>
            <option value="Easy">Easy</option>
            <option value="Medium">Medium</option>
            <option value="Hard">Hard</option>
          </select>
        </div>
      </div>

      <div class="mt-8 space-y-6">
        <div v-for="(q,i) in filtered" :key="i" class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-md transition-shadow group">
          <div class="flex items-start justify-between mb-4">
            <div class="flex items-center gap-2">
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 capitalize">{{ q.category }}</span>
              <span class="px-3 py-1 rounded-full text-xs font-medium border" :class="diffBadge(q.difficulty)">{{ q.difficulty }}</span>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100 capitalize">{{ q.type }}</span>
            </div>
            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
              <button class="px-4 py-1.5 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-50 transition-colors" @click="edit(i)">Edit</button>
              <button class="px-4 py-1.5 rounded-full border border-red-100 text-red-600 text-sm font-medium hover:bg-red-50 transition-colors" @click="remove(i)">Delete</button>
            </div>
          </div>
          
          <div class="flex gap-6">
            <div v-if="q.image_url" class="flex-shrink-0">
               <img :src="q.image_url" alt="Question Image" class="w-32 h-32 object-cover rounded-xl border border-gray-200" />
            </div>
            <div class="flex-grow">
              <h2 class="text-xl font-medium text-[#1A1A1A] leading-relaxed">{{ q.question }}</h2>
              
              <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-for="opt in q.options" :key="opt.key" class="rounded-xl border px-5 py-3 flex items-center justify-between transition-colors" :class="opt.key===q.correct ? 'border-[#9DB359] bg-[#9DB359]/5' : 'border-gray-200 bg-white'">
                  <div class="flex items-center">
                    <span class="w-6 h-6 rounded-full border flex items-center justify-center text-xs font-medium mr-3" :class="opt.key===q.correct ? 'border-[#9DB359] text-[#9DB359] bg-white' : 'border-gray-300 text-gray-500'">{{ opt.key }}</span>
                    <span :class="opt.key===q.correct ? 'text-[#1A1A1A] font-medium' : 'text-gray-600'">{{ opt.label }}</span>
                  </div>
                  <span v-if="opt.key===q.correct" class="px-2 py-0.5 rounded-full bg-[#9DB359] text-white text-[10px] font-bold uppercase tracking-wider">Correct</span>
                </div>
              </div>
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

const questions = ref([])
const loading = ref(false)
const search = ref('')
const filterCategory = ref('')
const filterDifficulty = ref('')
const showModal = ref(false)
const editingItem = ref(null)

const categories = computed(() => {
  if (!questions.value || !Array.isArray(questions.value)) return []
  return [...new Set(questions.value.map(q => q.category))]
})

const filtered = computed(() => {
  if (!questions.value || !Array.isArray(questions.value)) return []
  return questions.value.filter(q => {
    const s = search.value.toLowerCase()
    const matchSearch = q.question.toLowerCase().includes(s)
    const matchCat = !filterCategory.value || q.category === filterCategory.value
    const matchDiff = !filterDifficulty.value || q.difficulty === filterDifficulty.value
    return matchSearch && matchCat && matchDiff
  })
})

const total = computed(() => questions.value?.length || 0)
const easy = computed(() => questions.value?.filter(q => q.difficulty === 'Easy').length || 0)
const medium = computed(() => questions.value?.filter(q => q.difficulty === 'Medium').length || 0)
const hard = computed(() => questions.value?.filter(q => q.difficulty === 'Hard').length || 0)

const diffBadge = (d) => {
  if (d === 'Easy') return 'bg-green-50 text-green-700 border-green-100'
  if (d === 'Medium') return 'bg-yellow-50 text-yellow-700 border-yellow-100'
  return 'bg-red-50 text-red-700 border-red-100'
}

const loadQuestions = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/questions')
    questions.value = data.items
  } catch (e) {
    toast.error('Error', 'Failed to load questions')
  } finally {
    loading.value = false
  }
}

const openAdd = () => {
  editingItem.value = null
  showModal.value = true
}

const edit = (i) => {
  // Use actual question object from filtered list
  const questionToEdit = filtered.value[i]
  editingItem.value = { ...questionToEdit }
  showModal.value = true
}

const remove = async (i) => {
  const confirmed = await confirm({
    title: 'Delete Question',
    message: 'Are you sure you want to delete this question?',
    confirmText: 'Delete',
    type: 'danger'
  })
  
  if (confirmed) {
    try {
      const questionToDelete = filtered.value[i]
      await window.axios.delete(`/api/questions/${questionToDelete.id}`)
      questions.value = questions.value.filter(q => q.id !== questionToDelete.id)
      toast.success('Success', 'Question deleted')
    } catch (e) {
      toast.error('Error', 'Failed to delete question')
    }
  }
}

const closeModal = () => {
  showModal.value = false
  editingItem.value = null
}

const onSubmit = async (formData) => {
  try {
    if (editingItem.value) {
      const { data } = await window.axios.post(`/api/questions/${editingItem.value.id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      const idx = questions.value.findIndex(q => q.id === editingItem.value.id)
      if (idx !== -1) questions.value[idx] = data.data
      toast.success('Success', 'Question updated')
    } else {
      const { data } = await window.axios.post('/api/questions', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      questions.value.unshift(data.data)
      toast.success('Success', 'Question created')
    }
    closeModal()
  } catch (e) {
    toast.error('Error', 'Failed to save question')
  }
}

onMounted(() => {
  loadQuestions()
})
</script>

<style scoped>
</style>
