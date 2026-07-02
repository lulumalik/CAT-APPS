<template>
  <div>
    <div class="flex items-start justify-between mb-4 gap-4">
      <div class="flex items-center gap-2 flex-wrap">
        <span
          v-if="highlighted"
          class="px-3 py-1 rounded-full text-xs font-semibold bg-[#9DB359] text-white"
        >
          {{ t('questionBank.myQuestionBadge') }}
        </span>
        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200 capitalize">{{ question.category }}</span>
        <span class="px-3 py-1 rounded-full text-xs font-medium border" :class="diffBadge(question.difficulty)">{{ question.difficulty }}</span>
        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600 border border-blue-100 capitalize">{{ question.type }}</span>
      </div>
      <div v-if="canManage" class="flex items-center gap-2 shrink-0">
        <button class="px-4 py-1.5 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-50 transition-colors" @click="$emit('edit')">{{ t('common.edit') }}</button>
        <button class="px-4 py-1.5 rounded-full border border-red-100 text-red-600 text-sm font-medium hover:bg-red-50 transition-colors" @click="$emit('remove')">{{ t('common.delete') }}</button>
      </div>
    </div>

    <div class="flex gap-6">
      <div v-if="question.image_url" class="flex-shrink-0">
        <img :src="question.image_url" :alt="t('questionBank.title')" class="w-32 h-32 object-cover rounded-xl border border-gray-200" />
      </div>
      <div class="flex-grow">
        <h2 class="text-xl font-medium text-[#1A1A1A] leading-relaxed">{{ question.question }}</h2>

        <div v-if="question.type === 'multiple_choice'" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
          <div
            v-for="opt in question.options"
            :key="opt.key"
            class="rounded-xl border px-5 py-3 flex items-start justify-between gap-3 transition-colors"
            :class="opt.key === question.correct ? 'border-[#9DB359] bg-[#9DB359]/5' : 'border-gray-200 bg-white'"
          >
            <div class="flex items-start gap-3 min-w-0 flex-1">
              <span
                class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border text-xs font-medium aspect-square"
                :class="opt.key === question.correct ? 'border-[#9DB359] text-[#9DB359] bg-white' : 'border-gray-300 text-gray-500'"
              >{{ opt.key }}</span>
              <span class="pt-0.5 leading-relaxed" :class="opt.key === question.correct ? 'text-[#1A1A1A] font-medium' : 'text-gray-600'">{{ opt.label }}</span>
            </div>
            <span v-if="opt.key === question.correct" class="shrink-0 px-2 py-0.5 rounded-full bg-[#9DB359] text-white text-[10px] font-bold uppercase tracking-wider">{{ t('questionBank.correct') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useI18n } from '@/composables/useI18n'

defineProps({
  question: { type: Object, required: true },
  canManage: { type: Boolean, default: false },
  highlighted: { type: Boolean, default: false },
})

defineEmits(['edit', 'remove'])

const { t } = useI18n()

const diffBadge = (d) => {
  if (d === 'Easy') return 'bg-green-50 text-green-700 border-green-100'
  if (d === 'Medium') return 'bg-yellow-50 text-yellow-700 border-yellow-100'
  return 'bg-red-50 text-red-700 border-red-100'
}
</script>
