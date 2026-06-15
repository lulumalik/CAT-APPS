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

    <div class="mb-8 flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-[#1A1A1A]">
          {{ isAdminViewingStudent ? 'Dashboard Siswa' : 'Dashboard' }}
        </h1>
        <p v-if="isAdminViewingStudent" class="text-gray-500 mt-1 flex flex-wrap items-center gap-2">
          <span>{{ viewedStudent?.name || 'Memuat...' }}</span>
          <span v-if="viewedStudent?.username" class="text-gray-400">@{{ viewedStudent.username }}</span>
          <span class="text-xs rounded-full px-2 py-1" :class="displayProgramBadge.className">{{ displayProgramBadge.label }}</span>
        </p>
        <p v-else class="text-gray-500 mt-1 flex flex-wrap items-center gap-2">
          <span>{{ user?.name }}</span>
          <span class="capitalize">{{ user?.role }}</span>
          <span class="text-xs rounded-full px-2 py-1" :class="programBadge.className">{{ programBadge.label }}</span>
        </p>
      </div>
      <div class="flex flex-wrap items-center gap-2">
        <button
          v-if="canDownloadPdf"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-[#9DB359]/40 bg-[#9DB359]/10 hover:bg-[#9DB359]/20 text-sm font-medium text-[#5a6b2e] disabled:opacity-50"
          :disabled="loading || exportingPdf || !!errorMessage"
          @click="downloadPdf"
        >
          <Download class="h-4 w-4" />
          {{ exportingPdf ? 'Menyiapkan PDF...' : 'Download PDF' }}
        </button>
        <button
          type="button"
          class="pdf-hide px-4 py-2 rounded-full border border-gray-200 bg-white hover:bg-gray-50 text-sm"
          :disabled="exportingPdf"
          @click="loadOverview"
        >
          Refresh
        </button>
      </div>
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
          Fitur dashboard dan kelas akan terbuka setelah pendaftaran selesai: administrasi, psikologi, kesehatan, lalu fisik.
        </p>
        <router-link to="/registration" class="inline-flex mt-5 rounded-full bg-[#1A1A1A] px-5 py-2.5 text-sm font-semibold text-white">
          Lanjutkan Pendaftaran
        </router-link>
      </section>

      <!-- ADMIN -->
      <template v-else-if="isAdmin && !isAdminViewingStudent">
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
        ref="pdfContentRef"
        class="dashboard-pdf-export"
        :class="{ 'is-exporting': exportingPdf }"
      >
        <div class="pdf-header">
          <h2 class="text-xl font-bold text-[#1A1A1A]">Laporan Dashboard</h2>
          <p class="text-sm text-gray-600 mt-1">
            {{ pdfReportName }}
            <span class="text-gray-400">·</span>
            {{ displayProgramBadge.label }}
          </p>
          <p v-if="exportingPdf" class="text-xs text-gray-400 mt-1">Dicetak: {{ pdfGeneratedAt }}</p>
        </div>

        <div class="pdf-grid-2">
          <section class="pdf-section">
            <h2 class="pdf-section-title">Kelas Saya</h2>
            <div v-if="!overview.classes?.length" class="pdf-muted">Belum ada kelas yang ditambahkan.</div>
            <div v-else>
              <div v-for="c in overview.classes" :key="c.id" class="pdf-card">
                <div class="font-semibold text-sm">{{ c.name }}</div>
                <div class="pdf-muted">{{ c.class_code }} · {{ formatProgram(c.program_type) }}</div>
                <div class="pdf-text-sm mt-1">
                  Aktivitas terakhir:
                  <span class="font-medium">{{ c.latest_activity?.title || 'Belum ada aktivitas' }}</span>
                </div>
              </div>
            </div>
          </section>

          <section class="pdf-section">
            <h2 class="pdf-section-title">Aktivitas Kelas</h2>
            <div v-if="!overview.class_activities?.length" class="pdf-muted">Belum ada aktivitas kelas.</div>
            <div v-else>
              <div v-for="a in overview.class_activities" :key="a.id" class="pdf-card">
                <div class="font-semibold text-sm">{{ a.title }}</div>
                <div class="pdf-muted">{{ a.bimble_class?.name }} · {{ a.creator?.name }} · {{ formatDate(a.happened_at || a.created_at) }}</div>
                <div v-if="a.description" class="pdf-text-sm mt-1">{{ a.description }}</div>
              </div>
            </div>
          </section>
        </div>

        <div class="mt-8">
          <h2 class="pdf-section-title">Perkembangan Saya</h2>
          <StudentProgressPanel
            v-if="activeStudentId"
            ref="progressPanelRef"
            :student-id="activeStudentId"
            :pdf-mode="exportingPdf"
          />
        </div>
      </div>
    </template>
  </main>
</template>

<script setup>
import { computed, nextTick, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { storeToRefs } from 'pinia'
import { ArrowLeft, Download, LockKeyhole } from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'
import { getProgramBadge, programCategoryLabel, registrationCompleted } from '@/utils/userMeta'
import { downloadElementAsPdf, sanitizePdfFilename } from '@/utils/html2pdf'
import StudentProgressPanel from '@/components/StudentProgressPanel.vue'

const store = useAppStore()
const { user } = storeToRefs(store)
const route = useRoute()

const loading = ref(false)
const errorMessage = ref('')
const overview = ref({})
const viewedStudent = ref(null)
const exportingPdf = ref(false)
const pdfContentRef = ref(null)
const progressPanelRef = ref(null)
const pdfGeneratedAt = ref('')

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
const isLockedForStudent = computed(() => isStudent.value && !isAdminViewingStudent.value && !registrationCompleted(user.value))
const showStudentDashboard = computed(() => isStudent.value || isAdminViewingStudent.value)
const activeStudentId = computed(() => viewingStudentId.value || user.value?.id)
const canDownloadPdf = computed(() => {
  if (isAdminViewingStudent.value) return !!activeStudentId.value
  return isStudent.value && !isLockedForStudent.value
})
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

const downloadPdf = async () => {
  if (!pdfContentRef.value || exportingPdf.value) return

  exportingPdf.value = true
  pdfGeneratedAt.value = formatDate(new Date())

  try {
    await progressPanelRef.value?.prepareForPdfExport?.()
    await nextTick()
    await new Promise((resolve) => setTimeout(resolve, 600))

    const dateLabel = new Intl.DateTimeFormat('en-CA', { timeZone: 'Asia/Jakarta' }).format(new Date())
    const filename = `Laporan-Dashboard-${sanitizePdfFilename(pdfReportName.value)}-${dateLabel}.pdf`

    const el = pdfContentRef.value
    const prevWidth = el.style.width
    el.style.width = '720px'

    try {
      await downloadElementAsPdf(el, filename, {
        html2canvas: {
          width: el.scrollWidth,
          height: el.scrollHeight,
        },
      })
    } finally {
      el.style.width = prevWidth
    }
  } catch (error) {
    console.error('Gagal membuat PDF:', error)
    window.alert('Gagal membuat PDF. Silakan coba lagi.')
  } finally {
    progressPanelRef.value?.restoreAfterPdfExport?.()
    exportingPdf.value = false
  }
}

watch(viewingStudentId, () => {
  viewedStudent.value = null
  loadOverview()
}, { immediate: true })
</script>

<style scoped>
.dashboard-pdf-export.is-exporting :deep(.pdf-hide) {
  display: none !important;
}

.dashboard-pdf-export:not(.is-exporting) .pdf-header {
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid #f3f4f6;
}

.dashboard-pdf-export:not(.is-exporting) .pdf-grid-2 {
  display: grid;
  gap: 1.5rem;
}

@media (min-width: 1024px) {
  .dashboard-pdf-export:not(.is-exporting) .pdf-grid-2 {
    grid-template-columns: 1fr 1fr;
  }
}

.dashboard-pdf-export:not(.is-exporting) .pdf-section {
  background: #ffffff;
  border: 1px solid #f3f4f6;
  border-radius: 1rem;
  padding: 1.25rem;
}

.dashboard-pdf-export:not(.is-exporting) .pdf-card {
  border: 1px solid #f3f4f6;
  border-radius: 0.75rem;
  padding: 0.75rem;
  margin-bottom: 0.75rem;
}

.dashboard-pdf-export:not(.is-exporting) .pdf-section-title {
  font-size: 1.125rem;
  font-weight: 700;
  margin-bottom: 1rem;
}

.dashboard-pdf-export:not(.is-exporting) .pdf-muted {
  color: #6b7280;
  font-size: 0.75rem;
}

.dashboard-pdf-export:not(.is-exporting) .pdf-text-sm {
  font-size: 0.75rem;
  color: #4b5563;
}
</style>
