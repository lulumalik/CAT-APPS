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
            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.fullName') }}</label>
            <input v-model="form.administration.full_name" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.phone') }}</label>
            <input v-model="form.administration.phone" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.address') }}</label>
            <textarea v-model="form.administration.address" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
            <label class="block text-sm font-medium text-gray-700">URL foto profil</label>
            <input v-model="form.administration.profile_photo_url" type="url" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" placeholder="https://..." />
          </template>

          <template v-if="activeStep === 'psychology'">
            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.motivation') }}</label>
            <textarea v-model="form.psychology.motivation" rows="4" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.expectations') }}</label>
            <textarea v-model="form.psychology.expectations" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
          </template>

          <template v-if="activeStep === 'health'">
            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.healthNotes') }}</label>
            <textarea v-model="form.health.notes" rows="4" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
            <label class="block text-sm font-medium text-gray-700">{{ t('registration.fields.allergies') }}</label>
            <input v-model="form.health.allergies" type="text" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
          </template>

          <template v-if="activeStep === 'physical'">
            <label class="block text-sm font-medium text-gray-700">Tinggi badan (cm)</label>
            <input v-model="form.physical.height_cm" type="number" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
            <label class="block text-sm font-medium text-gray-700">Berat badan (kg)</label>
            <input v-model="form.physical.weight_kg" type="number" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
            <label class="block text-sm font-medium text-gray-700">Catatan latihan fisik</label>
            <textarea v-model="form.physical.notes" rows="3" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm" />
          </template>
        </div>

        <div class="mt-8 flex justify-end gap-3">
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

const { t } = useI18n()

const loading = ref(true)
const saving = ref(false)
const progress = ref(null)
const errorMessage = ref('')

const form = reactive({
  administration: { full_name: '', phone: '', address: '', profile_photo_url: '' },
  psychology: { motivation: '', expectations: '' },
  health: { notes: '', allergies: '' },
  physical: { height_cm: '', weight_kg: '', notes: '' },
})

const steps = computed(() => [
  { key: 'administration', label: t('registration.steps.admin'), statusKey: 'administration_status' },
  { key: 'psychology', label: t('registration.steps.psychology'), statusKey: 'psychology_status' },
  { key: 'health', label: t('registration.steps.health'), statusKey: 'health_status' },
  { key: 'physical', label: 'Fisik', statusKey: 'physical_status' },
])

const activeStep = computed(() => {
  if (!progress.value) return 'administration'
  const cs = progress.value.current_step
  if (cs === 'completed') return 'physical'
  return cs
})

const stepRevisionNote = computed(() => {
  const p = progress.value
  if (!p) return ''
  const key = `${activeStep.value}_admin_note`
  return p[key] || ''
})

const activeStepMeta = computed(() => steps.value.find((s) => s.key === activeStep.value) || steps.value[0])
const activeStepIndex = computed(() => steps.value.findIndex((s) => s.key === activeStep.value))

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

function hydrateForm() {
  const p = progress.value
  if (!p) return
  if (p.administration_data) Object.assign(form.administration, p.administration_data)
  if (p.psychology_data) Object.assign(form.psychology, p.psychology_data)
  if (p.health_data) Object.assign(form.health, p.health_data)
  if (p.physical_data) Object.assign(form.physical, p.physical_data)
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

async function submitStep() {
  if (!progress.value || progress.value.fully_completed) return
  const step = progress.value.current_step
  if (!step || step === 'completed') return
  saving.value = true
  try {
    const data =
      step === 'administration'
        ? { ...form.administration }
        : step === 'psychology'
          ? { ...form.psychology }
          : step === 'health'
            ? { ...form.health }
            : { ...form.physical }
    const { data: updated } = await axios.put('/api/my-registration', { step, data })
    progress.value = updated
    hydrateForm()
    errorMessage.value = ''
  } catch (error) {
    errorMessage.value = error?.response?.data?.message || 'Gagal menyimpan data langkah pendaftaran.'
  } finally {
    saving.value = false
  }
}

onMounted(fetchProgress)
</script>
