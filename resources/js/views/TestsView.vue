<template>
  <main class="container-main py-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-semibold">Test Management</h1>
        <p class="text-muted">Create and manage your tests</p>
      </div>
      <button class="px-4 py-2 rounded-md bg-navy text-white cursor-pointer" @click="openCreate">Create Test</button>
    </div>

    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ tests.length }}</div><div class="text-muted">Total Tests</div></div>
      <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ activeCount }}</div><div class="text-muted">Active Tests</div></div>
      <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ questions.length }}</div><div class="text-muted">Available Questions</div></div>
      <div class="ui-card text-center"><div class="text-3xl font-semibold">{{ assignments }}</div><div class="text-muted">Total Assignments</div></div>
    </div>

    <div class="ui-card mt-6">
      <input v-model="search" type="text" placeholder="Search tests..." class="rounded-md border border-gray-200 bg-white px-3 py-2 w-full" />
    </div>

    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
      <div class="lg:col-span-2">
        <div v-if="filtered.length===0" class="ui-card text-center">
          <div class="text-6xl">🗎</div>
          <div class="mt-2 font-medium">No tests found</div>
          <div class="text-muted">Start by creating your first test</div>
          <button class="mt-4 w-full px-4 py-2 rounded-md bg-navy text-white cursor-pointer" @click="openCreate">+ Create Test</button>
        </div>

        <div v-for="(t,i) in filtered" :key="i" class="ui-card">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-xl font-semibold">{{ t.name }}</h2>
              <p class="text-sm text-muted">{{ t.description }}</p>
            </div>
            <div class="text-right">
              <div v-if="t.startTime" class="text-sm">
                <span class="text-muted">Start:</span> <span class="font-medium">{{ formatDate(t.startTime) }}</span>
              </div>
              <div v-if="t.endTime" class="text-sm">
                <span class="text-muted">End:</span> <span class="font-medium">{{ formatDate(t.endTime) }}</span>
              </div>
              <div v-else class="text-sm">Scheduled: <span class="font-medium">{{ formatDate(t.scheduleAt) }}</span></div>
              <div class="text-sm">Duration: {{ t.duration }} min</div>
            </div>
          </div>
          <div class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">Category: {{ t.category }}</div>
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">Questions: {{ t.questionIds.length }}</div>
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
              Status: 
              <span v-if="t.status === 'upcoming'" class="text-yellow-600">🔒 Upcoming</span>
              <span v-else-if="t.status === 'ongoing'" class="text-green-600">✓ Ongoing</span>
              <span v-else-if="t.status === 'ended'" class="text-gray-600">Ended</span>
              <span v-else>{{ isActive(t) ? 'Active' : 'Scheduled' }}</span>
            </div>
            <div class="rounded-md border border-gray-200 bg-gray-50 px-3 py-2 text-sm">
              <span v-if="t.isActive">✓ Active</span>
              <span v-else class="text-gray-500">Inactive</span>
            </div>
          </div>
          <div class="mt-4 flex items-center justify-end gap-2">
            <button class="px-3 py-1 rounded-md border border-gray-200 cursor-pointer" @click="edit(i)">Edit</button>
            <button class="px-3 py-1 rounded-md border border-red-200 text-red-600 cursor-pointer" @click="remove(i)">Delete</button>
          </div>
        </div>
      </div>

      <aside>
        <div class="ui-card">
          <h3 class="font-medium">Upcoming Tests</h3>
          <ul class="mt-3 space-y-2">
            <li v-for="u in upcoming" :key="u.name" class="flex items-center justify-between">
              <span>{{ u.name }}</span>
              <span class="text-sm">{{ formatDate(u.scheduleAt) }}</span>
            </li>
          </ul>
        </div>
      </aside>
    </div>

    <TestCreateModal v-if="showCreate" :questions="questions" :categories="categories" :initial="editingItem" @close="closeCreate" @submit="saveTest" />
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import TestCreateModal from '@/components/TestCreateModal.vue'
import { useModal, useToast } from '@/composables/useNotification'

const { confirm } = useModal()
const toast = useToast()

const categories = ['Geography','Math','Science','History','IT']
const questions = ref([])
const tests = ref([])
const assignments = 0
const search = ref('')
const filtered = computed(() => tests.value.filter(t => !search.value || t.name.toLowerCase().includes(search.value.toLowerCase())))
const isActive = (t) => new Date(t.scheduleAt) <= new Date()
const activeCount = computed(() => tests.value.filter(isActive).length)
const upcoming = computed(() => tests.value.filter(t => !isActive(t)).sort((a,b)=> new Date(a.scheduleAt)-new Date(b.scheduleAt)))

const showCreate = ref(false)
const editingIndex = ref(null)
const editingItem = computed(() => editingIndex.value!==null ? tests.value[editingIndex.value] : null)
const openCreate = () => { editingIndex.value=null; showCreate.value=true }
const closeCreate = () => { showCreate.value=false }
const saveTest = async (payload) => {
  try {
    if (editingIndex.value!==null) {
      const id = tests.value[editingIndex.value].id
      await window.axios.put(`/api/tests/${id}`, payload)
      toast.success('Test Updated', 'The test has been updated successfully.')
    } else {
      await window.axios.post('/api/tests', payload)
      toast.success('Test Created', 'New test has been created successfully.')
    }
    showCreate.value=false
    await load()
  } catch (error) {
    toast.error('Error', 'Failed to save test. Please try again.')
  }
}
const edit = (i) => { editingIndex.value=i; showCreate.value=true }
const remove = async (i) => { 
  const testName = tests.value[i].name
  const confirmed = await confirm(
    'Delete Test',
    `Are you sure you want to delete "${testName}"? This action cannot be undone.`,
    'Delete',
    'Cancel'
  )
  
  if (confirmed) {
    try {
      const id = tests.value[i].id
      await window.axios.delete(`/api/tests/${id}`)
      toast.success('Test Deleted', 'The test has been deleted successfully.')
      await load()
    } catch (error) {
      toast.error('Error', 'Failed to delete test. Please try again.')
    }
  }
}

const formatDate = (d) => new Date(d).toLocaleString()

const load = async () => {
  try {
    const qRes = await window.axios.get('/api/questions')
    questions.value = qRes.data.items
    const tRes = await window.axios.get('/api/tests')
    tests.value = tRes.data
  } catch (error) {
    toast.error('Error', 'Failed to load data. Please refresh the page.')
  }
}

onMounted(load)
</script>

<style scoped>
</style>