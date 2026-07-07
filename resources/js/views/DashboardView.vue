<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <router-link
      v-if="isAdminViewingStudent"
      to="/users"
      class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#1A1A1A] mb-4"
    >
      <ArrowLeft class="h-4 w-4" />
      Kembali ke Manajemen User
    </router-link>

    <PageHeroHeader
      :title="isAdminViewingStudent ? 'Dashboard Siswa' : 'Dashboard'"
      theme="blue"
      :icon="LayoutDashboard"
    >
      <template #subtitle>
        <template v-if="isAdminViewingStudent">
          <span>{{ viewedStudent?.name || 'Memuat...' }}</span>
          <span v-if="viewedStudent?.username" class="text-gray-400"> · @{{ viewedStudent.username }}</span>
          <span class="inline-block ml-1 text-xs rounded-full px-2 py-1" :class="displayProgramBadge.className">{{ displayProgramBadge.label }}</span>
        </template>
        <template v-else>
          <span>{{ user?.name }}</span>
          <span class="capitalize"> · {{ user?.role }}</span>
          <span class="inline-block ml-1 text-xs rounded-full px-2 py-1" :class="programBadge.className">{{ programBadge.label }}</span>
        </template>
      </template>
      <template #actions>
        <button
          type="button"
          class="pdf-hide px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm"
          @click="loadOverview"
        >
          Refresh
        </button>
      </template>
    </PageHeroHeader>

    <div v-if="loading" class="py-20 text-center text-gray-500">Memuat data dashboard...</div>
    <div v-else-if="errorMessage" class="rounded-2xl border border-red-100 bg-red-50 p-6 text-red-700 text-sm">
      {{ errorMessage }}
    </div>

    <template v-else>
      <section
        v-if="isExpiredForStudent"
        class="rounded-[2rem] border border-red-200 bg-red-50 p-8 text-red-900"
      >
        <h2 class="text-xl font-bold flex items-center gap-2">
          <LockKeyhole class="h-5 w-5" />
          Masa aktif aplikasi berakhir
        </h2>
        <p class="text-sm mt-2">
          Akses dashboard dan kelas telah ditutup. Anda masih dapat melihat profil dan riwayat aktivitas.
        </p>
        <div class="mt-5 flex flex-wrap gap-3">
          <router-link to="/profile" class="inline-flex rounded-full bg-[#1A1A1A] px-5 py-2.5 text-sm font-semibold text-white">
            Buka Profil
          </router-link>
          <router-link to="/activity-history" class="inline-flex rounded-full border border-red-300 bg-white px-5 py-2.5 text-sm font-semibold text-red-900">
            Riwayat Aktivitas
          </router-link>
        </div>
      </section>

      <section
        v-else-if="isLockedForStudent"
        class="rounded-[2rem] border border-amber-200 bg-amber-50 p-8 text-amber-900"
      >
        <h2 class="text-xl font-bold flex items-center gap-2">
          <LockKeyhole class="h-5 w-5" />
          Dashboard terkunci
        </h2>
        <p class="text-sm mt-2">
          <template v-if="usesSimplifiedOnboarding(user)">
            Verifikasi email Anda terlebih dahulu melalui tautan yang dikirim ke inbox. Setelah terverifikasi, dashboard dan ujian akan terbuka.
          </template>
          <template v-else>
            Fitur dashboard dan kelas akan terbuka setelah pendaftaran selesai: administrasi, psikologi, kesehatan, lalu fisik.
          </template>
        </p>
        <router-link to="/registration" class="inline-flex mt-5 rounded-full bg-[#1A1A1A] px-5 py-2.5 text-sm font-semibold text-white">
          {{ usesSimplifiedOnboarding(user) ? 'Verifikasi Email' : 'Lanjutkan Pendaftaran' }}
        </router-link>
      </section>

      <!-- ADMIN -->
      <template v-else-if="isAdmin && !isAdminViewingStudent">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/5 p-5"><div class="text-xs text-gray-500">Total Soal</div><div class="text-3xl font-bold">{{ overview.stats?.questions ?? 0 }}</div></div>
          <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/5 p-5"><div class="text-xs text-gray-500">Peserta Terdaftar</div><div class="text-3xl font-bold">{{ overview.stats?.registered_users ?? 0 }}</div></div>
          <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/5 p-5"><div class="text-xs text-gray-500">Peserta Diterima</div><div class="text-3xl font-bold">{{ overview.stats?.accepted_users ?? 0 }}</div></div>
          <div class="bg-white border border-gray-100 rounded-2xl shadow-xl shadow-black/5 p-5"><div class="text-xs text-gray-500">Kelas Dibuat</div><div class="text-3xl font-bold">{{ overview.stats?.classes_count ?? 0 }}</div></div>
        </div>

        <div class="mt-8 space-y-6">
          <section class="bg-white border border-gray-100 rounded-2xl p-5">
            <div class="flex items-center justify-between gap-3 mb-4">
              <h2 class="font-bold text-lg">Daftar Kelas</h2>
              <router-link to="/bimble-classes" class="text-sm font-semibold text-[#5a6b2e] hover:underline">
                Lihat semua
              </router-link>
            </div>
            <div v-if="!overview.classes?.length" class="text-sm text-gray-500">Belum ada kelas.</div>
            <div v-else class="grid gap-4 md:grid-cols-2">
              <router-link
                v-for="(c, idx) in overview.classes"
                :key="c.id"
                :to="{ name: 'bimble-class-room', params: { id: c.id } }"
                class="rounded-[1.75rem] border border-gray-100 bg-white shadow-lg shadow-black/5 overflow-hidden block hover:shadow-xl transition-shadow"
                :class="cardTheme(idx).topBorder"
              >
                <div class="p-5 flex flex-col gap-3">
                  <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0" :class="cardTheme(idx).iconWrap">
                      <component :is="cardTheme(idx).icon" class="h-5 w-5" :class="cardTheme(idx).iconColor" />
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-lg text-[#1A1A1A] leading-tight">{{ c.name }}</div>
                      <span class="inline-block mt-1.5 rounded-lg border border-gray-200 bg-gray-50 px-2 py-0.5 text-[10px] font-semibold text-gray-600">
                        {{ c.class_code }}
                      </span>
                    </div>
                  </div>
                  <p class="text-sm text-gray-600">{{ formatProgram(c.program_type) }}</p>
                  <div class="flex items-center gap-2 text-xs text-gray-500">
                    <Users class="h-3.5 w-3.5" />
                    {{ c.students_count ?? 0 }} peserta
                  </div>
                </div>
              </router-link>
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
        <div class="space-y-6">
          <section class="bg-white border border-gray-100 rounded-2xl p-5">
            <div class="flex items-center justify-between gap-3 mb-4">
              <h2 class="font-bold text-lg">Kelas yang Diusung</h2>
              <router-link to="/bimble-classes" class="text-sm font-semibold text-[#5a6b2e] hover:underline">
                Lihat semua
              </router-link>
            </div>
            <div v-if="!overview.classes?.length" class="text-sm text-gray-500">Belum ada kelas mentor.</div>
            <div v-else class="grid gap-4 md:grid-cols-2">
              <router-link
                v-for="(c, idx) in overview.classes"
                :key="c.id"
                :to="{ name: 'bimble-class-room', params: { id: c.id } }"
                class="rounded-[1.75rem] border border-gray-100 bg-white shadow-lg shadow-black/5 overflow-hidden block hover:shadow-xl transition-shadow"
                :class="cardTheme(idx).topBorder"
              >
                <div class="p-5 flex flex-col gap-3">
                  <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0" :class="cardTheme(idx).iconWrap">
                      <component :is="cardTheme(idx).icon" class="h-5 w-5" :class="cardTheme(idx).iconColor" />
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-lg text-[#1A1A1A] leading-tight">{{ c.name }}</div>
                      <span class="inline-block mt-1.5 rounded-lg border border-gray-200 bg-gray-50 px-2 py-0.5 text-[10px] font-semibold text-gray-600">
                        {{ c.class_code }}
                      </span>
                    </div>
                  </div>
                  <div class="flex items-center gap-2 text-xs text-gray-500">
                    <Users class="h-3.5 w-3.5" />
                    {{ c.students_count ?? 0 }} peserta
                  </div>
                  <p class="text-xs text-gray-600">
                    Aktivitas terakhir:
                    <span class="font-medium">{{ c.latest_activity?.title || 'Belum ada aktivitas' }}</span>
                  </p>
                </div>
              </router-link>
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

      <!-- PARENT -->
      <template v-else-if="isParent">
        <div v-if="!overview.children?.length" class="rounded-[2rem] border border-gray-100 bg-white p-8 text-center">
          <p class="text-gray-600 font-medium">Belum ada peserta yang terhubung.</p>
          <p class="text-sm text-gray-500 mt-1">Hubungi tim kami untuk menghubungkan akun Anda dengan ananda.</p>
        </div>
        <template v-else>
          <h2 class="font-bold text-lg mb-4">Ananda Anda</h2>
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <router-link v-for="c in overview.children" :key="c.link_id" :to="`/child/${c.student.id}`"
              class="block rounded-2xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all">
              <div class="font-semibold text-[#1A1A1A]">{{ c.student.name }}</div>
              <div class="text-xs text-gray-500 mt-0.5">{{ c.relationship }} · {{ formatProgram(c.student.program_category) }}</div>
              <div class="mt-3 text-xs text-gray-600">
                Laporan terbaru:
                <span class="font-medium">{{ c.latest_report?.title || 'Belum ada laporan' }}</span>
              </div>
              <span class="inline-block mt-3 text-xs font-semibold text-[#9DB359]">Lihat perkembangan →</span>
            </router-link>
          </div>

          <section class="bg-white border border-gray-100 rounded-2xl p-5 mt-6">
            <h2 class="font-bold text-lg mb-4">Laporan Terbaru</h2>
            <div v-if="!overview.recent_reports?.length" class="text-sm text-gray-500">Belum ada laporan.</div>
            <div v-else class="space-y-3">
              <div v-for="r in overview.recent_reports" :key="r.id" class="rounded-xl border border-gray-100 p-3">
                <div class="flex items-center justify-between gap-2">
                  <div class="font-semibold text-sm">{{ r.title }}</div>
                  <span class="text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full"
                    :class="r.type === 'weekly_summary' ? 'bg-[#9DB359]/15 text-[#6f8235]' : 'bg-gray-100 text-gray-500'">
                    {{ r.type === 'weekly_summary' ? 'Mingguan' : 'Harian' }}
                  </span>
                </div>
                <div class="text-xs text-gray-500 mt-0.5">{{ r.student }} · {{ formatDate(r.report_date) }}</div>
              </div>
            </div>
          </section>
        </template>
      </template>

      <!-- STUDENT/USER (or admin viewing student) -->
      <div
        v-else-if="showStudentDashboard"
        class="dashboard-pdf-export"
      >
        <div class="pdf-header">
          <h2 class="text-xl font-bold text-[#1A1A1A]">Laporan Dashboard</h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ pdfReportName }}
            <span class="text-gray-400">·</span>
            {{ displayProgramBadge.label }}
          </p>
        </div>

        <div class="pdf-grid-2">
          <section class="pdf-section lg:col-span-2">
            <h2 class="pdf-section-title">Kelas Saya</h2>
            <div v-if="!overview.classes?.length" class="pdf-muted">Belum ada kelas yang ditambahkan.</div>
            <div v-else class="grid gap-4 md:grid-cols-2">
              <router-link
                v-for="(c, idx) in overview.classes"
                :key="c.id"
                :to="{ name: 'bimble-class-room', params: { id: c.id } }"
                class="rounded-[1.75rem] border border-gray-100 bg-white shadow-lg shadow-black/5 overflow-hidden block hover:shadow-xl transition-shadow"
                :class="cardTheme(idx).topBorder"
              >
                <div class="p-5 flex flex-col gap-3">
                  <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0" :class="cardTheme(idx).iconWrap">
                      <component :is="cardTheme(idx).icon" class="h-5 w-5" :class="cardTheme(idx).iconColor" />
                    </div>
                    <div class="min-w-0">
                      <div class="font-bold text-lg text-[#1A1A1A] leading-tight">{{ c.name }}</div>
                      <span class="inline-block mt-1.5 rounded-lg border border-gray-200 bg-gray-50 px-2 py-0.5 text-[10px] font-semibold text-gray-600">
                        {{ c.class_code }}
                      </span>
                    </div>
                  </div>
                  <p class="text-sm text-gray-600">{{ formatProgram(c.program_type) }}</p>
                  <p class="text-xs text-gray-600">
                    Aktivitas terakhir:
                    <span class="font-medium">{{ c.latest_activity?.title || 'Belum ada aktivitas' }}</span>
                  </p>
                </div>
              </router-link>
            </div>
          </section>

          <section class="pdf-section">
            <h2 class="pdf-section-title">Riwayat Aktivitas</h2>
            <div v-if="!overview.class_activities?.length" class="pdf-muted">Belum ada aktivitas.</div>
            <div v-else>
              <div v-for="a in overview.class_activities" :key="a.id" class="pdf-card">
                <div class="flex items-start justify-between gap-2">
                  <div class="font-semibold text-sm">{{ a.title }}</div>
                  <span
                    v-if="activityTypeLabel(a.activity_type)"
                    class="shrink-0 text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full"
                    :class="activityTypeClass(a.activity_type)"
                  >
                    {{ activityTypeLabel(a.activity_type) }}
                  </span>
                </div>
                <div class="pdf-muted">
                  <template v-if="a.activity_type === 'class'">
                    {{ a.bimble_class?.name }} · {{ a.creator?.name }} ·
                  </template>
                  {{ formatDate(a.happened_at || a.created_at) }}
                </div>
                <div v-if="a.description" class="pdf-text-sm mt-1">{{ a.description }}</div>
              </div>
            </div>
          </section>
        </div>

        <div class="mt-8">
          <h2 class="pdf-section-title">Perkembangan Saya</h2>
          <StudentProgressPanel
            v-if="activeStudentId"
            :student-id="activeStudentId"
          />
        </div>
      </div>
    </template>
  </main>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { ArrowLeft, Calculator, Globe, GraduationCap, LayoutDashboard, LockKeyhole, Users } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useAppStore } from '@/stores/app'
import { getProgramBadge, programCategoryLabel, registrationCompleted, isAppExpired, usesSimplifiedOnboarding } from '@/utils/userMeta'
import StudentProgressPanel from '@/components/StudentProgressPanel.vue'

const store = useAppStore()
const { user } = storeToRefs(store)
const route = useRoute()

const loading = ref(false)
const errorMessage = ref('')
const overview = ref({})
const viewedStudent = ref(null)

const viewingStudentId = computed(() => {
  if (route.name !== 'student-dashboard') return null
  const id = Number(route.params.id)
  return Number.isFinite(id) && id > 0 ? id : null
})

const isAdmin = computed(() => user.value?.role === 'admin')
const isMentor = computed(() => user.value?.role === 'mentor')
const isParent = computed(() => user.value?.role === 'parent')
const isStudent = computed(() => user.value?.role === 'user')
const isAdminViewingStudent = computed(() => isAdmin.value && !!viewingStudentId.value)
const isExpiredForStudent = computed(() => isStudent.value && !isAdminViewingStudent.value && isAppExpired(user.value))
const isLockedForStudent = computed(() => isStudent.value && !isAdminViewingStudent.value && !isExpiredForStudent.value && !registrationCompleted(user.value))
const showStudentDashboard = computed(() => isStudent.value || isAdminViewingStudent.value)
const activeStudentId = computed(() => viewingStudentId.value || user.value?.id)
const programBadge = computed(() => getProgramBadge(user.value))
const displayProgramBadge = computed(() => {
  if (isAdminViewingStudent.value && viewedStudent.value) {
    return getProgramBadge({ role: 'user', program_category: viewedStudent.value.program_category })
  }
  return programBadge.value
})
const pdfReportName = computed(() => {
  if (isAdminViewingStudent.value) return viewedStudent.value?.name || 'Siswa'
  return user.value?.name || 'Peserta'
})
const formatProgram = (programType) => programCategoryLabel(programType)

const cardThemes = [
  {
    topBorder: 'border-t-4 border-t-[#9DB359]',
    iconWrap: 'bg-[#9DB359]/15',
    iconColor: 'text-[#5a6b2e]',
    icon: GraduationCap,
  },
  {
    topBorder: 'border-t-4 border-t-blue-500',
    iconWrap: 'bg-blue-50',
    iconColor: 'text-blue-600',
    icon: Calculator,
  },
  {
    topBorder: 'border-t-4 border-t-purple-500',
    iconWrap: 'bg-purple-50',
    iconColor: 'text-purple-600',
    icon: Globe,
  },
]

function cardTheme(index) {
  return cardThemes[index % cardThemes.length]
}

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

const activityTypeLabel = (type) => {
  if (type === 'exam') return 'Ujian'
  if (type === 'quiz') return 'Quiz'
  if (type === 'class') return 'Kelas'
  return ''
}

const activityTypeClass = (type) => {
  if (type === 'exam') return 'bg-blue-100 text-blue-800'
  if (type === 'quiz') return 'bg-emerald-100 text-emerald-800'
  if (type === 'class') return 'bg-gray-100 text-gray-600'
  return 'bg-gray-100 text-gray-600'
}

const loadOverview = async () => {
  loading.value = true
  try {
    const url = viewingStudentId.value
      ? `/api/dashboard/students/${viewingStudentId.value}/overview`
      : '/api/dashboard/overview'
    const { data } = await window.axios.get(url)
    overview.value = data || {}
    viewedStudent.value = data?.student || null
    errorMessage.value = ''
  } catch (error) {
    overview.value = {}
    viewedStudent.value = null
    errorMessage.value = error?.response?.data?.message || 'Gagal memuat dashboard.'
  } finally {
    loading.value = false
  }
}

watch(viewingStudentId, () => {
  viewedStudent.value = null
  loadOverview()
}, { immediate: true })
</script>

<style scoped>
.dashboard-pdf-export .pdf-header {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.dashboard-pdf-export .pdf-grid-2 {
  display: grid;
  gap: 1.5rem;
}

@media (min-width: 1024px) {
  .dashboard-pdf-export .pdf-grid-2 {
    grid-template-columns: 1fr 1fr;
  }
}

.dashboard-pdf-export .pdf-section {
  background: #ffffff;
  border: 1px solid #f3f4f6;
  border-radius: 1rem;
  padding: 1.25rem;
}

.dashboard-pdf-export .pdf-card {
  border: 1px solid #f3f4f6;
  border-radius: 0.75rem;
  padding: 0.75rem;
  margin-bottom: 0.75rem;
}

.dashboard-pdf-export .pdf-section-title {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.dashboard-pdf-export .pdf-muted {
  color: #6b7280;
  font-size: 0.75rem;
}

.dashboard-pdf-export .pdf-text-sm {
  font-size: 0.75rem;
  color: #4b5563;
}
</style>
