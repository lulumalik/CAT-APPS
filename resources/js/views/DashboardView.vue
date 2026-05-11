<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-[#1A1A1A]">Dashboard</h1>
        <p class="text-gray-500 mt-1 flex flex-wrap items-center gap-2">
          <span>{{ user?.name }}</span>
          <span class="capitalize">{{ user?.role }}</span>
          <span class="text-xs rounded-full px-2 py-1" :class="programBadge.className">{{ programBadge.label }}</span>
        </p>
      </div>
      <button @click="loadOverview" class="px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm">
        Refresh
      </button>
    </div>

    <div v-if="loading" class="py-20 text-center text-gray-500">Memuat data dashboard...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-red-700 text-sm">
      {{ errorMessage }}
    </div>

    <template v-else>
      <section
        v-if="isLockedForStudent"
        class="rounded-[2rem] border border-amber-200 bg-amber-50 p-8 text-amber-900"
      >
        <h2 class="text-xl font-bold flex items-center gap-2">
          <LockKeyhole class="h-5 w-5" />
          Dashboard terkunci
        </h2>
        <p class="text-sm mt-2">
          Fitur dashboard dan kelas akan terbuka setelah onboarding selesai: administrasi, psikologi, kesehatan, lalu fisik.
        </p>
        <router-link to="/registration" class="inline-flex mt-5 rounded-full bg-[#1A1A1A] px-5 py-2.5 text-sm font-semibold text-white">
          Lanjutkan onboarding
        </router-link>
      </section>

      <!-- ADMIN -->
      <template v-else-if="isAdmin">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/5 p-5 relative"><div class="text-xs text-gray-500">Total Soal</div><div class="text-3xl font-bold">{{ overview.stats?.questions ?? 0 }}</div> <img :src="patternUrl" alt="Pattern" class="absolute w-12 bottom-0 right-0" /></div>
          <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/5 p-5 relative"><div class="text-xs text-gray-500">Peserta Terdaftar</div><div class="text-3xl font-bold">{{ overview.stats?.registered_users ?? 0 }}</div> <img :src="patternUrl" alt="Pattern" class="absolute w-12 bottom-0 right-0" /></div>
          <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/5 p-5 relative"><div class="text-xs text-gray-500">Peserta Diterima</div><div class="text-3xl font-bold">{{ overview.stats?.accepted_users ?? 0 }}</div> <img :src="patternUrl" alt="Pattern" class="absolute w-12 bottom-0 right-0" /></div>
          <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/5 p-5 relative"><div class="text-xs text-gray-500">Kelas Dibuat</div><div class="text-3xl font-bold">{{ overview.stats?.classes_count ?? 0 }}</div> <img :src="patternUrl" alt="Pattern" class="absolute w-12 bottom-0 right-0" /></div>
        </div>

        <div class="mt-8 grid lg:grid-cols-2 gap-6">
          <section class="bg-white border border-gray-100 rounded-2xl p-5">
            <h2 class="font-bold text-lg mb-4">Daftar Kelas</h2>
            <div v-if="!overview.classes?.length" class="text-sm text-gray-500">Belum ada kelas.</div>
            <div v-else class="space-y-3">
              <div v-for="c in overview.classes" :key="c.id" class="rounded-xl border border-gray-100 p-3">
                <div class="font-semibold">{{ c.name }}</div>
                <div class="text-xs text-gray-500">{{ c.class_code }} · {{ formatProgram(c.program_type) }} · {{ c.students_count }} peserta</div>
              </div>
            </div>
          </section>
          <section class="bg-white border border-gray-100 rounded-2xl p-5">
            <h2 class="font-bold text-lg mb-4">Recent History Aktivitas Kelas</h2>
            <div v-if="!overview.recent_activities?.length" class="text-sm text-gray-500">Belum ada aktivitas.</div>
            <div v-else class="space-y-3">
              <div v-for="a in overview.recent_activities" :key="a.id" class="rounded-xl border border-gray-100 p-3">
                <div class="font-semibold">{{ a.title }}</div>
                <div class="text-xs text-gray-500">{{ a.bimble_class?.name }} · {{ a.creator?.name }} · {{ formatDate(a.happened_at || a.created_at) }}</div>
              </div>
            </div>
          </section>
        </div>
      </template>

      <!-- MENTOR -->
      <template v-else-if="isMentor">
        <div class="grid lg:grid-cols-2 gap-6">
          <section class="bg-white border border-gray-100 rounded-2xl p-5">
            <h2 class="font-bold text-lg mb-4">Kelas yang Diusung</h2>
            <div v-if="!overview.classes?.length" class="text-sm text-gray-500">Belum ada kelas mentor.</div>
            <div v-else class="space-y-3">
              <div v-for="c in overview.classes" :key="c.id" class="rounded-xl border border-gray-100 p-3">
                <div class="font-semibold">{{ c.name }}</div>
                <div class="text-xs text-gray-500">{{ c.class_code }} · {{ c.students_count }} peserta</div>
                <div class="text-xs text-gray-600 mt-1">
                  Aktivitas terakhir:
                  <span class="font-medium">{{ c.latest_activity?.title || 'Belum ada aktivitas' }}</span>
                </div>
              </div>
            </div>
          </section>

          <section class="bg-white border border-gray-100 rounded-2xl p-5">
            <h2 class="font-bold text-lg mb-4">Test Akan Berlangsung</h2>
            <div v-if="!overview.upcoming_tests?.length" class="text-sm text-gray-500">Belum ada test terjadwal.</div>
            <div v-else class="space-y-3">
              <div v-for="t in overview.upcoming_tests" :key="t.id" class="rounded-xl border border-gray-100 p-3">
                <div class="font-semibold">{{ t.name }}</div>
                <div class="text-xs text-gray-500">{{ t.category }} · {{ formatDate(t.start_time) }}</div>
                <div class="text-xs text-gray-600 mt-1">
                  Kelas: {{ (t.classes || []).map((x) => x.name).join(', ') || '-' }}
                </div>
              </div>
            </div>
          </section>
        </div>

        <section class="bg-white border border-gray-100 rounded-2xl p-5 mt-6">
          <h2 class="font-bold text-lg mb-4">Aktivitas Kelas Terbaru</h2>
          <div v-if="!overview.recent_activities?.length" class="text-sm text-gray-500">Belum ada aktivitas.</div>
          <div v-else class="space-y-3">
            <div v-for="a in overview.recent_activities" :key="a.id" class="rounded-xl border border-gray-100 p-3">
              <div class="font-semibold">{{ a.title }}</div>
              <div class="text-xs text-gray-500">{{ a.bimble_class?.name }} · {{ formatDate(a.happened_at || a.created_at) }}</div>
            </div>
          </div>
        </section>
      </template>

      <!-- STUDENT/USER -->
      <template v-else>
        <div class="grid lg:grid-cols-2 gap-6">
          <section class="bg-white border border-gray-100 rounded-2xl p-5">
            <h2 class="font-bold text-lg mb-4">Kelas Saya</h2>
            <div v-if="!overview.classes?.length" class="text-sm text-gray-500">Belum ada kelas yang ditambahkan.</div>
            <div v-else class="space-y-3">
              <div v-for="c in overview.classes" :key="c.id" class="rounded-xl border border-gray-100 p-3">
                <div class="font-semibold">{{ c.name }}</div>
                <div class="text-xs text-gray-500">{{ c.class_code }} · {{ formatProgram(c.program_type) }}</div>
                <div class="text-xs text-gray-600 mt-1">
                  Aktivitas terakhir:
                  <span class="font-medium">{{ c.latest_activity?.title || 'Belum ada aktivitas' }}</span>
                </div>
              </div>
            </div>
          </section>

          <section class="bg-white border border-gray-100 rounded-2xl p-5">
            <h2 class="font-bold text-lg mb-4">Aktivitas Kelas</h2>
            <div v-if="!overview.class_activities?.length" class="text-sm text-gray-500">Belum ada aktivitas kelas.</div>
            <div v-else class="space-y-3">
              <div v-for="a in overview.class_activities" :key="a.id" class="rounded-xl border border-gray-100 p-3">
                <div class="font-semibold">{{ a.title }}</div>
                <div class="text-xs text-gray-500">{{ a.bimble_class?.name }} · {{ a.creator?.name }} · {{ formatDate(a.happened_at || a.created_at) }}</div>
                <div v-if="a.description" class="text-xs text-gray-600 mt-1">{{ a.description }}</div>
              </div>
            </div>
          </section>
        </div>
      </template>
    </template>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { LockKeyhole } from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'
import { getProgramBadge, programCategoryLabel, registrationCompleted } from '@/utils/userMeta'

const store = useAppStore()
const { user } = storeToRefs(store)

const loading = ref(false)
const errorMessage = ref('')
const overview = ref({})

const isAdmin = computed(() => user.value?.role === 'admin')
const isMentor = computed(() => user.value?.role === 'mentor')
const isLockedForStudent = computed(() => user.value?.role === 'user' && !registrationCompleted(user.value))
const programBadge = computed(() => getProgramBadge(user.value))
const formatProgram = (programType) => programCategoryLabel(programType)

const patternUrl = new URL('../../assets/Pattern.svg', import.meta.url).href

const formatDate = (d) => {
  if (!d) return '-'
  const dt = new Date(d)
  const opts = {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  }
  try {
    return dt.toLocaleString('id-ID', { ...opts, timeZone: 'Asia/Jakarta' })
  } catch (e) {
    return dt.toLocaleString('id-ID', opts)
  }
}

const loadOverview = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/dashboard/overview')
    overview.value = data || {}
    errorMessage.value = ''
  } catch (error) {
    overview.value = {}
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat dashboard.'
  } finally {
    loading.value = false
  }
}

onMounted(loadOverview)
</script>
