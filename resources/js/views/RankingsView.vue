<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      :title="t('rankings.title')"
      :subtitle="t('rankings.subtitle')"
      theme="amber"
      :icon="Trophy"
    />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <aside class="lg:col-span-4 space-y-4">
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-6">
          <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">{{ t('rankings.categoryManagement') }}</h2>

          <div v-if="loadingCategories" class="py-8 text-center text-sm text-gray-400">{{ t('common.refresh') }}…</div>
          <div v-else class="space-y-2">
            <div v-for="group in groups" :key="group.id" class="rounded-xl border border-gray-100 overflow-hidden">
              <button
                type="button"
                class="w-full flex items-center justify-between px-4 py-3 text-left font-medium text-[#1A1A1A] hover:bg-gray-50 transition-colors"
                @click="toggleGroup(group.id)"
              >
                <span class="flex items-center gap-2">
                  {{ group.label }}
                  <span class="text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 font-semibold">
                    {{ scoringModeLabel(group.scoring_mode) }}
                  </span>
                </span>
                <svg class="w-4 h-4 text-gray-400 transition-transform shrink-0" :class="{ 'rotate-180': expandedGroup === group.id }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </button>
              <ul v-show="expandedGroup === group.id" class="border-t border-gray-50 bg-gray-50/50 py-1">
                <li v-for="sub in group.subcategories" :key="sub.id">
                  <button
                    type="button"
                    class="w-full text-left px-6 py-2.5 text-sm transition-colors"
                    :class="selectedGroupId === group.id && selectedSubId === sub.id ? 'text-[#9DB359] font-semibold bg-white' : 'text-gray-600 hover:text-[#1A1A1A]'"
                    @click="selectSubcategory(group.id, sub.id)"
                  >
                    {{ sub.label }}
                  </button>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div v-if="classGuide" class="rounded-2xl border border-[#9DB359]/30 bg-[#9DB359]/5 p-4 text-sm text-gray-700 leading-relaxed">
          <p class="font-semibold text-[#1A1A1A] mb-1">{{ t('rankings.classGuideTitle') }}</p>
          {{ classGuide }}
        </div>

        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-lg shadow-black/5 p-6">
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('rankings.selectClass') }}</label>
          <select
            v-model="selectedClassId"
            :disabled="loadingFilters"
            class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 text-sm focus:bg-white focus:border-gray-200 focus:ring-0 disabled:opacity-60"
          >
            <option value="">{{ loadingFilters ? `${t('common.refresh')}…` : t('rankings.selectClassPlaceholder') }}</option>
            <option v-for="c in classes" :key="c.id" :value="c.id">{{ c.name }} ({{ c.class_code }})</option>
          </select>
          <p v-if="!loadingFilters && classes.length === 0" class="text-xs text-amber-700 mt-2">
            Belum ada kelas tersedia. Buat kelas di menu Kelas kursus terlebih dahulu.
          </p>
          <p v-if="selectedClassMeta" class="text-xs text-gray-500 mt-2">
            {{ t('rankings.classPeriod') }}: {{ selectedClassMeta.academic_period || '—' }}
          </p>
        </div>
      </aside>

      <section class="lg:col-span-8">
        <div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl shadow-black/5 overflow-hidden">
          <div class="px-6 py-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div>
              <h2 class="text-xl font-bold text-[#1A1A1A]">
                {{ activeSubLabel || t('rankings.pickSubcategory') }}
              </h2>
              <p class="text-sm text-gray-500 mt-0.5">
                <span v-if="activeGroupLabel">{{ activeGroupLabel }} · </span>
                {{ scopeLabel }}
                <span v-if="isJasmaniGroup && selectedScoreDate"> · {{ formatScoreDate(selectedScoreDate) }}</span>
              </p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
              <div v-if="isJasmaniGroup" class="flex items-center gap-2">
                <label class="sr-only" for="score-date-filter">{{ t('rankings.selectScoreDate') }}</label>
                <input
                  id="score-date-filter"
                  v-model="selectedScoreDate"
                  type="date"
                  list="score-date-options"
                  class="rounded-full border border-gray-100 bg-gray-50 px-4 py-2 text-sm focus:bg-white focus:border-gray-200 focus:ring-0"
                  :title="t('rankings.selectScoreDate')"
                  @change="loadEntries"
                />
                <datalist id="score-date-options">
                  <option v-for="d in availableDates" :key="d" :value="d" />
                </datalist>
              </div>
              <button
                v-if="isStaff && canLoad && allowsManualInput"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-[#9DB359] text-white hover:bg-[#8aa44d] disabled:opacity-50"
                @click="openManualAdd"
              >
                + {{ t('rankings.manualAdd') }}
              </button>
              <button
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-100 disabled:opacity-50"
                :disabled="loadingEntries || !canLoad"
                @click="loadEntries"
              >
                <svg class="w-4 h-4" :class="{ 'animate-spin': loadingEntries }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ t('common.refresh') }}
              </button>
            </div>
          </div>

          <div v-if="!canLoad" class="p-12 text-center text-gray-500 text-sm">
            {{ scopeHint }}
          </div>
          <div v-else-if="isJasmaniGroup && !selectedScoreDate && availableDates.length === 0" class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">📅</div>
            <p class="text-gray-600 font-medium">{{ t('rankings.selectScoreDatePlaceholder') }}</p>
            <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">{{ t('rankings.emptyManualHint') }}</p>
            <button
              v-if="isStaff && allowsManualInput"
              type="button"
              class="mt-4 px-5 py-2 rounded-full bg-[#1A1A1A] text-white text-sm font-medium"
              @click="openManualAdd"
            >
              + {{ t('rankings.manualAdd') }}
            </button>
          </div>
          <div v-else-if="loadingEntries" class="p-12 text-center text-gray-400">{{ t('rankings.loading') }}</div>
          <div v-else-if="entries.length === 0" class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-2xl mx-auto mb-4">📊</div>
            <p class="text-gray-600 font-medium">{{ t('rankings.emptyTitle') }}</p>
            <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">{{ emptyHint }}</p>
            <button
              v-if="isStaff && allowsManualInput"
              type="button"
              class="mt-4 px-5 py-2 rounded-full bg-[#1A1A1A] text-white text-sm font-medium"
              @click="openManualAdd"
            >
              + {{ t('rankings.manualAdd') }}
            </button>
          </div>
          <div v-else class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="bg-gray-50/80 text-xs uppercase tracking-wider text-gray-500">
                  <th class="px-6 py-4 font-semibold w-16">{{ t('rankings.rank') }}</th>
                  <th class="px-6 py-4 font-semibold">{{ t('rankings.participant') }}</th>
                  <th class="px-6 py-4 font-semibold text-right">{{ t('common.score') }}</th>
                  <th v-if="isStaff && allowsManualInput" class="px-6 py-4 font-semibold w-28 text-right">{{ t('common.actions') }}</th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="row in entries"
                  :key="`${row.manual_id || row.user_id}-${row.rank}-${row.score_date || ''}`"
                  class="border-t border-gray-50 hover:bg-[#9DB359]/5 transition-colors"
                  :class="{ 'bg-yellow-50/60': row.rank <= 3 }"
                >
                  <td class="px-6 py-4">
                    <span
                      class="inline-flex w-8 h-8 items-center justify-center rounded-full text-sm font-bold"
                      :class="rankBadgeClass(row.rank)"
                    >
                      {{ row.rank }}
                    </span>
                  </td>
                  <td class="px-6 py-4">
                    <div class="font-medium text-[#1A1A1A]">{{ row.name }}</div>
                    <p v-if="row.assessment_name" class="text-xs text-gray-500 mt-0.5">{{ row.assessment_name }}</p>
                    <span
                      v-if="row.source === 'manual'"
                      class="inline-block mt-0.5 text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-semibold"
                    >
                      {{ t('rankings.sourceManual') }}
                    </span>
                  </td>
                  <td class="px-6 py-4 text-right">
                    <span
                      v-if="!isJasmaniGroup"
                      class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold tabular-nums"
                      :class="scoreTagClass(row.score)"
                    >
                      {{ row.display }}
                    </span>
                    <span v-else class="font-semibold text-[#9DB359]">{{ row.display }}</span>
                  </td>
                  <td v-if="isStaff && allowsManualInput" class="px-6 py-4 text-right">
                    <template v-if="row.source === 'manual' && row.manual_id">
                      <div class="inline-flex items-center justify-end gap-1">
                        <button
                          type="button"
                          class="inline-flex items-center justify-center w-8 h-8 rounded-full text-gray-600 hover:bg-gray-100 hover:text-[#1A1A1A] transition-colors"
                          :title="t('common.edit')"
                          @click="openManualEdit(row)"
                        >
                          <Pencil class="h-3.5 w-3.5" />
                        </button>
                        <button
                          type="button"
                          class="inline-flex items-center justify-center w-8 h-8 rounded-full text-red-600 hover:bg-red-50 hover:text-red-800 transition-colors"
                          :title="t('common.delete')"
                          @click="deleteManual(row)"
                        >
                          <Trash2 class="h-3.5 w-3.5" />
                        </button>
                      </div>
                    </template>
                    <button
                      v-else
                      type="button"
                      class="text-xs font-medium text-[#9DB359] hover:underline"
                      @click="openManualAddForUser(row)"
                    >
                      {{ isJasmaniGroup ? t('rankings.manualAddForUser') : t('rankings.manualOverride') }}
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>

    <RankingManualModal
      v-if="showManualModal"
      :initial="editingManual"
      :context="rankingContext"
      :context-label="manualContextLabel"
      :unit-placeholder="activeSub?.unit || ''"
      :is-akademik="isAkademikGroup"
      :assessment-options="assessmentOptions"
      @close="closeManualModal"
      @saved="onManualSaved"
    />
  </main>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Pencil, Trash2, Trophy } from 'lucide-vue-next'
import axios from 'axios'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useI18n } from '@/composables/useI18n'
import { scoreTagClass } from '@/utils/scoreMeta'
import { useAppStore } from '@/stores/app'
import { useModal, useToast } from '@/composables/useNotification'
import RankingManualModal from '@/components/RankingManualModal.vue'

const { t } = useI18n()
const store = useAppStore()
const { confirm } = useModal()
const toast = useToast()

const isStaff = computed(() => ['admin', 'mentor'].includes(store.role))

const groups = ref([])
const classGuides = ref({})
const classGuide = ref('')
const classes = ref([])
const entries = ref([])
const availableDates = ref([])

const loadingCategories = ref(true)
const loadingFilters = ref(false)
const loadingEntries = ref(false)
const expandedGroup = ref('jasmani')
const selectedGroupId = ref('jasmani')
const selectedSubId = ref('sprint')
const selectedClassId = ref('')
const selectedScoreDate = ref('')

const showManualModal = ref(false)
const editingManual = ref(null)
const manualEntriesCache = ref([])
const assessmentOptions = ref([])

const activeGroup = computed(() => groups.value.find((g) => g.id === selectedGroupId.value))
const activeSub = computed(() => activeGroup.value?.subcategories?.find((s) => s.id === selectedSubId.value))
const activeGroupLabel = computed(() => activeGroup.value?.label || '')
const activeSubLabel = computed(() => activeSub.value?.label || '')
const isJasmaniGroup = computed(() => selectedGroupId.value === 'jasmani')
const isAkademikGroup = computed(() => selectedGroupId.value === 'akademik')
const allowsManualInput = computed(() => {
  const group = activeGroup.value
  if (!group) return false
  return group.id === 'jasmani' || group.allows_manual === true
})

const selectedClassMeta = computed(() =>
  classes.value.find((c) => String(c.id) === String(selectedClassId.value)),
)

const rankingContext = computed(() => ({
  scope: 'class',
  group_id: selectedGroupId.value,
  subcategory_id: selectedSubId.value,
  class_id: selectedClassId.value,
  cohort: null,
}))

const manualContextLabel = computed(() => {
  const parts = [activeGroupLabel.value, activeSubLabel.value, scopeLabel.value]
  if (selectedClassMeta.value) parts.push(selectedClassMeta.value.name)
  return parts.filter(Boolean).join(' · ')
})

const scopeLabel = computed(() => {
  const base = t('rankings.scopeClass')
  if (selectedClassMeta.value?.name) {
    return `${base}: ${selectedClassMeta.value.name}`
  }
  return base
})

const canLoad = computed(() => {
  if (!selectedGroupId.value || !selectedSubId.value) return false
  if (!selectedClassId.value) return false
  return true
})

const scopeHint = computed(() => {
  if (!selectedSubId.value) return t('rankings.pickSubcategory')
  if (!selectedClassId.value) return t('rankings.selectClassPlaceholder')
  if (isJasmaniGroup.value && !selectedScoreDate.value) return t('rankings.selectScoreDatePlaceholder')
  return ''
})

const emptyHint = computed(() => {
  const mode = activeGroup.value?.scoring_mode
  if (mode === 'test') return t('rankings.emptyTestHint')
  return t('rankings.emptyManualHint')
})

function scoringModeLabel(mode) {
  if (mode === 'test') return t('rankings.modeTest')
  if (mode === 'material_only') return t('rankings.modeMaterial')
  return t('rankings.modeManual')
}

function toggleGroup(id) {
  expandedGroup.value = expandedGroup.value === id ? '' : id
}

function selectSubcategory(groupId, subId) {
  selectedGroupId.value = groupId
  selectedSubId.value = subId
  expandedGroup.value = groupId
  classGuide.value = classGuides.value[groupId] || ''
}

function rankBadgeClass(rank) {
  if (rank === 1) return 'bg-yellow-400 text-yellow-900'
  if (rank === 2) return 'bg-gray-300 text-gray-800'
  if (rank === 3) return 'bg-amber-600 text-white'
  return 'bg-gray-100 text-gray-600'
}

function formatScoreDate(value) {
  if (!value) return '—'
  try {
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(`${value}T00:00:00`))
  } catch {
    return value
  }
}

function todayIso() {
  return new Date().toISOString().slice(0, 10)
}

async function loadCategories() {
  loadingCategories.value = true
  try {
    const { data } = await axios.get('/api/rankings/categories')
    groups.value = data.groups || []
    classGuides.value = data.class_subject_guide || {}
    classGuide.value = classGuides.value[selectedGroupId.value] || ''
    if (groups.value.length && !groups.value.find((g) => g.id === selectedGroupId.value)) {
      selectedGroupId.value = groups.value[0].id
      selectedSubId.value = groups.value[0].subcategories?.[0]?.id || ''
      expandedGroup.value = groups.value[0].id
      classGuide.value = classGuides.value[selectedGroupId.value] || ''
    }
  } finally {
    loadingCategories.value = false
  }
}

async function loadFilters() {
  loadingFilters.value = true
  try {
    const { data } = await axios.get('/api/rankings/filters')
    classes.value = data.classes || []
    if (classes.value.length === 1 && !selectedClassId.value) {
      selectedClassId.value = String(classes.value[0].id)
    }
  } catch (error) {
    classes.value = []
    const message = error?.response?.data?.message || 'Gagal memuat daftar kelas.'
    toast.error('Error', message)
  } finally {
    loadingFilters.value = false
  }
}

async function loadAssessmentOptions() {
  if (!selectedClassId.value || !isAkademikGroup.value) {
    assessmentOptions.value = []
    return
  }
  try {
    const { data } = await axios.get(`/api/bimble-classes/${selectedClassId.value}`)
    const className = data?.name
    const tests = (data?.test_definitions || data?.testDefinitions || [])
      .map((t) => t.name)
      .filter(Boolean)
    const options = className ? [className, ...tests] : tests
    assessmentOptions.value = [...new Set(options)]
  } catch {
    assessmentOptions.value = selectedClassMeta.value?.name ? [selectedClassMeta.value.name] : []
  }
}

async function loadEntries() {
  if (!canLoad.value) return
  loadingEntries.value = true
  try {
    const params = {
      scope: 'class',
      group_id: selectedGroupId.value,
      subcategory_id: selectedSubId.value,
      class_id: selectedClassId.value,
    }
    if (isJasmaniGroup.value && selectedScoreDate.value) {
      params.score_date = selectedScoreDate.value
    }

    const { data } = await axios.get('/api/rankings', { params })
    entries.value = data.entries || []
    if (data.class_guide) classGuide.value = data.class_guide

    if (isJasmaniGroup.value) {
      availableDates.value = data.available_dates || []
      if (!selectedScoreDate.value && data.score_date) {
        selectedScoreDate.value = data.score_date
      }
    } else {
      selectedScoreDate.value = ''
      availableDates.value = []
    }

    if (isStaff.value) {
      try {
        const manualParams = { ...params }
        if (isJasmaniGroup.value && selectedScoreDate.value) {
          manualParams.score_date = selectedScoreDate.value
        }
        const manualRes = await axios.get('/api/rankings/manual', { params: manualParams })
        manualEntriesCache.value = manualRes.data.items || []
      } catch {
        manualEntriesCache.value = []
      }
    }
  } catch {
    entries.value = []
  } finally {
    loadingEntries.value = false
  }
}

function openManualAdd() {
  if (isAkademikGroup.value) loadAssessmentOptions()
  editingManual.value = null
  showManualModal.value = true
}

function openManualAddForUser(row) {
  if (isAkademikGroup.value) loadAssessmentOptions()
  editingManual.value = {
    user_id: row.user_id,
    user: { id: row.user_id, name: row.name },
    score: '',
    unit: row.unit || activeSub.value?.unit || '',
    score_date: todayIso(),
  }
  showManualModal.value = true
}

function openManualEdit(row) {
  if (isAkademikGroup.value) loadAssessmentOptions()
  const cached = manualEntriesCache.value.find((m) => m.id === row.manual_id)
  editingManual.value = cached || {
    id: row.manual_id,
    user_id: row.user_id,
    user: { id: row.user_id, name: row.name },
    score: row.score,
    unit: row.unit,
    notes: row.notes,
    assessment_name: row.assessment_name,
    score_date: row.score_date,
  }
  showManualModal.value = true
}

function closeManualModal() {
  showManualModal.value = false
  editingManual.value = null
}

function onManualSaved(payload) {
  toast.success('OK', t('rankings.manualSaved'))
  if (isJasmaniGroup.value && payload?.score_date) {
    selectedScoreDate.value = payload.score_date
  }
  loadEntries()
}

async function deleteManual(row) {
  const ok = await confirm({
    title: t('rankings.manualDeleteTitle'),
    message: t('rankings.manualDeleteMessage', { name: row.name }),
    confirmText: t('common.delete'),
    type: 'danger',
  })
  if (!ok || !row.manual_id) return
  try {
    await axios.delete(`/api/rankings/manual/${row.manual_id}`)
    toast.success('OK', t('rankings.manualDeleted'))
    loadEntries()
  } catch {
    toast.error('Error', t('rankings.manualDeleteFailed'))
  }
}

watch([selectedGroupId, selectedSubId, selectedClassId], () => {
  if (isJasmaniGroup.value) {
    selectedScoreDate.value = ''
    availableDates.value = []
  }
  if (isAkademikGroup.value && selectedClassId.value) {
    loadAssessmentOptions()
  } else {
    assessmentOptions.value = []
  }
  if (canLoad.value) loadEntries()
})

onMounted(async () => {
  await store.fetchUser()
  await Promise.all([loadCategories(), loadFilters()])
  if (canLoad.value) loadEntries()
})
</script>

<style scoped>
</style>
