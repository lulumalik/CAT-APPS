<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      :title="t('batches.title')"
      :subtitle="t('batches.subtitle')"
      theme="purple"
      :icon="Layers"
    >
      <template #actions>
        <div class="relative">
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="t('batches.searchPlaceholder')"
            class="rounded-full border border-gray-200 bg-white px-4 py-2 pl-10 text-sm focus:border-[#9DB359] focus:ring-[#9DB359]"
            @input="handleSearch"
          />
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8" /><line x1="21" y1="21" x2="16.65" y2="16.65" /></svg>
        </div>
        <button
          type="button"
          class="px-6 py-2 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 shadow-lg shadow-black/10 flex items-center gap-2 text-sm font-medium"
          @click="openCreate"
        >
          {{ t('batches.addBatch') }}
        </button>
      </template>
    </PageHeroHeader>

    <p class="mb-6 text-sm text-gray-500">
      {{ t('batches.helpText') }}
      <router-link to="/users" class="text-[#9DB359] font-medium hover:underline">{{ t('nav.users') }}</router-link>
    </p>

    <div v-if="loading" class="space-y-4">
      <div v-for="n in 4" :key="n" class="h-24 animate-pulse rounded-[2rem] bg-white shadow-sm" />
    </div>

    <div v-else-if="!batches.length" class="rounded-[2rem] border border-gray-100 bg-white p-12 text-center text-gray-500 text-sm">
      {{ t('batches.empty') }}
    </div>

    <div v-else class="grid gap-4 md:grid-cols-2">
      <article
        v-for="batch in batches"
        :key="batch.id"
        class="rounded-[1.75rem] border border-gray-100 bg-white p-6 shadow-lg shadow-black/5 hover:shadow-xl transition-shadow"
      >
        <div class="flex items-start justify-between gap-3">
          <div>
            <h2 class="text-lg font-bold text-[#1A1A1A]">{{ batch.name }}</h2>
            <p v-if="batch.code" class="mt-1 text-xs text-gray-500">{{ batch.code }}</p>
          </div>
          <span
            class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold"
            :class="batch.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-50 text-gray-500 border border-gray-200'"
          >
            {{ batch.is_active ? t('batches.active') : t('batches.inactive') }}
          </span>
        </div>
        <p class="mt-3 text-sm text-gray-600">{{ formatRange(batch) }}</p>
        <div class="mt-4 flex flex-wrap gap-3 text-xs text-gray-500">
          <span>{{ t('batches.studentCount', { count: batch.students_count || 0 }) }}</span>
          <span>·</span>
          <span>{{ t('batches.classCount', { count: batch.bimble_classes_count || 0 }) }}</span>
        </div>
        <div class="mt-5 flex flex-wrap gap-2">
          <button type="button" class="rounded-full border border-[#9DB359] px-4 py-2 text-sm font-semibold text-[#5a6b2e] hover:bg-[#9DB359]/10" @click="openManage(batch)">
            {{ t('batches.manage') }}
          </button>
          <button type="button" class="rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50" @click="openEdit(batch)">
            {{ t('common.edit') }}
          </button>
          <button type="button" class="rounded-full border border-red-100 px-4 py-2 text-sm font-medium text-red-500 hover:bg-red-50" @click="removeBatch(batch)">
            {{ t('common.delete') }}
          </button>
        </div>
      </article>
    </div>

    <!-- Create / Edit modal -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="closeForm">
      <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
        <h3 class="mb-4 text-lg font-bold">{{ editingBatch ? t('batches.editTitle') : t('batches.createTitle') }}</h3>
        <form class="space-y-3" @submit.prevent="saveBatch">
          <div>
            <label class="text-sm font-medium text-gray-700">{{ t('batches.nameLabel') }}</label>
            <input v-model="form.name" required class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          </div>
          <div>
            <label class="text-sm font-medium text-gray-700">{{ t('batches.codeLabel') }}</label>
            <input v-model="form.code" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" :placeholder="t('batches.codePlaceholder')" />
          </div>
          <div class="grid grid-cols-2 gap-2">
            <div>
              <label class="text-sm font-medium text-gray-700">{{ t('batches.startsOn') }}</label>
              <input v-model="form.starts_on" type="date" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
            </div>
            <div>
              <label class="text-sm font-medium text-gray-700">{{ t('batches.endsOn') }}</label>
              <input v-model="form.ends_on" type="date" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
            </div>
          </div>
          <label class="flex items-center gap-2 text-sm text-gray-700">
            <input v-model="form.is_active" type="checkbox" class="rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359]" />
            {{ t('batches.active') }}
          </label>
          <div>
            <label class="text-sm font-medium text-gray-700">{{ t('batches.notesLabel') }}</label>
            <textarea v-model="form.notes" rows="2" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="rounded-full border border-gray-200 px-4 py-2 text-sm" @click="closeForm">{{ t('common.cancel') }}</button>
            <button type="submit" class="rounded-full bg-[#1A1A1A] px-4 py-2 text-sm font-semibold text-white" :disabled="saving">
              {{ saving ? '…' : t('common.save') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Manage roster modal -->
    <div v-if="showManage && managedBatch" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="closeManage">
      <div class="max-h-[92vh] w-full max-w-5xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl">
        <div class="mb-5 flex items-start justify-between gap-3">
          <div>
            <h3 class="text-xl font-bold text-[#1A1A1A]">{{ managedBatch.name }}</h3>
            <p class="text-xs text-gray-500">{{ t('batches.manageHint') }}</p>
          </div>
          <button type="button" class="rounded-full border border-gray-200 px-3 py-1.5 text-sm" @click="closeManage">{{ t('common.close') }}</button>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
          <section class="rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
            <h4 class="font-semibold text-[#1A1A1A]">{{ t('batches.rosterTitle') }}</h4>
            <p class="mt-1 mb-3 text-xs text-gray-500">{{ t('batches.rosterHint') }}</p>
            <input
              v-model="studentSearch"
              type="text"
              :placeholder="t('batches.studentSearch')"
              class="mb-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm"
              @input="searchStudents"
            />
            <select v-model="selectedStudentId" class="mb-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
              <option :value="null">{{ t('batches.pickStudent') }}</option>
              <option v-for="s in studentOptions" :key="s.id" :value="s.id">
                {{ s.name }}{{ s.username ? ` (@${s.username})` : '' }}
              </option>
            </select>
            <div class="mb-2">
              <label class="text-xs text-gray-500">{{ t('batches.setExpiresOptional') }}</label>
              <input v-model="attachExpires" type="date" class="mt-1 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm" />
            </div>
            <button type="button" class="w-full rounded-xl bg-[#1A1A1A] py-2 text-sm font-medium text-white" @click="attachStudent">
              {{ t('batches.addStudent') }}
            </button>

            <ul class="mt-4 max-h-80 space-y-2 overflow-y-auto rounded-xl border border-gray-100 bg-white p-3">
              <li
                v-for="s in managedBatch.students || []"
                :key="s.id"
                class="flex flex-col gap-2 border-b border-gray-50 pb-3 last:border-b-0 last:pb-0 sm:flex-row sm:items-center sm:justify-between"
              >
                <div class="min-w-0">
                  <p class="truncate text-sm font-medium text-[#1A1A1A]">{{ s.name }}</p>
                  <p class="truncate text-xs text-gray-400">{{ s.username ? `@${s.username}` : s.email }}</p>
                  <p class="text-xs text-gray-500">{{ formatExpires(s.app_expires_at) }}</p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                  <input
                    v-model="expiresDraft[s.id]"
                    type="date"
                    class="w-32 rounded-lg border border-gray-200 px-2 py-1 text-xs"
                  />
                  <button
                    type="button"
                    class="rounded-lg bg-[#9DB359] px-2 py-1 text-xs font-semibold text-white disabled:opacity-50"
                    :disabled="savingExpiresId === s.id"
                    @click="saveExpires(s)"
                  >
                    {{ t('users.expiresSave') }}
                  </button>
                  <button type="button" class="text-xs text-red-500" @click="detachStudent(s.id)">{{ t('common.delete') }}</button>
                </div>
              </li>
              <li v-if="!(managedBatch.students || []).length" class="text-xs text-gray-400">{{ t('batches.noStudents') }}</li>
            </ul>
          </section>

          <section class="rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
            <h4 class="font-semibold text-[#1A1A1A]">{{ t('batches.classesTitle') }}</h4>
            <p class="mt-1 mb-3 text-xs text-gray-500">{{ t('batches.classesHint') }}</p>
            <select v-model="selectedClassId" class="mb-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
              <option :value="null">{{ t('batches.pickClass') }}</option>
              <option v-for="c in availableClasses" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <button type="button" class="w-full rounded-xl bg-[#1A1A1A] py-2 text-sm font-medium text-white" @click="attachClass">
              {{ t('batches.linkClass') }}
            </button>

            <ul class="mt-4 space-y-2 rounded-xl border border-gray-100 bg-white p-3">
              <li
                v-for="c in managedBatch.bimble_classes || []"
                :key="c.id"
                class="flex items-center justify-between gap-2 border-b border-gray-50 pb-2 text-sm last:border-b-0 last:pb-0"
              >
                <span class="truncate font-medium">{{ c.name }}</span>
                <div class="flex gap-2">
                  <button type="button" class="text-xs text-[#9DB359]" @click="resyncClass(c.id)">{{ t('batches.resync') }}</button>
                  <button type="button" class="text-xs text-red-500" @click="detachClass(c.id)">{{ t('common.delete') }}</button>
                </div>
              </li>
              <li v-if="!(managedBatch.bimble_classes || []).length" class="text-xs text-gray-400">{{ t('batches.noClasses') }}</li>
            </ul>
          </section>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { Layers } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useI18n } from '@/composables/useI18n'
import { useModal, useToast } from '@/composables/useNotification'
import { dateInputToExpiresAt, formatAppExpiresAt, toDateInputValue } from '@/utils/userMeta'

const { t } = useI18n()
const toast = useToast()
const { confirm } = useModal()

const loading = ref(false)
const saving = ref(false)
const batches = ref([])
const searchQuery = ref('')
let searchTimeout = null

const showForm = ref(false)
const editingBatch = ref(null)
const form = reactive({
  name: '',
  code: '',
  starts_on: '',
  ends_on: '',
  is_active: true,
  notes: '',
})

const showManage = ref(false)
const managedBatch = ref(null)
const studentSearch = ref('')
const studentOptions = ref([])
const selectedStudentId = ref(null)
const attachExpires = ref('')
const classOptions = ref([])
const selectedClassId = ref(null)
const expiresDraft = ref({})
const savingExpiresId = ref(null)

const availableClasses = computed(() => {
  const linked = new Set((managedBatch.value?.bimble_classes || []).map((c) => c.id))
  return classOptions.value.filter((c) => !linked.has(c.id))
})

function formatRange(batch) {
  if (batch.starts_on && batch.ends_on) return `${batch.starts_on} s/d ${batch.ends_on}`
  if (batch.starts_on) return `Mulai ${batch.starts_on}`
  return t('batches.periodUnset')
}

function formatExpires(value) {
  return formatAppExpiresAt(value) || t('users.expiresNotSet')
}

function handleSearch() {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => loadBatches(), 300)
}

async function loadBatches() {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/batches', {
      params: { search: searchQuery.value || undefined },
    })
    batches.value = Array.isArray(data) ? data : []
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastLoadFailed'))
  } finally {
    loading.value = false
  }
}

function resetForm() {
  form.name = ''
  form.code = ''
  form.starts_on = ''
  form.ends_on = ''
  form.is_active = true
  form.notes = ''
}

function openCreate() {
  editingBatch.value = null
  resetForm()
  showForm.value = true
}

function openEdit(batch) {
  editingBatch.value = batch
  form.name = batch.name || ''
  form.code = batch.code || ''
  form.starts_on = batch.starts_on || ''
  form.ends_on = batch.ends_on || ''
  form.is_active = !!batch.is_active
  form.notes = batch.notes || ''
  showForm.value = true
}

function closeForm() {
  showForm.value = false
  editingBatch.value = null
}

async function saveBatch() {
  saving.value = true
  try {
    const payload = {
      name: form.name,
      code: form.code || null,
      starts_on: form.starts_on || null,
      ends_on: form.ends_on || null,
      is_active: form.is_active,
      notes: form.notes || null,
    }
    if (editingBatch.value) {
      await window.axios.put(`/api/batches/${editingBatch.value.id}`, payload)
      toast.success('Success', t('batches.toastUpdated'))
    } else {
      await window.axios.post('/api/batches', payload)
      toast.success('Success', t('batches.toastCreated'))
    }
    closeForm()
    await loadBatches()
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastSaveFailed'))
  } finally {
    saving.value = false
  }
}

async function removeBatch(batch) {
  const ok = await confirm({
    title: t('batches.deleteTitle'),
    message: t('batches.deleteMessage', { name: batch.name }),
    confirmText: t('common.delete'),
    type: 'danger',
  })
  if (!ok) return
  try {
    await window.axios.delete(`/api/batches/${batch.id}`)
    toast.success('Success', t('batches.toastDeleted'))
    await loadBatches()
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastDeleteFailed'))
  }
}

async function openManage(batch) {
  showManage.value = true
  managedBatch.value = null
  selectedStudentId.value = null
  selectedClassId.value = null
  attachExpires.value = ''
  expiresDraft.value = {}
  try {
    const [detail, classes] = await Promise.all([
      window.axios.get(`/api/batches/${batch.id}`),
      window.axios.get('/api/bimble-classes'),
    ])
    managedBatch.value = detail.data
    classOptions.value = Array.isArray(classes.data) ? classes.data : []
    for (const s of managedBatch.value.students || []) {
      expiresDraft.value[s.id] = toDateInputValue(s.app_expires_at)
    }
    await searchStudents()
  } catch (e) {
    showManage.value = false
    toast.error('Error', e?.response?.data?.message || t('batches.toastLoadFailed'))
  }
}

function closeManage() {
  showManage.value = false
  managedBatch.value = null
}

async function searchStudents() {
  try {
    const { data } = await window.axios.get('/api/students/search', {
      params: {
        search: studentSearch.value || undefined,
      },
    })
    const already = new Set((managedBatch.value?.students || []).map((s) => s.id))
    studentOptions.value = (Array.isArray(data) ? data : []).filter((s) => !already.has(s.id))
  } catch {
    studentOptions.value = []
  }
}

async function attachStudent() {
  if (!managedBatch.value?.id || !selectedStudentId.value) return
  try {
    const { data } = await window.axios.post(`/api/batches/${managedBatch.value.id}/students`, {
      user_id: selectedStudentId.value,
      app_expires_at: dateInputToExpiresAt(attachExpires.value) || undefined,
    })
    managedBatch.value = data.batch
    selectedStudentId.value = null
    attachExpires.value = ''
    for (const s of managedBatch.value.students || []) {
      expiresDraft.value[s.id] = toDateInputValue(s.app_expires_at)
    }
    const attached = data.auto_assigned?.attached || 0
    toast.success('Success', t('batches.toastStudentAdded', { count: attached }))
    await searchStudents()
    await loadBatches()
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastSaveFailed'))
  }
}

async function detachStudent(userId) {
  if (!managedBatch.value?.id) return
  try {
    const { data } = await window.axios.delete(`/api/batches/${managedBatch.value.id}/students/${userId}`)
    managedBatch.value = data.batch
    await searchStudents()
    await loadBatches()
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastSaveFailed'))
  }
}

async function saveExpires(student) {
  if (!managedBatch.value?.id) return
  savingExpiresId.value = student.id
  try {
    await window.axios.patch(`/api/batches/${managedBatch.value.id}/students/${student.id}/expires`, {
      app_expires_at: dateInputToExpiresAt(expiresDraft.value[student.id]),
    })
    const { data } = await window.axios.get(`/api/batches/${managedBatch.value.id}`)
    managedBatch.value = data
    toast.success('Success', t('users.toastUpdated'))
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('users.toastSaveFailed'))
  } finally {
    savingExpiresId.value = null
  }
}

async function attachClass() {
  if (!managedBatch.value?.id || !selectedClassId.value) return
  try {
    const { data } = await window.axios.post(`/api/batches/${managedBatch.value.id}/classes`, {
      bimble_class_id: selectedClassId.value,
    })
    managedBatch.value = data.batch
    selectedClassId.value = null
    const attached = data.auto_assigned?.attached || 0
    toast.success('Success', t('batches.toastClassLinked', { count: attached }))
    await loadBatches()
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastSaveFailed'))
  }
}

async function detachClass(classId) {
  if (!managedBatch.value?.id) return
  try {
    const { data } = await window.axios.delete(`/api/batches/${managedBatch.value.id}/classes/${classId}`)
    managedBatch.value = data.batch
    await loadBatches()
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastSaveFailed'))
  }
}

async function resyncClass(classId) {
  if (!managedBatch.value?.id) return
  try {
    const { data } = await window.axios.post(`/api/batches/${managedBatch.value.id}/classes/${classId}/sync`)
    const attached = data.auto_assigned?.attached || 0
    toast.success('Success', t('batches.toastSynced', { count: attached }))
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastSaveFailed'))
  }
}

onMounted(loadBatches)
</script>
