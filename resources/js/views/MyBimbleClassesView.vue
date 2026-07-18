<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      :title="t('bimble.myClassesTitle')"
      :subtitle="t('bimble.myClassesSubtitle')"
      theme="green"
      :icon="GraduationCap"
    />

    <section
      v-if="isExamOnly"
      class="rounded-[2rem] border border-purple-200 bg-purple-50 p-8 text-purple-900"
    >
      <h2 class="text-xl font-bold">Program Kelas Ujian</h2>
      <p class="text-sm mt-2">
        Peserta program Kelas Ujian hanya dapat mengakses menu ujian. Ruang kelas tidak tersedia untuk program ini.
      </p>
      <router-link to="/ujian" class="inline-flex mt-5 rounded-full bg-[#1A1A1A] px-5 py-2.5 text-sm font-semibold text-white">
        Buka halaman ujian
      </router-link>
    </section>

    <section
      v-else-if="isLocked"
      class="rounded-[2rem] border border-amber-200 bg-amber-50 p-8 text-amber-900"
    >
      <h2 class="text-xl font-bold flex items-center gap-2">
        <LockKeyhole class="h-5 w-5" />
        Kelas masih terkunci
      </h2>
      <p class="text-sm mt-2">
        Selesaikan pendaftaran hingga disetujui untuk membuka akses kelas.
      </p>
      <router-link to="/registration" class="inline-flex mt-5 rounded-full bg-[#1A1A1A] px-5 py-2.5 text-sm font-semibold text-white">
        Buka halaman pendaftaran
      </router-link>
    </section>

    <div v-if="!isLocked && !isExamOnly && loading" class="py-16 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="!isLocked && !isExamOnly && errorMessage"
      class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>
    <div v-else-if="!isLocked && !isExamOnly && !classes.length" class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 text-sm">
      {{ t('bimble.myClassesEmpty') }}
    </div>

    <div v-else-if="!isLocked && !isExamOnly" class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
      <router-link
        v-for="(c, idx) in classes"
        :key="c.id"
        :to="{ name: 'bimble-class-room', params: { id: c.id } }"
        class="rounded-[1.75rem] border border-gray-100 bg-white shadow-lg shadow-black/5 overflow-hidden block hover:shadow-xl transition-shadow"
        :class="cardTheme(idx).topBorder"
      >
        <div class="p-6 flex flex-col gap-4">
          <div class="flex items-start gap-3">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0" :class="cardTheme(idx).iconWrap">
              <component :is="cardTheme(idx).icon" class="h-6 w-6" :class="cardTheme(idx).iconColor" />
            </div>
            <div class="min-w-0">
              <div class="font-bold text-xl text-[#1A1A1A] leading-tight">{{ c.name }}</div>
              <span class="inline-block mt-2 rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-[11px] font-semibold text-gray-600">
                {{ t('bimble.code') }}: {{ c.class_code }}
              </span>
            </div>
          </div>
          <p class="text-sm font-medium text-gray-700">{{ formatProgram(c.program_type) }}</p>
          <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-center gap-2.5">
              <CalendarRange class="h-4 w-4 text-gray-400 shrink-0" />
              <span>{{ formatPeriod(c) }}</span>
            </li>
          </ul>
          <span class="inline-flex self-start rounded-full border-2 px-4 py-2 text-sm font-semibold" :class="cardTheme(idx).manageBtn">
            {{ t('bimble.openRoom') }} &gt;
          </span>
        </div>
      </router-link>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import { Calculator, CalendarRange, Globe, GraduationCap, LockKeyhole } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useI18n } from '@/composables/useI18n'
import { useAppStore } from '@/stores/app'
import { storeToRefs } from 'pinia'
import { programCategoryLabel, registrationCompleted, isExamOnlyProgram } from '@/utils/userMeta'

const { t } = useI18n()
const store = useAppStore()
const { user } = storeToRefs(store)
const loading = ref(true)
const classes = ref([])
const errorMessage = ref('')
const isExamOnly = computed(() => user.value?.role === 'user' && isExamOnlyProgram(user.value))
const isLocked = computed(() => user.value?.role === 'user' && !isExamOnly.value && !registrationCompleted(user.value))
const formatProgram = (programType) => programCategoryLabel(programType)

const cardThemes = [
  {
    topBorder: 'border-t-4 border-t-[#9DB359]',
    iconWrap: 'bg-[#9DB359]/15',
    iconColor: 'text-[#5a6b2e]',
    manageBtn: 'border-[#9DB359] text-[#5a6b2e]',
    icon: GraduationCap,
  },
  {
    topBorder: 'border-t-4 border-t-blue-500',
    iconWrap: 'bg-blue-50',
    iconColor: 'text-blue-600',
    manageBtn: 'border-blue-500 text-blue-600',
    icon: Calculator,
  },
  {
    topBorder: 'border-t-4 border-t-purple-500',
    iconWrap: 'bg-purple-50',
    iconColor: 'text-purple-600',
    manageBtn: 'border-purple-500 text-purple-600',
    icon: Globe,
  },
]

function cardTheme(index) {
  return cardThemes[index % cardThemes.length]
}

function formatPeriod(c) {
  const batch = c?.batches?.[0]
  const start = c?.academic_period_start || batch?.starts_on
  const end = c?.academic_period_end || batch?.ends_on
  if (start && end) return `${start} s/d ${end}`
  if (start) return `Mulai ${start}`
  return c?.academic_period || 'Periode belum diatur'
}

onMounted(async () => {
  if (isLocked.value || isExamOnly.value) {
    loading.value = false
    classes.value = []
    return
  }
  try {
    const { data } = await axios.get('/api/bimble-classes/mine')
    classes.value = (Array.isArray(data) ? data : []).filter((item) => item && item.id)
    errorMessage.value = ''
  } catch (error) {
    classes.value = []
    errorMessage.value =
      error?.response?.data?.message ||
      'Gagal memuat kelas. Pastikan database migration terbaru sudah dijalankan.'
  } finally {
    loading.value = false
  }
})
</script>
