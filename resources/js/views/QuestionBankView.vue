<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      :title="t('questionBank.title')"
      :subtitle="t('questionBank.subtitle')"
      theme="green"
      :icon="LibraryBig"
    >
      <template #actions>
        <button class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2" @click="openAdd">
          <Plus class="h-[18px] w-[18px]" />
          {{ t('questionBank.addQuestion') }}
        </button>
      </template>
    </PageHeroHeader>

    <!-- Skeleton Loader -->
    <div v-if="loading" class="mt-6">
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
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
          <div class="grid grid-cols-2 gap-3 md:gap-4">
            <div class="h-12 bg-gray-100 rounded-xl"></div>
            <div class="h-12 bg-gray-100 rounded-xl"></div>
            <div class="h-12 bg-gray-100 rounded-xl"></div>
            <div class="h-12 bg-gray-100 rounded-xl"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else>
      <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-[#9DB359]/30 transition-colors">
          <div class="text-4xl font-bold text-[#1A1A1A] mb-1 group-hover:text-[#9DB359] transition-colors">{{ total }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ t('questionBank.totalQuestions') }}</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-green-500/30 transition-colors">
          <div class="text-4xl font-bold text-green-600 mb-1">{{ easy }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ t('questionBank.easyQuestions') }}</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-yellow-500/30 transition-colors">
          <div class="text-4xl font-bold text-yellow-500 mb-1">{{ medium }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ t('questionBank.mediumQuestions') }}</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-red-500/30 transition-colors">
          <div class="text-4xl font-bold text-red-500 mb-1">{{ hard }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ t('questionBank.hardQuestions') }}</div>
        </div>
      </div>

      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="relative">
            <input v-model="search" type="text" :placeholder="t('questionBank.searchPlaceholder')" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 pl-10 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors" />
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </div>
          <select v-model="filterTrackId" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors appearance-none">
            <option value="">Semua track ujian</option>
            <optgroup v-for="cat in examCategories" :key="cat.id" :label="cat.name">
              <option v-for="tr in cat.tracks" :key="tr.id" :value="String(tr.id)">{{ tr.name }}</option>
            </optgroup>
          </select>
          <select v-model="filterDifficulty" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors appearance-none">
            <option value="">{{ t('questionBank.allDifficulties') }}</option>
            <option value="Easy">{{ t('modals.question.difficultyEasy') }}</option>
            <option value="Medium">{{ t('modals.question.difficultyMedium') }}</option>
            <option value="Hard">{{ t('modals.question.difficultyHard') }}</option>
          </select>
        </div>
      </div>

      <div class="mt-8 space-y-8">
        <template v-if="isMentor">
          <section v-if="ownFiltered.length" class="space-y-4">
            <div class="flex items-center justify-between gap-3">
              <h2 class="text-lg font-semibold text-[#1A1A1A]">{{ t('questionBank.myQuestionsSection') }}</h2>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#9DB359]/15 text-[#6c7c3f] border border-[#9DB359]/30">
                {{ ownFiltered.length }}
              </span>
            </div>
            <div class="grid grid-cols-1 gap-4">
              <article
                v-for="q in ownFiltered"
                :key="q.id"
                class="rounded-[2rem] shadow-sm border-2 border-[#9DB359]/40 bg-gradient-to-br from-[#9DB359]/10 via-white to-white p-5 md:p-8 hover:shadow-md transition-shadow group"
              >
                <QuestionCardBody
                  :question="q"
                  :can-manage="true"
                  highlighted
                  @edit="edit(q)"
                  @remove="remove(q)"
                />
              </article>
            </div>
          </section>

          <section v-if="globalFiltered.length" class="space-y-4">
            <div class="flex items-center justify-between gap-3">
              <h2 class="text-lg font-semibold text-[#1A1A1A]">{{ t('questionBank.globalQuestionsSection') }}</h2>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                {{ globalFiltered.length }}
              </span>
            </div>
            <div class="grid grid-cols-1 gap-4">
              <article
                v-for="q in globalFiltered"
                :key="q.id"
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-5 md:p-8 hover:shadow-md transition-shadow group"
              >
                <QuestionCardBody
                  :question="q"
                  :can-manage="false"
                  @edit="edit(q)"
                  @remove="remove(q)"
                />
              </article>
            </div>
          </section>

          <p v-if="!ownFiltered.length && !globalFiltered.length" class="text-center text-gray-500 py-8">
            {{ t('questionBank.noResultsFound') }}
          </p>
        </template>

        <template v-else>
          <div class="grid grid-cols-1 gap-4">
            <article
              v-for="q in filtered"
              :key="q.id"
              class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-5 md:p-8 hover:shadow-md transition-shadow group"
            >
              <QuestionCardBody
                :question="q"
                :can-manage="canManageQuestion(q)"
                @edit="edit(q)"
                @remove="remove(q)"
              />
            </article>
          </div>
        </template>
      </div>
    </div>

    <QuestionModal v-if="showModal" :initial="editingItem" @close="closeModal" @submit="onSubmit" />
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { LibraryBig, Plus } from 'lucide-vue-next'
import QuestionModal from '@/components/QuestionModal.vue'
import QuestionCardBody from '@/components/QuestionCardBody.vue'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useModal, useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

import { useAppStore } from '@/stores/app'

const { confirm } = useModal()
const toast = useToast()
const { t } = useI18n()
const store = useAppStore()

const questions = ref([])
const examCategories = ref([])
const loading = ref(false)
const search = ref('')
const filterTrackId = ref('')
const filterDifficulty = ref('')
const showModal = ref(false)
const editingItem = ref(null)

const isMentor = computed(() => store.role === 'mentor')

const matchesFilters = (q) => {
  const s = search.value.toLowerCase()
  const matchSearch = q.question.toLowerCase().includes(s)
  const matchTrack = !filterTrackId.value || String(q.exam_track_id) === String(filterTrackId.value)
  const matchDiff = !filterDifficulty.value || q.difficulty === filterDifficulty.value
  return matchSearch && matchTrack && matchDiff
}

const isOwnQuestion = (q) => Number(q?.created_by) === Number(store.user?.id)

const filtered = computed(() => {
  if (!questions.value || !Array.isArray(questions.value)) return []
  return questions.value.filter(matchesFilters)
})

const ownFiltered = computed(() => filtered.value.filter(isOwnQuestion))
const globalFiltered = computed(() => filtered.value.filter((q) => !isOwnQuestion(q)))

const total = computed(() => questions.value?.length || 0)
const easy = computed(() => questions.value?.filter(q => q.difficulty === 'Easy').length || 0)
const medium = computed(() => questions.value?.filter(q => q.difficulty === 'Medium').length || 0)
const hard = computed(() => questions.value?.filter(q => q.difficulty === 'Hard').length || 0)

const canManageQuestion = (question) => {
  if (store.role === 'admin') return true
  if (store.role !== 'mentor') return false
  return isOwnQuestion(question)
}

const sortQuestionsForDisplay = (items) => {
  if (!isMentor.value) return items
  return [...items].sort((a, b) => {
    const aOwn = isOwnQuestion(a) ? 0 : 1
    const bOwn = isOwnQuestion(b) ? 0 : 1
    if (aOwn !== bOwn) return aOwn - bOwn
    return Number(b.id) - Number(a.id)
  })
}

const loadQuestions = async () => {
  loading.value = true
  try {
    const [qRes, catRes] = await Promise.all([
      window.axios.get('/api/questions'),
      window.axios.get('/api/exam-categories'),
    ])
    questions.value = sortQuestionsForDisplay((qRes.data.items || []).map(normalizeQuestion))
    examCategories.value = catRes.data.items || catRes.data || []
  } catch (e) {
    toast.error('Error', t('questionBank.toastLoadFailed'))
  } finally {
    loading.value = false
  }
}

const openAdd = () => {
  editingItem.value = null
  showModal.value = true
}

const edit = (question) => {
  editingItem.value = { ...question }
  showModal.value = true
}

const remove = async (question) => {
  const confirmed = await confirm({
    title: t('questionBank.deleteConfirmTitle'),
    message: t('questionBank.deleteConfirmMessage'),
    confirmText: t('common.delete'),
    type: 'danger'
  })
  
  if (confirmed) {
    try {
      await window.axios.delete(`/api/questions/${question.id}`)
      questions.value = questions.value.filter(q => q.id !== question.id)
      toast.success('Success', t('questionBank.toastDeleted'))
    } catch (e) {
      toast.error('Error', t('questionBank.toastDeleteFailed'))
    }
  }
}

const closeModal = () => {
  showModal.value = false
  editingItem.value = null
}

const normalizeQuestion = (item) => ({
  ...item,
  image_url: item?.image_url || item?.image || null,
})

const toQuestionFormData = (payload) => {
  if (!(payload.image instanceof File)) return payload

  const fd = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value == null || value === '') return
    if (key === 'options') {
      value.forEach((opt, idx) => {
        fd.append(`options[${idx}][key]`, opt.key)
        fd.append(`options[${idx}][label]`, opt.label)
      })
      return
    }
    fd.append(key, value)
  })
  return fd
}

const onSubmit = async (payload) => {
  try {
    const body = toQuestionFormData(payload)
    const multipart = body instanceof FormData
    const config = multipart ? { headers: { 'Content-Type': 'multipart/form-data' } } : undefined

    if (editingItem.value) {
      const { data } = await window.axios.put(`/api/questions/${editingItem.value.id}`, body, config)
      const idx = questions.value.findIndex(q => q.id === editingItem.value.id)
      if (idx !== -1) questions.value[idx] = normalizeQuestion(data)
      questions.value = sortQuestionsForDisplay(questions.value)
      toast.success('Success', t('questionBank.toastUpdated'))
    } else {
      const { data } = await window.axios.post('/api/questions', body, config)
      questions.value = sortQuestionsForDisplay([normalizeQuestion(data), ...questions.value])
      toast.success('Success', t('questionBank.toastCreated'))
    }
    closeModal()
  } catch (e) {
    toast.error('Error', t('questionBank.toastSaveFailed'))
  }
}

onMounted(() => {
  loadQuestions()
})
</script>

<style scoped>
</style>
