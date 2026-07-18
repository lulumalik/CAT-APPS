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

    <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
        <div class="mt-5 flex flex-wrap items-center gap-2">
          <router-link
            :to="{ name: 'batch-detail', params: { id: batch.id } }"
            class="rounded-full border border-[#9DB359] px-4 py-2 text-sm font-semibold text-[#5a6b2e] hover:bg-[#9DB359]/10"
          >
            {{ t('batches.manage') }}
          </router-link>
          <button
            type="button"
            class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors"
            :title="t('common.edit')"
            @click="openEdit(batch)"
          >
            <Pencil class="h-4 w-4" />
          </button>
          <button
            type="button"
            class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-red-100 text-red-500 hover:bg-red-50 transition-colors"
            :title="t('common.delete')"
            @click="removeBatch(batch)"
          >
            <Trash2 class="h-4 w-4" />
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
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { Layers, Pencil, Trash2 } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useI18n } from '@/composables/useI18n'
import { useModal, useToast } from '@/composables/useNotification'

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

function formatRange(batch) {
  if (batch.starts_on && batch.ends_on) return `${batch.starts_on} s/d ${batch.ends_on}`
  if (batch.starts_on) return `Mulai ${batch.starts_on}`
  return t('batches.periodUnset')
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

onMounted(loadBatches)
</script>
