<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
    <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Create New Test</h2>
          <p class="text-gray-500 mt-1">Buat test terlebih dahulu. Pertanyaan akan ditambahkan setelah test dibuat.</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form class="space-y-6" @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Test Name *</label>
            <input v-model="form.name" placeholder="e.g., Mathematics Final Exam" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
            <textarea v-model="form.description" rows="2" placeholder="Brief description of the test..." class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all resize-none"></textarea>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
            <select v-model="form.category" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
              <option value="">Select category</option>
              <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
            <input v-model.number="form.duration" type="number" min="1" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Schedule Date *</label>
            <input v-model="form.scheduleAt" type="datetime-local" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" lang="id" />
          </div>
          <div></div>
        </div>

        <!-- Start and End Time -->
        <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-6">
          <h3 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
            <span class="text-lg">🕐</span> Test Time Settings
          </h3>
          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
              <input v-model="form.startTime" type="datetime-local" class="w-full rounded-xl border-blue-100 bg-white px-4 py-3 focus:border-blue-300 focus:ring-0 transition-all" required lang="id" />
              <p class="text-xs text-gray-500 mt-2">Test will be locked before this time</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
              <input v-model="form.endTime" type="datetime-local" class="w-full rounded-xl border-blue-100 bg-white px-4 py-3 focus:border-blue-300 focus:ring-0 transition-all" required lang="id" />
              <p class="text-xs text-gray-500 mt-2">Test will end at this time</p>
            </div>
          </div>
          <div class="mt-4 text-xs text-blue-600 font-medium">Status aktif akan tersedia setelah Anda menambahkan pertanyaan ke test.</div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
          <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="$emit('close')">Cancel</button>
          <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform active:scale-95 transition-all">Create Test</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch, ref } from 'vue'

const props = defineProps({
  initial: { type: Object, default: null },
  questions: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
})
const emit = defineEmits(['close','submit'])

const toLocalDatetimeInputValue = (value) => {
  const date = value instanceof Date ? value : new Date(value)
  if (Number.isNaN(date.getTime())) return ''
  const yyyy = date.getFullYear()
  const mm = String(date.getMonth() + 1).padStart(2, '0')
  const dd = String(date.getDate()).padStart(2, '0')
  const hh = String(date.getHours()).padStart(2, '0')
  const mi = String(date.getMinutes()).padStart(2, '0')
  return `${yyyy}-${mm}-${dd}T${hh}:${mi}`
}

const base = () => ({ 
  name: '', 
  description: '', 
  category: '', 
  duration: 30, 
  scheduleAt: toLocalDatetimeInputValue(new Date()),
  startTime: toLocalDatetimeInputValue(new Date()),
  endTime: toLocalDatetimeInputValue(new Date(Date.now() + 3600000)),
  isActive: false,
  questionIds: []
})
const form = reactive(base())
const showPicker = ref(false)
const togglePicker = () => {}
const selectedCount = computed(() => 0)

watch(() => props.initial, (val) => {
  if (!val) {
    Object.assign(form, base())
    return
  }

  const next = JSON.parse(JSON.stringify(val))
  next.scheduleAt = toLocalDatetimeInputValue(val.scheduleAt ?? val.schedule_at)
  next.startTime = toLocalDatetimeInputValue(val.startTime ?? val.start_time)
  next.endTime = toLocalDatetimeInputValue(val.endTime ?? val.end_time)
  next.isActive = val.isActive ?? val.is_active ?? false
  next.questionIds = Array.isArray(val.questionIds ?? val.question_ids) ? (val.questionIds ?? val.question_ids) : (next.questionIds ?? [])
  Object.assign(form, next)
}, { immediate: true })

const submit = () => {
  emit('submit', JSON.parse(JSON.stringify(form)))
}
</script>

<style scoped>
</style>
