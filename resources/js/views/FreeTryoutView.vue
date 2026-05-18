<template>
  <div class="min-h-screen bg-background text-text">
    <Transition name="ft-cross" mode="out-in">
      <div v-if="step === 'choose'" key="landing" class="min-h-screen">
        <section class="relative min-h-[420px] md:min-h-[480px] overflow-hidden rounded-none md:rounded-b-3xl border-b border-border">
          <img
            :src="bookUrl"
            alt=""
            class="absolute inset-0 h-full w-full object-cover object-center"
          />
          <div class="relative z-10 mx-auto max-w-6xl px-4 md:px-8 pt-6 pb-12 md:pb-16">
            <header
              class="flex flex-wrap items-center justify-between gap-4 rounded-2xl border border-border/80 bg-white/90 px-4 py-3 shadow-sm shadow-primary/5 backdrop-blur-md"
            >
              <router-link to="/" class="text-lg font-extrabold tracking-tight text-primary">
                {{ t('app.name') }}
              </router-link>
              <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-text">
                <router-link to="/" class="hover:text-secondary transition-colors">Beranda</router-link>
                <router-link to="/" class="inline-flex items-center gap-1 hover:text-secondary transition-colors">
                  Tentang Kami
                  <ChevronDown class="h-4 w-4 opacity-60" />
                </router-link>
                <router-link to="/" class="hover:text-secondary transition-colors">Kontak</router-link>
              </nav>
              <div class="flex items-center gap-2">
                <router-link
                  to="/signup"
                  class="rounded-full bg-primary px-4 py-2 text-xs font-bold text-white shadow-md shadow-primary/20 hover:bg-secondary transition-colors"
                >
                  Registrasi
                </router-link>
                <router-link
                  to="/login"
                  class="rounded-full border-2 border-text px-4 py-2 text-xs font-bold text-text hover:bg-sky transition-colors"
                >
                  Login
                </router-link>
              </div>
            </header>

            <div class="mt-10 md:mt-14 grid lg:items-center">
              <div>
                <div class="text-center">
                  <h1 class="text-3xl md:text-5xl font-black leading-tight tracking-tight text-white">
                    Tryout gratis, kapan saja siap berlatih
                  </h1>
                  <p class="mt-4 text-white md:text-lg font-medium text-muted leading-relaxed">
                    Latih kemampuan Anda dengan paket tryout gratis dari tim pembina. Isi data singkat, kerjakan soal, dan lihat
                    hasilnya langsung.
                  </p>
                </div>
                <div class="mt-8 flex flex-wrap gap-3 justify-center">
                  <button
                    type="button"
                    class="rounded-full bg-secondary px-6 py-3 text-sm font-bold text-white shadow-lg shadow-secondary/25 hover:bg-primary transition-colors"
                    @click="scrollToTryouts"
                  >
                    Lihat tryout
                  </button>
                  <router-link
                    to="/"
                    class="rounded-full border-2 border-primary px-6 py-3 text-sm font-bold text-primary bg-sky transition-colors"
                  >
                    Kembali ke beranda
                  </router-link>
                </div>
              </div>
            </div>
          </div>
        </section>

        <section id="tryout-list" class="bg-white py-12 md:py-16 px-4 md:px-8">
          <div class="mx-auto max-w-6xl">
            <h2 class="text-center text-2xl md:text-3xl font-black text-text">Sedang Berlangsung</h2>
            <p class="mx-auto mt-2 max-w-lg text-center text-sm text-muted">
              Pilih tryout yang sedang dibuka, periksa jadwal, lalu mulai saat periode aktif.
            </p>

            <div v-if="loading" class="mt-10 text-center text-sm text-muted">Memuat tryout...</div>
            <div
              v-else-if="tests.length === 0"
              class="mt-10 rounded-2xl border border-border bg-background py-14 text-center text-sm text-muted"
            >
              Belum ada tryout gratis yang aktif.
            </div>
            <div v-else class="mt-10 grid gap-6 md:grid-cols-2">
              <article
                v-for="test in tests"
                :key="test.id"
                class="flex flex-col overflow-hidden rounded-2xl border border-border bg-white shadow-sm shadow-primary/5"
              >
                <div
                  class="flex items-center gap-2 border-b border-border/80 bg-gradient-to-r from-cream to-sky px-4 py-3 text-xs font-bold text-text md:text-sm"
                >
                  <Calendar class="h-4 w-4 shrink-0 text-secondary" />
                  <span>Periode : {{ formatPeriod(test) }}</span>
                </div>
                <div class="flex flex-1 flex-col px-5 py-5">
                  <h3 class="text-lg md:text-xl font-black uppercase leading-snug tracking-tight text-text">
                    {{ test.name }}
                  </h3>
                  <p v-if="test.description" class="mt-2 line-clamp-2 text-sm text-muted">{{ test.description }}</p>
                  <div class="mt-auto border-t border-border pt-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-4 text-xs font-semibold text-muted">
                      <span class="inline-flex items-center gap-1.5">
                        <Clock class="h-4 w-4 text-secondary" />
                        {{ test.duration }} menit
                      </span>
                      <span class="inline-flex items-center gap-1.5">
                        <FileText class="h-4 w-4 text-secondary" />
                        {{ questionCount(test) }} soal
                      </span>
                    </div>
                    <button
                      v-if="test.status === 'ongoing' && test.can_submit"
                      type="button"
                      class="shrink-0 rounded-full bg-primary px-5 py-2 text-xs font-bold text-white hover:bg-secondary transition-colors"
                      @click="pickTest(test)"
                    >
                      Mulai
                    </button>
                    <span
                      v-else-if="test.status === 'upcoming' || test.status === 'scheduled'"
                      class="shrink-0 rounded-full border border-border bg-background px-4 py-2 text-xs font-bold text-muted"
                    >
                      {{ test.status === 'scheduled' ? 'Menunggu jadwal' : 'Belum dimulai' }}
                    </span>
                    <span
                      v-else
                      class="shrink-0 rounded-full border border-border bg-background px-4 py-2 text-xs font-bold text-muted"
                    >
                      Berakhir
                    </span>
                  </div>
                </div>
              </article>
            </div>
          </div>
        </section>
      </div>

      <div v-else key="session" class="relative min-h-screen overflow-hidden bg-background">
        <img
          :src="bookUrl"
          alt=""
          class="pointer-events-none absolute inset-0 z-0 h-full w-full object-cover object-center"
        />
        <div class="pointer-events-none absolute inset-0 z-[1] bg-gradient-to-b from-background/95 via-background/90 to-background/95" />

        <main class="relative z-20 mx-auto max-w-6xl px-4 py-8 md:px-8">
          <Transition name="ft-cross" mode="out-in">
            <section v-if="step === 'form'" key="form" class="rounded-3xl border border-border bg-white p-6 shadow-sm">
              <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-text">Form Peserta Tryout</h2>
                <button type="button" class="text-sm font-semibold text-secondary hover:text-primary" @click="backToChoose">
                  Kembali
                </button>
              </div>
              <p class="text-sm text-muted mt-1 mb-5">Tryout: {{ selectedTest?.name }}</p>

              <form class="grid md:grid-cols-2 gap-4" @submit.prevent="startTryout">
                <label class="text-sm font-medium text-text">
                  Nama Lengkap
                  <input v-model="form.full_name" required class="mt-1 w-full rounded-lg border border-border px-3 py-2 bg-background" />
                </label>
                <label class="text-sm font-medium text-text">
                  Jenis Kelamin
                  <select v-model="form.gender" required class="mt-1 w-full rounded-lg border border-border px-3 py-2 bg-background">
                    <option value="">Pilih</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                </label>
                <label class="text-sm font-medium text-text">
                  Alamat Lengkap
                  <textarea v-model="form.address" required class="mt-1 w-full rounded-lg border border-border px-3 py-2 bg-background">
                  </textarea>
                </label>
                <label class="text-sm font-medium text-text">
                  Tanggal Lahir
                  <input v-model="form.birth_date" required type="date" class="mt-1 w-full rounded-lg border border-border px-3 py-2 bg-background" />
                </label>
                <label class="text-sm font-medium text-text md:col-span-2">
                  Nomor Telepon / Whatsapp
                  <div
                    class="mt-1 flex w-full items-center overflow-hidden rounded-lg border border-border bg-background transition-colors focus-within:border-secondary"
                  >
                    <span class="shrink-0 border-r border-border px-3 py-2 text-sm text-muted select-none">+62</span>
                    <input
                      v-model="form.phone"
                      required
                      type="tel"
                      inputmode="numeric"
                      autocomplete="tel-national"
                      placeholder="812345678"
                      class="w-full min-w-0 border-0 bg-transparent px-3 py-2 focus:outline-none focus:ring-0"
                    />
                  </div>
                  <span v-if="form.phone && !isValidPhoneLocal(form.phone)" class="mt-1 block text-xs text-red-500">
                    Nomor tidak valid. Contoh: 812345678
                  </span>
                </label>
                <div class="md:col-span-2 pt-2">
                  <button
                    type="submit"
                    class="px-5 py-2.5 rounded-full bg-secondary text-white font-semibold hover:bg-primary transition-colors"
                  >
                    Mulai Tryout
                  </button>
                </div>
              </form>
            </section>

            <section v-else-if="step === 'test'" key="test" class="rounded-3xl border border-border bg-white p-6 shadow-sm">
              <div class="flex items-center justify-between gap-3 mb-6">
                <h2 class="text-xl font-bold text-text">{{ selectedTest?.name }}</h2>
                <div class="text-sm text-muted">Soal {{ currentIndex + 1 }} / {{ questions.length }}</div>
              </div>

              <article v-if="currentQuestion" class="rounded-2xl border border-border bg-background p-5">
                <p class="text-sm text-muted mb-2">Kategori: {{ currentQuestion.category }}</p>
                <h3 class="text-lg font-semibold text-text leading-relaxed">{{ currentQuestion.question }}</h3>

                <div v-if="(currentQuestion.type || 'multiple_choice') === 'multiple_choice'" class="mt-4 space-y-2">
                  <button
                    v-for="opt in (currentQuestion.options || [])"
                    :key="opt.key"
                    type="button"
                    class="w-full text-left rounded-lg border px-3 py-2 transition-colors"
                    :class="answers[currentQuestion.id] === opt.key ? 'border-secondary bg-sky' : 'border-border bg-white hover:border-secondary'"
                    @click="answers[currentQuestion.id] = opt.key"
                  >
                    <span class="font-semibold mr-2">{{ opt.key }}.</span>{{ opt.label }}
                  </button>
                </div>
                <textarea
                  v-else
                  v-model="answers[currentQuestion.id]"
                  rows="5"
                  class="mt-4 w-full rounded-lg border border-border px-3 py-2 bg-white"
                  placeholder="Tulis jawaban..."
                />
              </article>

              <div class="mt-6 flex items-center justify-between">
                <button type="button" class="px-4 py-2 rounded-full border border-border" :disabled="currentIndex === 0" @click="currentIndex -= 1">
                  Sebelumnya
                </button>
                <button
                  v-if="currentIndex < questions.length - 1"
                  type="button"
                  class="px-4 py-2 rounded-full bg-secondary text-white hover:bg-primary transition-colors"
                  @click="currentIndex += 1"
                >
                  Berikutnya
                </button>
                <button
                  v-else
                  type="button"
                  class="px-4 py-2 rounded-full bg-primary text-white hover:bg-secondary transition-colors"
                  @click="openSubmitConfirm"
                >
                  Selesai &amp; kirim
                </button>
              </div>
            </section>

            <section v-else-if="step === 'result'" key="result" class="flex min-h-[50vh] items-center justify-center px-2">
              <div
                ref="scorePanelRef"
                class="w-full max-w-md rounded-3xl border border-border bg-white p-8 text-center shadow-2xl shadow-primary/15"
              >
                <h2 class="text-2xl font-bold text-text">Hasil Tryout</h2>
                <p class="mt-2 text-sm text-muted">Selamat! Anda telah menyelesaikan tryout ini dengan sukses.</p>
                <div
                  ref="scoreSparkleTargetRef"
                  class="mt-6 inline-flex min-w-[12rem] flex-col items-center justify-center gap-1 rounded-2xl bg-sky px-6 py-5 border-2 border-secondary/30"
                >
                  <span class="text-xs font-bold uppercase tracking-wider text-primary">Skor</span>
                  <span class="text-4xl font-black tabular-nums text-secondary md:text-5xl">
                    {{ result.score }} <span class="text-lg font-bold text-muted">/</span> {{ result.total }}
                  </span>
                </div>
                <p class="mt-2 text-sm text-muted mt-8">Hasil tryout akan dikirim ke email Anda. terima kasih telah berpartisipasi.</p>
                <div class="mt-8">
                  <button
                    type="button"
                    class="px-6 py-3 rounded-full bg-secondary text-white font-semibold hover:bg-primary transition-colors"
                    @click="resetFlow"
                  >
                    Kembali ke beranda
                  </button>
                </div>
              </div>
            </section>
          </Transition>
        </main>
      </div>
    </Transition>

    <Teleport to="body">
      <Transition name="ft-fade">
        <div
          v-if="showSubmitConfirm"
          class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4 backdrop-blur-[2px]"
          role="dialog"
          aria-modal="true"
          aria-labelledby="submit-confirm-title"
          @click.self="showSubmitConfirm = false"
        >
          <div class="max-w-md w-full rounded-3xl border border-border bg-white p-6 shadow-2xl">
            <h3 id="submit-confirm-title" class="text-lg font-bold text-text">Kirim jawaban?</h3>
            <p class="mt-2 text-sm text-muted leading-relaxed">
              Pastikan Anda sudah memeriksa kembali jawaban di setiap soal. Setelah dikirim, jawaban tidak bisa diubah.
            </p>
            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
              <button
                type="button"
                class="w-full sm:w-auto rounded-full border border-border px-4 py-2.5 text-sm font-semibold text-text hover:bg-background transition-colors"
                @click="showSubmitConfirm = false"
              >
                Periksa lagi
              </button>
              <button
                type="button"
                class="w-full sm:w-auto rounded-full bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-secondary transition-colors disabled:opacity-50"
                :disabled="submitting"
                @click="confirmSubmitTryout"
              >
                {{ submitting ? 'Mengirim...' : 'Ya, kirim sekarang' }}
              </button>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref, watch } from 'vue'
import { Calendar, ChevronDown, Clock, FileText } from 'lucide-vue-next'
import { confetti, sparkles, variation } from 'party-js'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const bookUrl = new URL('../../assets/book.jpg', import.meta.url).href

const toast = useToast()
const { t } = useI18n()

const tests = ref([])
const selectedTest = ref(null)
const questions = ref([])
const step = ref('choose')
const loading = ref(false)
const submitting = ref(false)
const currentIndex = ref(0)
const answers = ref({})
const result = ref({ score: 0, total: 0 })

const showSubmitConfirm = ref(false)
const scoreSparkleTargetRef = ref(null)
const scorePanelRef = ref(null)

const form = ref({
  full_name: '',
  gender: '',
  city: '',
  birth_date: '',
  phone: '',
})

const currentQuestion = computed(() => questions.value[currentIndex.value] || null)

function normalizePhoneLocal(rawPhone) {
  const digitsOnly = String(rawPhone || '').replace(/\D/g, '')
  if (digitsOnly.startsWith('62')) return digitsOnly.slice(2)
  if (digitsOnly.startsWith('0')) return digitsOnly.slice(1)
  return digitsOnly
}

function isValidPhoneLocal(localPhone) {
  return /^8\d{8,11}$/.test(String(localPhone || ''))
}

function formatPhoneForBackend(localPhone) {
  const normalized = normalizePhoneLocal(localPhone)
  return normalized ? `62${normalized}` : ''
}

function runScoreCelebration() {
  const target = scoreSparkleTargetRef.value
  const panel = scorePanelRef.value
  if (!target) return

  sparkles(target, {
    count: variation.range(22, 40),
    speed: variation.range(120, 220),
    size: variation.range(0.9, 1.9),
  })

  if (panel) {
    confetti(panel, {
      count: variation.range(28, 48),
      spread: variation.range(38, 52),
    })
  }
}

watch(
  () => step.value,
  async (s) => {
    if (s !== 'result') return
    await nextTick()
    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        runScoreCelebration()
      })
    })
  },
)

watch(
  () => form.value.phone,
  (value) => {
    const normalized = normalizePhoneLocal(value)
    if (value !== normalized) form.value.phone = normalized
  },
)

function formatPeriod(test) {
  const s = test?.start_time
  const e = test?.end_time
  if (!s || !e) return '—'
  const opts = { day: '2-digit', month: '2-digit', year: 'numeric' }
  try {
    const a = new Date(s).toLocaleDateString('id-ID', opts)
    const b = new Date(e).toLocaleDateString('id-ID', opts)
    return `${a} - ${b}`
  } catch {
    return '—'
  }
}

function questionCount(test) {
  const ids = test?.question_ids
  return Array.isArray(ids) ? ids.length : 0
}

function scrollToTryouts() {
  const el = document.getElementById('tryout-list')
  el?.scrollIntoView({ behavior: 'smooth', block: 'start' })
}

function backToChoose() {
  selectedTest.value = null
  step.value = 'choose'
}

const loadTests = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/free-tryout/tests')
    tests.value = data || []
  } catch {
    toast.error('Error', 'Gagal memuat daftar tryout gratis')
  } finally {
    loading.value = false
  }
}

const pickTest = async (test) => {
  try {
    const { data } = await window.axios.get(`/api/free-tryout/tests/${test.id}`)
    selectedTest.value = data
    step.value = 'form'
  } catch (error) {
    toast.error('Error', error?.response?.data?.message || 'Tryout tidak tersedia')
  }
}

const startTryout = async () => {
  if (!selectedTest.value) return
  if (!isValidPhoneLocal(form.value.phone)) {
    toast.error('Info', 'Nomor telepon tidak valid. Gunakan format seperti 812345678.')
    return
  }
  try {
    const { data } = await window.axios.get(`/api/free-tryout/tests/${selectedTest.value.id}`)
    if (!data?.can_submit) {
      toast.error('Info', data?.status === 'upcoming' ? 'Tryout belum dimulai.' : 'Tryout sudah berakhir.')
      return
    }
    questions.value = data.questions || []
    answers.value = {}
    currentIndex.value = 0
    step.value = 'test'
  } catch (error) {
    toast.error('Error', error?.response?.data?.message || 'Gagal memulai tryout')
  }
}

function openSubmitConfirm() {
  showSubmitConfirm.value = true
}

async function confirmSubmitTryout() {
  if (!selectedTest.value || submitting.value) return
  if (!isValidPhoneLocal(form.value.phone)) {
    toast.error('Info', 'Nomor telepon tidak valid. Gunakan format seperti 812345678.')
    return
  }
  submitting.value = true
  try {
    const payload = {
      ...form.value,
      phone: formatPhoneForBackend(form.value.phone),
      answers: answers.value,
    }
    const { data } = await window.axios.post(`/api/free-tryout/tests/${selectedTest.value.id}/submit`, payload)
    result.value = {
      score: Number(data?.score || 0),
      total: Number(data?.total || questions.value.length),
    }
    showSubmitConfirm.value = false
    step.value = 'result'
  } catch (error) {
    toast.error('Error', error?.response?.data?.message || 'Gagal mengirim tryout')
  } finally {
    submitting.value = false
  }
}

const resetFlow = () => {
  step.value = 'choose'
  selectedTest.value = null
  questions.value = []
  answers.value = {}
  currentIndex.value = 0
  showSubmitConfirm.value = false
}

onMounted(loadTests)
</script>

<style scoped>
.ft-cross-enter-active,
.ft-cross-leave-active {
  transition: opacity 0.45s ease;
}
.ft-cross-enter-from,
.ft-cross-leave-to {
  opacity: 0;
}

.ft-fade-enter-active,
.ft-fade-leave-active {
  transition: opacity 0.28s ease;
}
.ft-fade-enter-from,
.ft-fade-leave-to {
  opacity: 0;
}
</style>
