<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30" @click="$emit('close')"></div>
    <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Assign Questions to Test</h2>
        <button class="px-3 py-1 rounded-md" @click="$emit('close')">✕</button>
      </div>
      <p class="text-muted">Pilih soal untuk test ini, lalu tentukan apakah test aktif dan terlihat oleh user.</p>

      <div class="mt-4 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
          <div>
            <label class="text-sm">Category</label>
            <select v-model="category" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2">
              <option v-for="c in categoryOptions" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="text-sm">Difficulty</label>
            <select v-model="difficulty" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2">
              <option value="">All</option>
              <option value="Easy">Easy</option>
              <option value="Medium">Medium</option>
              <option value="Hard">Hard</option>
            </select>
          </div>
          <div class="flex items-end">
            <div class="text-sm text-muted">Showing {{ filtered.length }} items</div>
          </div>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label v-for="q in filtered" :key="q.id" class="flex items-start gap-2 rounded-md border border-gray-200 bg-gray-50 px-3 py-2 cursor-pointer">
              <input type="checkbox" :value="q.id" v-model="selected" />
              <div>
                <div class="font-medium">{{ q.question }}</div>
                <div class="text-sm text-muted">{{ q.category }} • {{ q.difficulty }}</div>
                <div v-if="q.options && q.options.length" class="mt-1 grid grid-cols-2 gap-x-4 gap-y-1">
                  <div v-for="opt in q.options" :key="opt.key" class="text-xs text-gray-600">
                    <span class="font-medium">{{ opt.key }}.</span> {{ opt.label }}
                  </div>
                </div>
              </div>
            </label>
          </div>
        </div>

        <div class="flex items-center gap-3">
          <label class="inline-flex items-center gap-2 text-sm">
            <input v-model="active" type="checkbox" class="rounded" :disabled="selected.length===0" />
            <span>Active (visible to users)</span>
          </label>
          <span v-if="selected.length===0" class="text-xs text-red-600">Tambahkan minimal 1 pertanyaan untuk mengaktifkan test.</span>
        </div>

        <div class="flex items-center justify-end gap-3">
          <button class="px-4 py-2 rounded-md border border-gray-200" @click="$emit('close')">Cancel</button>
          <button class="px-4 py-2 rounded-md bg-navy text-white" @click="submit">Save</button>
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
