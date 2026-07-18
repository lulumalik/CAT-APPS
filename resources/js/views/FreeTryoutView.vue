<template>
  <div class="ft-page">
    <Transition name="ft-cross" mode="out-in">
      <div v-if="step === 'choose'" key="landing" class="ft-landing">
        <div class="ft-bg" aria-hidden="true">
          <div class="ft-bg__glow ft-bg__glow--a" />
          <div class="ft-bg__glow ft-bg__glow--b" />
          <div class="ft-bg__dots" />
        </div>

        <header class="ft-nav">
          <router-link to="/" class="ft-brand" aria-label="CATLab beranda">
            <img :src="logoUrl" alt="" class="ft-brand__logo" />
            <span class="ft-brand__text">CAT<span>Lab</span></span>
          </router-link>


          <div class="ft-nav__actions">
            <router-link to="/login" class="ft-btn ft-btn--ghost ft-btn--sm">Login</router-link>
            <router-link to="/signup" class="ft-btn ft-btn--primary ft-btn--sm">Registrasi</router-link>
            <button type="button" class="ft-nav__burger" aria-label="Menu" @click="mobileOpen = !mobileOpen">
              <Menu v-if="!mobileOpen" class="h-5 w-5" />
              <X v-else class="h-5 w-5" />
            </button>
          </div>
        </header>

        <div v-if="mobileOpen" class="ft-mobile-menu">
          <a href="#beranda" @click="mobileOpen = false">Beranda</a>
          <a href="#tryout" @click="mobileOpen = false">Tryout</a>
          <a href="mailto:halo@catlab.id" @click="mobileOpen = false">Feedback</a>
          <a href="#tentang" @click="mobileOpen = false">Tentang</a>
          <div class="ft-mobile-menu__actions">
            <router-link to="/login" class="ft-btn ft-btn--ghost" @click="mobileOpen = false">Login</router-link>
            <router-link to="/signup" class="ft-btn ft-btn--primary" @click="mobileOpen = false">Registrasi</router-link>
          </div>
        </div>

        <section id="beranda" class="ft-hero">
          <div class="ft-hero__copy">
            <span class="ft-badge">GRATIS 100%</span>
            <h1 class="ft-hero__title">
              Free Tryout CAT,<br />
              <span>siap uji kemampuanmu</span>
            </h1>
            <p class="ft-hero__desc">
              Bingung mau coba JLPT N4, TOEFL, SNMPTN, atau ujian lain? Kerjakan tryout gratis, lihat hasilnya,
              lalu kirim feedback biar CATLab makin berguna buat kamu.
            </p>
            <div class="ft-hero__cta">
              <button type="button" class="ft-btn ft-btn--primary ft-btn--lg" @click="scrollToTryouts">
                Mulai Tryout Gratis
                <ArrowRight class="h-4 w-4" />
              </button>
              <button type="button" class="ft-btn ft-btn--outline ft-btn--lg" @click="scrollToTryouts">
                <Calendar class="h-4 w-4" />
                Lihat Jadwal
              </button>
            </div>
          </div>

          <div class="ft-hero__visual" aria-hidden="true">
            <div class="ft-hero__orbit" />
            <img :src="monitorUrl" alt="" class="ft-hero__monitor" />
            <img :src="paperUrl" alt="" class="ft-hero__float ft-hero__float--paper" />
            <img :src="scoreUrl" alt="" class="ft-hero__float ft-hero__float--score" />
            <img :src="vasUrl" alt="" class="ft-hero__float ft-hero__float--vas" />
            <div class="ft-hero__timer">
              <img :src="clockUrl" alt="" class="ft-hero__timer-icon" />
              <span>60:00</span>
            </div>
          </div>
        </section>

        <section class="ft-stats" aria-label="Statistik platform">
          <div class="ft-stat">
            <span class="ft-stat__icon ft-stat__icon--blue"><img :src="paperUrl" alt="" /></span>
            <div>
              <strong>{{ stats.tryouts }}</strong>
              <span>Tryout Tersedia</span>
            </div>
          </div>
          <div class="ft-stat">
            <span class="ft-stat__icon ft-stat__icon--purple"><Users class="h-5 w-5" /></span>
            <div>
              <strong>15K+</strong>
              <span>Peserta Aktif</span>
            </div>
          </div>
          <div class="ft-stat">
            <span class="ft-stat__icon ft-stat__icon--amber"><Star class="h-5 w-5" /></span>
            <div>
              <strong>4.9</strong>
              <span>Rating Platform</span>
            </div>
          </div>
          <div class="ft-stat">
            <span class="ft-stat__icon ft-stat__icon--green"><img :src="pencilUrl" alt="" /></span>
            <div>
              <strong>{{ stats.questions }}</strong>
              <span>Soal Berkualitas</span>
            </div>
          </div>
        </section>

        <section id="tryout" class="ft-popular">
          <header class="ft-section-head">
            <h2>Tryout Populer</h2>
            <p>Pilih tryout yang sedang dibuka, lalu mulai saat periode aktif.</p>
          </header>

          <div v-if="loading" class="ft-empty">Memuat tryout...</div>
          <div v-else-if="tests.length === 0" class="ft-empty ft-empty--card">Belum ada tryout gratis yang aktif.</div>
          <div v-else class="ft-cards" :class="{ 'is-expanded': showAll }">
            <article
              v-for="(test, idx) in visibleTests"
              :key="test.id"
              class="ft-card"
              :class="[`ft-card--${toneFor(idx)}`, { 'is-primary': idx === 0 }]"
            >
              <div class="ft-card__top">
                <span class="ft-card__icon"><component :is="iconFor(test, idx)" class="h-5 w-5" /></span>
                <span class="ft-card__tag">Gratis</span>
              </div>
              <h3>{{ test.name }}</h3>
              <p v-if="test.description" class="ft-card__desc">{{ test.description }}</p>
              <div class="ft-card__meta">
                <span><FileText class="h-3.5 w-3.5" /> {{ questionCount(test) }} Soal</span>
                <span><Clock class="h-3.5 w-3.5" /> {{ test.duration }} Menit</span>
              </div>
              <p class="ft-card__period"><Calendar class="h-3.5 w-3.5" /> {{ formatPeriod(test) }}</p>
              <button
                v-if="test.status === 'ongoing' && test.can_submit"
                type="button"
                class="ft-card__cta"
                @click="pickTest(test)"
              >
                Mulai Tryout <ArrowRight class="h-4 w-4" />
              </button>
              <span v-else-if="test.status === 'upcoming' || test.status === 'scheduled'" class="ft-card__status">
                {{ test.status === 'scheduled' ? 'Menunggu jadwal' : 'Belum dimulai' }}
              </span>
              <span v-else class="ft-card__status">Berakhir</span>
            </article>
          </div>

          <div v-if="tests.length > 1 && !showAll" class="ft-chips">
            <button
              v-for="(test, idx) in tests.slice(1, 5)"
              :key="`chip-${test.id}`"
              type="button"
              class="ft-chip"
              :class="`ft-chip--${toneFor(idx + 1)}`"
              :title="test.name"
              @click="pickTest(test)"
            >
              <component :is="iconFor(test, idx + 1)" class="h-4 w-4" />
            </button>
          </div>

          <div v-if="tests.length > 1" class="ft-popular__more">
            <button type="button" class="ft-btn ft-btn--outline" @click="showAll = !showAll">
              {{ showAll ? 'Tutup' : 'Lihat Semua Tryout' }}
              <ChevronDown class="h-4 w-4" :class="{ 'ft-rotate': showAll }" />
            </button>
          </div>
        </section>

        <section id="tentang" class="ft-features">
          <div class="ft-feature">
            <span class="ft-feature__icon"><Shield class="h-5 w-5" /></span>
            <strong>100% Gratis</strong>
            <p>Tanpa biaya tersembunyi</p>
          </div>
          <div class="ft-feature">
            <span class="ft-feature__icon"><BarChart3 class="h-5 w-5" /></span>
            <strong>Hasil Instan</strong>
            <p>Skor langsung setelah submit</p>
          </div>
          <div class="ft-feature">
            <span class="ft-feature__icon"><Target class="h-5 w-5" /></span>
            <strong>Evaluasi Akurat</strong>
            <p>Soal sesuai standar ujian</p>
          </div>
          <div class="ft-feature">
            <span class="ft-feature__icon"><Heart class="h-5 w-5" /></span>
            <strong>Untuk Semua</strong>
            <p>JLPT, TOEFL, SNMPTN, dll.</p>
          </div>
        </section>

        <footer class="ft-footer">
          <span>© {{ year }} CATLab</span>
          <a href="mailto:halo@catlab.id">halo@catlab.id</a>
        </footer>
      </div>

      <div v-else-if="step === 'test'" key="test" class="ft-runner">
        <TestRunnerPanel
          v-if="selectedTest && questions.length"
          :test-data="selectedTest"
          :questions="questions"
          :submitting="submitting"
          @submit="handleTryoutSubmit"
        />
      </div>

      <div v-else key="session" class="ft-session">
        <div class="ft-bg" aria-hidden="true">
          <div class="ft-bg__glow ft-bg__glow--a" />
          <div class="ft-bg__dots" />
        </div>
        <main class="ft-session__main">
          <Transition name="ft-cross" mode="out-in">
            <section v-if="step === 'form'" key="form" class="ft-panel">
              <div class="ft-panel__head">
                <div>
                  <h2>Form Peserta Tryout</h2>
                  <p>Tryout: {{ selectedTest?.name }}</p>
                </div>
                <button type="button" class="ft-link" @click="backToChoose">Kembali</button>
              </div>
              <form class="ft-form" @submit.prevent="startTryout">
                <label>Nama Lengkap<input v-model="form.full_name" required /></label>
                <label>
                  Jenis Kelamin
                  <select v-model="form.gender" required>
                    <option value="">Pilih</option>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                  </select>
                </label>
                <label class="ft-form--span2">Alamat Lengkap<textarea v-model="form.address" required rows="2" /></label>
                <label>Tanggal Lahir<input v-model="form.birth_date" required type="date" /></label>
                <label>
                  Nomor Telepon / Whatsapp
                  <div class="ft-phone">
                    <span>+62</span>
                    <input v-model="form.phone" required type="tel" inputmode="numeric" placeholder="812345678" />
                  </div>
                  <small v-if="form.phone && !isValidPhoneLocal(form.phone)">Nomor tidak valid. Contoh: 812345678</small>
                </label>
                <label class="ft-form--span2">Alamat Email<input v-model="form.email" required type="email" placeholder="contoh@email.com" /></label>
                <div class="ft-form--span2">
                  <button type="submit" class="ft-btn ft-btn--primary">Mulai Tryout</button>
                </div>
              </form>
            </section>

            <section v-else-if="step === 'result'" key="result" class="ft-result">
              <div ref="scorePanelRef" class="ft-panel ft-panel--result">
                <h2>Hasil Tryout</h2>
                <p>Selamat! Anda telah menyelesaikan tryout ini dengan sukses.</p>
                <div ref="scoreSparkleTargetRef" class="ft-score">
                  <span>Skor</span>
                  <strong>{{ result.score }} <em>/</em> {{ result.total }}</strong>
                </div>
                <p class="ft-result__note">Hasil tryout akan dikirim ke email Anda. Terima kasih telah berpartisipasi.</p>
                <button type="button" class="ft-btn ft-btn--primary" @click="resetFlow">Kembali ke beranda</button>
              </div>
            </section>
          </Transition>
        </main>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import {
  ArrowRight, BarChart3, BookOpen, Building2, Calendar, ChevronDown, Clock, FileText,
  GraduationCap, Headphones, Heart, Languages, Menu, Shield, Star, Target, Users, X,
} from 'lucide-vue-next'
import { confetti, sparkles, variation } from 'party-js'
import { useToast } from '@/composables/useNotification'
import TestRunnerPanel from '@/components/TestRunnerPanel.vue'
import './free-tryout.css'

const toast = useToast()
const logoUrl = new URL('../../assets/favicon_io/android-chrome-192x192.png', import.meta.url).href
const monitorUrl = new URL('../../assets/properties/monitor.png', import.meta.url).href
const paperUrl = new URL('../../assets/properties/paper.png', import.meta.url).href
const scoreUrl = new URL('../../assets/properties/score.png', import.meta.url).href
const clockUrl = new URL('../../assets/properties/clockl.png', import.meta.url).href
const pencilUrl = new URL('../../assets/properties/pencil.png', import.meta.url).href
const vasUrl = new URL('../../assets/properties/vas.png', import.meta.url).href

const tests = ref([])
const selectedTest = ref(null)
const questions = ref([])
const step = ref('choose')
const loading = ref(false)
const submitting = ref(false)
const result = ref({ score: 0, total: 0 })
const showAll = ref(false)
const mobileOpen = ref(false)
const activeNav = ref('beranda')
const year = new Date().getFullYear()
const scoreSparkleTargetRef = ref(null)
const scorePanelRef = ref(null)
const form = ref({ full_name: '', gender: '', city: '', birth_date: '', phone: '', email: '', address: '' })

const tones = ['red', 'purple', 'blue', 'orange']
const categoryIcons = [Languages, Headphones, GraduationCap, Building2, BookOpen]
const visibleTests = computed(() => (showAll.value ? tests.value : tests.value.slice(0, 4)))
const stats = computed(() => {
  const tryoutCount = tests.value.length
  const questionSum = tests.value.reduce((sum, t) => sum + questionCount(t), 0)
  return {
    tryouts: tryoutCount > 0 ? `${tryoutCount}+` : '120+',
    questions: questionSum > 0 ? `${questionSum}+` : '8000+',
  }
})

function toneFor(idx) { return tones[idx % tones.length] }
function iconFor(test, idx) {
  const name = String(test?.name || test?.category || '').toLowerCase()
  if (name.includes('jlpt') || name.includes('jft') || name.includes('jepang')) return Languages
  if (name.includes('toefl') || name.includes('ielts') || name.includes('inggris')) return Headphones
  if (name.includes('snmptn') || name.includes('sbmptn') || name.includes('ptn')) return GraduationCap
  if (name.includes('cpns') || name.includes('polri') || name.includes('tni')) return Building2
  return categoryIcons[idx % categoryIcons.length]
}
function normalizePhoneLocal(rawPhone) {
  const digitsOnly = String(rawPhone || '').replace(/\D/g, '')
  if (digitsOnly.startsWith('62')) return digitsOnly.slice(2)
  if (digitsOnly.startsWith('0')) return digitsOnly.slice(1)
  return digitsOnly
}
function isValidPhoneLocal(localPhone) { return /^8\d{8,11}$/.test(String(localPhone || '')) }
function formatPhoneForBackend(localPhone) {
  const normalized = normalizePhoneLocal(localPhone)
  return normalized ? `62${normalized}` : ''
}
function runScoreCelebration() {
  const target = scoreSparkleTargetRef.value
  const panel = scorePanelRef.value
  if (!target) return
  sparkles(target, { count: variation.range(22, 40), speed: variation.range(120, 220), size: variation.range(0.9, 1.9) })
  if (panel) confetti(panel, { count: variation.range(28, 48), spread: variation.range(38, 52) })
}
watch(() => step.value, async (s) => {
  if (s !== 'result') return
  await nextTick()
  requestAnimationFrame(() => requestAnimationFrame(() => runScoreCelebration()))
})
watch(() => form.value.phone, (value) => {
  const normalized = normalizePhoneLocal(value)
  if (value !== normalized) form.value.phone = normalized
})
function formatPeriod(test) {
  const s = test?.start_time
  const e = test?.end_time
  if (!s || !e) return '—'
  const opts = { day: '2-digit', month: '2-digit', year: 'numeric' }
  try {
    return `${new Date(s).toLocaleDateString('id-ID', opts)} - ${new Date(e).toLocaleDateString('id-ID', opts)}`
  } catch { return '—' }
}
function questionCount(test) {
  const ids = test?.question_ids
  return Array.isArray(ids) ? ids.length : 0
}
function scrollToTryouts() {
  document.getElementById('tryout')?.scrollIntoView({ behavior: 'smooth', block: 'start' })
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
    mobileOpen.value = false
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
    step.value = 'test'
  } catch (error) {
    toast.error('Error', error?.response?.data?.message || 'Gagal memulai tryout')
  }
}
async function handleTryoutSubmit({ answers }) {
  if (!selectedTest.value || submitting.value) return
  if (!isValidPhoneLocal(form.value.phone)) {
    toast.error('Info', 'Nomor telepon tidak valid. Gunakan format seperti 812345678.')
    return
  }
  submitting.value = true
  try {
    const payload = {
      full_name: form.value.full_name,
      gender: form.value.gender,
      city: form.value.address,
      birth_date: form.value.birth_date,
      phone: formatPhoneForBackend(form.value.phone),
      answers,
    }
    const { data } = await window.axios.post(`/api/free-tryout/tests/${selectedTest.value.id}/submit`, payload)
    result.value = { score: Number(data?.score || 0), total: Number(data?.total || questions.value.length) }
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
}
function onScrollSpy() {
  const sections = ['beranda', 'tryout', 'tentang']
  let current = 'beranda'
  for (const id of sections) {
    const el = document.getElementById(id)
    if (el && el.getBoundingClientRect().top <= 120) current = id
  }
  activeNav.value = current
}
onMounted(() => {
  loadTests()
  window.addEventListener('scroll', onScrollSpy, { passive: true })
})
onUnmounted(() => window.removeEventListener('scroll', onScrollSpy))
</script>

