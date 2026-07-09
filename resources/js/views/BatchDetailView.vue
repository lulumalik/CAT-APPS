<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div class="mb-6">
      <router-link
        :to="backLink"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500 hover:text-[#5a6b2e]"
      >
        <ArrowLeft class="h-4 w-4" />
        {{ isAdmin ? t('batches.backToList') : t('batches.backToClasses') }}
      </router-link>
    </div>

    <PageHeroHeader
      v-if="batch"
      :title="batch.name"
      :subtitle="batchSubtitle"
      theme="purple"
      :icon="Layers"
    />

    <div v-if="loading" class="py-16 text-center text-gray-500 text-sm">{{ t('common.refresh') }}…</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">
      {{ errorMessage }}
    </div>

    <div v-else-if="batch" class="mt-6 flex flex-col gap-4 lg:flex-row lg:items-stretch">
      <section class="flex-1 rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
        <h4 class="font-semibold text-[#1A1A1A]">{{ t('batches.addStudent') }}</h4>
        <p class="mt-1 mb-3 text-xs text-gray-500">{{ t('batches.rosterHint') }}</p>

        <input
          v-model="pickerSearch"
          type="text"
          :placeholder="t('batches.studentSearch')"
          class="mb-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm"
          @input="onPickerSearch"
        />
        <select v-model="selectedStudentId" class="mb-2 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm">
          <option :value="null">{{ t('batches.pickStudent') }}</option>
          <option v-for="s in studentOptions" :key="s.id" :value="s.id">
            {{ s.name }}{{ s.username ? ` (@${s.username})` : '' }}
          </option>
        </select>
        <p v-if="pickerSearch && !studentOptions.length" class="mb-2 text-[11px] text-amber-600">
          {{ t('batches.noEligibleStudents') }}
        </p>
        <button type="button" class="w-full rounded-xl bg-[#1A1A1A] py-2 text-sm font-medium text-white" @click="attachStudent">
          {{ t('batches.addStudent') }}
        </button>
      </section>

      <section class="flex-1 rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
        <h4 class="font-semibold text-[#1A1A1A]">{{ t('batches.rosterTitle') }}</h4>
        <p class="mt-1 mb-3 text-xs text-gray-500">{{ t('batches.rosterListHint') }}</p>

        <input
          v-model="rosterSearch"
          type="text"
          :placeholder="t('batches.rosterSearch')"
          class="mb-3 w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm"
          @input="onRosterSearch"
        />

        <ul class="min-h-[16rem] space-y-2 rounded-xl border border-gray-100 bg-white p-3">
          <li v-if="rosterLoading" class="py-6 text-center text-xs text-gray-400">{{ t('common.refresh') }}…</li>
          <li
            v-for="s in rosterStudents"
            :key="s.id"
            class="flex items-center justify-between gap-3 border-b border-gray-50 pb-2 last:border-b-0 last:pb-0"
          >
            <div class="min-w-0">
              <p class="truncate text-sm font-medium text-[#1A1A1A]">{{ s.name }}</p>
              <p class="truncate text-xs text-gray-400">{{ s.username ? `@${s.username}` : s.email }}</p>
            </div>
            <div class="flex shrink-0 items-center gap-1">
              <router-link
                v-if="isAdmin"
                :to="{ name: 'student-dashboard', params: { id: s.id } }"
                class="inline-flex items-center justify-center w-7 h-7 rounded-full text-gray-600 hover:bg-[#9DB359]/15 hover:text-[#5a6b2e]"
                :title="t('users.studentDashboard')"
              >
                <LayoutDashboard class="h-3.5 w-3.5" />
              </router-link>
              <button
                v-if="isAdmin"
                type="button"
                class="inline-flex items-center justify-center w-7 h-7 rounded-full text-red-500 hover:bg-red-50 hover:text-red-700"
                :title="t('common.delete')"
                @click="detachStudent(s.id)"
              >
                <Trash2 class="h-3.5 w-3.5" />
              </button>
            </div>
          </li>
          <li v-if="!rosterLoading && !rosterStudents.length" class="py-4 text-center text-xs text-gray-400">
            {{ t('batches.noStudents') }}
          </li>
        </ul>

        <div v-if="rosterLastPage > 0" class="mt-3 flex items-center justify-between gap-2">
          <p class="text-xs text-gray-500">
            {{ t('batches.rosterPageInfo', { page: rosterPage, total: rosterLastPage, count: rosterTotal }) }}
          </p>
          <div class="flex gap-2">
            <button
              type="button"
              class="rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium disabled:opacity-50"
              :disabled="rosterPage <= 1 || rosterLoading"
              @click="loadRoster(rosterPage - 1)"
            >
              {{ t('common.previous') }}
            </button>
            <button
              type="button"
              class="rounded-full border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium disabled:opacity-50"
              :disabled="rosterPage >= rosterLastPage || rosterLoading"
              @click="loadRoster(rosterPage + 1)"
            >
              {{ t('common.next') }}
            </button>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { ArrowLeft, Layers, LayoutDashboard, Trash2 } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useI18n } from '@/composables/useI18n'
import { useToast } from '@/composables/useNotification'
import { useAppStore } from '@/stores/app'

const { t } = useI18n()
const toast = useToast()
const route = useRoute()
const store = useAppStore()

const isAdmin = computed(() => store.role === 'admin')
const backLink = computed(() => (isAdmin.value ? { name: 'batches' } : { name: 'bimble-classes' }))

const loading = ref(true)
const errorMessage = ref('')
const batch = ref(null)

const pickerSearch = ref('')
const studentOptions = ref([])
const selectedStudentId = ref(null)
let pickerTimeout = null

const rosterStudents = ref([])
const rosterSearch = ref('')
const rosterPage = ref(1)
const rosterLastPage = ref(1)
const rosterTotal = ref(0)
const rosterLoading = ref(false)
const rosterPerPage = 10
let rosterTimeout = null

const batchSubtitle = computed(() => {
  if (!batch.value) return ''
  const parts = [t('batches.manageHint')]
  if (batch.value.code) parts.unshift(batch.value.code)
  return parts.join(' · ')
})

function onPickerSearch() {
  if (pickerTimeout) clearTimeout(pickerTimeout)
  pickerTimeout = setTimeout(() => searchStudents(), 300)
}

function onRosterSearch() {
  if (rosterTimeout) clearTimeout(rosterTimeout)
  rosterTimeout = setTimeout(() => loadRoster(1), 300)
}

async function loadBatch() {
  loading.value = true
  errorMessage.value = ''
  try {
    const { data } = await window.axios.get(`/api/batches/${route.params.id}`)
    batch.value = data
    await Promise.all([searchStudents(), loadRoster(1)])
  } catch (e) {
    batch.value = null
    errorMessage.value = e?.response?.data?.message || t('batches.toastLoadFailed')
  } finally {
    loading.value = false
  }
}

async function searchStudents() {
  try {
    const { data } = await window.axios.get('/api/students/search', {
      params: {
        search: pickerSearch.value || undefined,
        exclude_batch_id: batch.value?.id || undefined,
        batch_eligible: 1,
      },
    })
    studentOptions.value = Array.isArray(data) ? data : []
  } catch {
    studentOptions.value = []
  }
}

async function loadRoster(page = 1) {
  if (!batch.value?.id) return
  rosterLoading.value = true
  try {
    const { data } = await window.axios.get(`/api/batches/${batch.value.id}/students`, {
      params: {
        page,
        per_page: rosterPerPage,
        search: rosterSearch.value || undefined,
      },
    })
    rosterStudents.value = data.data || []
    rosterPage.value = data.current_page || 1
    rosterLastPage.value = data.last_page || 1
    rosterTotal.value = data.total || 0
    if (batch.value) {
      batch.value.students_count = rosterTotal.value
    }
  } catch (e) {
    rosterStudents.value = []
    toast.error('Error', e?.response?.data?.message || t('batches.toastLoadFailed'))
  } finally {
    rosterLoading.value = false
  }
}

async function attachStudent() {
  if (!batch.value?.id || !selectedStudentId.value) return
  try {
    const { data } = await window.axios.post(`/api/batches/${batch.value.id}/students`, {
      user_id: selectedStudentId.value,
    })
    selectedStudentId.value = null
    const attached = data.auto_assigned?.attached || 0
    toast.success('Success', t('batches.toastStudentAdded', { count: attached }))
    await Promise.all([loadRoster(rosterPage.value), searchStudents()])
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastSaveFailed'))
  }
}

async function detachStudent(userId) {
  if (!batch.value?.id) return
  try {
    await window.axios.delete(`/api/batches/${batch.value.id}/students/${userId}`)
    const nextPage = rosterStudents.value.length === 1 && rosterPage.value > 1
      ? rosterPage.value - 1
      : rosterPage.value
    await Promise.all([loadRoster(nextPage), searchStudents()])
  } catch (e) {
    toast.error('Error', e?.response?.data?.message || t('batches.toastSaveFailed'))
  }
}

watch(() => route.params.id, () => {
  if (route.name === 'batch-detail') {
    pickerSearch.value = ''
    rosterSearch.value = ''
    rosterPage.value = 1
    loadBatch()
  }
})

onMounted(loadBatch)
</script>
