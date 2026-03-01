<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
    <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Assign Questions</h2>
          <p class="text-gray-500 mt-1">Pilih soal untuk test ini, lalu tentukan apakah test aktif dan terlihat oleh user.</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
            <select v-model="category" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-2.5 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
              <option v-for="c in categoryOptions" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
            <select v-model="difficulty" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-2.5 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
              <option value="">All</option>
              <option value="Easy">Easy</option>
              <option value="Medium">Medium</option>
              <option value="Hard">Hard</option>
            </select>
          </div>
          <div class="flex items-end pb-2">
            <div class="text-sm text-gray-500 font-medium">Showing <span class="text-gray-900">{{ filtered.length }}</span> items</div>
          </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-gray-50/50 p-4 max-h-[400px] overflow-y-auto custom-scrollbar">
          <div class="grid grid-cols-1 gap-3">
            <label v-for="q in filtered" :key="q.id" class="flex items-start gap-4 rounded-xl border border-gray-100 bg-white p-4 cursor-pointer hover:border-[#9DB359] hover:shadow-md transition-all group">
              <div class="pt-1">
                <input type="checkbox" :value="q.id" v-model="selected" class="w-5 h-5 rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359] transition-all" />
              </div>
              <div class="flex-1">
                <div class="font-medium text-gray-900 group-hover:text-[#9DB359] transition-colors">{{ q.question }}</div>
                <div class="flex items-center gap-2 mt-1">
                  <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ q.category }}</span>
                  <span class="px-2 py-0.5 rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ q.difficulty }}</span>
                </div>
              </div>
            </label>
            <div v-if="filtered.length === 0" class="text-center py-8 text-gray-500">
              No questions found matching your criteria
            </div>
          </div>
        </div>

        <div class="flex items-center justify-between border-t border-gray-100 pt-6">
          <div class="flex items-center gap-4">
            <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
              <input v-model="active" type="checkbox" class="w-4 h-4 rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359]" :disabled="selected.length===0" />
              <span>Active (visible to users)</span>
            </label>
            <span v-if="selected.length===0" class="text-xs text-red-500 font-medium">Add at least 1 question to activate test.</span>
          </div>

          <div class="flex items-center gap-3">
            <button class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="$emit('close')">Cancel</button>
            <button class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform active:scale-95 transition-all" @click="submit">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue'

const props = defineProps({
  test: { type: Object, required: true },
  questions: { type: Array, default: () => [] },
})
const emit = defineEmits(['close','submit'])

const selected = ref([])
const active = ref(false)
const category = ref('')
const difficulty = ref('')
const categoryOptions = computed(() => {
  const set = new Set(props.questions.map(q => q.category))
  const preselect = mapCategories(props.test?.category)
  preselect.forEach(c => set.add(c))
  return Array.from(set)
})
const filtered = computed(() => {
  const allow = mapCategories(category.value || props.test?.category)
  const seenIds = new Set()
  const uniqueQuestions = []
  for (const q of props.questions) {
    if (q?.id == null) continue
    if (seenIds.has(q.id)) continue
    seenIds.add(q.id)
    uniqueQuestions.push(q)
  }
  return uniqueQuestions.filter(q => 
    allow.includes(q.category) &&
    (!difficulty.value || q.difficulty === difficulty.value)
  )
})
function mapCategories(cat) {
  const m = {
    'Science': ['Science','Physics','Chemistry'],
    'Math': ['Math','Mathematics'],
    'IT': ['IT','General Knowledge'],
    'Geography': ['Geography'],
    'History': ['History'],
  }
  if (!cat) return []
  return m[cat] || [cat]
}

watch(() => props.test, (t) => {
  const ids = Array.isArray(t?.questionIds) ? t.questionIds : (Array.isArray(t?.question_ids) ? t.question_ids : [])
  selected.value = Array.from(new Set(ids))
  active.value = !!t?.isActive
  category.value = t?.category || ''
  difficulty.value = ''
}, { immediate: true })

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
