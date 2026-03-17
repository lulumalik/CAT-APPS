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
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.question.typeLabel') }}</label>
                <select v-model="form.type" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
                    <option value="multiple_choice">{{ t('modals.question.typeMultipleChoice') }}</option>
                    <option value="essay">{{ t('modals.question.typeEssay') }}</option>
                </select>
            </div>
            <div>
                 <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.question.imageLabel') }}</label>
                 <input type="file" @change="handleFileChange" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#1A1A1A] file:text-white hover:file:bg-black transition-all"/>
            </div>
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

        <div v-if="form.type === 'multiple_choice'" class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
          <label class="block text-sm font-medium text-gray-900 mb-4">{{ t('modals.question.optionsLabel') }}</label>
          <div class="space-y-3">
            <div v-for="(opt,idx) in form.options" :key="opt.key" class="flex items-center gap-3 group">
              <div class="relative flex items-center justify-center">
                <input type="radio" :value="opt.key" v-model="form.correct" class="peer sr-only" :id="'opt-'+idx" />
                <label :for="'opt-'+idx" class="w-8 h-8 rounded-full border-2 border-gray-300 peer-checked:border-[#9DB359] peer-checked:bg-[#9DB359] cursor-pointer flex items-center justify-center transition-all">
                  <span class="text-white text-xs font-bold opacity-0 peer-checked:opacity-100">{{ opt.key }}</span>
                </label>
              </div>
              <div class="flex-1">
                <input v-model="opt.label" :placeholder="t('modals.question.optionPlaceholder', { n: idx + 1 })" class="w-full rounded-xl border-transparent bg-white px-4 py-2 focus:border-gray-200 focus:ring-0 transition-all shadow-sm group-hover:shadow-md" />
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

const props = defineProps({ initial: { type: Object, default: null } })
const emit = defineEmits(['close','submit'])
const isEdit = computed(() => !!props.initial)
const { t } = useI18n()

const categories = ['Geography','Math','Science','History','IT']
const base = () => ({ 
  question: '', 
  category: '', 
  difficulty: 'Medium', 
  type: 'multiple_choice',
  image: null,
  image_url: null,
  options: [
    { key: 'A', label: '' }, { key: 'B', label: '' }, { key: 'C', label: '' }, { key: 'D', label: '' }
  ], 
  correct: 'A' 
})
const form = reactive(base())
const previewUrl = ref(null)

watch(() => props.initial, (val) => {
  if (val) {
      const data = JSON.parse(JSON.stringify(val))
      Object.assign(form, data)
      if (data.image) {
          form.image_url = data.image
          form.image = null
      }
      if (!form.type) form.type = 'multiple_choice'
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
