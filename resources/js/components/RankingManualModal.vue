<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="$emit('close')" />
    <div class="relative w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
      <h2 class="text-xl font-bold text-[#1A1A1A]">
        {{ isEdit ? t('rankings.manualEditTitle') : t('rankings.manualAddTitle') }}
      </h2>
      <p class="text-sm text-gray-500 mt-1">{{ contextLabel }}</p>

      <form class="mt-6 space-y-4" @submit.prevent="submit">
        <div v-if="!isEdit">
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('rankings.manualParticipant') }}</label>
          <input
            v-model="studentSearch"
            type="text"
            class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 text-sm focus:bg-white focus:border-gray-200 focus:ring-0"
            :placeholder="t('rankings.manualParticipantPlaceholder')"
            @input="searchStudents"
          />
          <ul v-if="studentResults.length" class="mt-2 rounded-xl border border-gray-100 bg-white shadow-lg max-h-40 overflow-y-auto">
            <li
              v-for="s in studentResults"
              :key="s.id"
              class="px-4 py-2.5 text-sm cursor-pointer hover:bg-[#9DB359]/10"
              :class="{ 'bg-[#9DB359]/10 font-medium': form.user_id === s.id }"
              @click="pickStudent(s)"
            >
              {{ s.name }}
              <span class="text-gray-400 text-xs ml-1">{{ s.email }}</span>
            </li>
          </ul>
          <p v-if="selectedStudentName" class="text-xs text-emerald-700 mt-2">
            {{ t('rankings.manualSelected') }}: {{ selectedStudentName }}
          </p>
        </div>
        <div v-else>
          <label class="block text-sm font-medium text-gray-700 mb-1">{{ t('rankings.participant') }}</label>
          <p class="text-sm font-medium text-[#1A1A1A]">{{ initial?.user?.name || '—' }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('common.score') }}</label>
          <input
            v-model.number="form.score"
            type="number"
            step="any"
            required
            class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 text-sm focus:bg-white focus:border-gray-200 focus:ring-0"
          />
        </div>

        <div v-if="isJasmani">
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('rankings.manualDate') }}</label>
          <input
            v-model="form.score_date"
            type="date"
            required
            class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 text-sm focus:bg-white focus:border-gray-200 focus:ring-0"
          />
          <p class="text-xs text-gray-400 mt-1">{{ t('rankings.manualDateHint') }}</p>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('rankings.manualNotes') }}</label>
          <textarea
            v-model="form.notes"
            rows="2"
            class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 text-sm resize-none focus:bg-white focus:border-gray-200 focus:ring-0"
            :placeholder="t('rankings.manualNotesPlaceholder')"
          />
        </div>

        <p v-if="errorMessage" class="text-sm text-red-600">{{ errorMessage }}</p>

        <div class="flex justify-end gap-3 pt-2">
          <button type="button" class="px-5 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 text-sm font-medium" @click="$emit('close')">
            {{ t('common.cancel') }}
          </button>
          <button
            type="submit"
            class="px-5 py-2.5 rounded-full bg-[#1A1A1A] text-white text-sm font-medium disabled:opacity-50"
            :disabled="saving || (!isEdit && !form.user_id)"
          >
            {{ saving ? '…' : (isEdit ? t('common.save') : t('rankings.manualAddSubmit')) }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, watch } from 'vue'
import { refreshCsrfToken } from '@/bootstrap'
import { useI18n } from '@/composables/useI18n'

const props = defineProps({
  initial: { type: Object, default: null },
  context: { type: Object, required: true },
  contextLabel: { type: String, default: '' },
  unitPlaceholder: { type: String, default: '' },
})

const emit = defineEmits(['close', 'saved'])
const { t } = useI18n()

const isEdit = computed(() => !!props.initial?.id)
const isJasmani = computed(() => props.context?.group_id === 'jasmani')
const today = () => new Date().toISOString().slice(0, 10)
const saving = ref(false)
const errorMessage = ref('')
const studentSearch = ref('')
const studentResults = ref([])
const selectedStudentName = ref('')

let searchTimer = null

const form = reactive({
  user_id: null,
  score: '',
  unit: '',
  notes: '',
  score_date: today(),
})

watch(
  () => props.initial,
  (val) => {
    if (val) {
      form.user_id = val.user_id
      form.score = val.score
      form.unit = val.unit || ''
      form.notes = val.notes || ''
      form.score_date = val.score_date || today()
      selectedStudentName.value = val.user?.name || ''
    } else {
      form.user_id = null
      form.score = ''
      form.unit = props.unitPlaceholder || ''
      form.notes = ''
      form.score_date = today()
      selectedStudentName.value = ''
      studentSearch.value = ''
    }
    errorMessage.value = ''
  },
  { immediate: true },
)

function pickStudent(s) {
  form.user_id = s.id
  selectedStudentName.value = s.name
  studentSearch.value = s.name
  studentResults.value = []
}

function searchStudents() {
  clearTimeout(searchTimer)
  const q = studentSearch.value.trim()
  if (q.length < 2) {
    studentResults.value = []
    return
  }
  searchTimer = setTimeout(async () => {
    try {
      const { data } = await window.axios.get('/api/students/search', { params: { search: q } })
      studentResults.value = data || []
    } catch {
      studentResults.value = []
    }
  }, 300)
}

function resolveUnit() {
  return form.unit || props.unitPlaceholder || undefined
}

async function submit() {
  saving.value = true
  errorMessage.value = ''
  try {
    await refreshCsrfToken()
    if (isEdit.value) {
      const payload = {
        score: form.score,
        unit: resolveUnit(),
        notes: form.notes || null,
      }
      if (isJasmani.value) payload.score_date = form.score_date
      await window.axios.put(`/api/rankings/manual/${props.initial.id}`, payload)
    } else {
      const payload = {
        scope: props.context.scope,
        group_id: props.context.group_id,
        subcategory_id: props.context.subcategory_id,
        user_id: form.user_id,
        score: form.score,
        unit: resolveUnit(),
        notes: form.notes || null,
      }
      if (props.context.scope === 'class') payload.class_id = props.context.class_id
      if (props.context.scope === 'cohort') payload.cohort = props.context.cohort
      if (isJasmani.value) payload.score_date = form.score_date
      await window.axios.post('/api/rankings/manual', payload)
    }
    emit('saved', {
      score_date: isJasmani.value ? form.score_date : null,
    })
    emit('close')
  } catch (e) {
    const msg = e?.response?.data?.message
    const errors = e?.response?.data?.errors
    errorMessage.value =
      (errors && Object.values(errors).flat()[0]) || msg || t('rankings.manualSaveFailed')
  } finally {
    saving.value = false
  }
}
</script>
