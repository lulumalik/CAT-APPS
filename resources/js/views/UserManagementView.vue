<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-[#1A1A1A]">{{ t('users.title') }}</h1>
        <p class="text-gray-500 mt-1">{{ t('users.subtitle') }}</p>
      </div>
      <div class="flex flex-wrap gap-3 items-center">
        <div class="relative">
          <input v-model="searchQuery" @input="handleSearch" type="text" :placeholder="t('users.searchPlaceholder')" class="rounded-full border border-gray-200 bg-white px-4 py-2 pl-10 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors" />
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>
        <select
          v-model="roleFilter"
          class="rounded-full border border-gray-200 bg-white px-4 py-2 text-sm focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors"
          @change="handleRoleChange"
        >
          <option value="">{{ t('users.allRoles') }}</option>
          <option value="admin">{{ t('users.roleAdmin') }}</option>
          <option value="user">{{ t('users.roleUser') }}</option>
          <option value="mentor">{{ t('users.roleMentor') }}</option>
          <option value="parent">{{ t('users.roleParent') }}</option>
        </select>
        <!-- <label class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 cursor-pointer text-sm font-medium">
          Import Excel/CSV
          <input type="file" accept=".xlsx,.csv,.txt" class="hidden" @change="onFilePicked" />
        </label> -->
        <button class="px-6 py-2 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2" @click="openAdd">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          {{ t('users.addUser') }}
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
              <th scope="col" class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableName') }}</th>
              <th scope="col" class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableEmail') }}</th>
              <th scope="col" class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableRole') }}</th>
              <th scope="col" class="px-8 py-5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableExpires') }}</th>
              <th scope="col" class="px-8 py-5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableActions') }}</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-50">
            <tr v-if="users.length === 0">
              <td colspan="5" class="px-8 py-12 text-center text-sm text-gray-500">
                {{ t('users.noUsers') }}
              </td>
            </tr>
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
                        'bg-amber-50 text-amber-700 border border-amber-100': user.role === 'parent',
                        'bg-green-50 text-green-700 border border-green-100': user.role === 'user'
                      }">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-8 py-5 whitespace-nowrap">
                <div v-if="user.role !== 'user'" class="text-sm text-gray-400">—</div>
                <div v-else-if="!user.app_expires_at" class="space-y-1.5">
                  <p class="text-xs text-gray-500">{{ t('users.expiresNotSet') }}</p>
                  <div class="flex items-center gap-2">
                    <input
                      v-model="expiresDraft[user.id]"
                      type="date"
                      class="rounded-lg border border-gray-200 bg-gray-50 px-2 py-1.5 text-sm focus:bg-white focus:border-[#9DB359] focus:ring-0 transition-colors"
                    />
                    <button
                      type="button"
                      :disabled="!expiresDraft[user.id] || savingExpiresId === user.id"
                      class="px-3 py-1.5 rounded-lg text-xs font-semibold text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed bg-[#9DB359] hover:bg-[#8ca34b]"
                      @click="saveExpires(user)"
                    >
                      {{ savingExpiresId === user.id ? '...' : t('users.expiresSave') }}
                    </button>
                  </div>
                </div>
                <div v-else class="space-y-1">
                  <div class="text-sm text-gray-700">{{ formatExpiresAt(user.app_expires_at) }}</div>
                  <span
                    class="px-2 py-0.5 inline-flex text-[11px] font-semibold rounded-full"
                    :class="isUserExpired(user) ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100'"
                  >
                    {{ isUserExpired(user) ? t('users.expiresExpired') : t('users.expiresActive') }}
                  </span>
                </div>
              </td>
              <td class="px-8 py-5 whitespace-nowrap text-right text-sm font-medium">
                <router-link
                  v-if="user.role === 'user'"
                  :to="`/dashboard/student/${user.id}`"
                  class="text-[#1A1A1A] hover:text-[#9DB359] mr-4 transition-colors font-medium"
                >
                  Dashboard Siswa
                </router-link>
                <button @click="edit(user)" class="text-[#9DB359] hover:text-[#8ca34b] mr-4 transition-colors">{{ t('common.edit') }}</button>
                <button @click="remove(user)" class="text-red-500 hover:text-red-700 transition-colors">{{ t('common.delete') }}</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div v-if="!loading && lastPage > 0" class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
      <p class="text-sm text-gray-500">
        {{ t('users.showingUsers', { from: rangeFrom, to: rangeTo, total: totalUsers }) }}
      </p>
      <div class="flex items-center justify-end gap-3">
        <span class="text-xs text-gray-500 px-1">
          {{ t('users.pageInfo', { page: currentPage, total: lastPage }) }}
        </span>
        <button
          type="button"
          @click="goToPage(currentPage - 1)"
          :disabled="currentPage <= 1"
          class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium"
        >
          {{ t('common.previous') }}
        </button>
        <button
          type="button"
          @click="goToPage(currentPage + 1)"
          :disabled="currentPage >= lastPage"
          class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors text-sm font-medium"
        >
          {{ t('common.next') }}
        </button>
      </div>
    </div>

    <!-- Modal -->
    <UserModal v-if="showModal" :initial="editingUser" @close="closeModal" @submit="saveUser" />
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import UserModal from '@/components/UserModal.vue'
import { useModal, useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'
import { formatAppExpiresAt, isAppExpired, dateInputToExpiresAt } from '@/utils/userMeta'

const { confirm } = useModal()
const toast = useToast()
const { t } = useI18n()

const users = ref([])
const loading = ref(false)
const currentPage = ref(1)
const lastPage = ref(1)
const totalUsers = ref(0)
const perPage = 10

const showModal = ref(false)
const editingUser = ref(null)
const importFile = ref(null)

const searchQuery = ref('')
const roleFilter = ref('')
const expiresDraft = ref({})
const savingExpiresId = ref(null)
let searchTimeout = null

const rangeFrom = computed(() => {
  if (totalUsers.value === 0) return 0
  return (currentPage.value - 1) * perPage + 1
})
const rangeTo = computed(() => {
  if (totalUsers.value === 0) return 0
  return Math.min(currentPage.value * perPage, totalUsers.value)
})

const formatExpiresAt = (value) => formatAppExpiresAt(value) || '—'
const isUserExpired = (user) => isAppExpired(user)

const handleSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    loadUsers(1)
  }, 300)
}

const handleRoleChange = () => {
  loadUsers(1)
}

const loadUsers = async (page = 1) => {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/users', {
      params: {
        page,
        per_page: perPage,
        search: searchQuery.value || undefined,
        role: roleFilter.value || undefined,
      },
    })
    users.value = data.data || []
    currentPage.value = data.current_page || 1
    lastPage.value = data.last_page || 1
    totalUsers.value = data.total || 0
  } catch (e) {
    toast.error('Error', t('users.toastLoadFailed'))
  } finally {
    loading.value = false
  }
}

const goToPage = (page) => {
  if (page < 1 || page > lastPage.value) return
  loadUsers(page)
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
    title: t('users.deleteConfirmTitle'),
    message: t('users.deleteConfirmMessage', { name: user.name }),
    confirmText: t('common.delete'),
    type: 'danger'
  })

  if (confirmed) {
    try {
      await window.axios.delete(`/api/users/${user.id}`)
      const nextPage = users.value.length === 1 && currentPage.value > 1
        ? currentPage.value - 1
        : currentPage.value
      await loadUsers(nextPage)
      toast.success('Success', t('users.toastDeleted'))
    } catch (e) {
      toast.error('Error', t('users.toastDeleteFailed'))
    }
  }
}

const closeModal = () => {
  showModal.value = false
  editingUser.value = null
}

const onFilePicked = async (event) => {
  const file = event?.target?.files?.[0]
  if (!file) return
  importFile.value = file
  const fd = new FormData()
  fd.append('file', file)
  try {
    const { data } = await window.axios.post('/api/users/import', fd, {
      headers: { 'Content-Type': 'multipart/form-data' },
    })
    await loadUsers()
    const stats = data?.stats || {}
    toast.success('Success', `Import selesai. Baru: ${stats.created || 0}, update: ${stats.updated || 0}, skip: ${stats.skipped || 0}`)
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || 'Import user gagal')
  } finally {
    event.target.value = ''
    importFile.value = null
  }
}

const formatApiError = (error) => {
  const data = error?.response?.data
  if (!data) return t('users.toastSaveFailed')
  if (typeof data.message === 'string' && data.message) return data.message
  if (data.errors && typeof data.errors === 'object') {
    return Object.values(data.errors).flat().join(' ')
  }
  return t('users.toastSaveFailed')
}

const saveExpires = async (user) => {
  const date = expiresDraft.value[user.id]
  if (!date) return

  savingExpiresId.value = user.id
  try {
    const { data } = await window.axios.put(`/api/users/${user.id}`, {
      name: user.name,
      email: user.email,
      role: user.role,
      username: user.username,
      program_category: user.program_category,
      app_expires_at: dateInputToExpiresAt(date),
    })
    const idx = users.value.findIndex((u) => u.id === user.id)
    if (idx !== -1) users.value[idx] = data
    delete expiresDraft.value[user.id]
    toast.success('Success', t('users.toastUpdated'))
  } catch (e) {
    toast.error('Error', formatApiError(e))
  } finally {
    savingExpiresId.value = null
  }
}

const saveUser = async (formData) => {
  try {
    if (editingUser.value) {
      const { data } = await window.axios.put(`/api/users/${editingUser.value.id}`, formData)
      const idx = users.value.findIndex(u => u.id === editingUser.value.id)
      if (idx !== -1) users.value[idx] = data
      toast.success('Success', t('users.toastUpdated'))
    } else {
      const { data } = await window.axios.post('/api/users', formData)
      // Reload to show new user in correct order/page
      loadUsers(currentPage.value)
      toast.success('Success', t('users.toastCreated'))
    }
    closeModal()
  } catch (e) {
    toast.error('Error', formatApiError(e))
  }
}

onMounted(() => {
  loadUsers()
})
</script>

<style scoped>
</style>
