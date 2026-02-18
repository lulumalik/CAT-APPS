<template>
  <main class="container-main py-8">
    <div class="flex items-center justify-between mb-6">
      <div>
        <h1 class="text-3xl font-semibold">User Management</h1>
        <p class="text-muted">Manage users and roles.</p>
      </div>
      <button class="px-4 py-2 rounded-md bg-navy text-white cursor-pointer" @click="openAdd">Add User</button>
    </div>

    <!-- Loading Skeleton -->
    <div v-if="loading" class="space-y-4">
      <div v-for="n in 5" :key="n" class="h-16 w-full animate-pulse bg-gray-100 rounded-md"></div>
    </div>

    <!-- User Table -->
    <div v-else class="bg-white shadow rounded-lg overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
              <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="user in users" :key="user.id">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ user.email }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                      :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button @click="edit(user)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                <button @click="remove(user)" class="text-red-600 hover:text-red-900">Delete</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-end">
      <button @click="loadPage(prevPage)" :disabled="!prevPage" class="px-3 py-1 rounded-md border border-gray-300 mr-2 disabled:opacity-50">Prev</button>
      <button @click="loadPage(nextPage)" :disabled="!nextPage" class="px-3 py-1 rounded-md border border-gray-300 disabled:opacity-50">Next</button>
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

const loadUsers = async (url = '/api/users') => {
  loading.value = true
  try {
    const { data } = await window.axios.get(url)
    users.value = data.data
    nextPage.value = data.next_page_url
    prevPage.value = data.prev_page_url
    currentPageUrl.value = url
  } catch (error) {
    toast.error('Error', 'Failed to load users.')
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
  editingUser.value = user
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  editingUser.value = null
}

const saveUser = async (payload) => {
  try {
    if (editingUser.value) {
      await window.axios.put(`/api/users/${editingUser.value.id}`, payload)
      toast.success('Updated', 'User updated successfully.')
    } else {
      await window.axios.post('/api/users', payload)
      toast.success('Created', 'User created successfully.')
    }
    closeModal()
    loadUsers(currentPageUrl.value)
  } catch (error) {
    toast.error('Error', error.response?.data?.message || 'Failed to save user.')
  }
}

const remove = async (user) => {
  const confirmed = await confirm('Delete User', `Are you sure you want to delete ${user.name}?`, 'Delete', 'Cancel')
  if (confirmed) {
    try {
      await window.axios.delete(`/api/users/${user.id}`)
      toast.success('Deleted', 'User deleted successfully.')
      loadUsers(currentPageUrl.value)
    } catch (error) {
      toast.error('Error', error.response?.data?.message || 'Failed to delete user.')
    }
  }
}

onMounted(() => loadUsers())
</script>
