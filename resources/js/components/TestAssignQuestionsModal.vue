<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
    <div class="relative flex w-full max-w-5xl max-h-[90vh] flex-col overflow-hidden rounded-[2rem] bg-white shadow-2xl shadow-black/10 border border-gray-100">
      <div class="shrink-0 p-6 pb-4 border-b border-gray-100">
        <div class="flex items-center justify-between gap-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ t('modals.testAssign.title') }}</h2>
            <p class="text-gray-500 mt-1">{{ t('modals.testAssign.subtitle') }}</p>
          </div>
          <div class="flex items-center gap-2 shrink-0">
            <button
              type="button"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-100 transition-colors disabled:opacity-50"
              :disabled="refreshing"
              :title="t('modals.testAssign.refreshHint')"
              @click="$emit('refresh')"
            >
              <svg class="w-4 h-4" :class="{ 'animate-spin': refreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
              {{ refreshing ? t('modals.testAssign.refreshing') : t('modals.testAssign.refresh') }}
            </button>
            <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
          <div class="flex gap-2">
            <button
              type="button"
              class="px-4 py-2 rounded-full text-sm font-semibold transition-colors"
              :class="activeTab === 'browse' ? 'bg-[#1A1A1A] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
              @click="activeTab = 'browse'"
            >
              {{ t('modals.testAssign.tabBrowse') }}
            </button>
            <button
              type="button"
              class="px-4 py-2 rounded-full text-sm font-semibold transition-colors"
              :class="activeTab === 'selected' ? 'bg-[#9DB359] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
              @click="activeTab = 'selected'"
            >
              {{ t('modals.testAssign.tabSelected', { count: selected.length }) }}
            </button>
          </div>
          <div v-if="activeTab === 'browse' && filtered.length > 0" class="flex items-center gap-2">
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-200 transition-colors"
              @click="selectAllFiltered"
            >
              + Pilih Semua ({{ filtered.length }} Soal)
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors"
              @click="deselectAllFiltered"
            >
              Hapus Pilihan Filter
            </button>
          </div>
        </div>
      </div>

      <div class="flex-1 min-h-0 overflow-y-auto px-6 py-4 custom-scrollbar">
        <template v-if="activeTab === 'browse'">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Batch / Paket Tryout</label>
              <select v-model="batch" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-2.5 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
                <option value="">-- Semua Batch --</option>
                <option v-for="b in batchOptions" :key="b" :value="b">{{ b }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testAssign.categoryLabel') }}</label>
              <select v-model="category" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-2.5 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
                <option value="">{{ t('modals.testAssign.categoryAll') }}</option>
                <option v-for="c in categoryOptions" :key="c" :value="c">{{ c }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testAssign.difficultyLabel') }}</label>
              <select v-model="difficulty" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-2.5 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
                <option value="">{{ t('modals.testAssign.difficultyAll') }}</option>
                <option value="Easy">Easy</option>
                <option value="Medium">Medium</option>
                <option value="Hard">Hard</option>
              </select>
            </div>
          </div>

          <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4">
            <div class="grid grid-cols-1 gap-3">
              <label v-for="q in filtered" :key="q.id" class="flex items-start gap-4 rounded-xl border border-gray-100 bg-white p-4 cursor-pointer hover:border-[#9DB359] hover:shadow-md transition-all group">
                <div class="pt-1">
                  <input type="checkbox" :value="q.id" v-model="selected" class="w-5 h-5 rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359] transition-all" />
                </div>
                <div class="flex-1">
                  <div v-if="q.question" class="font-medium text-gray-900 group-hover:text-[#9DB359] transition-colors mb-1.5">{{ q.question }}</div>
                  
                  <div v-if="q.image || q.image_url" class="my-1.5 inline-block">
                    <div
                      class="relative group/img inline-flex items-center gap-2 p-1.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-[#9DB359] transition-all cursor-pointer"
                      @click.stop.prevent="zoomImageUrl = q.image || q.image_url"
                      title="Klik untuk memperbesar gambar"
                    >
                      <img
                        :src="q.image || q.image_url"
                        alt="Soal Gambar"
                        class="h-16 w-auto max-w-[180px] object-cover rounded-lg shadow-sm group-hover/img:scale-105 transition-transform"
                      />
                      <div class="flex items-center gap-1 text-xs font-semibold text-gray-600 group-hover/img:text-[#9DB359] pr-2">
                        <Maximize2 class="w-3.5 h-3.5" />
                        <span>Perbesar</span>
                      </div>
                    </div>
                  </div>
                  <div v-else-if="!q.question" class="font-medium text-gray-400 italic">
                    [Soal Tanpa Teks]
                  </div>

                  <div class="flex items-center gap-2 mt-1">
                    <span v-if="q.batch" class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold border border-purple-200">{{ q.batch }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ q.category }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ q.difficulty }}</span>
                  </div>
                </div>
              </label>
              <div v-if="filtered.length === 0" class="text-center py-8 text-gray-500">
                {{ t('modals.testAssign.noneFound') }}
              </div>
            </div>
          </div>
        </template>

        <template v-else>
          <p class="text-sm text-gray-500 mb-4">{{ t('modals.testAssign.selectedHint', { count: selected.length }) }}</p>
          <div class="rounded-2xl border border-[#9DB359]/30 bg-[#9DB359]/5 p-4">
            <div class="grid grid-cols-1 gap-3">
              <label v-for="q in selectedQuestions" :key="q.id" class="flex items-start gap-4 rounded-xl border border-gray-100 bg-white p-4 cursor-pointer hover:border-red-200 transition-all group">
                <div class="pt-1">
                  <input type="checkbox" :value="q.id" v-model="selected" class="w-5 h-5 rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359] transition-all" />
                </div>
                <div class="flex-1">
                  <div v-if="q.question" class="font-medium text-gray-900 mb-1.5">{{ q.question }}</div>
                  
                  <div v-if="q.image || q.image_url" class="my-1.5 inline-block">
                    <div
                      class="relative group/img inline-flex items-center gap-2 p-1.5 rounded-xl border border-gray-200 bg-gray-50 hover:bg-gray-100 hover:border-[#9DB359] transition-all cursor-pointer"
                      @click.stop.prevent="zoomImageUrl = q.image || q.image_url"
                      title="Klik untuk memperbesar gambar"
                    >
                      <img
                        :src="q.image || q.image_url"
                        alt="Soal Gambar"
                        class="h-16 w-auto max-w-[180px] object-cover rounded-lg shadow-sm group-hover/img:scale-105 transition-transform"
                      />
                      <div class="flex items-center gap-1 text-xs font-semibold text-gray-600 group-hover/img:text-[#9DB359] pr-2">
                        <Maximize2 class="w-3.5 h-3.5" />
                        <span>Perbesar</span>
                      </div>
                    </div>
                  </div>
                  <div v-else-if="!q.question" class="font-medium text-gray-400 italic">
                    [Soal Tanpa Teks]
                  </div>

                  <div class="flex items-center gap-2 mt-1">
                    <span v-if="q.batch" class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 text-xs font-semibold border border-purple-200">{{ q.batch }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ q.category }}</span>
                    <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ q.difficulty }}</span>
                  </div>
                </div>
              </label>
              <div v-if="selectedQuestions.length === 0" class="text-center py-8 text-gray-500">
                {{ t('modals.testAssign.noneSelected') }}
              </div>
            </div>
          </div>
        </template>
      </div>

      <div class="shrink-0 border-t border-gray-100 bg-white px-6 py-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4">
          <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
            <input v-model="active" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359]" :disabled="selected.length===0" />
            <span>{{ t('modals.testAssign.activeVisible') }}</span>
          </label>
          <span v-if="selected.length===0" class="text-xs text-red-500 font-medium">{{ t('modals.testAssign.needOne') }}</span>
          <span v-else class="text-xs text-gray-500 font-medium">{{ t('modals.testAssign.selectedCount', { count: selected.length }) }}</span>
        </div>

        <div class="flex items-center gap-3">
          <button class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="$emit('close')">{{ t('modals.testAssign.cancel') }}</button>
          <button class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform active:scale-95 transition-all" @click="submit">{{ t('modals.testAssign.save') }}</button>
        </div>
      </div>
    </div>

    <ImageZoomModal :image-url="zoomImageUrl" @close="zoomImageUrl = ''" />
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { Maximize2 } from 'lucide-vue-next'
import ImageZoomModal from '@/components/ImageZoomModal.vue'
import { useI18n } from '@/composables/useI18n'

const props = defineProps({
  test: { type: Object, required: true },
  questions: { type: Array, default: () => [] },
  refreshing: { type: Boolean, default: false },
})
const emit = defineEmits(['close', 'submit', 'refresh'])
const { t } = useI18n()

const selected = ref([])
const active = ref(true)
const batch = ref('')
const category = ref('')
const difficulty = ref('')
const activeTab = ref('browse')
const zoomImageUrl = ref('')

const uniqueQuestions = computed(() => {
  const seenIds = new Set()
  const items = []
  for (const q of props.questions) {
    if (q?.id == null || seenIds.has(q.id)) continue
    seenIds.add(q.id)
    items.push(q)
  }
  return items
})

const batchOptions = computed(() => {
  const set = new Set(uniqueQuestions.value.map(q => q.batch || 'Tryout 1').filter(Boolean))
  return Array.from(set).sort()
})

const categoryOptions = computed(() => {
  const set = new Set(uniqueQuestions.value.map(q => q.category).filter(Boolean))
  return Array.from(set).sort()
})

const selectedQuestions = computed(() =>
  uniqueQuestions.value.filter(q => selected.value.includes(q.id))
)

const filtered = computed(() => {
  const allowAll = !category.value
  const allow = allowAll ? null : mapCategories(category.value)

  return uniqueQuestions.value.filter(q => {
    if (batch.value && (q.batch || 'Tryout 1') !== batch.value) return false
    if (!allowAll && allow && !allow.includes(q.category)) return false
    if (difficulty.value && q.difficulty !== difficulty.value) return false
    return true
  })
})

const selectAllFiltered = () => {
  const filteredIds = filtered.value.map(q => q.id)
  selected.value = Array.from(new Set([...selected.value, ...filteredIds]))
}

const deselectAllFiltered = () => {
  const filteredIdsSet = new Set(filtered.value.map(q => q.id))
  selected.value = selected.value.filter(id => !filteredIdsSet.has(id))
}

function mapCategories(cat) {
  const m = {
    'Kewarganegaraan': ['Kewarganegaraan', 'Citizenship', 'Law', 'Hukum'],
    'Math': ['Math', 'Mathematics', 'Matematika'],
    'English': ['English', 'Bahasa Inggris'],
    'Interpersonal Skill': ['Interpersonal Skill', 'Interpersonal'],
  }
  if (!cat) return null
  return m[cat] || [cat]
}

watch(() => props.test, (t) => {
  const ids = Array.isArray(t?.questionIds) ? t.questionIds : (Array.isArray(t?.question_ids) ? t.question_ids : [])
  selected.value = Array.from(new Set(ids))
  active.value = ids.length === 0 ? true : !!(t?.isActive ?? t?.is_active ?? true)
  batch.value = ''
  category.value = ''
  difficulty.value = ''
  activeTab.value = 'browse'
}, { immediate: true })

watch(() => props.questions, () => {
  const ids = Array.isArray(props.test?.questionIds)
    ? props.test.questionIds
    : (Array.isArray(props.test?.question_ids) ? props.test.question_ids : [])
  selected.value = Array.from(new Set(ids))
}, { deep: true })

const submit = () => {
  emit('submit', {
    ...props.test,
    questionIds: Array.from(new Set(selected.value)),
    isActive: selected.value.length > 0 ? !!active.value : false,
  })
}
</script>

<style scoped>
</style>
