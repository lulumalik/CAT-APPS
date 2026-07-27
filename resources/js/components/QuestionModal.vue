<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
    <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ isEdit ? t('modals.question.editTitle') : t('modals.question.addTitle') }}</h2>
          <p class="text-gray-500 mt-1">{{ t('modals.question.subtitle') }}</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form class="space-y-6" @submit.prevent="submit">
        <div class="grid grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Batch / Paket Tryout</label>
                <input v-model="form.batch" placeholder="Contoh: Tryout 1, Tryout 2" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.question.typeLabel') }}</label>
                <select v-model="form.type" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
                    <option value="multiple_choice">{{ t('modals.question.typeMultipleChoice') }}</option>
                    <option value="essay">{{ t('modals.question.typeEssay') }}</option>
                </select>
            </div>
        </div>

        <div>
           <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.question.imageLabel') }}</label>
           <input type="file" @change="handleFileChange" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#1A1A1A] file:text-white hover:file:bg-black transition-all"/>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.question.questionLabel') }}</label>
          <textarea v-model="form.question" rows="3" :placeholder="t('modals.question.questionPlaceholder')" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all resize-none"></textarea>
          <div v-if="form.image || form.image_url" class="mt-4">
              <img :src="previewUrl || form.image_url" class="h-40 object-contain border border-gray-100 rounded-xl bg-gray-50" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.question.categoryLabel') }}</label>
            <select v-model="form.category" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
              <option value="">{{ t('modals.testCreate.categoryPlaceholder') }}</option>
              <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.question.difficultyLabel') }}</label>
            <select v-model="form.difficulty" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
              <option value="Easy">{{ t('modals.question.difficultyEasy') }}</option>
              <option value="Medium">{{ t('modals.question.difficultyMedium') }}</option>
              <option value="Hard">{{ t('modals.question.difficultyHard') }}</option>
            </select>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Artikel Bacaan (Article Quiz)</label>
          <select v-model="form.article_quiz_id" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
            <option :value="null">-- Tanpa Artikel Bacaan --</option>
            <option v-for="art in articleQuizzes" :key="art.id" :value="art.id">{{ art.title }}</option>
          </select>
        </div>

        <div v-if="form.type === 'multiple_choice'" class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
          <div class="flex items-center justify-between mb-4">
            <label class="block text-sm font-medium text-gray-900">{{ t('modals.question.optionsLabel') }} ({{ form.options.length }} Opsi)</label>
            <button
              v-if="form.options.length < 6"
              type="button"
              class="px-3 py-1.5 rounded-full bg-[#9DB359]/15 text-[#6c7c3f] hover:bg-[#9DB359]/25 text-xs font-bold transition-all flex items-center gap-1 cursor-pointer"
              @click="addOption"
            >
              + Tambah Opsi (Maks. 6)
            </button>
          </div>

          <div class="space-y-3">
            <div v-for="(opt,idx) in form.options" :key="idx" class="flex items-start gap-3 group">
              <div class="relative flex shrink-0 items-center justify-center pt-1">
                <input type="radio" :value="opt.key" v-model="form.correct" class="peer sr-only" :id="'opt-'+idx" />
                <label :for="'opt-'+idx" class="flex h-8 w-8 shrink-0 aspect-square items-center justify-center rounded-full border-2 border-gray-300 cursor-pointer transition-all peer-checked:border-[#9DB359] peer-checked:bg-[#9DB359]">
                  <span class="text-white text-xs font-bold opacity-0 peer-checked:opacity-100">{{ opt.key }}</span>
                </label>
              </div>
              <div class="flex-1 flex items-center gap-2">
                <input v-model="opt.label" :placeholder="t('modals.question.optionPlaceholder', { n: idx + 1 })" class="w-full rounded-xl border-transparent bg-white px-4 py-2 focus:border-gray-200 focus:ring-0 transition-all shadow-sm group-hover:shadow-md" />
                <button
                  v-if="form.options.length > 2"
                  type="button"
                  class="p-2 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 transition-colors shrink-0"
                  title="Hapus Opsi"
                  @click="removeOption(idx)"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
              </div>
            </div>
          </div>
          <div class="text-xs text-gray-500 mt-3 pl-11">{{ t('modals.question.selectCorrectHint') }}</div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
          <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="$emit('close')">{{ t('modals.question.cancel') }}</button>
          <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform active:scale-95 transition-all">{{ isEdit ? t('modals.question.submitUpdate') : t('modals.question.submitAdd') }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, ref, computed } from 'vue'
import { useI18n } from '@/composables/useI18n'

const props = defineProps({
  initial: { type: Object, default: null },
  articleQuizzes: { type: Array, default: () => [] }
})
const emit = defineEmits(['close','submit'])
const isEdit = computed(() => !!props.initial)
const { t } = useI18n()

const keysList = ['A', 'B', 'C', 'D', 'E', 'F']

const categories = [
  'Kewarganegaraan',
  'Math',
  'English',
  'Interpersonal Skill',
  'Sinonim',
  'Antonim',
  'Analogi',
  'Penalaran Analitis',
  'Deret Angka',
  'Penalaran Logis',
  'Aljabar & Aritmatika',
  'Pemahaman Bahasa',
]

const base = () => ({ 
  batch: 'Tryout 1',
  question: '', 
  category: '', 
  difficulty: 'Medium', 
  type: 'multiple_choice',
  image: null,
  image_url: null,
  article_quiz_id: null,
  options: [
    { key: 'A', label: '' },
    { key: 'B', label: '' },
    { key: 'C', label: '' },
    { key: 'D', label: '' },
    { key: 'E', label: '' }
  ], 
  correct: 'A' 
})
const form = reactive(base())
const previewUrl = ref(null)

const reindexOptions = () => {
  form.options.forEach((opt, idx) => {
    opt.key = keysList[idx] || String.fromCharCode(65 + idx)
  })
  // If current correct key is no longer available in options, reset to first option key
  const validKeys = form.options.map(o => o.key)
  if (!validKeys.includes(form.correct)) {
    form.correct = validKeys[0] || 'A'
  }
}

const addOption = () => {
  if (form.options.length < 6) {
    const nextKey = keysList[form.options.length] || 'A'
    form.options.push({ key: nextKey, label: '' })
    reindexOptions()
  }
}

const removeOption = (index) => {
  if (form.options.length > 2) {
    form.options.splice(index, 1)
    reindexOptions()
  }
}

watch(() => props.initial, (val) => {
  if (val) {
      const data = JSON.parse(JSON.stringify(val))
      Object.assign(form, data)
      if (data.image) {
          form.image_url = data.image
          form.image = null
      }
      if (!form.type) form.type = 'multiple_choice'
      if (!form.batch) form.batch = 'Tryout 1'
      if (!form.options || form.options.length === 0) {
        form.options = [
          { key: 'A', label: '' },
          { key: 'B', label: '' },
          { key: 'C', label: '' },
          { key: 'D', label: '' },
          { key: 'E', label: '' }
        ]
      }
      reindexOptions()
  } else {
      Object.assign(form, base())
  }
  previewUrl.value = null
}, { immediate: true })

const handleFileChange = (e) => {
    const file = e.target.files[0]
    if (file) {
        form.image = file
        previewUrl.value = URL.createObjectURL(file)
    }
}

const submit = () => {
    const { image, ...rest } = form
    const payload = JSON.parse(JSON.stringify(rest))
    if (image instanceof File) {
        payload.image = image
    }
    emit('submit', payload)
}
</script>

<style scoped>
</style>
