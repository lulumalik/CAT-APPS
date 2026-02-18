<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30" @click="$emit('close')"></div>
    <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
      <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold">{{ isEdit ? 'Edit User' : 'Add New User' }}</h2>
        <button class="px-3 py-1 rounded-md" @click="$emit('close')">✕</button>
      </div>
      <p class="text-muted">Manage user details and role.</p>

      <form class="mt-4 space-y-4" @submit.prevent="submit">
        <div>
          <label class="text-sm">Name *</label>
          <input v-model="form.name" type="text" placeholder="Full Name" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" required />
        </div>

        <div>
          <label class="text-sm">Email *</label>
          <input v-model="form.email" type="email" placeholder="Email Address" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" required />
        </div>

        <div>
          <label class="text-sm">Role *</label>
          <select v-model="form.role" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2">
            <option value="user">User</option>
            <option value="admin">Admin</option>
          </select>
        </div>

        <div>
          <label class="text-sm">Password {{ isEdit ? '(Leave blank to keep current)' : '*' }}</label>
          <input v-model="form.password" type="password" placeholder="Password" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" :required="!isEdit" />
        </div>

        <div class="flex items-center justify-end gap-3 mt-6">
          <button type="button" class="px-4 py-2 rounded-md border border-gray-200" @click="$emit('close')">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded-md bg-navy text-white">{{ isEdit ? 'Update' : 'Create' }}</button>
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
