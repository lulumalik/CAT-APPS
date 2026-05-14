<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
    <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ t('modals.testCreate.title') }}</h2>
          <p class="text-gray-500 mt-1">{{ t('modals.testCreate.subtitle') }}</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form class="space-y-6" @submit.prevent="submit">
        <div class="grid grid-cols-1 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testCreate.nameLabel') }}</label>
            <input v-model="form.name" :placeholder="t('modals.testCreate.namePlaceholder')" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testCreate.descriptionLabel') }}</label>
            <textarea v-model="form.description" rows="2" :placeholder="t('modals.testCreate.descriptionPlaceholder')" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all resize-none"></textarea>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testCreate.categoryLabel') }}</label>
            <select v-model="form.category" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
              <option value="">{{ t('modals.testCreate.categoryPlaceholder') }}</option>
              <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testCreate.durationLabel') }}</label>
            <input v-model.number="form.duration" type="number" min="1" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
          </div>
        </div>

        <div class="rounded-xl border border-border bg-sky/40 px-4 py-3">
          <label class="inline-flex items-center gap-3 text-sm font-medium text-text cursor-pointer">
            <input v-model="form.isFreeTryout" type="checkbox" class="rounded border-border text-secondary focus:ring-secondary" />
            Jadikan ini soal Tryout Gratis (muncul di halaman publik free tryout)
          </label>
        </div>

        <div class="grid grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testCreate.scheduleLabel') }}</label>
            <div class="flex items-center w-full rounded-xl border border-gray-100 bg-gray-50 focus-within:bg-white focus-within:border-gray-200 focus-within:ring-2 focus-within:ring-gray-100 transition-all overflow-hidden">
              <input v-model="scheduleDate" type="date" class="bg-transparent border-none focus:ring-0 px-4 py-3 flex-1 min-w-[140px] text-gray-700" required />
              <div class="flex items-center px-3 border-l border-gray-200 gap-1 bg-gray-100/50 h-full">
                <select v-model="scheduleHour" class="bg-transparent border-none focus:ring-0 text-center font-medium p-0 cursor-pointer hover:text-[#9DB359] text-gray-700">
                  <option v-for="h in hourOptions" :key="h" :value="h">{{ h }}</option>
                </select>
                <span class="font-bold text-gray-400">:</span>
                <select v-model="scheduleMinute" class="bg-transparent border-none focus:ring-0 text-center font-medium p-0 cursor-pointer hover:text-[#9DB359] text-gray-700">
                  <option v-for="m in minuteOptions" :key="m" :value="m">{{ m }}</option>
                </select>
              </div>
            </div>
          </div>
          <div></div>
        </div>

        <!-- Start and End Time -->
        <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-6">
          <h3 class="font-bold text-gray-900 text-sm mb-4 flex items-center gap-2">
            <span class="text-lg">🕐</span> {{ t('modals.testCreate.timeSettingsTitle') }}
          </h3>
          <div class="grid grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testCreate.startTimeLabel') }}</label>
              <div class="flex items-center w-full rounded-xl border border-blue-100 bg-white focus-within:border-blue-300 focus-within:ring-2 focus-within:ring-blue-100 transition-all overflow-hidden">
                <input v-model="startDate" type="date" class="bg-transparent border-none focus:ring-0 pl-4 py-3 flex-1 min-w-[130px] text-gray-700" />
                <div class="flex items-center px-3 border-l border-blue-100 gap-1 bg-blue-50/30 h-full">
                  <select v-model="startHour" class="bg-transparent border-none focus:ring-0 text-center font-medium p-0 cursor-pointer hover:text-blue-600 text-gray-700">
                    <option v-for="h in hourOptions" :key="h" :value="h">{{ h }}</option>
                  </select>
                  <span class="font-bold text-blue-300">:</span>
                  <select v-model="startMinute" class="bg-transparent border-none focus:ring-0 text-center font-medium p-0 cursor-pointer hover:text-blue-600 text-gray-700">
                    <option v-for="m in minuteOptions" :key="m" :value="m">{{ m }}</option>
                  </select>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-2">{{ t('modals.testCreate.startTimeHint') }}</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.testCreate.endTimeLabel') }}</label>
              <div class="flex items-center w-full rounded-xl border border-blue-100 bg-white focus-within:border-blue-300 focus-within:ring-2 focus-within:ring-blue-100 transition-all overflow-hidden">
                <input v-model="endDate" type="date" class="bg-transparent border-none focus:ring-0 pl-4 py-3 flex-1 min-w-[130px] text-gray-700" :min="startDate" required />
                <div class="flex items-center px-3 border-l border-blue-100 gap-1 bg-blue-50/30 h-full">
                  <select v-model="endHour" class="bg-transparent border-none focus:ring-0 text-center font-medium p-0 cursor-pointer hover:text-blue-600 text-gray-700">
                    <option v-for="h in hourOptions" :key="h" :value="h">{{ h }}</option>
                  </select>
                  <span class="font-bold text-blue-300">:</span>
                  <select v-model="endMinute" class="bg-transparent border-none focus:ring-0 text-center font-medium p-0 cursor-pointer hover:text-blue-600 text-gray-700">
                    <option v-for="m in minuteOptions" :key="m" :value="m">{{ m }}</option>
                  </select>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-2">{{ t('modals.testCreate.endTimeHint') }}</p>
            </div>
          </div>
          <div class="mt-4 text-xs text-blue-600 font-medium">{{ t('modals.testCreate.activeHint') }}</div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
          <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="$emit('close')">{{ t('modals.testCreate.cancel') }}</button>
          <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform active:scale-95 transition-all">{{ isEdit ? t('modals.testCreate.update') : t('modals.testCreate.submit') }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, ref, watch } from 'vue'
import { useI18n } from '@/composables/useI18n'

const props = defineProps({
  initial: { type: Object, default: null },
  questions: { type: Array, default: () => [] },
  categories: { type: Array, default: () => [] },
})
const emit = defineEmits(['close','submit'])

const toJakartaDatetimeInputValue = (value) => {
  if (!value) return ''
  
  // Jika benar-benar sudah format lokal input (tanpa detik/timezone), biarkan.
  // Penting: string ISO dengan suffix timezone (contoh "...Z") harus tetap dikonversi.
  if (typeof value === 'string' && /^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(value)) {
    return value
  }

  const date = value instanceof Date ? value : new Date(value)
  if (Number.isNaN(date.getTime())) return ''

  try {
    const parts = new Intl.DateTimeFormat('en-CA', {
      timeZone: 'Asia/Jakarta',
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    }).formatToParts(date)

    const byType = Object.fromEntries(parts.filter(p => p.type !== 'literal').map(p => [p.type, p.value]))
    
    let hour = byType.hour
    if (hour === '24') hour = '00'

    return `${byType.year}-${byType.month}-${byType.day}T${hour}:${byType.minute}`
  } catch (e) {
    const yyyy = date.getFullYear()
    const mm = String(date.getMonth() + 1).padStart(2, '0')
    const dd = String(date.getDate()).padStart(2, '0')
    const hh = String(date.getHours()).padStart(2, '0')
    const mi = String(date.getMinutes()).padStart(2, '0')
    return `${yyyy}-${mm}-${dd}T${hh}:${mi}`
  }
}

const addMinutesToString = (datetimeStr, minutes) => {
  if (!datetimeStr) return ''
  // Parsing format YYYY-MM-DDTHH:mm
  const parts = datetimeStr.split('T')
  if (parts.length !== 2) return datetimeStr
  
  const [yyyy, mm, dd] = parts[0].split('-').map(Number)
  const [hh, mi] = parts[1].split(':').map(Number)
  
  // Buat date local supaya tidak geser timezone
  const d = new Date(yyyy, mm - 1, dd, hh, mi)
  d.setMinutes(d.getMinutes() + Number(minutes))
  
  const ny = d.getFullYear()
  const nm = String(d.getMonth() + 1).padStart(2, '0')
  const nd = String(d.getDate()).padStart(2, '0')
  const nh = String(d.getHours()).padStart(2, '0')
  const nmi = String(d.getMinutes()).padStart(2, '0')
  
  return `${ny}-${nm}-${nd}T${nh}:${nmi}`
}

const base = () => {
  const now = toJakartaDatetimeInputValue(new Date())
  return ({ 
  name: '', 
  description: '', 
  category: '', 
  duration: 30, 
  scheduleAt: now,
  startTime: now,
  endTime: toJakartaDatetimeInputValue(new Date(Date.now() + 3600000)),
  isActive: false,
  isFreeTryout: false,
  questionIds: []
})
}
const form = reactive(base())
const isEdit = computed(() => !!props.initial)
const { t } = useI18n()
const isHydratingFromInitial = ref(false)

const hourOptions = Array.from({ length: 24 }, (_, i) => String(i).padStart(2, '0'))
const minuteOptions = Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0'))

const getParts = (value) => {
  if (!value || typeof value !== 'string') return { date: '', hour: '00', minute: '00' }
  const date = value.slice(0, 10)
  const hour = value.length >= 13 ? value.slice(11, 13) : '00'
  const minute = value.length >= 16 ? value.slice(14, 16) : '00'
  return { date, hour, minute }
}

const setDatePart = (field, date) => {
  const { hour, minute } = getParts(form[field])
  form[field] = date ? `${date}T${hour}:${minute}` : ''
}

const setHourPart = (field, hour) => {
  const { date, minute } = getParts(form[field])
  form[field] = date ? `${date}T${hour}:${minute}` : ''
}

const setMinutePart = (field, minute) => {
  const { date, hour } = getParts(form[field])
  form[field] = date ? `${date}T${hour}:${minute}` : ''
}

const scheduleDate = computed({
  get: () => getParts(form.startTime).date,
  set: (v) => setDatePart('startTime', v),
})
const scheduleHour = computed({
  get: () => getParts(form.startTime).hour,
  set: (v) => setHourPart('startTime', v),
})
const scheduleMinute = computed({
  get: () => getParts(form.startTime).minute,
  set: (v) => setMinutePart('startTime', v),
})

const startDate = computed({
  get: () => getParts(form.startTime).date,
  set: (v) => setDatePart('startTime', v),
})
const startHour = computed({
  get: () => getParts(form.startTime).hour,
  set: (v) => setHourPart('startTime', v),
})
const startMinute = computed({
  get: () => getParts(form.startTime).minute,
  set: (v) => setMinutePart('startTime', v),
})

const endDate = computed({
  get: () => getParts(form.endTime).date,
  set: (v) => setDatePart('endTime', v),
})
const endHour = computed({
  get: () => getParts(form.endTime).hour,
  set: (v) => setHourPart('endTime', v),
})
const endMinute = computed({
  get: () => getParts(form.endTime).minute,
  set: (v) => setMinutePart('endTime', v),
})

// Schedule date selalu sama dengan start time (single source of truth).
watch(() => form.startTime, (newStart) => {
  form.scheduleAt = newStart || ''
}, { immediate: true })

watch([() => form.startTime, () => form.duration], ([newStart, newDuration]) => {
  if (isHydratingFromInitial.value) return
  if (newStart && newDuration) {
    form.endTime = addMinutesToString(newStart, newDuration)
  }
})

watch(() => props.initial, (val) => {
  isHydratingFromInitial.value = true

  if (!val) {
    Object.assign(form, base())
    isHydratingFromInitial.value = false
    return
  }

  const next = JSON.parse(JSON.stringify(val))
  next.startTime = toJakartaDatetimeInputValue(val.startTime ?? val.start_time)
  next.scheduleAt = next.startTime
  next.endTime = toJakartaDatetimeInputValue(val.endTime ?? val.end_time)
  next.isActive = val.isActive ?? val.is_active ?? false
  next.isFreeTryout = val.isFreeTryout ?? val.is_free_tryout ?? false
  next.questionIds = Array.isArray(val.questionIds ?? val.question_ids) ? (val.questionIds ?? val.question_ids) : (next.questionIds ?? [])
  Object.assign(form, next)

  // Lepas mode hidrasi setelah assignment awal selesai,
  // agar endTime dari backend tidak langsung tertimpa auto-sync.
  queueMicrotask(() => {
    isHydratingFromInitial.value = false
  })
}, { immediate: true })

const submit = () => {
  emit('submit', {
    ...JSON.parse(JSON.stringify(form)),
    scheduleAt: form.startTime,
  })
}
</script>

<style scoped>
</style>
