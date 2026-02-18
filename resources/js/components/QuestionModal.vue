<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30" @click="$emit('close')"></div>
    <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Add New Question</h2>
        <button class="px-3 py-1 rounded-md" @click="$emit('close')">✕</button>
      </div>
      <p class="text-muted">Create a new question for your question bank</p>

      <form class="mt-4 space-y-4" @submit.prevent="submit">
        <div>
          <label class="text-sm">Question *</label>
          <textarea v-model="form.question" rows="3" placeholder="Enter your question here..." class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2"></textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="text-sm">Category *</label>
            <select v-model="form.category" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2">
              <option value="">Select category</option>
              <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="text-sm">Difficulty *</label>
            <select v-model="form.difficulty" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2">
              <option>Easy</option>
              <option>Medium</option>
              <option>Hard</option>
            </select>
          </div>
        </div>

        <div>
          <label class="text-sm">Answer Options *</label>
          <div class="space-y-3 mt-2">
            <div v-for="(opt,idx) in form.options" :key="opt.key" class="flex items-center gap-3">
              <input type="radio" :value="opt.key" v-model="form.correct" />
              <div class="flex-1">
                <input v-model="opt.label" :placeholder="`Enter option ${idx+1}...`" class="w-full rounded-md border border-gray-200 bg-white px-3 py-2" />
              </div>
            </div>
          </div>
          <div class="text-xs text-muted mt-1">Select the radio button to mark the correct answer</div>
        </div>

        <div class="flex items-center justify-end gap-3 mt-4">
          <button type="button" class="px-4 py-2 rounded-md border border-gray-200" @click="$emit('close')">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white">Add Question</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch } from 'vue'

const props = defineProps({ initial: { type: Object, default: null } })
const emit = defineEmits(['close','submit'])

const categories = ['Geography','Math','Science','History','IT']
const base = () => ({ question: '', category: '', difficulty: 'Medium', options: [
  { key: 'A', label: '' }, { key: 'B', label: '' }, { key: 'C', label: '' }, { key: 'D', label: '' }
], correct: 'A' })
const form = reactive(base())

watch(() => props.initial, (val) => {
  Object.assign(form, val ? JSON.parse(JSON.stringify(val)) : base())
}, { immediate: true })

const submit = () => {
  emit('submit', JSON.parse(JSON.stringify(form)))
}
</script>

<style scoped>
</style>