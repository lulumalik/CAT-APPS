<template>
  <main class="max-w-3xl mx-auto px-4 pb-10">
    <h1 class="text-2xl font-bold text-[#1A1A1A] mb-2">{{ t('registration.title') }}</h1>
    <p class="text-gray-500 text-sm mb-8">{{ t('registration.subtitle') }}</p>

    <div v-if="loading" class="py-20 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="errorMessage"
      class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>

    <div v-else-if="progress" class="space-y-8">
      <div class="rounded-2xl border border-gray-200 bg-[#F3F4F6] p-4">
        <ol class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
          <li v-for="(s, i) in steps" :key="`stepper-${s.key}`" class="min-w-0">
            <p class="text-sm font-semibold transition-colors" :class="stepperTextClass(i)">
              {{ i + 1 }}. {{ s.label }}
            </p>
            <div class="mt-1 text-xs">
              <span class="rounded-full px-2 py-0.5 capitalize" :class="statusBadgeClass(progress?.[s.statusKey])">
                {{ formatStatus(progress?.[s.statusKey]) }}
              </span>
            </div>
            <div class="mt-2 h-1.5 w-full rounded-full transition-colors" :class="stepperBarClass(i)"></div>
          </li>
        </ol>
      </div>

      <div class="bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-8 space-y-4">
        <h2 class="font-semibold text-lg mb-4">{{ activeStepMeta.label }}</h2>
        <p class="text-xs text-gray-500 mb-2">
          {{ t('registration.statusLabel') }}:
          <span class="font-medium capitalize">{{ formatStatus(statusLabel(activeStepMeta.statusKey)) }}</span>
        </p>

        <div v-if="stepRevisionNote" class="mb-4 p-3 rounded-xl bg-amber-50 text-amber-900 text-sm border border-amber-100">
          <span class="font-semibold">{{ t('registration.adminNote') }}:</span>
          {{ stepRevisionNote }}
        </div>

        <div class="space-y-4">
          <template v-if="activeStep === 'administration'">
            <p class="text-xs text-gray-500 -mt-1">{{ t('registration.fileUploadHint') }}</p>

            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.fullNameKk') }}</label>
            <input v-model="form.administration.full_name" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.whatsapp') }}</label>
                <input v-model="form.administration.whatsapp" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.phone') }}</label>
                <input v-model="form.administration.phone" type="text" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
              </div>
            </div>

            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.addressKk') }}</label>
            <textarea v-model="form.administration.address_kk" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />

            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.addressDomicile') }}</label>
            <textarea v-model="form.administration.address_domicile" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />

            <div v-for="slot in fileSlots" :key="slot.input" class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 space-y-2">
              <label class="block text-sm font-medium text-gray-700">{{ t(slot.labelKey) }}</label>
              <p v-if="slot.hintKey" class="text-xs text-gray-500">{{ t(slot.hintKey) }}</p>
              <p v-if="storedPath(slot.pathKey)" class="text-xs text-emerald-700 flex flex-wrap items-center gap-2">
                {{ t('registration.fileUploaded') }}
                <a
                  :href="registrationFileHref(progress, slot.pathKey)"
                  class="text-[#9DB359] font-medium underline"
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  {{ t('registration.openFile') }}
                </a>
              </p>
              <input
                :key="`f-${slot.input}-${uploadKey}`"
                type="file"
                :accept="slot.accept"
                class="block w-full text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-white file:px-3 file:py-2 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-50"
                @change="onFile(slot.input, $event)"
              />
              <p v-if="pendingFiles[slot.input]" class="text-xs text-gray-600">
                {{ t('registration.pickFile') }}: {{ pendingFiles[slot.input].name }}
              </p>
            </div>

            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.gender') }}</label>
            <select v-model="form.administration.gender" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm bg-white">
              <option disabled value="">{{ t('registration.fields.gender') }}</option>
              <option value="L">{{ t('registration.fields.genderMale') }}</option>
              <option value="P">{{ t('registration.fields.genderFemale') }}</option>
            </select>

            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.heightCm') }}</label>
                <input v-model="form.administration.height_cm" type="number" min="50" max="280" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.weightKg') }}</label>
                <input v-model="form.administration.weight_kg" type="number" min="15" max="250" step="0.1" class="mt-1 w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
              </div>
            </div>
          </template>

          <template v-else>
            <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-5 space-y-2">
              <p class="font-medium text-gray-800">{{ t('registration.offlineStepTitle') }}</p>
              <p class="text-sm text-gray-600 leading-relaxed">{{ t('registration.offlineStepBody') }}</p>
            </div>
          </template>
        </div>

        <div v-if="showSubmitAdmin" class="mt-8 flex justify-end gap-3">
          <button
            type="button"
            :disabled="saving || progress.fully_completed"
            class="rounded-full bg-[#9DB359] px-6 py-2.5 text-sm font-semibold text-white shadow-md disabled:opacity-50"
            @click="submitStep"
          >
            {{ saving ? '…' : t('registration.submitStep') }}
          </button>
        </div>
      </div>

      <div v-if="progress.fully_completed" class="rounded-2xl bg-emerald-50 border border-emerald-100 p-6 text-emerald-900 text-sm">
        {{ t('registration.completeMessage') }}
      </div>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, reactive, ref, watch } from 'vue'
import axios from 'axios'
import { useI18n } from '@/composables/useI18n'
import { registrationFileHref } from '@/utils/storageUrl'

const { t } = useI18n()

const fileSlots = [
  {
    input: 'id_document',
    pathKey: 'id_document_path',
    labelKey: 'registration.fields.idDocumentFile',
    hintKey: 'registration.hints.idDocument',
    accept: 'image/jpeg,image/png,image/webp,.pdf,application/pdf',
  },
  {
    input: 'kk',
    pathKey: 'kk_path',
    labelKey: 'registration.fields.kkFile',
    hintKey: null,
    accept: 'image/jpeg,image/png,image/webp,.pdf,application/pdf',
  },
  {
    input: 'report_card',
    pathKey: 'report_card_path',
    labelKey: 'registration.fields.reportCardFile',
    hintKey: null,
    accept: 'image/jpeg,image/png,image/webp,.pdf,application/pdf',
  },
  {
    input: 'passport_photo',
    pathKey: 'passport_photo_path',
    labelKey: 'registration.fields.passportPhotoFile',
    hintKey: 'registration.hints.passportPhoto',
    accept: 'image/jpeg,image/png,image/webp',
  },
  {
    input: 'full_body_photo',
    pathKey: 'full_body_photo_path',
    labelKey: 'registration.fields.fullBodyPhotoFile',
    hintKey: 'registration.hints.fullBody',
    accept: 'image/jpeg,image/png,image/webp',
  },
]

const loading = ref(true)
const saving = ref(false)
const progress = ref(null)
const errorMessage = ref('')
const uploadKey = ref(0)

const administrationDefaults = () => ({
  full_name: '',
  whatsapp: '',
  phone: '',
  address_kk: '',
  address_domicile: '',
  gender: '',
  height_cm: '',
  weight_kg: '',
})

const form = reactive({
  administration: administrationDefaults(),
})

const pendingFiles = reactive({
  id_document: null,
  kk: null,
  report_card: null,
  passport_photo: null,
  full_body_photo: null,
})

const steps = computed(() => [
  { key: 'administration', label: t('registration.steps.admin'), statusKey: 'administration_status' },
  { key: 'psychology', label: t('registration.steps.psychology'), statusKey: 'psychology_status' },
  { key: 'health', label: t('registration.steps.health'), statusKey: 'health_status' },
  { key: 'physical', label: t('registration.steps.physical'), statusKey: 'physical_status' },
])

const activeStep = computed(() => {
  if (!progress.value) return 'administration'
  const cs = progress.value.current_step
  if (cs === 'completed') return 'physical'
  return cs
})

const showSubmitAdmin = computed(() => {
  if (!progress.value || progress.value.fully_completed) return false
  return progress.value.current_step === 'administration'
})

const stepRevisionNote = computed(() => {
  const p = progress.value
  if (!p) return ''
  const key = `${activeStep.value}_admin_note`
  return p[key] || ''
})

const activeStepMeta = computed(() => steps.value.find((s) => s.key === activeStep.value) || steps.value[0])
const activeStepIndex = computed(() => steps.value.findIndex((s) => s.key === activeStep.value))

function storedPath(pathKey) {
  return progress.value?.administration_data?.[pathKey] || ''
}

function onFile(input, e) {
  const f = e.target.files?.[0]
  pendingFiles[input] = f || null
}

function statusLabel(key) {
  return progress.value?.[activeStepMeta.value.statusKey] || '—'
}

function stepperBarClass(index) {
  return isStepReached(index) ? 'bg-[#22C55E]' : 'bg-gray-300'
}

function stepperTextClass(index) {
  return isStepReached(index) ? 'text-[#16A34A]' : 'text-gray-400'
}

function isStepReached(index) {
  if (!progress.value) return index === 0
  if (progress.value.fully_completed) return true
  const safeActiveIndex = activeStepIndex.value < 0 ? 0 : activeStepIndex.value
  return index <= safeActiveIndex
}

function formatStatus(status) {
  if (status === 'approved') return 'complete'
  if (!status) return 'not started'
  return String(status).replaceAll('_', ' ')
}

function statusBadgeClass(status) {
  if (status === 'approved') return 'bg-emerald-100 text-emerald-700'
  if (status === 'submitted') return 'bg-amber-100 text-amber-700'
  if (status === 'revision_requested') return 'bg-orange-100 text-orange-700'
  if (status === 'not_started') return 'bg-red-100 text-red-700'
  return 'bg-gray-100 text-gray-600'
}

function migrateLegacyAdministration(raw) {
  const text = administrationDefaults()
  if (!raw || typeof raw !== 'object') return text
  Object.assign(text, {
    full_name: raw.full_name ?? '',
    whatsapp: raw.whatsapp ?? '',
    phone: raw.phone ?? '',
    address_kk: raw.address_kk ?? raw.address ?? '',
    address_domicile: raw.address_domicile ?? raw.address ?? '',
    gender: raw.gender ?? '',
    height_cm: raw.height_cm != null ? String(raw.height_cm) : '',
    weight_kg: raw.weight_kg != null ? String(raw.weight_kg) : '',
  })
  if (!text.whatsapp && text.phone) text.whatsapp = text.phone
  return text
}

function hydrateForm() {
  const p = progress.value
  if (!p) return
  const next = migrateLegacyAdministration(p.administration_data)
  Object.keys(administrationDefaults()).forEach((k) => {
    form.administration[k] = next[k] ?? ''
  })
  if (!form.administration.height_cm && p.physical_data?.height_cm != null) {
    form.administration.height_cm = String(p.physical_data.height_cm)
  }
  if (!form.administration.weight_kg && p.physical_data?.weight_kg != null) {
    form.administration.weight_kg = String(p.physical_data.weight_kg)
  }
  fileSlots.forEach((s) => {
    pendingFiles[s.input] = null
  })
}

async function fetchProgress() {
  loading.value = true
  try {
    const { data } = await axios.get('/api/my-registration')
    progress.value = data
    hydrateForm()
    errorMessage.value = ''
  } catch (error) {
    progress.value = null
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat progress pendaftaran.'
  } finally {
    loading.value = false
  }
}

watch(progress, hydrateForm)

function clearPendingFiles() {
  fileSlots.forEach((s) => {
    pendingFiles[s.input] = null
  })
  uploadKey.value += 1
}

async function submitStep() {
  if (!progress.value || progress.value.fully_completed) return
  const step = progress.value.current_step
  if (step !== 'administration') return
  saving.value = true
  try {
    const fd = new FormData()
    fd.append('step', 'administration')
    fd.append('full_name', form.administration.full_name)
    fd.append('whatsapp', form.administration.whatsapp)
    fd.append('phone', form.administration.phone)
    fd.append('address_kk', form.administration.address_kk)
    fd.append('address_domicile', form.administration.address_domicile)
    fd.append('gender', form.administration.gender)
    fd.append('height_cm', String(form.administration.height_cm))
    fd.append('weight_kg', String(form.administration.weight_kg))
    fileSlots.forEach((s) => {
      if (pendingFiles[s.input]) fd.append(s.input, pendingFiles[s.input])
    })
    const { data: updated } = await axios.post('/api/my-registration', fd)
    progress.value = updated
    hydrateForm()
    clearPendingFiles()
    errorMessage.value = ''
  } catch (error) {
    const msg = error?.response?.data?.message
    const errs = error?.response?.data?.errors
    if (errs && typeof errs === 'object') {
      const first = Object.values(errs).flat()[0]
      errorMessage.value = first || msg || 'Gagal menyimpan data administrasi.'
    } else {
      errorMessage.value = msg || 'Gagal menyimpan data administrasi.'
    }
  } finally {
    saving.value = false
  }
}

onMounted(fetchProgress)
</script>
