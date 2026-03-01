<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-[#1A1A1A]">User Management</h1>
        <p class="text-gray-500 mt-1">Manage users and roles.</p>
      </div>
      <div class="flex gap-4">
        <div class="relative">
          <input v-model="searchQuery" @input="handleSearch" type="text" placeholder="Search name or email..." class="rounded-full border border-gray-200 bg-white px-4 py-2 pl-10 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors" />
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>
        <button class="px-6 py-2 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2" @click="openAdd">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          Add User
        </button>
      </div>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-4">
      <div v-for="n in 5" :key="n" class="h-16 w-full animate-pulse bg-white rounded-[2rem] shadow-sm"></div>
    </div>

    <!-- User Table -->
    <div v-else class="bg-white shadow-xl shadow-black/5 rounded-[2rem] border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
          <thead class="bg-gray-50/50">
            <tr>
              <th scope="col" class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
              <th scope="col" class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
              <th scope="col" class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
              <th scope="col" class="px-8 py-5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-50">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-8 py-5 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-8 w-8 rounded-full bg-[#9DB359]/10 text-[#9DB359] flex items-center justify-center font-bold text-xs mr-3">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div class="text-sm font-medium text-[#1A1A1A]">{{ user.name }}</div>
                </div>
              </td>
              <td class="px-8 py-5 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ user.email }}</div>
              </td>
              <td class="px-8 py-5 whitespace-nowrap">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                      :class="{
                        'bg-purple-50 text-purple-700 border border-purple-100': user.role === 'admin',
                        'bg-blue-50 text-blue-700 border border-blue-100': user.role === 'mentor',
                        'bg-green-50 text-green-700 border border-green-100': user.role === 'user'
                      }">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium">
                <button @click="edit(user)" class="text-[#9DB359] hover:text-[#8ca34b] mr-4 transition-colors">Edit</button>
                <button @click="remove(user)" class="text-red-500 hover:text-red-700 transition-colors">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-end gap-3">
      <button @click="loadPage(prevPage)" :disabled="!prevPage" class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium">Previous</button>
      <button @click="loadPage(nextPage)" :disabled="!nextPage" class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium">Next</button>
    </div>

    <!-- Modal -->
    <UserModal v-if="showModal" :initial="editingUser" @close="closeModal" @submit="saveUser" />
  </main>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import UserModal from '@/components/UserModal.vue'
import { useModal, useToast } from '@/composables/useNotification'

const { confirm } = useModal()
const toast = useToast()

const users = ref([])
const loading = ref(false)
const nextPage = ref(null)
const prevPage = ref(null)
const currentPageUrl = ref('/api/users')

const showModal = ref(false)
const editingUser = ref(null)

const searchQuery = ref('')
let searchTimeout = null

const handleSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadUsers('/api/users?search=' + searchQuery.value)
  }, 300)
}

const loadUsers = async (url = '/api/users') => {
  loading.value = true
  try {
    // Ensure search query is appended if not already present in url
    let requestUrl = url
    if (searchQuery.value && !url.includes('search=')) {
        requestUrl += (url.includes('?') ? '&' : '?') + 'search=' + searchQuery.value
    }

    const { data } = await window.axios.get(requestUrl)
    users.value = data.data
    nextPage.value = data.links.next
    prevPage.value = data.links.prev
  } catch (e) {
    toast.error('Error', 'Failed to load users')
  } finally {
    loading.value = false
  }
}

const loadPage = (url) => {
  if (url) loadUsers(url)
}

const openAdd = () => {
  editingUser.value = null
  showModal.value = true
}

const edit = (user) => {
  editingUser.value = { ...user }
  showModal.value = true
}

const remove = async (user) => {
  const confirmed = await confirm({
    title: 'Delete User',
    message: `Are you sure you want to delete ${user.name}?`,
    confirmText: 'Delete',
    type: 'danger'
  })

  if (confirmed) {
    try {
      await window.axios.delete(`/api/users/${user.id}`)
      users.value = users.value.filter(u => u.id !== user.id)
      toast.success('Success', 'User deleted')
    } catch (e) {
      toast.error('Error', 'Failed to delete user')
    }
  }
}

const closeModal = () => {
  showModal.value = false
  editingUser.value = null
}

const saveUser = async (formData) => {
  try {
    if (editingUser.value) {
      const { data } = await window.axios.put(`/api/users/${editingUser.value.id}`, formData)
      const idx = users.value.findIndex(u => u.id === editingUser.value.id)
      if (idx !== -1) users.value[idx] = data.data
      toast.success('Success', 'User updated')
    } else {
      const { data } = await window.axios.post('/api/users', formData)
      // Reload to show new user in correct order/page
      loadUsers()
      toast.success('Success', 'User created')
    }
    closeModal()
  } catch (e) {
    toast.error('Error', 'Failed to save user')
  }
}

onMounted(() => {
  loadUsers()
})
</script>

<style scoped>
</style>
