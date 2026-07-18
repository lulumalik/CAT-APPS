<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      :title="t('users.title')"
      :subtitle="t('users.subtitleSlim')"
      theme="purple"
      :icon="Users"
    >
      <template #actions>
        <router-link
          to="/batches"
          class="px-4 py-2 rounded-full border border-[#9DB359]/40 bg-[#9DB359]/10 text-[#5a6b2e] text-sm font-semibold hover:bg-[#9DB359]/20"
        >
          {{ t('nav.batches') }}
        </router-link>
        <div class="relative">
          <input v-model="searchQuery" @input="handleSearch" type="text" :placeholder="t('users.searchPlaceholder')" class="rounded-full border border-gray-200 bg-white px-4 py-2 pl-10 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors" />
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        </div>
        <button class="px-6 py-2 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2" @click="openAdd">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
          {{ t('users.addUser') }}
        </button>
      </template>
    </PageHeroHeader>

    <div v-if="loading" class="space-y-4">
      <div v-for="n in 5" :key="n" class="h-16 w-full animate-pulse bg-white rounded-[2rem] shadow-sm"></div>
    </div>

    <div v-else class="bg-white shadow-xl shadow-black/5 rounded-[2rem] border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
          <thead class="bg-gray-50/50">
            <tr>
              <th scope="col" class="px-6 py-4 text-left">
                <button
                  type="button"
                  class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider hover:text-gray-800"
                  :class="sortBy === 'name' ? 'text-[#5a6b2e]' : 'text-gray-500'"
                  @click="toggleSort('name')"
                >
                  {{ t('users.tableName') }}
                  <span class="inline-flex flex-col leading-none">
                    <ChevronUp
                      class="h-3.5 w-3.5"
                      :stroke-width="sortBy === 'name' && sortDir === 'asc' ? 3 : 1.75"
                      :class="sortBy === 'name' && sortDir === 'asc' ? 'text-[#9DB359]' : 'text-gray-300'"
                    />
                    <ChevronDown
                      class="h-3.5 w-3.5 -mt-1"
                      :stroke-width="sortBy === 'name' && sortDir === 'desc' ? 3 : 1.75"
                      :class="sortBy === 'name' && sortDir === 'desc' ? 'text-[#9DB359]' : 'text-gray-300'"
                    />
                  </span>
                </button>
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableEmail') }}</th>
              <th scope="col" class="px-6 py-4 text-left">
                <button
                  ref="roleFilterBtn"
                  type="button"
                  data-role-filter
                  class="inline-flex items-center gap-1 text-xs font-semibold uppercase tracking-wider hover:text-gray-800"
                  :class="roleFilter ? 'text-[#9DB359]' : 'text-gray-500'"
                  @click.stop="toggleRoleMenu"
                >
                  {{ roleFilterLabel }}
                  <ChevronDown class="h-3.5 w-3.5" />
                </button>
              </th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableBatch') }}</th>
              <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableExpires') }}</th>
              <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ t('users.tableActions') }}</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-50">
            <tr v-if="users.length === 0">
              <td colspan="6" class="px-8 py-12 text-center text-sm text-gray-500">
                {{ t('users.noUsers') }}
              </td>
            </tr>
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50/50 transition-colors">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="h-8 w-8 rounded-full bg-[#9DB359]/10 text-[#9DB359] flex items-center justify-center font-bold text-xs mr-3">
                    {{ user.name.charAt(0).toUpperCase() }}
                  </div>
                  <div>
                    <div class="text-sm font-medium text-[#1A1A1A]">{{ user.name }}</div>
                    <div v-if="user.username" class="text-xs text-gray-400">@{{ user.username }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-500">{{ user.email }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full capitalize"
                      :class="{
                        'bg-purple-50 text-purple-700 border border-purple-100': user.role === 'admin',
                        'bg-blue-50 text-blue-700 border border-blue-100': user.role === 'mentor',
                        'bg-green-50 text-green-700 border border-green-100': user.role === 'user'
                      }">
                  {{ roleLabel(user.role) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div v-if="user.role === 'user' && (user.batches || []).length" class="flex flex-wrap gap-1">
                  <span
                    v-for="b in user.batches"
                    :key="b.id"
                    class="px-2 py-0.5 text-[11px] font-semibold rounded-full bg-gray-50 text-gray-600 border border-gray-200"
                  >
                    {{ b.name }}
                  </span>
                </div>
                <span v-else-if="user.role === 'user'" class="text-xs text-gray-400">{{ t('users.noBatch') }}</span>
                <span v-else class="text-sm text-gray-400">—</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div v-if="user.role !== 'user'" class="text-sm text-gray-400">—</div>
                <div v-else class="space-y-1.5 max-w-[10rem]">
                  <input
                    v-model="expiresDraft[user.id]"
                    type="date"
                    class="w-full rounded-lg border border-gray-200 bg-gray-50 px-2 py-1.5 text-sm focus:bg-white focus:border-[#9DB359] focus:ring-0"
                  />
                  <div class="flex items-center gap-2">
                    <button
                      type="button"
                      :disabled="savingExpiresId === user.id"
                      class="px-2.5 py-1 rounded-lg text-[11px] font-semibold text-white bg-[#9DB359] hover:bg-[#8ca34b] disabled:opacity-50"
                      @click="saveExpires(user)"
                    >
                      {{ savingExpiresId === user.id ? '…' : t('users.expiresSave') }}
                    </button>
                    <span
                      v-if="user.app_expires_at"
                      class="px-1.5 py-0.5 text-[10px] font-semibold rounded-full"
                      :class="isUserExpired(user) ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700'"
                    >
                      {{ isUserExpired(user) ? t('users.expiresExpired') : t('users.expiresActive') }}
                    </span>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="inline-flex items-center justify-end gap-1">
                  <button
                    v-if="user.role === 'user'"
                    type="button"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-600 hover:bg-[#9DB359]/15 hover:text-[#5a6b2e] transition-colors"
                    :title="t('users.studentDashboard')"
                    @click="openDashboardPopup(user)"
                  >
                    <LayoutDashboard class="h-4 w-4" />
                  </button>
                  <button
                    type="button"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-[#9DB359] hover:bg-[#9DB359]/15 hover:text-[#8ca34b] transition-colors"
                    :title="t('common.edit')"
                    @click="edit(user)"
                  >
                    <Pencil class="h-4 w-4" />
                  </button>
                  <button
                    type="button"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-full text-red-500 hover:bg-red-50 hover:text-red-700 transition-colors"
                    :title="t('common.delete')"
                    @click="remove(user)"
                  >
                    <Trash2 class="h-4 w-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

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

    <UserModal v-if="showModal" :initial="editingUser" @close="closeModal" @submit="saveUser" />

    <Teleport to="body">
      <div
        v-if="roleMenuOpen"
        data-role-filter
        class="fixed z-[80] w-44 rounded-xl border border-gray-100 bg-white py-1 shadow-xl"
        :style="roleMenuStyle"
        @click.stop
      >
        <button
          v-for="opt in roleOptions"
          :key="opt.value || 'all'"
          type="button"
          class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-50"
          :class="roleFilter === opt.value ? 'font-semibold text-[#9DB359]' : 'text-gray-700'"
          @click="selectRole(opt.value)"
        >
          {{ opt.label }}
        </button>
      </div>
    </Teleport>

    <!-- Dashboard siswa popup -->
    <div v-if="dashboardUser" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="dashboardUser = null">
      <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl">
        <div class="flex items-start gap-3">
          <div class="flex h-10 w-10 items-center justify-center rounded-full bg-[#9DB359]/15 text-[#5a6b2e]">
            <LayoutDashboard class="h-5 w-5" />
          </div>
          <div class="min-w-0 flex-1">
            <h3 class="text-lg font-bold text-[#1A1A1A]">{{ t('users.studentDashboard') }}</h3>
            <p class="mt-1 text-sm text-gray-600 truncate">{{ dashboardUser.name }}</p>
            <p v-if="dashboardUser.username" class="text-xs text-gray-400">@{{ dashboardUser.username }}</p>
          </div>
        </div>
        <p class="mt-4 text-sm text-gray-500">{{ t('users.studentDashboardHint') }}</p>
        <div class="mt-5 flex justify-end gap-2">
          <button type="button" class="rounded-full border border-gray-200 px-4 py-2 text-sm" @click="dashboardUser = null">
            {{ t('common.cancel') }}
          </button>
          <router-link
            :to="`/dashboard/student/${dashboardUser.id}`"
            class="rounded-full bg-[#1A1A1A] px-4 py-2 text-sm font-semibold text-white"
            @click="dashboardUser = null"
          >
            {{ t('users.openDashboard') }}
          </router-link>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from 'vue'
import { ChevronDown, ChevronUp, LayoutDashboard, Pencil, Trash2, Users } from 'lucide-vue-next'
import UserModal from '@/components/UserModal.vue'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useModal, useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'
import { dateInputToExpiresAt, isAppExpired, toDateInputValue } from '@/utils/userMeta'

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
const dashboardUser = ref(null)
const importFile = ref(null)

const searchQuery = ref('')
const roleFilter = ref('')
const roleMenuOpen = ref(false)
const roleFilterBtn = ref(null)
const roleMenuStyle = ref({})
const sortBy = ref('created_at')
const sortDir = ref('desc')
const expiresDraft = ref({})
const savingExpiresId = ref(null)
let searchTimeout = null

const roleOptions = computed(() => [
  { value: '', label: t('users.filterByRole') },
  { value: 'admin', label: t('users.roleAdmin') },
  { value: 'user', label: t('users.roleUser') },
  { value: 'mentor', label: t('users.roleMentor') },
])

const roleFilterLabel = computed(() => {
  const found = roleOptions.value.find((o) => o.value === roleFilter.value)
  return found?.label || t('users.filterByRole')
})

const rangeFrom = computed(() => {
  if (totalUsers.value === 0) return 0
  return (currentPage.value - 1) * perPage + 1
})
const rangeTo = computed(() => {
  if (totalUsers.value === 0) return 0
  return Math.min(currentPage.value * perPage, totalUsers.value)
})

const isUserExpired = (user) => isAppExpired(user)

const roleLabel = (role) => {
  const map = {
    admin: t('users.roleAdmin'),
    user: t('users.roleUser'),
    mentor: t('users.roleMentor'),
  }
  return map[role] || role
}

const handleSearch = () => {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => loadUsers(1), 300)
}

const updateRoleMenuPosition = () => {
  const el = roleFilterBtn.value
  if (!el) return
  const rect = el.getBoundingClientRect()
  roleMenuStyle.value = {
    top: `${rect.bottom + 8}px`,
    left: `${rect.left}px`,
  }
}

const toggleRoleMenu = async () => {
  roleMenuOpen.value = !roleMenuOpen.value
  if (roleMenuOpen.value) {
    await nextTick()
    updateRoleMenuPosition()
  }
}

const selectRole = (value) => {
  roleFilter.value = value
  roleMenuOpen.value = false
  loadUsers(1)
}

const toggleSort = (column) => {
  if (sortBy.value === column) {
    sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortDir.value = 'asc'
  }
  loadUsers(1)
}

const closeMenus = () => {
  roleMenuOpen.value = false
}

const onDocClick = (e) => {
  if (!e.target.closest('[data-role-filter]')) closeMenus()
}

const onReposition = () => {
  if (roleMenuOpen.value) updateRoleMenuPosition()
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
        sort_by: sortBy.value,
        sort_dir: sortDir.value,
      },
    })
    users.value = data.data || []
    currentPage.value = data.current_page || 1
    lastPage.value = data.last_page || 1
    totalUsers.value = data.total || 0
    const nextDraft = { ...expiresDraft.value }
    for (const u of users.value) {
      if (u.role === 'user') nextDraft[u.id] = toDateInputValue(u.app_expires_at)
    }
    expiresDraft.value = nextDraft
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

const openDashboardPopup = (user) => {
  dashboardUser.value = user
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
  savingExpiresId.value = user.id
  try {
    const { data } = await window.axios.put(`/api/users/${user.id}`, {
      name: user.name,
      email: user.email,
      role: user.role,
      username: user.username,
      program_category: user.program_category,
      app_expires_at: dateInputToExpiresAt(expiresDraft.value[user.id]),
    })
    const idx = users.value.findIndex((u) => u.id === user.id)
    if (idx !== -1) users.value[idx] = { ...users.value[idx], ...data, batches: users.value[idx].batches }
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
      if (idx !== -1) users.value[idx] = { ...users.value[idx], ...data }
      if (data.role === 'user') expiresDraft.value[data.id] = toDateInputValue(data.app_expires_at)
      toast.success('Success', t('users.toastUpdated'))
    } else {
      await window.axios.post('/api/users', formData)
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
  document.addEventListener('click', onDocClick)
  window.addEventListener('resize', onReposition)
  window.addEventListener('scroll', onReposition, true)
})

onUnmounted(() => {
  document.removeEventListener('click', onDocClick)
  window.removeEventListener('resize', onReposition)
  window.removeEventListener('scroll', onReposition, true)
})
</script>
