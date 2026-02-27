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
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="text-sm">Type *</label>
                <select v-model="form.type" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2">
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="essay">Essay</option>
                </select>
            </div>
            <div>
                 <label class="text-sm">Image (Optional)</label>
                 <input type="file" @change="handleFileChange" accept="image/*" class="mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-navy file:text-white hover:file:bg-opacity-80"/>
            </div>
        </div>

        <div>
          <label class="text-sm">Question *</label>
          <textarea v-model="form.question" rows="3" placeholder="Enter your question here..." class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2"></textarea>
          <div v-if="form.image || form.image_url" class="mt-2">
              <img :src="previewUrl || form.image_url" class="h-32 object-contain border rounded" />
          </div>
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

        <div v-if="form.type === 'multiple_choice'">
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
import { reactive, watch, ref } from 'vue'

const props = defineProps({ initial: { type: Object, default: null } })
const emit = defineEmits(['close','submit'])

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