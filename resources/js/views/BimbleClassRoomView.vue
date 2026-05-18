<template>
  <div class="min-h-screen bg-gradient-to-b from-[#F7FAF4] via-[#F4F6F3] to-[#EEF3E8] font-sans text-[#1A1A1A]">
    <div v-if="loading" class="py-24 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="errorMessage"
      class="max-w-3xl mx-auto mt-10 rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>

    <template v-else-if="workspace">
      <!-- Header -->
      <header class="bg-gradient-to-r from-primary via-[#2F6BFF] to-primary text-white px-4 md:px-10 py-6 md:py-10 rounded-b-[1.5rem] md:rounded-b-[2rem] shadow-xl shadow-[#7CB342]/20 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 pointer-events-none bg-[radial-gradient(circle_at_20%_20%,white,transparent_45%)]" />
        <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10 blur-3xl pointer-events-none" />
        <div class="relative max-w-7xl mx-auto flex flex-col gap-5 md:gap-8 lg:flex-row lg:items-end">
          <div class="flex gap-3 md:gap-5 items-start">
            <div class="w-14 h-14 md:w-20 md:h-20 rounded-2xl bg-white/20 flex items-center justify-center text-[10px] md:text-xs font-bold text-center leading-tight shrink-0 border border-white/30 shadow-lg shadow-black/10">
              BIMBEL
            </div>
            <div>
              <p class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold tracking-wide text-white/90">
                {{ t('bimble.classLabel') }}
              </p>
              <h1 class="text-xl md:text-3xl font-bold tracking-tight leading-tight">{{ workspace.class.name }}</h1>
              <p class="text-white/90 text-sm mt-1">{{ t('bimble.code') }}: {{ workspace.class.class_code }}</p>
            </div>
          </div>
          <div class="grid w-full grid-cols-2 gap-4 text-sm sm:flex sm:w-auto sm:flex-wrap sm:gap-8 lg:ml-auto">
            <div>
              <div class="text-white/70 text-xs uppercase tracking-wide">{{ t('bimble.instructor') }}</div>
              <div class="font-medium">{{ workspace.class.instructor?.name || workspace.class.instructor_name || '—' }}</div>
            </div>
            <div>
              <div class="text-white/70 text-xs uppercase tracking-wide">{{ t('bimble.period') }}</div>
              <div class="font-medium">{{ formattedPeriod }}</div>
            </div>
            <div>
              <div class="text-white/70 text-xs uppercase tracking-wide">{{ t('bimble.programType') }}</div>
              <div class="font-medium">{{ formatProgram(workspace.class.program_type) }}</div>
            </div>
          </div>
        </div>
      </header>

      <div class="max-w-7xl mx-auto px-4 py-5 md:py-8 space-y-5 md:space-y-6">
        <section class="grid grid-cols-1 gap-3 sm:grid-cols-3">
          <div class="rounded-2xl border border-[#9DB359]/20 bg-white/90 px-5 py-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ t('bimble.menu.sessions') }}</p>
            <p class="mt-1 text-2xl font-bold text-[#1A1A1A]">{{ totalSessions }}</p>
          </div>
          <div class="rounded-2xl border border-[#9DB359]/20 bg-white/90 px-5 py-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ t('bimble.menu.tests') }}</p>
            <p class="mt-1 text-2xl font-bold text-[#1A1A1A]">{{ totalTests }}</p>
          </div>
          <div class="rounded-2xl border border-[#9DB359]/20 bg-white/90 px-5 py-4 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ t('bimble.openMaterial') }}</p>
            <p class="mt-1 text-2xl font-bold text-[#1A1A1A]">{{ totalMaterials }}</p>
          </div>
        </section>

        <div class="grid grid-cols-1 gap-5 md:gap-8 lg:grid-cols-[240px_1fr_280px]">
        <!-- Sidebar -->
        <aside class="rounded-2xl border border-white/70 bg-white/70 p-2 md:p-3 shadow-sm backdrop-blur-sm lg:sticky lg:top-4 self-start">
          <router-link to="/dashboard" class="block px-4 py-2 rounded-xl text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-100">
            ← {{ t('bimble.back') }}
          </router-link>
          <div class="mt-2 flex gap-2 overflow-x-auto pb-1 lg:mt-0 lg:block lg:space-y-2 lg:overflow-visible lg:pb-0">
            <div
              v-for="item in sidebarItems"
              :key="item.key"
              class="px-4 py-2.5 rounded-xl text-sm cursor-pointer border transition-all whitespace-nowrap"
              :class="section === item.key ? 'bg-gradient-to-r from-[#ECF5DD] to-white shadow-sm border-[#9DB359]/40 text-[#1A1A1A] font-semibold' : 'border-transparent text-gray-600 hover:bg-white/80'"
              @click="section = item.key"
            >
              {{ item.label }}
            </div>
          </div>
        </aside>

        <!-- Main -->
        <section class="space-y-6">
          <div v-if="section === 'diskusi'" class="rounded-2xl md:rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-4 md:p-8 min-h-[200px]">
            <div class="rounded-2xl border border-dashed border-[#9DB359]/40 bg-[#F8FBEF] px-4 py-5 text-sm text-gray-600">
              {{ t('bimble.discussionPlaceholder') }}
            </div>
          </div>

          <div v-if="section === 'sesi' && !workspace.flags.hide_materials" class="space-y-6">
            <div v-for="(materials, sessionNum) in workspace.materials_by_session" :key="sessionNum" class="rounded-2xl md:rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 overflow-hidden">
              <div class="px-4 md:px-6 py-4 bg-gradient-to-r from-[#F7FAF1] to-[#F2F7E8] border-b border-gray-100 font-semibold text-[#1A1A1A]">
                {{ t('bimble.session') }} {{ sessionNum }}
              </div>
              <ul class="divide-y divide-gray-100">
                <li v-for="m in materials" :key="m.id" class="px-4 md:px-6 py-4 flex flex-col items-start justify-between gap-2 sm:flex-row sm:items-center sm:gap-4 hover:bg-gray-50/80">
                  <span class="font-medium text-sm md:text-base">{{ m.title }}</span>
                  <router-link
                    v-if="m?.slug"
                    :to="{ name: 'blog-detail', params: { slug: m.slug } }"
                    class="text-sm font-semibold text-[#7CB342] shrink-0 hover:text-[#689F38]"
                  >
                    {{ t('bimble.openMaterial') }}
                  </router-link>
                  <span v-else class="text-xs text-gray-400">slug missing</span>
                </li>
              </ul>
            </div>
            <p v-if="sessionKeys.length === 0" class="text-gray-500 text-sm">{{ t('bimble.noMaterials') }}</p>
          </div>

          <div v-if="section === 'sesi' && workspace.flags.hide_materials" class="rounded-2xl md:rounded-[2rem] bg-amber-50 border border-amber-100 p-4 md:p-6 text-amber-900 text-sm">
            {{ t('bimble.tryoutNoMaterials') }}
          </div>

          <div v-if="section === 'tes'" class="space-y-4">
            <div v-for="test in workspace.tests" :key="test.id" class="rounded-2xl bg-white border border-gray-100 p-4 md:p-5 shadow-sm flex flex-col sm:flex-row sm:flex-wrap justify-between gap-3 md:gap-4 items-start sm:items-center hover:shadow-md transition-shadow">
              <div>
                <div class="font-semibold text-[#1A1A1A]">{{ test.name }}</div>
                <div class="text-xs text-gray-500 mt-1 capitalize">{{ test.kind }} · {{ test.category }}</div>
              </div>
              <router-link
                v-if="test?.id"
                :to="{ name: 'quick-test', params: { id: test.id } }"
                class="w-full sm:w-auto text-center rounded-full bg-gradient-to-r from-[#9DB359] to-[#7CB342] text-white px-5 py-2 text-sm font-semibold shadow-sm hover:opacity-95"
              >
                {{ t('bimble.startTest') }}
              </router-link>
              <span v-else class="text-xs text-gray-400">test id missing</span>
            </div>
            <p v-if="!workspace.tests?.length" class="text-gray-500 text-sm">{{ t('bimble.noTests') }}</p>
          </div>

          <div v-if="section === 'info'" class="rounded-2xl md:rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-4 md:p-8 text-sm text-gray-600 space-y-2">
            <p><strong>{{ t('bimble.classLabel') }}:</strong> {{ workspace.class.name }}</p>
            <p><strong>{{ t('bimble.code') }}:</strong> {{ workspace.class.class_code }}</p>
            <p v-if="workspace.flags.vip_quarantine_note"><strong>Kelas 1 (online):</strong> {{ t('bimble.quarantineHint') }}</p>
          </div>
        </section>

        <!-- Right column -->
        <aside class="space-y-4 lg:sticky lg:top-4 self-start">
          <div class="rounded-2xl bg-white border border-gray-100 p-5 shadow-sm">
            <div class="font-semibold text-[#1A1A1A] mb-2">{{ t('bimble.sidebar.testsTitle') }}</div>
            <p class="text-xs text-gray-500">{{ t('bimble.sidebar.testsHint') }}</p>
          </div>
          <div v-if="workspace.flags.hide_materials" class="rounded-2xl bg-white border border-gray-100 p-5 shadow-sm text-sm text-gray-600">
            {{ t('bimble.sidebar.tryoutOnly') }}
          </div>
        </aside>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useI18n } from '@/composables/useI18n'
import { programCategoryLabel } from '@/utils/userMeta'

const { t } = useI18n()
const route = useRoute()

const loading = ref(true)
const workspace = ref(null)
const section = ref('sesi')
const errorMessage = ref('')
const formatProgram = (programType) => programCategoryLabel(programType)

const sessionKeys = computed(() => {
  const m = workspace.value?.materials_by_session
  if (!m) return []
  return Object.keys(m)
})

const totalSessions = computed(() => sessionKeys.value.length)
const totalTests = computed(() => workspace.value?.tests?.length || 0)
const totalMaterials = computed(() => {
  const bySession = workspace.value?.materials_by_session
  if (!bySession || typeof bySession !== 'object') return 0
  return Object.values(bySession).reduce((sum, items) => sum + (Array.isArray(items) ? items.length : 0), 0)
})

const formattedPeriod = computed(() => {
  const cls = workspace.value?.class
  if (!cls) return '—'
  if (cls.academic_period_start && cls.academic_period_end) {
    return `${cls.academic_period_start} s/d ${cls.academic_period_end}`
  }
  return cls.academic_period || '—'
})

const sidebarItems = computed(() => {
  const base = [
    { key: 'info', label: t('bimble.menu.info') },
    { key: 'diskusi', label: t('bimble.menu.discussion') },
  ]
  const mid = workspace.value?.flags?.hide_materials
    ? []
    : [{ key: 'sesi', label: t('bimble.menu.sessions') }]
  const end = [{ key: 'tes', label: t('bimble.menu.tests') }]
  return [...base, ...mid, ...end]
})

watch(
  workspace,
  (w) => {
    if (w?.flags?.hide_materials) section.value = 'tes'
  },
  { immediate: true }
)

async function load() {
  loading.value = true
  try {
    const id = route.params.id
    if (!id) {
      throw new Error('ID kelas tidak ditemukan pada URL.')
    }
    const { data } = await axios.get(`/api/bimble-classes/${id}/workspace`)
    workspace.value = data
    errorMessage.value = ''
  } catch (error) {
    workspace.value = null
    errorMessage.value = error?.response?.data?.message || error?.message || 'Gagal memuat ruang kelas.'
  } finally {
    loading.value = false
  }
}

onMounted(load)
</script>
