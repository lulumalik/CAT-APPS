<template>
  <div class="fixed inset-0 z-50">
    <div class="absolute inset-0 bg-black/30" @click="$emit('close')"></div>
    <div class="relative mx-auto mt-16 max-w-2xl max-h-[80vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">Create New Test</h2>
        <button class="px-3 py-1 rounded-md" @click="$emit('close')">✕</button>
      </div>
      <p class="text-muted">Buat test terlebih dahulu. Pertanyaan akan ditambahkan setelah test dibuat, kemudian Anda bisa mengaktifkannya agar terlihat oleh user.</p>

      <form class="mt-4 space-y-4" @submit.prevent="submit">
        <div>
          <label class="text-sm">Test Name *</label>
          <input v-model="form.name" placeholder="e.g., Mathematics Final Exam" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" />
        </div>

        <div>
          <label class="text-sm">Description</label>
          <input v-model="form.description" placeholder="Brief description of the test..." class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" />
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
            <label class="text-sm">Duration (minutes) *</label>
            <input v-model.number="form.duration" type="number" min="1" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" />
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="text-sm">Schedule Date *</label>
            <input v-model="form.scheduleAt" type="datetime-local" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" />
          </div>
          <div></div>
        </div>

        <!-- Start and End Time -->
        <div class="rounded-lg border border-blue-200 bg-blue-50 p-4">
          <h3 class="font-medium text-sm mb-3">🕐 Test Time Settings</h3>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="text-sm">Start Time *</label>
              <input v-model="form.startTime" type="datetime-local" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" required />
              <p class="text-xs text-muted mt-1">Test will be locked before this time</p>
            </div>
            <div>
              <label class="text-sm">End Time *</label>
              <input v-model="form.endTime" type="datetime-local" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" required />
              <p class="text-xs text-muted mt-1">Test will end at this time</p>
            </div>
          </div>
          <div class="mt-3 text-xs text-muted">Status aktif akan tersedia setelah Anda menambahkan pertanyaan ke test.</div>
        </div>

        <div></div>

        <div class="flex items-center justify-end gap-3 mt-4">
          <button type="button" class="px-4 py-2 rounded-md border border-gray-200" @click="$emit('close')">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white">Create Test</button>
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

const base = () => ({ 
  name: '', 
  description: '', 
  category: '', 
  duration: 30, 
  scheduleAt: new Date().toISOString().slice(0,16),
  startTime: new Date().toISOString().slice(0,16),
  endTime: new Date(Date.now() + 3600000).toISOString().slice(0,16), // 1 hour from now
  isActive: false,
  questionIds: []
})
const form = reactive(base())
const showPicker = ref(false)
const togglePicker = () => {}
const selectedCount = computed(() => 0)

watch(() => props.initial, (val) => {
  Object.assign(form, val ? JSON.parse(JSON.stringify(val)) : base())
}, { immediate: true })

const submit = () => {
  emit('submit', JSON.parse(JSON.stringify(form)))
}
</script>

<style scoped>
</style>
