<template>
  <main class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-[#1A1A1A] mb-6">{{ t('adminRegistration.title') }}</h1>

    <div class="mb-4 flex flex-wrap gap-3 items-center">
      <input
        v-model="search"
        type="search"
        class="rounded-xl border border-gray-200 px-4 py-2 text-sm max-w-xs"
        :placeholder="t('common.search')"
        @keyup.enter="load"
      />
      <button type="button" class="rounded-full bg-[#1A1A1A] text-white px-4 py-2 text-sm font-medium" @click="load">
        {{ t('common.refresh') }}
      </button>
    </div>

    <div v-if="loading" class="py-16 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="errorMessage"
      class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>

    <div v-else class="overflow-x-auto rounded-2xl border border-gray-100 bg-white shadow-sm">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-left text-gray-600">
          <tr>
            <th class="px-4 py-3 font-semibold">{{ t('adminRegistration.user') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('registration.steps.admin') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('registration.steps.psychology') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('registration.steps.health') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('registration.steps.physical') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('common.status') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('common.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in items" :key="row.id" class="border-t border-gray-100 hover:bg-gray-50/80">
            <td class="px-4 py-3">
              <div class="font-medium text-[#1A1A1A]">{{ row.user?.name }}</div>
              <div class="text-xs text-gray-500">{{ row.user?.email }}</div>
            </td>
            <td class="px-4 py-3 capitalize">{{ row.administration_status }}</td>
            <td class="px-4 py-3 capitalize">{{ row.psychology_status }}</td>
            <td class="px-4 py-3 capitalize">{{ row.health_status }}</td>
            <td class="px-4 py-3 capitalize">{{ row.physical_status }}</td>
            <td class="px-4 py-3">
              <span class="text-xs rounded-full px-2 py-0.5 bg-gray-100">{{ row.current_step }}</span>
              <Check v-if="row.fully_completed" class="ml-1 inline h-3.5 w-3.5 text-emerald-600" />
            </td>
            <td class="px-4 py-3">
              <button type="button" class="text-[#9DB359] font-medium hover:underline" @click="openReview(row)">
                {{ t('adminRegistration.review') }}
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="modal.open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4" @click.self="modal.open = false">
      <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-lg mb-4">{{ t('adminRegistration.review') }}</h3>
        <div v-if="modal.row" class="space-y-5 text-sm">
          <p class="text-xs text-amber-900 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 leading-relaxed">
            {{ t('adminRegistration.offlineStaffHint') }}
          </p>

          <a
            v-if="modal.row?.user_id"
            :href="`/api/admin/registration-progress/${modal.row.user_id}/berkas-pdf`"
            class="inline-flex rounded-full bg-[#1A1A1A] px-4 py-2 text-xs font-semibold text-white"
            target="_blank"
            rel="noopener noreferrer"
          >
            {{ t('registration.forms.downloadPdf') }}
          </a>

          <div>
            <div class="font-semibold text-gray-800 mb-2">{{ t('registration.steps.admin') }}</div>
            <dl class="grid gap-3 sm:grid-cols-2">
              <template v-for="f in administrationFieldOrder" :key="f.key">
                <div class="sm:col-span-2" :class="{ 'sm:col-span-1': f.short }">
                  <dt class="text-xs font-medium text-gray-500">{{ t(f.labelKey) }}</dt>
                  <dd class="mt-0.5 text-gray-900 break-words">
                    <a
                      v-if="f.isFile && isUploadedFile(modal.row, f.key) && registrationFileHref(modal.row, f.key)"
                      :href="registrationFileHref(modal.row, f.key)"
                      class="text-[#9DB359] underline"
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      {{ registrationFileHref(modal.row, f.key) }}
                    </a>
                    <span v-else-if="f.isFile">—</span>
                    <a
                      v-else-if="isHttpUrl(modal.row.administration_data?.[f.key])"
                      :href="modal.row.administration_data[f.key]"
                      class="text-[#9DB359] underline"
                      target="_blank"
                      rel="noopener noreferrer"
                    >
                      {{ modal.row.administration_data[f.key] }}
                    </a>
                    <span v-else>{{ formatAdminScalar(f.key, modal.row.administration_data?.[f.key]) }}</span>
                  </dd>
                </div>
              </template>
            </dl>
          </div>

          <div class="grid gap-4 sm:grid-cols-3">
            <div>
              <div class="font-semibold text-gray-700">{{ t('registration.steps.psychology') }}</div>
              <p class="mt-1 text-xs text-gray-500 capitalize">{{ modal.row.psychology_status }}</p>
              <dl v-if="hasJsonData(modal.row.psychology_data)" class="mt-2 space-y-1 rounded-xl bg-gray-50 p-3 text-xs">
                <div
                  v-for="item in toDisplayEntries(modal.row.psychology_data)"
                  :key="`psychology-${item.key}`"
                  class="flex items-start justify-between gap-2"
                >
                  <dt class="font-medium text-gray-600">{{ item.label }}</dt>
                  <dd class="text-right text-gray-900">{{ item.value }}</dd>
                </div>
              </dl>
              <p v-else class="mt-2 text-xs text-gray-400">{{ t('adminRegistration.noJson') }}</p>
              <div class="mt-3 space-y-2">
                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold"
                    :class="modal.actions.psychology.decision === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'"
                    @click="setDecision('psychology', 'approved')"
                  >
                    {{ t('adminRegistration.approve') }}
                  </button>
                  <button
                    type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold"
                    :class="modal.actions.psychology.decision === 'revision_requested' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600'"
                    @click="setDecision('psychology', 'revision_requested')"
                  >
                    {{ t('adminRegistration.requestRevision') }}
                  </button>
                </div>
                <textarea
                  v-if="modal.actions.psychology.decision === 'revision_requested'"
                  v-model="modal.actions.psychology.note"
                  rows="2"
                  class="w-full rounded-xl border border-gray-200 px-2 py-1.5 text-xs"
                  :placeholder="t('adminRegistration.note')"
                />
                <button
                  type="button"
                  class="rounded-full bg-[#9DB359] px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-50"
                  :disabled="modal.actions.psychology.saving"
                  @click="submitDecision('psychology')"
                >
                  {{ modal.actions.psychology.saving ? '…' : t('common.save') }}
                </button>
              </div>
            </div>
            <div>
              <div class="font-semibold text-gray-700">{{ t('registration.steps.health') }}</div>
              <p class="mt-1 text-xs text-gray-500 capitalize">{{ modal.row.health_status }}</p>
              <dl v-if="hasJsonData(modal.row.health_data)" class="mt-2 space-y-1 rounded-xl bg-gray-50 p-3 text-xs">
                <div
                  v-for="item in toDisplayEntries(modal.row.health_data)"
                  :key="`health-${item.key}`"
                  class="flex items-start justify-between gap-2"
                >
                  <dt class="font-medium text-gray-600">{{ item.label }}</dt>
                  <dd class="text-right text-gray-900">{{ item.value }}</dd>
                </div>
              </dl>
              <p v-else class="mt-2 text-xs text-gray-400">{{ t('adminRegistration.noJson') }}</p>
              <div class="mt-3 space-y-2">
                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold"
                    :class="modal.actions.health.decision === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'"
                    @click="setDecision('health', 'approved')"
                  >
                    {{ t('adminRegistration.approve') }}
                  </button>
                  <button
                    type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold"
                    :class="modal.actions.health.decision === 'revision_requested' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600'"
                    @click="setDecision('health', 'revision_requested')"
                  >
                    {{ t('adminRegistration.requestRevision') }}
                  </button>
                </div>
                <textarea
                  v-if="modal.actions.health.decision === 'revision_requested'"
                  v-model="modal.actions.health.note"
                  rows="2"
                  class="w-full rounded-xl border border-gray-200 px-2 py-1.5 text-xs"
                  :placeholder="t('adminRegistration.note')"
                />
                <button
                  type="button"
                  class="rounded-full bg-[#9DB359] px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-50"
                  :disabled="modal.actions.health.saving"
                  @click="submitDecision('health')"
                >
                  {{ modal.actions.health.saving ? '…' : t('common.save') }}
                </button>
              </div>
            </div>
            <div>
              <div class="font-semibold text-gray-700">{{ t('registration.steps.physical') }}</div>
              <p class="mt-1 text-xs text-gray-500 capitalize">{{ modal.row.physical_status }}</p>
              <dl v-if="hasJsonData(modal.row.physical_data)" class="mt-2 space-y-1 rounded-xl bg-gray-50 p-3 text-xs">
                <div
                  v-for="item in toDisplayEntries(modal.row.physical_data)"
                  :key="`physical-${item.key}`"
                  class="flex items-start justify-between gap-2"
                >
                  <dt class="font-medium text-gray-600">{{ item.label }}</dt>
                  <dd class="text-right text-gray-900">{{ item.value }}</dd>
                </div>
              </dl>
              <p v-else class="mt-2 text-xs text-gray-400">{{ t('adminRegistration.noJson') }}</p>
              <div class="mt-3 space-y-2">
                <div class="flex items-center gap-2">
                  <button
                    type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold"
                    :class="modal.actions.physical.decision === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'"
                    @click="setDecision('physical', 'approved')"
                  >
                    {{ t('adminRegistration.approve') }}
                  </button>
                  <button
                    type="button"
                    class="rounded-full px-3 py-1.5 text-xs font-semibold"
                    :class="modal.actions.physical.decision === 'revision_requested' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600'"
                    @click="setDecision('physical', 'revision_requested')"
                  >
                    {{ t('adminRegistration.requestRevision') }}
                  </button>
                </div>
                <textarea
                  v-if="modal.actions.physical.decision === 'revision_requested'"
                  v-model="modal.actions.physical.note"
                  rows="2"
                  class="w-full rounded-xl border border-gray-200 px-2 py-1.5 text-xs"
                  :placeholder="t('adminRegistration.note')"
                />
                <button
                  type="button"
                  class="rounded-full bg-[#9DB359] px-3 py-1.5 text-xs font-semibold text-white disabled:opacity-50"
                  :disabled="modal.actions.physical.saving"
                  @click="submitDecision('physical')"
                >
                  {{ modal.actions.physical.saving ? '…' : t('common.save') }}
                </button>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-full border border-gray-200 text-sm" @click="modal.open = false">{{ t('common.cancel') }}</button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { Check } from 'lucide-vue-next'
import { useI18n } from '@/composables/useI18n'
import { registrationFileHref } from '@/utils/storageUrl'

const { t } = useI18n()

const administrationFieldOrder = [
  { key: 'full_name', labelKey: 'registration.fields.fullNameKk', short: false },
  { key: 'whatsapp', labelKey: 'registration.fields.whatsapp', short: true },
  { key: 'phone', labelKey: 'registration.fields.phone', short: true },
  { key: 'address_kk', labelKey: 'registration.fields.addressKk', short: false },
  { key: 'address_domicile', labelKey: 'registration.fields.addressDomicile', short: false },
  { key: 'id_document_path', labelKey: 'registration.fields.idDocumentFile', short: false, isFile: true },
  { key: 'kk_path', labelKey: 'registration.fields.kkFile', short: false, isFile: true },
  { key: 'report_card_path', labelKey: 'registration.fields.reportCardFile', short: false, isFile: true },
  { key: 'gender', labelKey: 'registration.fields.gender', short: true },
  { key: 'height_cm', labelKey: 'registration.fields.heightCm', short: true },
  { key: 'weight_kg', labelKey: 'registration.fields.weightKg', short: true },
  { key: 'birth_place', labelKey: 'registration.forms.birthPlace', short: true },
  { key: 'birth_date', labelKey: 'registration.forms.birthDate', short: true },
  { key: 'religion', labelKey: 'registration.forms.religion', short: true },
  { key: 'ethnicity', labelKey: 'registration.forms.ethnicity', short: true },
  { key: 'education', labelKey: 'registration.forms.education', short: false },
  { key: 'nik', labelKey: 'registration.forms.nik', short: true },
  { key: 'parent_name', labelKey: 'registration.forms.parentName', short: false },
  { key: 'passport_photo_path', labelKey: 'registration.fields.passportPhotoFile', short: false, isFile: true },
  { key: 'full_body_photo_path', labelKey: 'registration.fields.fullBodyPhotoFile', short: false, isFile: true },
]

const loading = ref(false)
const items = ref([])
const search = ref('')
const errorMessage = ref('')

const modal = reactive({
  open: false,
  row: null,
  actions: {
    psychology: { decision: 'approved', note: '', saving: false },
    health: { decision: 'approved', note: '', saving: false },
    physical: { decision: 'approved', note: '', saving: false },
  },
})

function isHttpUrl(val) {
  return typeof val === 'string' && /^https?:\/\//i.test(val.trim())
}

function formatAdminScalar(key, val) {
  if (val === null || val === undefined || val === '') return '—'
  if (key === 'gender') {
    if (val === 'L') return t('registration.fields.genderMale')
    if (val === 'P') return t('registration.fields.genderFemale')
  }
  return String(val)
}

function hasJsonData(data) {
  return data != null && typeof data === 'object' && Object.keys(data).length > 0
}

function formatDataLabel(key) {
  return String(key || '')
    .replaceAll('_', ' ')
    .replace(/\b\w/g, (char) => char.toUpperCase())
}

function formatDataValue(value) {
  if (value === null || value === undefined || value === '') return '—'
  if (typeof value === 'boolean') return value ? 'Ya' : 'Tidak'
  if (Array.isArray(value)) return value.join(', ')
  if (typeof value === 'object') return JSON.stringify(value)
  return String(value)
}

function toDisplayEntries(data) {
  if (!hasJsonData(data)) return []
  return Object.entries(data).map(([key, value]) => ({
    key,
    label: formatDataLabel(key),
    value: formatDataValue(value),
  }))
}

function isUploadedFile(row, pathKey) {
  return Boolean(row?.administration_files_present?.[pathKey])
}

async function load() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/admin/registration-progress', {
      params: { search: search.value || undefined },
    })
    items.value = Array.isArray(data.data) ? data.data : Array.isArray(data) ? data : []
    errorMessage.value = ''
  } catch (error) {
    items.value = []
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat data administrasi pendaftaran.'
  } finally {
    loading.value = false
  }
}

function openReview(row) {
  modal.open = true
  modal.row = row
  modal.actions.psychology.decision = row.psychology_status === 'revision_requested' ? 'revision_requested' : 'approved'
  modal.actions.psychology.note = ''
  modal.actions.psychology.saving = false
  modal.actions.health.decision = row.health_status === 'revision_requested' ? 'revision_requested' : 'approved'
  modal.actions.health.note = ''
  modal.actions.health.saving = false
  modal.actions.physical.decision = row.physical_status === 'revision_requested' ? 'revision_requested' : 'approved'
  modal.actions.physical.note = ''
  modal.actions.physical.saving = false
}

function setDecision(step, decision) {
  modal.actions[step].decision = decision
  if (decision !== 'revision_requested') {
    modal.actions[step].note = ''
  }
}

async function submitDecision(step) {
  const uid = modal.row?.user_id ?? modal.row?.user?.id
  if (!uid) return
  modal.actions[step].saving = true
  try {
    await axios.patch(`/api/admin/registration-progress/${uid}`, {
      step,
      status: modal.actions[step].decision,
      admin_note: modal.actions[step].decision === 'revision_requested'
        ? (modal.actions[step].note || null)
        : null,
    })
    const { data } = await axios.get(`/api/admin/registration-progress/${uid}`)
    modal.row = data
    modal.actions[step].note = ''
    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menyimpan keputusan admin.'
  } finally {
    modal.actions[step].saving = false
  }
}

onMounted(load)
</script>
