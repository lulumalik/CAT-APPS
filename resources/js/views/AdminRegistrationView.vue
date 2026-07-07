<template>
  <main class="max-w-7xl mx-auto px-4 py-8">
    <PageHeroHeader
      :title="t('adminRegistration.title')"
      subtitle="Kelola dan verifikasi pendaftaran peserta."
      theme="green"
      :icon="ClipboardCheck"
    >
      <template #actions>
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
      </template>
    </PageHeroHeader>

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
            <th class="px-4 py-3 font-semibold">{{ t('adminRegistration.programColumn') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('registration.steps.admin') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('registration.steps.psychology') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('registration.steps.health') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('registration.steps.physical') }}</th>
            <th class="px-4 py-3 font-semibold">{{ t('adminRegistration.paymentColumn') }}</th>
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
            <td class="px-4 py-3 text-xs text-gray-600">{{ programLabel(row.user?.program_category) }}</td>
            <td class="px-4 py-3 capitalize">{{ isSimplifiedRow(row) ? '—' : row.administration_status }}</td>
            <td class="px-4 py-3 capitalize">{{ isSimplifiedRow(row) ? '—' : row.psychology_status }}</td>
            <td class="px-4 py-3 capitalize">{{ isSimplifiedRow(row) ? '—' : row.health_status }}</td>
            <td class="px-4 py-3 capitalize">{{ isSimplifiedRow(row) ? '—' : row.physical_status }}</td>
            <td class="px-4 py-3">
              <span
                v-if="isSimplifiedRow(row)"
                class="text-xs rounded-full px-2 py-0.5 font-semibold"
                :class="row.payment_confirmed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
              >
                {{ row.payment_confirmed ? t('adminRegistration.paymentConfirmed') : t('adminRegistration.paymentPending') }}
              </span>
              <span v-else class="text-gray-400">—</span>
            </td>
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
      <div class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-lg mb-4">{{ t('adminRegistration.review') }}</h3>
        <div v-if="modal.row && isSimplifiedRow(modal.row)" class="space-y-5 text-sm">
          <div>
            <div class="font-medium text-[#1A1A1A]">{{ modal.row.user?.name }}</div>
            <div class="text-xs text-gray-500">{{ modal.row.user?.email }}</div>
            <div class="mt-1 text-xs text-gray-600">{{ programLabel(modal.row.user?.program_category) }}</div>
          </div>
          <p class="text-xs text-amber-900 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 leading-relaxed">
            {{ t('adminRegistration.paymentSubtitle') }}
          </p>
          <div class="rounded-2xl border border-gray-200 p-5 space-y-4">
            <div class="text-sm font-semibold text-gray-700">{{ t('adminRegistration.paymentTitle') }}</div>
            <span
              class="inline-flex text-xs rounded-full px-2.5 py-1 font-semibold"
              :class="modal.row.payment_confirmed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
            >
              {{ modal.row.payment_confirmed ? t('adminRegistration.paymentConfirmed') : t('adminRegistration.paymentPending') }}
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 text-sm font-semibold transition-colors"
                :class="modal.row.payment_confirmed
                  ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                  : 'border-gray-200 bg-white text-gray-600 hover:border-emerald-200'"
                :disabled="modal.paymentSaving"
                @click="submitPayment(true)"
              >
                <Check class="h-4 w-4" />
                {{ t('adminRegistration.paymentConfirmAction') }}
              </button>
              <button
                type="button"
                class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 text-sm font-semibold transition-colors"
                :class="!modal.row.payment_confirmed
                  ? 'border-amber-500 bg-amber-50 text-amber-700'
                  : 'border-gray-200 bg-white text-gray-600 hover:border-amber-200'"
                :disabled="modal.paymentSaving"
                @click="submitPayment(false)"
              >
                <AlertCircle class="h-4 w-4" />
                {{ t('adminRegistration.paymentUnconfirmAction') }}
              </button>
            </div>
          </div>
          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-full border border-gray-200 text-sm" @click="modal.open = false">{{ t('common.close') }}</button>
          </div>
        </div>
        <div v-else-if="modal.row" class="space-y-5 text-sm">
          <p class="text-xs text-amber-900 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 leading-relaxed">
            {{ t('adminRegistration.offlineStaffHint') }}
          </p>

          <div
            v-for="step in reviewSteps"
            :key="step.key"
            class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden"
          >
            <div class="flex items-start gap-3 p-5 border-b border-gray-100">
              <div
                class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl"
                :class="step.iconBg"
              >
                <component :is="step.icon" class="h-5 w-5" :class="step.iconColor" />
              </div>
              <div class="min-w-0 flex-1">
                <div class="font-bold text-base text-[#1A1A1A]">{{ t(step.labelKey) }}</div>
                <span
                  class="mt-1.5 inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold"
                  :class="statusBadgeClass(modal.row[`${step.key}_status`])"
                >
                  <Check v-if="modal.row[`${step.key}_status`] === 'approved'" class="h-3 w-3" />
                  <AlertCircle v-else-if="modal.row[`${step.key}_status`] === 'revision_requested'" class="h-3 w-3" />
                  {{ formatStepStatus(modal.row[`${step.key}_status`]) }}
                </span>
              </div>
            </div>

            <div v-if="step.key === 'administration'" class="border-b border-gray-100 bg-gray-50/50 px-5 py-4">
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

            <div class="p-5 space-y-4">
              <div>
                <div class="text-sm font-semibold text-gray-700 mb-2">{{ t('adminRegistration.action') }}</div>
                <div class="grid grid-cols-2 gap-3">
                  <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 text-sm font-semibold transition-colors"
                    :class="modal.actions[step.key].decision === 'approved'
                      ? 'border-emerald-500 bg-emerald-50 text-emerald-700'
                      : 'border-gray-200 bg-white text-gray-600 hover:border-emerald-200'"
                    @click="setDecision(step.key, 'approved')"
                  >
                    <Check class="h-4 w-4" />
                    {{ t('adminRegistration.approve') }}
                  </button>
                  <button
                    type="button"
                    class="flex items-center justify-center gap-2 rounded-xl border-2 px-4 py-3 text-sm font-semibold transition-colors"
                    :class="modal.actions[step.key].decision === 'revision_requested'
                      ? 'border-amber-500 bg-amber-50 text-amber-700'
                      : 'border-gray-200 bg-white text-gray-600 hover:border-amber-200'"
                    @click="setDecision(step.key, 'revision_requested')"
                  >
                    <AlertCircle class="h-4 w-4" />
                    {{ t('adminRegistration.requestRevision') }}
                  </button>
                </div>
              </div>

              <div>
                <div class="text-sm font-semibold text-gray-700 mb-2">{{ t('adminRegistration.note') }}</div>
                <div class="relative">
                  <textarea
                    v-model="modal.actions[step.key].note"
                    rows="3"
                    maxlength="500"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm resize-none focus:border-blue-400 focus:ring-1 focus:ring-blue-400"
                    :placeholder="t('adminRegistration.notePlaceholder')"
                  />
                  <span class="absolute bottom-2 right-3 text-xs text-gray-400">
                    {{ modal.actions[step.key].note.length }}/500
                  </span>
                </div>
              </div>

              <button
                type="button"
                class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50 transition-colors"
                :disabled="modal.actions[step.key].saving"
                @click="submitDecision(step.key)"
              >
                <Save class="h-4 w-4" />
                {{ modal.actions[step.key].saving ? '…' : t('common.save') }}
              </button>
            </div>
          </div>

          <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="px-4 py-2 rounded-full border border-gray-200 text-sm" @click="modal.open = false">{{ t('common.close') }}</button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import axios from 'axios'
import { AlertCircle, Brain, Check, ClipboardCheck, ClipboardList, Dumbbell, HeartPulse, Save } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useI18n } from '@/composables/useI18n'
import { programCategoryLabel, usesSimplifiedOnboarding } from '@/utils/userMeta'
import { registrationFileHref } from '@/utils/storageUrl'

const { t } = useI18n()

const reviewSteps = [
  {
    key: 'administration',
    labelKey: 'registration.steps.admin',
    icon: ClipboardList,
    iconBg: 'bg-sky-100',
    iconColor: 'text-sky-600',
  },
  {
    key: 'psychology',
    labelKey: 'registration.steps.psychology',
    icon: Brain,
    iconBg: 'bg-sky-100',
    iconColor: 'text-sky-600',
  },
  {
    key: 'health',
    labelKey: 'registration.steps.health',
    icon: HeartPulse,
    iconBg: 'bg-rose-100',
    iconColor: 'text-rose-600',
  },
  {
    key: 'physical',
    labelKey: 'registration.steps.physical',
    icon: Dumbbell,
    iconBg: 'bg-emerald-100',
    iconColor: 'text-emerald-600',
  },
]

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

const defaultAction = () => ({ decision: 'approved', note: '', saving: false })

const modal = reactive({
  open: false,
  row: null,
  paymentSaving: false,
  actions: {
    administration: defaultAction(),
    psychology: defaultAction(),
    health: defaultAction(),
    physical: defaultAction(),
  },
})

function isSimplifiedRow(row) {
  return usesSimplifiedOnboarding(row?.user)
}

function programLabel(category) {
  return programCategoryLabel(category)
}

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

function isUploadedFile(row, pathKey) {
  return Boolean(row?.administration_files_present?.[pathKey])
}

function formatStepStatus(status) {
  if (status === 'approved') return t('adminRegistration.statusApproved')
  if (status === 'revision_requested') return t('adminRegistration.statusRevision')
  if (status === 'not_started') return t('adminRegistration.statusNotStarted')
  return t('adminRegistration.statusPending')
}

function statusBadgeClass(status) {
  if (status === 'approved') return 'bg-emerald-50 text-emerald-700 border border-emerald-200'
  if (status === 'revision_requested') return 'bg-amber-50 text-amber-700 border border-amber-200'
  return 'bg-gray-100 text-gray-600 border border-gray-200'
}

function hydrateAction(step, row) {
  const status = row[`${step}_status`]
  modal.actions[step].decision = status === 'revision_requested' ? 'revision_requested' : 'approved'
  modal.actions[step].note = row[`${step}_admin_note`] || ''
  modal.actions[step].saving = false
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
  modal.paymentSaving = false
  if (!isSimplifiedRow(row)) {
    reviewSteps.forEach((step) => hydrateAction(step.key, row))
  }
}

async function submitPayment(confirmed) {
  const uid = modal.row?.user_id ?? modal.row?.user?.id
  if (!uid) return
  modal.paymentSaving = true
  try {
    const { data } = await axios.patch(`/api/admin/registration-progress/${uid}/payment`, {
      payment_confirmed: confirmed,
    })
    modal.row = data
    const listIdx = items.value.findIndex((item) => (item.user_id ?? item.user?.id) === uid)
    if (listIdx !== -1) items.value[listIdx] = data
    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal memperbarui status pembayaran.'
  } finally {
    modal.paymentSaving = false
  }
}

function setDecision(step, decision) {
  modal.actions[step].decision = decision
}

async function submitDecision(step) {
  const uid = modal.row?.user_id ?? modal.row?.user?.id
  if (!uid) return
  modal.actions[step].saving = true
  try {
    const action = modal.actions[step]
    await axios.patch(`/api/admin/registration-progress/${uid}`, {
      step,
      status: action.decision,
      admin_note: action.note.trim() || null,
    })
    const { data } = await axios.get(`/api/admin/registration-progress/${uid}`)
    modal.row = data
    hydrateAction(step, data)
    const listIdx = items.value.findIndex((item) => (item.user_id ?? item.user?.id) === uid)
    if (listIdx !== -1) items.value[listIdx] = data
    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menyimpan keputusan admin.'
  } finally {
    modal.actions[step].saving = false
  }
}

onMounted(load)
</script>
