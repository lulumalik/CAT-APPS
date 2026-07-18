<template>
  <div class="min-h-screen bg-[#F3F4F6] font-sans text-[#1A1A1A]">
    <div v-if="loading" class="py-24 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="errorMessage"
      class="max-w-3xl mx-auto mt-10 rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>

    <template v-else-if="workspace">
      <!-- Back -->
      <div class="bg-[#F3F4F6] border-b border-gray-200/80">
        <div class="max-w-7xl mx-auto px-4 md:px-10 py-3">
          <router-link
            :to="backRoute"
            class="inline-flex items-center gap-2.5 rounded-full bg-white border border-gray-200 shadow-sm px-5 py-2.5 text-sm font-semibold text-[#1A1A1A] hover:bg-gray-50 hover:shadow transition-all"
          >
            <ArrowLeft class="h-4 w-4 shrink-0" stroke-width="2.25" />
            {{ t('bimble.back') }}
          </router-link>
        </div>
      </div>

      <!-- Header — keep existing gray gradient -->
      <header class="bg-gradient-to-r from-[#333333] via-[#636363] to-[#595959] text-white px-4 md:px-10 py-6 md:py-9 relative overflow-hidden">
        <div class="absolute inset-0 opacity-20 pointer-events-none bg-[radial-gradient(circle_at_20%_20%,white,transparent_45%)]" />
        <div class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10 blur-3xl pointer-events-none" />
        <div class="absolute bottom-0 left-0 right-0 h-8 pointer-events-none opacity-30">
          <svg viewBox="0 0 1440 48" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full" preserveAspectRatio="none">
            <path d="M0 24C240 48 480 0 720 24C960 48 1200 0 1440 24V48H0V24Z" fill="white" fill-opacity="0.08" />
          </svg>
        </div>

        <div class="relative max-w-7xl mx-auto flex flex-col gap-6 xl:flex-row xl:items-start xl:justify-between xl:gap-10">
          <div class="flex gap-4 items-start flex-1 min-w-0 xl:max-w-[58%]">
            <span class="inline-flex h-16 w-16 md:h-20 md:w-20 shrink-0 items-center justify-center rounded-2xl bg-white/15">
              <GraduationCap class="h-8 w-8 md:h-10 md:w-10 text-white" />
            </span>
            <div class="min-w-0 flex-1">
              <p class="inline-flex rounded-full bg-white/15 px-3 py-1 text-[11px] font-semibold tracking-wide text-white/90 mb-2">
                {{ t('bimble.classLabel') }}
              </p>
              <h1 class="text-2xl md:text-4xl font-bold tracking-tight leading-tight md:whitespace-nowrap">{{ workspace.class.name }}</h1>
              <p class="text-white/85 text-sm mt-1.5">{{ t('bimble.code') }}: {{ workspace.class.class_code }}</p>
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-5 gap-y-4 lg:gap-x-6 text-sm xl:shrink-0 xl:max-w-[42%]">
            <div class="flex items-start gap-2.5">
              <UserRound class="h-5 w-5 text-white/70 shrink-0 mt-0.5" stroke-width="1.75" />
              <div>
                <div class="text-[10px] font-semibold uppercase tracking-wider text-white/60">{{ t('bimble.instructor') }}</div>
                <div class="font-semibold mt-0.5">{{ workspace.class.instructor?.name || workspace.class.instructor_name || '—' }}</div>
              </div>
            </div>
            <div class="flex items-start gap-2.5">
              <CalendarRange class="h-5 w-5 text-white/70 shrink-0 mt-0.5" stroke-width="1.75" />
              <div>
                <div class="text-[10px] font-semibold uppercase tracking-wider text-white/60">{{ t('bimble.period') }}</div>
                <div class="font-semibold mt-0.5">{{ formattedPeriod }}</div>
              </div>
            </div>
            <div class="flex items-start gap-2.5 min-w-0">
              <BookOpen class="h-5 w-5 text-white/70 shrink-0 mt-0.5" stroke-width="1.75" />
              <div class="min-w-0">
                <div class="text-[10px] font-semibold uppercase tracking-wider text-white/60">{{ t('bimble.programType') }}</div>
                <div class="font-semibold mt-0.5 leading-snug">{{ formatProgram(workspace.class.program_type) }}</div>
              </div>
            </div>
          </div>
        </div>
      </header>

      <div class="max-w-7xl mx-auto px-4 py-6 md:py-8 space-y-6 md:space-y-8">
        <!-- Summary cards -->
        <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <button
            v-if="!workspace.flags.hide_materials"
            type="button"
            class="rounded-2xl bg-white p-4 md:p-5 shadow-md shadow-black/5 border border-gray-100 flex items-center gap-4 text-left transition-all hover:shadow-lg hover:-translate-y-0.5"
            :class="activeSection === 'sesi' ? 'ring-2 ring-[#9DB359]/40' : ''"
            @click="setSection('sesi')"
          >
            <div class="w-12 h-12 rounded-xl bg-[#9DB359]/15 flex items-center justify-center shrink-0">
              <GraduationCap class="h-6 w-6 text-[#5a6b2e]" stroke-width="1.75" />
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400">{{ t('bimble.menu.sessions') }}</p>
              <p class="text-3xl font-bold text-[#1A1A1A] leading-none mt-1">{{ totalSessions }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ totalSessions }} sesi tersedia</p>
            </div>
            <ChevronRight class="h-5 w-5 text-[#9DB359] shrink-0" />
          </button>

          <button
            type="button"
            class="rounded-2xl bg-white p-4 md:p-5 shadow-md shadow-black/5 border border-gray-100 flex items-center gap-4 text-left transition-all hover:shadow-lg hover:-translate-y-0.5"
            :class="activeSection === 'tes' ? 'ring-2 ring-blue-400/40' : ''"
            @click="setSection('tes')"
          >
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
              <ClipboardList class="h-6 w-6 text-blue-500" stroke-width="1.75" />
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400">{{ t('bimble.menu.tests') }}</p>
              <p class="text-3xl font-bold text-[#1A1A1A] leading-none mt-1">{{ totalTests }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ totalTests }} quiz tersedia</p>
            </div>
            <ChevronRight class="h-5 w-5 text-blue-500 shrink-0" />
          </button>

          <button
            v-if="!workspace.flags.hide_materials"
            type="button"
            class="rounded-2xl bg-white p-4 md:p-5 shadow-md shadow-black/5 border border-gray-100 flex items-center gap-4 text-left transition-all hover:shadow-lg hover:-translate-y-0.5"
            :class="activeSection === 'materi' ? 'ring-2 ring-purple-400/40' : ''"
            @click="setSection('materi')"
          >
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
              <FolderOpen class="h-6 w-6 text-purple-500" stroke-width="1.75" />
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-[10px] font-bold uppercase tracking-wide text-gray-400">{{ t('bimble.openMaterial') }}</p>
              <p class="text-3xl font-bold text-[#1A1A1A] leading-none mt-1">{{ totalMaterials }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ totalMaterials }} materi tersedia</p>
            </div>
            <ChevronRight class="h-5 w-5 text-purple-500 shrink-0" />
          </button>
        </section>

        <!-- Main content -->
        <div ref="contentRef" class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 lg:gap-8 items-start">
          <!-- Left: sessions / materials / tests -->
          <div class="space-y-6">
            <!-- Sessions -->
            <section v-if="!workspace.flags.hide_materials && activeSection !== 'tes'" class="rounded-2xl bg-white border border-gray-100 shadow-md shadow-black/5 overflow-hidden">
              <div class="px-5 md:px-6 py-5 border-b border-gray-100 flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                  <div class="w-10 h-10 rounded-xl bg-[#9DB359]/15 flex items-center justify-center shrink-0">
                    <GraduationCap class="h-5 w-5 text-[#5a6b2e]" />
                  </div>
                  <div>
                    <h2 class="font-bold text-lg text-[#1A1A1A]">{{ t('bimble.menu.sessions') }}</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Ikuti sesi pembelajaran sesuai jadwal yang tersedia.</p>
                  </div>
                </div>
                <button
                  v-if="sessionKeys.length > 1"
                  type="button"
                  class="text-sm font-semibold text-[#5a6b2e] hover:underline"
                  @click="showAllSessions = !showAllSessions"
                >
                  {{ showAllSessions ? 'Tampilkan ringkas' : 'Lihat semua sesi' }} &gt;
                </button>
              </div>

              <div v-if="sessionKeys.length === 0" class="px-6 py-10 text-sm text-gray-500 text-center">
                {{ t('bimble.noMaterials') }}
              </div>

              <div v-else class="p-4 md:p-5 space-y-4">
                <article
                  v-for="sessionNum in visibleSessions"
                  :key="sessionNum"
                  class="rounded-xl border border-gray-100 overflow-hidden"
                >
                  <div class="px-4 py-3 bg-[#F7FAF1] border-b border-gray-100 flex items-center justify-between gap-3">
                    <span class="font-semibold text-[#1A1A1A]">{{ t('bimble.session') }} {{ sessionNum }}</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-[#9DB359]/15 px-3 py-1 text-xs font-semibold text-[#5a6b2e]">
                      <CheckCircle2 class="h-3.5 w-3.5" />
                      Selesai
                    </span>
                  </div>
                  <ul class="divide-y divide-gray-50">
                    <li
                      v-for="m in workspace.materials_by_session[sessionNum]"
                      :key="m.id"
                      class="px-4 py-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between"
                    >
                      <div class="flex items-start gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-lg bg-[#9DB359]/10 flex items-center justify-center shrink-0">
                          <FileText class="h-4 w-4 text-[#5a6b2e]" />
                        </div>
                        <div class="min-w-0">
                          <div class="font-semibold text-[#1A1A1A] truncate">{{ m.title }}</div>
                          <div class="flex items-center gap-1.5 text-xs text-gray-400 mt-1">
                            <CalendarDays class="h-3.5 w-3.5" />
                            Dipublikasikan {{ formatPublishedDate(m) }}
                          </div>
                        </div>
                      </div>
                      <router-link
                        v-if="m?.slug"
                        :to="{ name: 'blog-detail', params: { slug: m.slug } }"
                        class="inline-flex items-center justify-center gap-1.5 shrink-0 rounded-full border-2 border-[#9DB359] px-4 py-2 text-sm font-semibold text-[#5a6b2e] hover:bg-[#9DB359]/5 transition-colors"
                      >
                        {{ t('bimble.openMaterial') }}
                        <ChevronRight class="h-4 w-4" />
                      </router-link>
                      <span v-else class="text-xs text-gray-400">slug missing</span>
                    </li>
                  </ul>
                </article>
              </div>
            </section>

            <!-- Tryout notice -->
            <section v-if="workspace.flags.hide_materials" class="rounded-2xl bg-amber-50 border border-amber-100 p-5 text-amber-900 text-sm">
              {{ t('bimble.tryoutNoMaterials') }}
            </section>

            <!-- Quiz list -->
            <section
              v-if="activeSection === 'tes' || workspace.flags.hide_materials"
              class="rounded-2xl bg-white border border-gray-100 shadow-md shadow-black/5 overflow-hidden"
            >
              <div class="px-5 py-5 border-b border-gray-100 flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                  <ClipboardList class="h-5 w-5 text-blue-500" />
                </div>
                <div>
                  <h2 class="font-bold text-lg">{{ t('bimble.sidebar.testsTitle') }}</h2>
                  <p class="text-sm text-gray-500 mt-0.5">{{ t('bimble.sidebar.testsHint') }}</p>
                </div>
              </div>
              <div class="p-4 space-y-3">
                <div
                  v-for="test in workspace.tests"
                  :key="test.id"
                  class="rounded-xl border border-gray-100 p-4 flex flex-col sm:flex-row sm:items-center gap-3 sm:justify-between"
                >
                  <div>
                    <div class="font-semibold">{{ test.name }}</div>
                    <div class="text-xs text-gray-500 mt-1 capitalize">{{ test.kind }} · {{ test.category }}</div>
                  </div>
                  <router-link
                    v-if="test?.id && !isExpired(test)"
                    :to="{ name: 'quick-test', params: { id: test.id } }"
                    class="inline-flex items-center justify-center rounded-full bg-blue-500 text-white px-5 py-2 text-sm font-semibold hover:bg-blue-600"
                  >
                    {{ t('bimble.startQuiz') }}
                  </router-link>
                  <span v-else-if="isExpired(test)" class="text-xs font-semibold text-red-600">{{ t('bimble.testExpiredLabel') }}</span>
                </div>
                <p v-if="!workspace.tests?.length" class="text-sm text-gray-500 text-center py-4">{{ t('bimble.noQuizzes') }}</p>
              </div>
            </section>

            <!-- Step indicator -->
            <nav class="rounded-2xl bg-white border border-gray-100 shadow-sm px-4 py-4 flex flex-wrap items-center gap-4 md:gap-8">
              <button
                v-if="!workspace.flags.hide_materials"
                type="button"
                class="flex items-center gap-2.5 text-sm"
                :class="activeSection === 'sesi' ? 'text-[#5a6b2e] font-semibold' : 'text-gray-500'"
                @click="setSection('sesi')"
              >
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" :class="activeSection === 'sesi' ? 'bg-[#9DB359] text-white' : 'bg-gray-100 text-gray-500'">1</span>
                <span>{{ t('bimble.menu.sessions') }} <span class="text-gray-400 font-normal">({{ totalSessions }} sesi)</span></span>
              </button>
              <button
                type="button"
                class="flex items-center gap-2.5 text-sm"
                :class="activeSection === 'tes' ? 'text-blue-600 font-semibold' : 'text-gray-500'"
                @click="setSection('tes')"
              >
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" :class="activeSection === 'tes' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-500'">2</span>
                <span>{{ t('bimble.menu.tests') }} <span class="text-gray-400 font-normal">({{ totalTests }} quiz)</span></span>
              </button>
              <button
                v-if="!workspace.flags.hide_materials"
                type="button"
                class="flex items-center gap-2.5 text-sm"
                :class="activeSection === 'materi' ? 'text-purple-600 font-semibold' : 'text-gray-500'"
                @click="setSection('materi')"
              >
                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold" :class="activeSection === 'materi' ? 'bg-purple-500 text-white' : 'bg-gray-100 text-gray-500'">3</span>
                <span>{{ t('bimble.openMaterial') }} <span class="text-gray-400 font-normal">({{ totalMaterials }} materi)</span></span>
              </button>
            </nav>
          </div>

          <!-- Right: class tests -->
          <aside class="rounded-2xl bg-white border border-gray-100 shadow-md shadow-black/5 overflow-hidden lg:sticky lg:top-4">
            <div class="px-5 py-5 border-b border-gray-100">
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center shrink-0">
                  <ClipboardList class="h-5 w-5 text-purple-500" />
                </div>
                <div>
                  <h2 class="font-bold text-lg text-[#1A1A1A]">{{ t('bimble.sidebar.testsTitle') }}</h2>
                  <p class="text-xs text-gray-500 mt-1">{{ t('bimble.sidebar.testsHint') }}</p>
                </div>
              </div>
            </div>

            <div v-if="workspace.tests?.length" class="p-4 space-y-3 max-h-[320px] overflow-y-auto">
              <div
                v-for="test in workspace.tests"
                :key="test.id"
                class="rounded-xl border border-gray-100 p-3 hover:border-purple-100 transition-colors"
              >
                <div class="font-semibold text-sm">{{ test.name }}</div>
                <div class="text-[11px] text-gray-500 mt-1 capitalize">{{ test.kind }} · {{ test.category }}</div>
                <div v-if="test.start_time" class="text-[11px] text-gray-400 mt-1">{{ formatTestSchedule(test) }}</div>
                <router-link
                  v-if="test?.id && !isExpired(test)"
                  :to="{ name: 'quick-test', params: { id: test.id } }"
                  class="mt-2 inline-flex text-xs font-semibold text-purple-600 hover:underline"
                >
                  {{ t('bimble.startQuiz') }} &gt;
                </router-link>
                <span v-else-if="isExpired(test)" class="mt-2 inline-block text-[11px] font-semibold text-red-600">{{ t('bimble.testExpiredLabel') }}</span>
              </div>
            </div>

            <div v-else class="px-5 py-8 flex flex-col items-center text-center">
              <div class="w-24 h-24 rounded-2xl bg-purple-50 flex items-center justify-center mb-4">
                <CalendarClock class="h-12 w-12 text-purple-400" stroke-width="1.25" />
              </div>
              <p class="text-sm text-gray-500">{{ t('bimble.noQuizzes') }}</p>
            </div>

            <div class="px-4 pb-5">
              <button
                type="button"
                class="w-full rounded-xl border-2 border-purple-200 bg-purple-50/50 py-3 text-sm font-semibold text-purple-700 hover:bg-purple-50 transition-colors"
                @click="setSection('tes')"
              >
                Lihat jadwal lengkap &gt;
              </button>
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
import {
  ArrowLeft,
  BookOpen,
  CalendarClock,
  CalendarDays,
  CalendarRange,
  CheckCircle2,
  ChevronRight,
  ClipboardList,
  FileText,
  FolderOpen,
  GraduationCap,
  UserRound,
} from 'lucide-vue-next'
import { useI18n } from '@/composables/useI18n'
import { useAppStore } from '@/stores/app'
import { programCategoryLabel } from '@/utils/userMeta'

const { t } = useI18n()
const route = useRoute()
const appStore = useAppStore()

const backRoute = computed(() => {
  const role = appStore.user?.role
  return ['admin', 'mentor'].includes(role) ? '/bimble-classes' : '/my-classes'
})

const loading = ref(true)
const workspace = ref(null)
const activeSection = ref('sesi')
const showAllSessions = ref(false)
const contentRef = ref(null)
const errorMessage = ref('')
const formatProgram = (programType) => programCategoryLabel(programType)

const sessionKeys = computed(() => {
  const m = workspace.value?.materials_by_session
  if (!m) return []
  return Object.keys(m).sort((a, b) => Number(a) - Number(b))
})

const visibleSessions = computed(() => {
  if (showAllSessions.value) return sessionKeys.value
  return sessionKeys.value.slice(0, 1)
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
  const batch = cls.batches?.[0]
  const start = cls.academic_period_start || batch?.starts_on
  const end = cls.academic_period_end || batch?.ends_on
  if (start && end) {
    return `${start} s/d ${end}`
  }
  if (start) return `Mulai ${start}`
  return cls.academic_period || '—'
})

watch(
  workspace,
  (w) => {
    if (w?.flags?.hide_materials) activeSection.value = 'tes'
  },
  { immediate: true },
)

function setSection(key) {
  activeSection.value = key
  if (key === 'sesi' || key === 'materi') {
    showAllSessions.value = key === 'materi'
  }
  contentRef.value?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function formatPublishedDate(material) {
  const raw = material?.created_at || material?.updated_at
  if (!raw) return '—'
  try {
    return new Date(raw).toLocaleDateString('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      timeZone: 'Asia/Jakarta',
    })
  } catch {
    return '—'
  }
}

function formatTestSchedule(test) {
  if (!test?.start_time) return ''
  try {
    return new Date(test.start_time).toLocaleString('id-ID', {
      day: '2-digit',
      month: 'short',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  } catch {
    return ''
  }
}

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

function isExpired(test) {
  if (!test?.end_time) return false
  const end = new Date(test.end_time)
  if (Number.isNaN(end.getTime())) return false
  return end < new Date()
}

onMounted(load)
</script>
