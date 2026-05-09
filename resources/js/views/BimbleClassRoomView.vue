<template>
  <div class="min-h-screen bg-[#F4F6F3] font-sans text-[#1A1A1A]">
    <div v-if="loading" class="py-24 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="errorMessage"
      class="max-w-3xl mx-auto mt-10 rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>

    <template v-else-if="workspace">
      <!-- Header -->
      <header class="bg-[#7CB342] text-white px-4 md:px-10 py-8 md:py-10 rounded-b-[2rem] shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 pointer-events-none bg-[radial-gradient(circle_at_20%_20%,white,transparent)]" />
        <div class="relative max-w-7xl mx-auto flex flex-col lg:flex-row lg:items-end gap-8">
          <div class="flex gap-5 items-start">
            <div class="w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center text-xs font-bold text-center leading-tight shrink-0 border border-white/30">
              BIMBLE
            </div>
            <div>
              <p class="text-white/80 text-sm">{{ t('bimble.classLabel') }}</p>
              <h1 class="text-2xl md:text-3xl font-bold tracking-tight">{{ workspace.class.name }}</h1>
              <p class="text-white/90 text-sm mt-1">{{ t('bimble.code') }}: {{ workspace.class.class_code }}</p>
            </div>
          </div>
          <div class="flex flex-wrap gap-8 text-sm lg:ml-auto">
            <div>
              <div class="text-white/70 text-xs uppercase tracking-wide">{{ t('bimble.instructor') }}</div>
              <div class="font-medium">{{ workspace.class.instructor_name || '—' }}</div>
            </div>
            <div>
              <div class="text-white/70 text-xs uppercase tracking-wide">{{ t('bimble.period') }}</div>
              <div class="font-medium">{{ workspace.class.academic_period || '—' }}</div>
            </div>
            <div>
              <div class="text-white/70 text-xs uppercase tracking-wide">{{ t('bimble.programType') }}</div>
              <div class="font-medium">{{ workspace.class.program_type }}</div>
            </div>
          </div>
        </div>
      </header>

      <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-[240px_1fr_280px] gap-8">
        <!-- Sidebar -->
        <aside class="space-y-1 lg:sticky lg:top-4 self-start">
          <router-link to="/dashboard" class="block px-4 py-2 rounded-xl text-sm text-gray-600 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-100">
            ← {{ t('bimble.back') }}
          </router-link>
          <div
            v-for="item in sidebarItems"
            :key="item.key"
            class="px-4 py-2 rounded-xl text-sm cursor-pointer border transition-colors"
            :class="section === item.key ? 'bg-white shadow-md border-[#9DB359]/40 text-[#1A1A1A] font-semibold' : 'border-transparent text-gray-600 hover:bg-white/80'"
            @click="section = item.key"
          >
            {{ item.label }}
          </div>
        </aside>

        <!-- Main -->
        <section class="space-y-6">
          <div v-if="section === 'diskusi'" class="rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-8 min-h-[200px]">
            <p class="text-gray-500 text-sm">{{ t('bimble.discussionPlaceholder') }}</p>
          </div>

          <div v-if="section === 'sesi' && !workspace.flags.hide_materials" class="space-y-6">
            <div v-for="(materials, sessionNum) in workspace.materials_by_session" :key="sessionNum" class="rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 overflow-hidden">
              <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 font-semibold text-[#1A1A1A]">
                {{ t('bimble.session') }} {{ sessionNum }}
              </div>
              <ul class="divide-y divide-gray-100">
                <li v-for="m in materials" :key="m.id" class="px-6 py-4 flex justify-between items-center gap-4 hover:bg-gray-50/80">
                  <span class="font-medium">{{ m.title }}</span>
                  <router-link
                    v-if="m?.slug"
                    :to="{ name: 'blog-detail', params: { slug: m.slug } }"
                    class="text-sm font-semibold text-[#9DB359] shrink-0"
                  >
                    {{ t('bimble.openMaterial') }}
                  </router-link>
                  <span v-else class="text-xs text-gray-400">slug missing</span>
                </li>
              </ul>
            </div>
            <p v-if="sessionKeys.length === 0" class="text-gray-500 text-sm">{{ t('bimble.noMaterials') }}</p>
          </div>

          <div v-if="section === 'sesi' && workspace.flags.hide_materials" class="rounded-[2rem] bg-amber-50 border border-amber-100 p-6 text-amber-900 text-sm">
            {{ t('bimble.tryoutNoMaterials') }}
          </div>

          <div v-if="section === 'tes'" class="space-y-4">
            <div v-for="test in workspace.tests" :key="test.id" class="rounded-2xl bg-white border border-gray-100 p-5 shadow-sm flex flex-wrap justify-between gap-4 items-center">
              <div>
                <div class="font-semibold text-[#1A1A1A]">{{ test.name }}</div>
                <div class="text-xs text-gray-500 mt-1 capitalize">{{ test.kind }} · {{ test.category }}</div>
              </div>
              <router-link
                v-if="test?.id"
                :to="{ name: 'quick-test', params: { id: test.id } }"
                class="rounded-full bg-[#9DB359] text-white px-5 py-2 text-sm font-semibold"
              >
                {{ t('bimble.startTest') }}
              </router-link>
              <span v-else class="text-xs text-gray-400">test id missing</span>
            </div>
            <p v-if="!workspace.tests?.length" class="text-gray-500 text-sm">{{ t('bimble.noTests') }}</p>
          </div>

          <div v-if="section === 'info'" class="rounded-[2rem] bg-white border border-gray-100 shadow-lg shadow-black/5 p-8 text-sm text-gray-600 space-y-2">
            <p><strong>{{ t('bimble.classLabel') }}:</strong> {{ workspace.class.name }}</p>
            <p><strong>{{ t('bimble.code') }}:</strong> {{ workspace.class.class_code }}</p>
            <p v-if="workspace.flags.vip_quarantine_note"><strong>VIP online:</strong> {{ t('bimble.quarantineHint') }}</p>
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
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()
const route = useRoute()

const loading = ref(true)
const workspace = ref(null)
const section = ref('sesi')
const errorMessage = ref('')

const sessionKeys = computed(() => {
  const m = workspace.value?.materials_by_session
  if (!m) return []
  return Object.keys(m)
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
