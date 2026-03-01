<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
    <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ isEdit ? 'Edit User' : 'Add New User' }}</h2>
          <p class="text-gray-500 mt-1">Manage user details and role.</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form class="space-y-6" @submit.prevent="submit">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
          <input v-model="form.name" type="text" placeholder="Full Name" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
          <input v-model="form.email" type="email" placeholder="Email Address" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
          <select v-model="form.role" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
            <option value="user">User</option>
            <option value="mentor">Mentor</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Password {{ isEdit ? '(Leave blank to keep current)' : '*' }}</label>
          <input v-model="form.password" type="password" placeholder="Password" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" :required="!isEdit" />
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
          <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="$emit('close')">Cancel</button>
          <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform active:scale-95 transition-all">{{ isEdit ? 'Update' : 'Create' }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'

const props = defineProps({
  initial: { type: Object, default: null }
})
const emit = defineEmits(['close', 'submit'])

const isEdit = computed(() => !!props.initial)

const form = reactive({
  name: '',
  email: '',
  role: 'user',
  password: ''
})

watch(() => props.initial, (val) => {
  if (val) {
    form.name = val.name
    form.email = val.email
    form.role = val.role
    form.password = ''
  } else {
    form.name = ''
    form.email = ''
    form.role = 'user'
    form.password = ''
  }
}, { immediate: true })

const submit = () => {
  emit('submit', JSON.parse(JSON.stringify(form)))
}
</script>
