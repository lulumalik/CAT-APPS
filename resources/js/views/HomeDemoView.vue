<template>
  <div ref="viewportRef" class="cs-viewport">
    <!-- Slide dots -->
    <nav class="cs-dots" aria-label="Navigasi slide">
      <a v-for="slide in slides" :key="slide.id" :href="`#${slide.id}`" class="cs-dots__dot"
        :class="{ 'is-active': activeSlide === slide.id, 'is-light': activeOnDark }" :aria-label="slide.label" />
    </nav>

    <!-- ===================== SLIDE 1 : HERO ===================== -->
    <section id="beranda" ref="slideRefs" class="cs-slide cs-slide--hero">
      <div class="cs-hero__dark" aria-hidden="true">
        <div class="cs-hero__dots-pattern" />
      </div>

      <!-- Nav -->
      <header class="cs-nav">
        <router-link to="/" class="cs-brand" aria-label="CATLab beranda">
          <span class="cs-brand__mark">
            <CheckSquare class="h-5 w-5" />
          </span>
          <span class="cs-brand__text">
            <span class="cs-brand__title"><span class="cs-brand__cat">CAT</span><span
                class="cs-brand__sim">Lab</span></span>
            <span class="cs-brand__sub">Computer Assisted Test</span>
          </span>
        </router-link>

        <nav class="cs-nav__links" aria-label="Navigasi utama">
          <a href="#beranda" class="cs-nav__link">Beranda</a>
          <a href="#fitur" class="cs-nav__link">Fitur</a>
          <a href="#simulasi" class="cs-nav__link">Simulasi</a>
          <a href="#tentang" class="cs-nav__link">Tentang</a>
          <a href="#kontak" class="cs-nav__link">Kontak</a>
        </nav>

        <div class="cs-nav__actions">
          <router-link to="/login" class="cs-nav__avatar" aria-label="Masuk">
            <User class="h-4 w-4" />
          </router-link>
          <button type="button" class="cs-nav__burger" aria-label="Menu" @click="mobileOpen = !mobileOpen">
            <Menu class="h-5 w-5" />
          </button>
        </div>
      </header>

      <Transition name="cs-fade">
        <div v-if="mobileOpen" class="cs-nav__mobile">
          <a v-for="slide in slides" :key="slide.id" :href="`#${slide.id}`" class="cs-nav__link"
            @click="mobileOpen = false">
            {{ slide.label }}
          </a>
          <router-link to="/login" class="cs-nav__link" @click="mobileOpen = false">Masuk</router-link>
          <router-link to="/signup" class="cs-nav__link" @click="mobileOpen = false">Daftar</router-link>
        </div>
      </Transition>

      <!-- Vertical brand text (left edge) -->
      <p class="cs-hero__vertical" aria-hidden="true">
        <span class="cs-hero__vertical-dot" /> CATLab
      </p>

      <!-- Copy -->
      <div class="cs-hero__copy">
        <p class="cs-hero__eyebrow">Latihan &bull; Simulasi &bull; Sukses</p>

        <h1 class="cs-hero__title">
          Berlatih hari ini,<br />
          Tingkatkan hasil<br />
          <span class="cs-hero__title-blue">ujianmu.</span>
        </h1>

        <p class="cs-hero__lead">
          Platform simulasi ujian berbasis CAT untuk membantu kamu mempersiapkan
          JLPT/N4, TOEFL, SNMPTN, SBMPTN, JFT, dan ujian lainnya — lebih efektif,
          terukur, dan gratis.
        </p>

        <div class="cs-hero__actions">
          <router-link to="/free-tryout" class="cs-cta">
            <Play class="h-4 w-4" fill="currentColor" />
            Mulai Simulasi Sekarang
          </router-link>
        </div>

        <div class="cs-hero__features">
          <div v-for="feature in heroFeatures" :key="feature.title" class="cs-hero__feature">
            <span class="cs-hero__feature-icon" :style="{ background: feature.bg, color: feature.color }">
              <component :is="feature.icon" class="h-5 w-5" />
            </span>
            <div>
              <p class="cs-hero__feature-title">{{ feature.title }}</p>
              <p class="cs-hero__feature-text">{{ feature.text }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Big circle on the split line -->
      <div class="cs-circle">
        <div class="cs-circle__ring" />
        <div class="cs-circle__orbit" aria-hidden="true">
          <span
            v-for="i in 5"
            :key="i"
            class="cs-circle__ring-dot-wrap"
            :style="{ '--i': i - 1 }"
          >
            <span class="cs-circle__ring-dot" />
          </span>
        </div>

        <div class="cs-circle__disc">
          <div class="cs-circle__half-light">
            <span class="cs-float cs-float--clipboard">
              <ClipboardList class="h-6 w-6" />
            </span>
            <span class="cs-float cs-float--clock">
              <Clock class="h-6 w-6" />
            </span>
            <span class="cs-float cs-float--cap">
              <GraduationCap class="h-7 w-7" />
            </span>

            <div class="cs-monitor">
              <div class="cs-monitor__screen">
                <p class="cs-monitor__q">Soal 12 dari 40</p>
                <div class="cs-monitor__progress"><span style="width: 32%" /></div>
                <ul class="cs-monitor__options">
                  <li class="is-active"><span>A</span></li>
                  <li><span>B</span></li>
                  <li><span>C</span></li>
                  <li><span>D</span></li>
                </ul>
              </div>
              <div class="cs-monitor__stand" />
              <div class="cs-monitor__base" />
            </div>
            <div class="cs-books">
              <span /><span /><span />
            </div>
          </div>
          <div class="cs-circle__half-dark">
            <div class="cs-hero__dots-pattern cs-hero__dots-pattern--circle" />
            <div class="cs-score-card">
              <p class="cs-score-card__value">86<span>/100</span></p>
              <div class="cs-score-card__bars">
                <span style="height: 40%" /><span style="height: 65%" /><span style="height: 50%" /><span
                  style="height: 85%" />
              </div>
            </div>
          </div>
        </div>

        <button type="button" class="cs-circle__play" aria-label="Lihat fitur" @click="goToSlide('fitur')">
          <Play class="h-5 w-5" fill="currentColor" />
        </button>
        <svg class="cs-circle__wire" viewBox="0 0 120 40" fill="none" aria-hidden="true">
          <path d="M4 4 C 30 44, 60 -10, 116 24" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" />
        </svg>
      </div>

      <!-- Vertical timeline (right, on dark) -->
      <ol class="cs-timeline">
        <li v-for="(step, index) in timelineSteps" :key="step" :class="{ 'is-first': index === 0 }">
          <span class="cs-timeline__num">0{{ index + 1 }}</span>
          <span class="cs-timeline__label">{{ step }}</span>
        </li>
      </ol>

      <!-- Floating badges on dark -->
      <div class="cs-badge cs-badge--chart">
        <BarChart3 class="h-4 w-4" />
      </div>
      <div class="cs-badge cs-badge--clock">
        <Clock class="h-4 w-4" />
      </div>
    </section>

    <!-- ===================== SLIDE 2 : FITUR ===================== -->
    <section id="fitur" ref="slideRefs" class="cs-slide cs-slide--light">
      <div class="cs-features__wash" aria-hidden="true" />
      <div class="cs-feature-float cs-feature-float--quiz" aria-hidden="true">
        <span class="cs-feature-float__bar" />
        <span v-for="option in ['A', 'B', 'C']" :key="option" class="cs-feature-float__option">{{ option }}</span>
        <span class="cs-feature-float__check">
          <Check class="h-4 w-4" />
        </span>
      </div>
      <div class="cs-feature-float cs-feature-float--chart" aria-hidden="true">
        <PieChart class="cs-feature-float__pie" />
        <BarChart3 class="cs-feature-float__bars" />
        <span class="cs-feature-float__mini">
          <TrendingUp class="h-5 w-5" />
        </span>
      </div>

      <div class="cs-slide__inner cs-slide__inner--features">
        <div class="cs-section__head">
          <p class="cs-section__eyebrow">Fitur</p>
          <h2 class="cs-section__title">
            Semua yang kamu butuhkan buat
            <span class="cs-section__title-blue">berlatih</span>
          </h2>
          <p class="cs-section__desc">
            Dirancang supaya latihan CAT terasa seperti ujian sungguhan — cepat, jujur, dan mudah dipantau.
          </p>
        </div>

        <div class="cs-feature-grid">
          <article v-for="feature in mainFeatures" :key="feature.title" class="cs-feature-card">
            <div class="cs-feature-card__body">
              <span class="cs-feature-card__icon">
                <component :is="feature.icon" class="cs-feature-card__icon-svg" />
              </span>
              <div class="cs-feature-card__copy">
                <h3 class="cs-feature-card__title">{{ feature.title }}</h3>
                <p class="cs-feature-card__text">{{ feature.text }}</p>
              </div>
            </div>
            <div class="cs-feature-card__visual" :class="`is-${feature.visual}`" aria-hidden="true">
              <component :is="feature.visualIcon" class="cs-feature-card__visual-icon" />
              <div v-if="feature.visual === 'result'" class="cs-feature-card__chart">
                <span style="height: 36%" /><span style="height: 58%" /><span style="height: 46%" /><span
                  style="height: 80%" />
              </div>
              <span v-if="feature.visual === 'result'" class="cs-feature-card__score">76%</span>
              <span v-if="feature.visual === 'gift'" class="cs-feature-card__check">
                <Check class="h-4 w-4" />
              </span>
            </div>
          </article>
        </div>

        <div class="cs-slide__hint">
          <a href="#simulasi" class="cs-scroll-hint" aria-label="Slide berikutnya">
            <ChevronDown class="h-5 w-5" />
          </a>
        </div>
      </div>
    </section>

    <!-- ===================== SLIDE 3 : SIMULASI ===================== -->
    <section id="simulasi" ref="slideRefs" class="cs-slide cs-slide--tint">
      <div class="cs-simulation__wash" aria-hidden="true" />
      <div class="cs-sim-float cs-sim-float--language" aria-hidden="true">
        <Languages class="cs-sim-float__main-icon" />
        <span>あ</span><span>A</span>
      </div>
      <div class="cs-sim-float cs-sim-float--score" aria-hidden="true">
        <Trophy class="cs-sim-float__main-icon" />
        <strong>86</strong>
        <small>SKOR</small>
      </div>

      <div class="cs-slide__inner cs-slide__inner--simulation">
        <div class="cs-section__head">
          <p class="cs-section__eyebrow">Simulasi</p>
          <h2 class="cs-section__title">
            Pilih target ujianmu,
            <span class="cs-section__title-blue">mulai berlatih sekarang</span>
          </h2>
          <p class="cs-section__desc">
            Bingung mau coba ujian apa dulu? Ini beberapa jalur yang bisa kamu latih lewat free tryout kami.
          </p>
        </div>

        <div class="cs-exam-grid">
          <article v-for="exam in exams" :key="exam.name" class="cs-exam-card">
            <div class="cs-exam-card__top">
              <span class="cs-exam-card__icon" :style="{ background: exam.softColor, color: exam.color }">
                <component :is="exam.icon" class="h-5 w-5" />
              </span>
              <span class="cs-exam-card__tag">{{ exam.tag }}</span>
            </div>
            <h3 class="cs-exam-card__name">{{ exam.name }}</h3>
            <p class="cs-exam-card__blurb">{{ exam.blurb }}</p>

            <div class="cs-exam-card__visual" :style="{ '--exam-color': exam.color, '--exam-soft': exam.softColor }"
              aria-hidden="true">
              <component :is="exam.visualIcon" class="cs-exam-card__visual-icon" />
              <div class="cs-exam-card__sheet">
                <span /><span /><span />
              </div>
              <span class="cs-exam-card__code">{{ exam.code }}</span>
            </div>

            <router-link to="/free-tryout" class="cs-exam-card__action">
              Mulai latihan
              <ArrowUpRight class="h-4 w-4" />
            </router-link>
          </article>
        </div>

        <div class="cs-slide__hint">
          <a href="#tentang" class="cs-scroll-hint" aria-label="Slide berikutnya">
            <ChevronDown class="h-5 w-5" />
          </a>
        </div>
      </div>
    </section>

    <!-- ===================== SLIDE 4 : TENTANG ===================== -->
    <section id="tentang" ref="slideRefs" class="cs-slide cs-slide--light">
      <div class="cs-slide__inner">
        <div class="cs-section__head">
          <p class="cs-section__eyebrow">Tentang</p>
          <h2 class="cs-section__title">Kenapa CATLab?</h2>
          <p class="cs-section__desc">
            Latihan terbaik adalah latihan yang mendekati kondisi ujian sesungguhnya. Gratis, tanpa
            tekanan, dan terus berkembang dari masukan penggunanya.
          </p>
        </div>

        <ol class="cs-steps">
          <li v-for="(step, index) in steps" :key="step.title" class="cs-step">
            <span class="cs-step__num">0{{ index + 1 }}</span>
            <div>
              <h3 class="cs-step__title">{{ step.title }}</h3>
              <p class="cs-step__text">{{ step.text }}</p>
            </div>
          </li>
        </ol>

        <div class="cs-about__stats">
          <div v-for="stat in aboutStats" :key="stat.label" class="cs-about__stat">
            <p class="cs-about__stat-value">{{ stat.value }}</p>
            <p class="cs-about__stat-label">{{ stat.label }}</p>
          </div>
        </div>

        <div class="cs-slide__hint">
          <a href="#kontak" class="cs-scroll-hint" aria-label="Slide berikutnya">
            <ChevronDown class="h-5 w-5" />
          </a>
        </div>
      </div>
    </section>

    <!-- ===================== SLIDE 5 : KONTAK ===================== -->
    <section id="kontak" ref="slideRefs" class="cs-slide cs-slide--dark">
      <div class="cs-slide__inner cs-slide__inner--center">
        <p class="cs-section__eyebrow cs-section__eyebrow--light">Bantu kami berkembang</p>
        <h2 class="cs-feedback__title">Coba dulu, lalu kasih kabar</h2>
        <p class="cs-feedback__text">
          Setelah tryout, ceritakan soal mana yang membantu, mana yang kurang, atau ujian apa yang
          ingin kamu lihat berikutnya. Feedback kamu sangat berarti — gratis, tanpa ikatan berbayar.
        </p>
        <div class="cs-feedback__actions">
          <router-link to="/free-tryout" class="cs-cta">
            <Play class="h-4 w-4" fill="currentColor" />
            Kerjakan free tryout
          </router-link>
          <a :href="feedbackMailto" class="cs-feedback__secondary">
            <Mail class="h-4 w-4" />
            Kirim feedback
          </a>
        </div>

        <footer class="cs-footer">
          <div class="cs-brand cs-footer__brand">
            <span class="cs-brand__mark cs-brand__mark--sm">
              <CheckSquare class="h-4 w-4" />
            </span>
            <span class="cs-brand__title cs-brand__title--sm cs-brand__title--light">CAT<span
                class="cs-brand__sim">Lab</span></span>
          </div>
          <p class="cs-footer__note">Simulasi Computer Assisted Test untuk latihan kemampuan ujian.</p>
          <div class="cs-footer__links">
            <router-link to="/free-tryout">Free Tryout</router-link>
            <router-link to="/login">Masuk</router-link>
            <router-link to="/signup">Daftar</router-link>
            <a :href="feedbackMailto">Feedback</a>
          </div>
        </footer>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import {
  ArrowRight,
  ArrowUpRight,
  BarChart3,
  BookOpen,
  BriefcaseBusiness,
  Check,
  CheckSquare,
  ChevronDown,
  CircleCheckBig,
  ClipboardList,
  Cloud,
  Clock,
  FileText,
  Gift,
  GraduationCap,
  Languages,
  LockKeyhole,
  Mail,
  Menu,
  PieChart,
  Play,
  School,
  Smartphone,
  Timer,
  TrendingUp,
  Trophy,
  User,
} from 'lucide-vue-next'

const mobileOpen = ref(false)
const viewportRef = ref(null)
const activeSlide = ref('beranda')

const slides = [
  { id: 'beranda', label: 'Beranda' },
  { id: 'fitur', label: 'Fitur' },
  { id: 'simulasi', label: 'Simulasi' },
  { id: 'tentang', label: 'Tentang' },
  { id: 'kontak', label: 'Kontak' },
]

const activeOnDark = computed(() => activeSlide.value === 'kontak')

const feedbackMailto = computed(() => {
  const subject = encodeURIComponent('Feedback CATLab')
  const body = encodeURIComponent(
    'Halo tim CATLab,\n\nSaya baru coba free tryout. Feedback saya:\n- \n\nJenis ujian yang saya harapkan berikutnya:\n- \n',
  )
  return `mailto:halo@catlab.id?subject=${subject}&body=${body}`
})

const heroFeatures = [
  { icon: FileText, title: 'Bank Soal', text: 'Ribuan soal siap untuk latihan', bg: '#eaf1fd', color: '#2563eb' },
  { icon: TrendingUp, title: 'Analisis Hasil', text: 'Pantau progres dan tingkatkan kemampuan', bg: '#e5f7f0', color: '#0d9488' },
  { icon: Clock, title: 'Ujian Realistis', text: 'Simulasi mirip ujian sebenarnya', bg: '#f1ecfd', color: '#7c3aed' },
  { icon: Gift, title: 'Gratis & Fleksibel', text: 'Coba kapan saja tanpa biaya', bg: '#fff4e5', color: '#ea580c' },
]

const timelineSteps = ['Berlatih', 'Simulasi', 'Evaluasi']

const mainFeatures = [
  {
    icon: FileText,
    visualIcon: ClipboardList,
    visual: 'questions',
    title: 'Bank Soal',
    text: 'Ribuan soal siap untuk latihan',
  },
  {
    icon: TrendingUp,
    visualIcon: PieChart,
    visual: 'result',
    title: 'Analisis Hasil',
    text: 'Pantau progres dan tingkatkan kemampuan',
  },
  {
    icon: Clock,
    visualIcon: Timer,
    visual: 'timer',
    title: 'Ujian Realistis',
    text: 'Simulasi mirip ujian sebenarnya',
  },
  {
    icon: Gift,
    visualIcon: Gift,
    visual: 'gift',
    title: 'Gratis & Fleksibel',
    text: 'Coba kapan saja tanpa biaya',
  },
]

const exams = [
  {
    tag: 'Bahasa Jepang',
    name: 'JLPT / N4',
    code: '日本語',
    icon: Languages,
    visualIcon: BookOpen,
    color: '#2563eb',
    softColor: '#eaf1fd',
    blurb: 'Coba pola soal reading & grammar khas JLPT sebelum ujian resmi.',
  },
  {
    tag: 'Bahasa Inggris',
    name: 'TOEFL',
    code: 'EN',
    icon: BookOpen,
    visualIcon: Languages,
    color: '#0d9488',
    softColor: '#e5f7f0',
    blurb: 'Latihan tempo soal bahasa Inggris agar terbiasa dengan tekanan waktu.',
  },
  {
    tag: 'Seleksi Kampus',
    name: 'SNMPTN / SBMPTN',
    code: 'PTN',
    icon: School,
    visualIcon: GraduationCap,
    color: '#7c3aed',
    softColor: '#f1ecfd',
    blurb: 'Simulasi CAT untuk mengukur kesiapan masuk perguruan tinggi.',
  },
  {
    tag: 'Persiapan Kerja',
    name: 'JFT & lainnya',
    code: 'JFT',
    icon: BriefcaseBusiness,
    visualIcon: BriefcaseBusiness,
    color: '#ea580c',
    softColor: '#fff1e8',
    blurb: 'Mulai dari free tryout, lalu usulkan jenis ujian yang kamu butuhkan.',
  },
]

const steps = [
  { title: 'Pilih free tryout', text: 'Masuk ke halaman tryout gratis, pilih paket yang sedang dibuka, isi data singkat.' },
  { title: 'Kerjakan seperti ujian asli', text: 'Timer jalan, soal muncul berurutan — rasakan ritme CAT yang sesungguhnya.' },
  { title: 'Lihat hasil & kirim feedback', text: 'Cek skormu, lalu ceritakan ke kami apa yang bisa lebih baik.' },
]

const aboutStats = [
  { value: 'Gratis', label: 'Tanpa biaya tersembunyi' },
  { value: '5+', label: 'Jenis ujian simulasi' },
  { value: '100%', label: 'Berbasis masukan pengguna' },
]

function goToSlide(id) {
  document.getElementById(id)?.scrollIntoView({ behavior: 'smooth' })
}

let observer = null

onMounted(() => {
  observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (entry.isIntersecting) activeSlide.value = entry.target.id
      }
    },
    { root: viewportRef.value, threshold: 0.55 },
  )
  viewportRef.value?.querySelectorAll('.cs-slide').forEach((el) => observer.observe(el))
})

onBeforeUnmount(() => {
  observer?.disconnect()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

.cs-viewport {
  --ink: #0f172a;
  --ink-deep: #0b1220;
  --blue: #2563eb;
  --blue-dark: #1d4ed8;
  --blue-soft: #eaf1fd;
  --paper: #ffffff;
  --paper-tint: #f8fafc;
  --muted: #64748b;
  --line: #e2e8f0;
  --dark-w: clamp(18rem, 34vw, 34rem);
  height: 100vh;
  height: 100dvh;
  overflow-y: auto;
  overflow-x: hidden;
  scrollbar-width: none;
  -ms-overflow-style: none;
  scroll-snap-type: y mandatory;
  scroll-behavior: smooth;
  color: var(--ink);
  background: var(--paper);
  font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;
}

.cs-viewport::-webkit-scrollbar {
  display: none;
  width: 0;
  height: 0;
}

/* ---------- Slides ---------- */
.cs-slide {
  position: relative;
  height: 100vh;
  height: 100dvh;
  scroll-snap-align: start;
  scroll-snap-stop: always;
  overflow: hidden;
}

.cs-slide--light {
  background: var(--paper);
}

.cs-slide--tint {
  background: var(--paper-tint);
}

.cs-slide--dark {
  background: linear-gradient(160deg, var(--ink) 0%, var(--ink-deep) 100%);
  color: #fff;
}

.cs-slide__inner {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  max-width: 72rem;
  margin: 0 auto;
  padding: 3rem clamp(1.25rem, 4vw, 3.5rem);
}

.cs-slide__inner--center {
  align-items: center;
  text-align: center;
}

.cs-slide__hint {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
}

.cs-scroll-hint {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.5rem;
  height: 2.5rem;
  border-radius: 999px;
  border: 1px solid var(--line);
  color: var(--muted);
  animation: cs-bob 2.2s ease-in-out infinite;
}

.cs-scroll-hint:hover {
  color: var(--blue);
  border-color: var(--blue);
}

@keyframes cs-bob {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(5px);
  }
}

/* ---------- Dots nav ---------- */
.cs-dots {
  position: fixed;
  right: 1.1rem;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  flex-direction: column;
  gap: 0.7rem;
  z-index: 50;
}

.cs-dots__dot {
  width: 0.55rem;
  height: 0.55rem;
  border-radius: 999px;
  background: rgba(100, 116, 139, 0.35);
  transition: background 0.2s ease, transform 0.2s ease;
}

.cs-dots__dot.is-light {
  background: rgba(255, 255, 255, 0.35);
}

.cs-dots__dot.is-active {
  background: var(--blue);
  transform: scale(1.35);
}

/* ---------- Hero ---------- */
.cs-slide--hero {
  background: var(--paper);
}

.cs-hero__dark {
  position: absolute;
  top: 0;
  right: 0;
  bottom: 0;
  width: var(--dark-w);
  background: linear-gradient(170deg, #16213a 0%, var(--ink-deep) 100%);
}

.cs-hero__dots-pattern {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(rgba(255, 255, 255, 0.14) 1px, transparent 1px);
  background-size: 16px 16px;
  mask-image: radial-gradient(ellipse at 80% 85%, black 0%, transparent 55%);
}

.cs-hero__dots-pattern--circle {
  mask-image: radial-gradient(ellipse at 70% 30%, black 0%, transparent 70%);
}

.cs-nav {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  z-index: 30;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1.25rem clamp(1.25rem, 4vw, 3rem);
}

.cs-brand {
  display: inline-flex;
  align-items: center;
  gap: 0.65rem;
  text-decoration: none;
  color: inherit;
}

.cs-brand__mark {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.1rem;
  height: 2.1rem;
  border-radius: 0.6rem;
  background: linear-gradient(150deg, var(--blue), var(--blue-dark));
  color: #fff;
  flex-shrink: 0;
}

.cs-brand__mark--sm {
  width: 1.75rem;
  height: 1.75rem;
  border-radius: 0.5rem;
}

.cs-brand__text {
  display: flex;
  flex-direction: column;
  line-height: 1.1;
}

.cs-brand__title {
  font-weight: 800;
  font-size: 1rem;
  letter-spacing: -0.01em;
}

.cs-brand__title--sm {
  font-size: 0.9rem;
}

.cs-brand__title--light {
  color: #fff;
}

.cs-brand__cat {
  color: var(--ink);
}

.cs-brand__title--light .cs-brand__cat {
  color: #fff;
}

.cs-brand__sim {
  color: var(--blue);
}

.cs-brand__sub {
  font-size: 0.62rem;
  color: var(--muted);
  letter-spacing: 0.02em;
}

.cs-nav__links {
  display: none;
  align-items: center;
  gap: 1.9rem;
  margin-right: calc(var(--dark-w) * 0.55);
}

.cs-nav__link {
  color: var(--ink);
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 600;
  transition: color 0.2s ease;
}

.cs-nav__link:hover {
  color: var(--blue);
}

.cs-nav__actions {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.cs-nav__avatar {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.35rem;
  height: 2.35rem;
  border-radius: 0.65rem;
  background: var(--blue);
  color: #fff;
  transition: background 0.2s ease;
}

.cs-nav__avatar:hover {
  background: var(--blue-dark);
}

.cs-nav__burger {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.35rem;
  height: 2.35rem;
  border-radius: 0.65rem;
  border: none;
  background: transparent;
  color: #fff;
  cursor: pointer;
}

.cs-nav__mobile {
  position: absolute;
  top: 4.6rem;
  left: 0;
  right: 0;
  z-index: 40;
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
  padding: 1rem clamp(1.25rem, 4vw, 3rem) 1.5rem;
  background: var(--paper);
  border-bottom: 1px solid var(--line);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
}

.cs-nav__mobile .cs-nav__link {
  padding: 0.6rem 0;
}

.cs-fade-enter-active,
.cs-fade-leave-active {
  transition: opacity 0.18s ease;
}

.cs-fade-enter-from,
.cs-fade-leave-to {
  opacity: 0;
}

.cs-hero__vertical {
  position: absolute;
  left: 0.9rem;
  top: 50%;
  z-index: 10;
  margin: 0;
  transform: rotate(180deg) translateY(50%);
  writing-mode: vertical-rl;
  font-size: 0.6rem;
  font-weight: 700;
  letter-spacing: 0.34em;
  color: var(--blue);
  display: none;
  align-items: center;
  gap: 0.6rem;
}

.cs-hero__vertical-dot {
  width: 0.35rem;
  height: 0.35rem;
  border-radius: 999px;
  background: var(--blue);
}

.cs-hero__copy {
  position: absolute;
  z-index: 10;
  left: clamp(1.5rem, 6vw, 6rem);
  top: 50%;
  transform: translateY(-50%);
  max-width: 26rem;
}

.cs-hero__eyebrow {
  margin: 0 0 1rem;
  color: var(--blue);
  font-size: 0.72rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  font-weight: 700;
}

.cs-hero__title {
  margin: 0;
  font-size: clamp(1.9rem, 3.6vw, 2.9rem);
  font-weight: 800;
  line-height: 1.14;
  letter-spacing: -0.02em;
  color: var(--ink);
}

.cs-hero__title-blue {
  color: var(--blue);
}

.cs-hero__lead {
  margin: 1.25rem 0 0;
  color: var(--muted);
  font-size: 0.92rem;
  line-height: 1.7;
}

.cs-hero__actions {
  margin-top: 1.75rem;
}

.cs-cta {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  padding: 0.85rem 1.5rem;
  border-radius: 0.7rem;
  background: var(--blue);
  color: #fff;
  text-decoration: none;
  font-weight: 700;
  font-size: 0.88rem;
  box-shadow: 0 12px 24px rgba(37, 99, 235, 0.28);
  transition: background 0.2s ease, transform 0.2s ease;
}

.cs-cta:hover {
  background: var(--blue-dark);
  transform: translateY(-1px);
}

.cs-cta--dark {
  background: var(--ink);
  box-shadow: 0 12px 24px rgba(15, 23, 42, 0.22);
}

.cs-cta--dark:hover {
  background: var(--ink-deep);
}

.cs-hero__features {
  display: flex;
  gap: 1.15rem;
  margin-top: 2.25rem;
  flex-wrap: wrap;
}

.cs-hero__feature {
  display: flex;
  align-items: flex-start;
  gap: 0.55rem;
  max-width: 9.5rem;
}

.cs-hero__feature-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.3rem;
  height: 2.3rem;
  border-radius: 999px;
  flex-shrink: 0;
}

.cs-hero__feature-title {
  margin: 0;
  font-size: 0.78rem;
  font-weight: 700;
  color: var(--ink);
}

.cs-hero__feature-text {
  margin: 0.15rem 0 0;
  font-size: 0.66rem;
  color: var(--muted);
  line-height: 1.4;
}

/* ---------- Circle ---------- */
.cs-circle {
  position: absolute;
  z-index: 15;
  top: 50%;
  right: var(--dark-w);
  transform: translate(50%, -50%);
  width: min(30rem, 58vh);
  aspect-ratio: 1;
}

.cs-circle__ring {
  position: absolute;
  inset: -2rem;
  border-radius: 999px;
  border: 1px solid rgba(148, 163, 184, 0.5);
}

.cs-circle__orbit {
  position: absolute;
  inset: -2rem;
  border-radius: 999px;
  pointer-events: none;
  animation: cs-orbit-spin 20s linear infinite;
}

.cs-circle__ring-dot-wrap {
  position: absolute;
  inset: 0;
  transform: rotate(calc(var(--i) * 72deg));
}

.cs-circle__ring-dot {
  position: absolute;
  top: 0;
  left: 50%;
  width: 0.7rem;
  height: 0.7rem;
  border-radius: 999px;
  background: var(--blue);
  box-shadow: 0 0 0 6px rgba(37, 99, 235, 0.22);
  transform: translate(-50%, -50%);
}

@keyframes cs-orbit-spin {
  to {
    transform: rotate(-360deg);
  }
}

.cs-circle__disc {
  position: absolute;
  inset: 0;
  border-radius: 999px;
  overflow: hidden;
  display: grid;
  grid-template-columns: 1fr 1fr;
  box-shadow: 0 30px 60px rgba(15, 23, 42, 0.25);
}

.cs-circle__half-light {
  position: relative;
  background: linear-gradient(150deg, #cfe0fa 0%, #7ba0e8 55%, #3b62c4 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.cs-circle__half-dark {
  position: relative;
  background: linear-gradient(160deg, #1c2c4a 0%, #10192e 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.cs-float {
  position: absolute;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: rgba(255, 255, 255, 0.9);
  background: rgba(255, 255, 255, 0.16);
  border: 1px solid rgba(255, 255, 255, 0.3);
  border-radius: 0.7rem;
  padding: 0.45rem;
  backdrop-filter: blur(3px);
  animation: cs-drift 6s ease-in-out infinite;
}

.cs-float--clipboard {
  top: 20%;
  left: 32%;
}

.cs-float--clock {
  top: 10%;
  right: 10%;
  animation-delay: 1.2s;
}

.cs-float--cap {
  top: 30%;
  left: 60%;
  animation-delay: 2.1s;
}

@keyframes cs-drift {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(-6px);
  }
}

.cs-monitor {
  position: relative;
  z-index: 2;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 22%;
}

.cs-monitor__screen {
  width: clamp(7.5rem, 11vw, 9.5rem);
  border-radius: 0.6rem;
  background: #fff;
  padding: 0.6rem 0.65rem;
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.3);
}

.cs-monitor__q {
  margin: 0 0 0.35rem;
  font-size: 0.55rem;
  font-weight: 700;
  color: var(--ink);
}

.cs-monitor__progress {
  height: 0.22rem;
  border-radius: 999px;
  background: var(--line);
  overflow: hidden;
  margin-bottom: 0.45rem;
}

.cs-monitor__progress span {
  display: block;
  height: 100%;
  border-radius: 999px;
  background: var(--blue);
}

.cs-monitor__options {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  gap: 0.3rem;
}

.cs-monitor__options li span {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.05rem;
  height: 1.05rem;
  border-radius: 999px;
  background: var(--blue-soft);
  color: var(--blue);
  font-size: 0.5rem;
  font-weight: 800;
}

.cs-monitor__options li.is-active span {
  background: var(--blue);
  color: #fff;
}

.cs-monitor__stand {
  width: 0.5rem;
  height: 0.8rem;
  background: #dbe3f0;
}

.cs-monitor__base {
  width: 2.4rem;
  height: 0.3rem;
  border-radius: 999px;
  background: #dbe3f0;
}

.cs-books {
  position: absolute;
  bottom: 16%;
  left: 5%;
  display: flex;
  flex-direction: column-reverse;
  gap: 2px;
}

.cs-books span {
  height: 1rem;
  border-radius: 5px;
  background: rgba(255, 255, 255, 0.75);
}

.cs-books span:nth-child(1) {
  width: 12.6rem;
}

.cs-books span:nth-child(2) {
  width: 12.2rem;
  background: rgba(255, 224, 130, 0.85);
}

.cs-books span:nth-child(3) {
  width: 11.8rem;
  background: rgba(129, 199, 245, 0.9);
}

.cs-score-card {
  position: relative;
  z-index: 2;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.14);
  border-radius: 0.8rem;
  padding: 0.85rem 1rem;
  backdrop-filter: blur(4px);
}

.cs-score-card__label {
  margin: 0;
  font-size: 0.55rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.6);
  font-weight: 700;
}

.cs-score-card__value {
  margin: 0.2rem 0 0.5rem;
  font-size: 1.5rem;
  font-weight: 800;
  color: #fff;
}

.cs-score-card__value span {
  font-size: 0.7rem;
  color: rgba(255, 255, 255, 0.55);
  font-weight: 600;
}

.cs-score-card__bars {
  display: flex;
  align-items: flex-end;
  gap: 0.3rem;
  height: 2.2rem;
}

.cs-score-card__bars span {
  width: 0.5rem;
  border-radius: 3px 3px 0 0;
  background: var(--blue);
  opacity: 0.85;
}

.cs-circle__play {
  position: absolute;
  bottom: -1.4rem;
  left: 50%;
  transform: translateX(-50%);
  width: 3.2rem;
  height: 3.2rem;
  border-radius: 999px;
  background: #fff;
  color: var(--blue);
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: none;
  box-shadow: 0 14px 30px rgba(15, 23, 42, 0.25);
  cursor: pointer;
  z-index: 3;
  transition: transform 0.2s ease;
}

.cs-circle__play:hover {
  transform: translateX(-50%) scale(1.06);
}

.cs-circle__wire {
  position: absolute;
  bottom: -4.2rem;
  left: 34%;
  width: 7rem;
  height: 2.5rem;
}

/* ---------- Timeline ---------- */
.cs-timeline {
  position: absolute;
  z-index: 20;
  right: clamp(1.75rem, 3.6vw, 3.75rem);
  top: 50%;
  transform: translateY(-50%);
  list-style: none;
  margin: 0;
  padding: 0;
  display: none;
  flex-direction: column;
  gap: 0;
}

.cs-timeline li {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.45rem;
  min-width: 5.5rem;
  padding-bottom: 4rem;
}

.cs-timeline li:last-child {
  padding-bottom: 0;
}

.cs-timeline li:not(:last-child)::after {
  content: '';
  position: absolute;
  top: 3.25rem;
  bottom: 0.7rem;
  width: 2px;
  background: rgba(255, 255, 255, 0.28);
}

.cs-timeline__num {
  font-size: 1.2rem;
  line-height: 1;
  font-weight: 800;
  color: rgba(255, 255, 255, 0.55);
}

.cs-timeline li.is-first .cs-timeline__num {
  color: var(--blue);
}

.cs-timeline__label {
  font-size: 0.82rem;
  line-height: 1.2;
  color: rgba(255, 255, 255, 0.65);
  font-weight: 700;
}

.cs-timeline li.is-first .cs-timeline__label {
  color: #93c5fd;
}

/* ---------- Badges ---------- */
.cs-badge {
  position: absolute;
  z-index: 20;
  display: none;
  align-items: center;
  justify-content: center;
  width: 2.4rem;
  height: 2.4rem;
  border-radius: 0.7rem;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.2);
  color: #fff;
  backdrop-filter: blur(4px);
}

.cs-badge--chart {
  bottom: 35%;
  right: 25%;
}

.cs-badge--clock {
  bottom: 60%;
  right: 20%;
}

/* ---------- Sections (slides 2-5) ---------- */
.cs-slide--light {
  background:
    radial-gradient(circle at 8% 8%, rgba(37, 99, 235, 0.07), transparent 24%),
    radial-gradient(circle at 88% 85%, rgba(37, 99, 235, 0.05), transparent 28%),
    #fbfcff;
}

.cs-slide__inner--features {
  position: relative;
  z-index: 2;
  max-width: 68rem;
  padding-top: 2.25rem;
  padding-bottom: 1.25rem;
}

.cs-features__wash {
  position: absolute;
  inset: 0;
  pointer-events: none;
  background:
    linear-gradient(115deg, rgba(255, 255, 255, 0.85), transparent 28%),
    linear-gradient(300deg, rgba(255, 255, 255, 0.8), transparent 30%);
}

.cs-feature-float {
  position: absolute;
  z-index: 1;
  display: none;
  color: #80a9ec;
  border: 1px solid rgba(148, 176, 224, 0.3);
  background: rgba(255, 255, 255, 0.58);
  box-shadow: 0 18px 36px rgba(37, 99, 235, 0.08);
  backdrop-filter: blur(3px);
  opacity: 0.72;
}

.cs-feature-float--quiz {
  top: 12%;
  left: 4%;
  width: 7.5rem;
  height: 8.5rem;
  padding: 1rem;
  border-radius: 0.85rem;
  transform: rotate(5deg);
}

.cs-feature-float__bar {
  display: block;
  width: 75%;
  height: 0.45rem;
  margin-bottom: 0.85rem;
  border-radius: 999px;
  background: #dbe8fb;
}

.cs-feature-float__option {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 1.15rem;
  height: 1.15rem;
  margin-bottom: 0.45rem;
  border-radius: 999px;
  background: #edf4ff;
  color: #729ce0;
  font-size: 0.55rem;
  font-weight: 800;
}

.cs-feature-float__check {
  position: absolute;
  right: -0.7rem;
  bottom: -0.6rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.2rem;
  height: 2.2rem;
  border-radius: 999px;
  background: var(--blue);
  color: #fff;
  box-shadow: 0 8px 18px rgba(37, 99, 235, 0.3);
}

.cs-feature-float--chart {
  top: 12%;
  right: 4%;
  width: 7.8rem;
  height: 8.4rem;
  border-radius: 0.85rem;
  transform: rotate(-4deg);
}

.cs-feature-float__pie {
  position: absolute;
  top: 0.8rem;
  left: 1rem;
  width: 2.7rem;
  height: 2.7rem;
  color: #79a3e8;
}

.cs-feature-float__bars {
  position: absolute;
  right: 0.8rem;
  bottom: 0.8rem;
  width: 3.5rem;
  height: 3.5rem;
  color: #8ab0eb;
}

.cs-feature-float__mini {
  position: absolute;
  left: -1.1rem;
  bottom: 0.4rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 3rem;
  height: 2rem;
  border-radius: 0.45rem;
  background: rgba(255, 255, 255, 0.9);
  box-shadow: 0 8px 18px rgba(37, 99, 235, 0.12);
}

.cs-section__head {
  max-width: 38rem;
  margin: 0 auto 2.25rem;
  text-align: center;
}

.cs-section__eyebrow {
  margin: 0 0 0.65rem;
  color: var(--blue);
  font-size: 0.72rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  font-weight: 700;
}

.cs-section__eyebrow--light {
  color: #93c5fd;
}

.cs-section__title {
  margin: 0;
  font-size: clamp(1.6rem, 3.2vw, 2.2rem);
  font-weight: 800;
  letter-spacing: -0.02em;
  color: var(--ink);
}

.cs-section__title-blue {
  display: block;
  color: var(--blue);
}

.cs-section__desc {
  margin: 0.85rem 0 0;
  color: var(--muted);
  line-height: 1.65;
}

.cs-section__cta {
  display: flex;
  justify-content: center;
  margin-top: 2rem;
}

.cs-feature-grid {
  display: grid;
  gap: 0.7rem;
  grid-template-columns: 1fr;
}

.cs-feature-card {
  position: relative;
  display: flex;
  align-items: center;
  gap: 0.85rem;
  min-height: 0;
  padding: 0.85rem 0.95rem;
  border-radius: 0.9rem;
  border: 1px solid #e6ebf4;
  background: #fff;
  overflow: hidden;
  box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.cs-feature-card:hover {
  transform: none;
  box-shadow: 0 6px 16px rgba(15, 23, 42, 0.04);
}

.cs-feature-card__body {
  display: flex;
  align-items: flex-start;
  gap: 0.75rem;
  flex: 1;
  min-width: 0;
}

.cs-feature-card__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.4rem;
  height: 2.4rem;
  flex-shrink: 0;
  border-radius: 0.65rem;
  background: var(--blue-soft);
  color: var(--blue);
  margin-bottom: 0;
}

.cs-feature-card__icon-svg {
  width: 1.15rem;
  height: 1.15rem;
}

.cs-feature-card__copy {
  min-width: 0;
}

.cs-feature-card__title {
  margin: 0 0 0.2rem;
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--ink);
  line-height: 1.25;
}

.cs-feature-card__text {
  margin: 0;
  font-size: 0.75rem;
  color: var(--muted);
  line-height: 1.45;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.cs-feature-card__visual {
  display: none;
}

.cs-feature-card__visual-icon {
  width: 4.5rem;
  height: 4.5rem;
  stroke-width: 1.25;
  filter: drop-shadow(0 8px 10px rgba(37, 99, 235, 0.12));
}

.cs-feature-card__visual.is-timer .cs-feature-card__visual-icon {
  color: #4384e8;
}

.cs-feature-card__visual.is-result {
  justify-content: flex-start;
  padding-left: 1.2rem;
}

.cs-feature-card__visual.is-result .cs-feature-card__visual-icon {
  width: 3.8rem;
  height: 3.8rem;
}

.cs-feature-card__score {
  position: absolute;
  left: 2rem;
  top: 2.75rem;
  color: var(--blue);
  font-size: 0.65rem;
  font-weight: 800;
}

.cs-feature-card__chart {
  position: absolute;
  right: 1rem;
  bottom: 1.1rem;
  height: 3.4rem;
  display: flex;
  align-items: flex-end;
  gap: 0.32rem;
}

.cs-feature-card__chart span {
  width: 0.55rem;
  border-radius: 0.2rem 0.2rem 0 0;
  background: linear-gradient(#73a4ec, #2563eb);
}

.cs-feature-card__visual.is-gift {
  color: #5f91df;
}

.cs-feature-card__check {
  position: absolute;
  right: 1.3rem;
  bottom: 1rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2rem;
  height: 2rem;
  border-radius: 999px;
  color: #fff;
  background: var(--blue);
  box-shadow: 0 7px 14px rgba(37, 99, 235, 0.26);
}

.cs-trust-pills {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.75rem;
  margin-top: 1.25rem;
}

.cs-trust-pills span {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.5rem 0.9rem;
  border: 1px solid #edf0f5;
  border-radius: 999px;
  background: rgba(255, 255, 255, 0.78);
  color: #7a879b;
  font-size: 0.68rem;
  box-shadow: 0 6px 18px rgba(15, 23, 42, 0.035);
}

.cs-trust-pills svg {
  color: #4384e8;
}

.cs-slide__inner--simulation {
  position: relative;
  z-index: 2;
  max-width: 68rem;
  padding-top: 2.25rem;
  padding-bottom: 1.25rem;
}

.cs-simulation__wash {
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 8% 75%, rgba(124, 58, 237, 0.07), transparent 24%),
    radial-gradient(circle at 92% 15%, rgba(37, 99, 235, 0.08), transparent 25%),
    linear-gradient(180deg, #f9fbff, #f4f7fd);
  pointer-events: none;
}

.cs-sim-float {
  position: absolute;
  z-index: 1;
  display: none;
  align-items: center;
  justify-content: center;
  color: #7da5e8;
  border: 1px solid rgba(148, 176, 224, 0.3);
  background: rgba(255, 255, 255, 0.6);
  box-shadow: 0 18px 36px rgba(37, 99, 235, 0.08);
  backdrop-filter: blur(3px);
  opacity: 0.72;
}

.cs-sim-float--language {
  top: 12%;
  left: 4%;
  width: 7.4rem;
  height: 7.4rem;
  border-radius: 1.2rem;
  transform: rotate(-5deg);
}

.cs-sim-float--language span {
  position: absolute;
  font-weight: 800;
}

.cs-sim-float--language span:nth-of-type(1) {
  right: 1rem;
  top: 0.8rem;
  color: #7c3aed;
}

.cs-sim-float--language span:nth-of-type(2) {
  left: 1rem;
  bottom: 0.8rem;
  color: #0d9488;
}

.cs-sim-float--score {
  top: 12%;
  right: 4%;
  width: 7.2rem;
  height: 8rem;
  border-radius: 1.2rem;
  flex-direction: column;
  transform: rotate(4deg);
}

.cs-sim-float__main-icon {
  width: 3rem;
  height: 3rem;
  stroke-width: 1.35;
}

.cs-sim-float--score strong {
  margin-top: 0.25rem;
  color: #2563eb;
  font-size: 1.2rem;
}

.cs-sim-float--score small {
  font-size: 0.52rem;
  letter-spacing: 0.14em;
  font-weight: 800;
}

.cs-exam-grid {
  display: grid;
  gap: 1rem;
  grid-template-columns: 1fr;
}

.cs-exam-card {
  position: relative;
  min-height: 18.5rem;
  padding: 1.15rem 1.15rem 9.4rem;
  border-radius: 1.1rem;
  background: #fff;
  border: 1px solid #e6ebf4;
  overflow: hidden;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.055);
  transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}

.cs-exam-card:hover {
  border-color: #b8cdf3;
  transform: translateY(-3px);
  box-shadow: 0 16px 30px rgba(15, 23, 42, 0.085);
}

.cs-exam-card__top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
}

.cs-exam-card__icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.25rem;
  height: 2.25rem;
  border-radius: 0.65rem;
}

.cs-exam-card__tag {
  display: inline-block;
  color: #8591a4;
  font-size: 0.6rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  font-weight: 700;
}

.cs-exam-card__name {
  margin: 0.55rem 0 0;
  font-size: 1.08rem;
  font-weight: 700;
  color: var(--ink);
}

.cs-exam-card__blurb {
  margin: 0.5rem 0 0;
  font-size: 0.82rem;
  color: var(--muted);
  line-height: 1.55;
}

.cs-exam-card__visual {
  position: absolute;
  left: 0.75rem;
  right: 0.75rem;
  bottom: 2.7rem;
  height: 6.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 1rem;
  background: linear-gradient(145deg, var(--exam-soft), rgba(255, 255, 255, 0.5));
  color: var(--exam-color);
}

.cs-exam-card__visual-icon {
  position: relative;
  z-index: 2;
  width: 3.8rem;
  height: 3.8rem;
  stroke-width: 1.25;
  filter: drop-shadow(0 7px 10px rgba(15, 23, 42, 0.1));
}

.cs-exam-card__sheet {
  position: absolute;
  left: 1rem;
  bottom: 1rem;
  width: 2.8rem;
  height: 3.5rem;
  padding: 0.65rem 0.5rem;
  border-radius: 0.4rem;
  background: rgba(255, 255, 255, 0.8);
  transform: rotate(-8deg);
  box-shadow: 0 7px 14px rgba(15, 23, 42, 0.08);
}

.cs-exam-card__sheet span {
  display: block;
  height: 0.22rem;
  margin-bottom: 0.38rem;
  border-radius: 999px;
  background: color-mix(in srgb, var(--exam-color) 38%, white);
}

.cs-exam-card__code {
  position: absolute;
  right: 0.8rem;
  top: 0.6rem;
  color: var(--exam-color);
  font-size: 0.75rem;
  font-weight: 800;
  opacity: 0.6;
}

.cs-exam-card__action {
  position: absolute;
  left: 1.15rem;
  right: 1.15rem;
  bottom: 0.8rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  color: #4b5d78;
  text-decoration: none;
  font-size: 0.72rem;
  font-weight: 700;
}

.cs-exam-card__action:hover {
  color: var(--blue);
}

.cs-simulation__footer {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
  margin-top: 1.2rem;
}

.cs-simulation__footer span,
.cs-simulation__footer a {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.48rem 0.85rem;
  border-radius: 999px;
  color: #7a879b;
  font-size: 0.68rem;
  text-decoration: none;
}

.cs-simulation__footer span {
  border: 1px solid #e7ebf2;
  background: rgba(255, 255, 255, 0.75);
}

.cs-simulation__footer svg {
  color: #4384e8;
}

.cs-simulation__footer a {
  color: var(--blue);
  font-weight: 800;
}

.cs-steps {
  list-style: none;
  margin: 0 auto;
  padding: 0;
  display: grid;
  gap: 0.75rem;
  max-width: 40rem;
  width: 100%;
}

.cs-step {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 1rem;
  align-items: start;
  padding: 0.85rem 0;
  border-bottom: 1px solid var(--line);
}

.cs-step__num {
  color: var(--blue);
  font-weight: 800;
  font-size: 1rem;
}

.cs-step__title {
  margin: 0;
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--ink);
}

.cs-step__text {
  margin: 0.3rem 0 0;
  color: var(--muted);
  font-size: 0.83rem;
  line-height: 1.55;
}

.cs-about__stats {
  margin: 2rem auto 0;
  display: grid;
  gap: 1.25rem;
  grid-template-columns: repeat(3, 1fr);
  max-width: 40rem;
  width: 100%;
  text-align: center;
}

.cs-about__stat-value {
  margin: 0;
  font-size: 1.7rem;
  font-weight: 800;
  color: var(--blue);
}

.cs-about__stat-label {
  margin: 0.3rem 0 0;
  font-size: 0.76rem;
  color: var(--muted);
}

/* ---------- Kontak slide ---------- */
.cs-feedback__title {
  margin: 0;
  font-size: clamp(1.6rem, 3.4vw, 2.3rem);
  font-weight: 800;
  color: #fff;
}

.cs-feedback__text {
  margin: 1rem auto 0;
  max-width: 34rem;
  color: rgba(255, 255, 255, 0.72);
  line-height: 1.7;
}

.cs-feedback__actions {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 0.85rem;
  margin-top: 1.75rem;
}

.cs-feedback__secondary {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.85rem 1.5rem;
  border-radius: 0.7rem;
  border: 1px solid rgba(255, 255, 255, 0.25);
  color: #fff;
  text-decoration: none;
  font-weight: 700;
  font-size: 0.88rem;
  transition: border-color 0.2s ease, background 0.2s ease;
}

.cs-feedback__secondary:hover {
  border-color: var(--blue);
  background: rgba(37, 99, 235, 0.16);
}

.cs-footer {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 0.85rem 2rem;
  margin-top: 3.5rem;
  padding-top: 1.75rem;
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  width: 100%;
}

.cs-footer__brand {
  gap: 0.5rem;
}

.cs-footer__note {
  margin: 0;
  color: rgba(255, 255, 255, 0.5);
  font-size: 0.75rem;
}

.cs-footer__links {
  display: flex;
  flex-wrap: wrap;
  gap: 1rem 1.25rem;
}

.cs-footer__links a {
  color: rgba(255, 255, 255, 0.65);
  text-decoration: none;
  font-size: 0.78rem;
}

.cs-footer__links a:hover {
  color: #93c5fd;
}

/* ---------- Responsive ---------- */
@media (min-width: 768px) {
  .cs-feature-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
  }

  .cs-feature-card {
    display: block;
    min-height: 17rem;
    padding: 1.15rem 1.15rem 0;
    border-radius: 1.1rem;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.055);
  }

  .cs-feature-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 30px rgba(15, 23, 42, 0.08);
  }

  .cs-feature-card__body {
    display: block;
  }

  .cs-feature-card__icon {
    width: 2.1rem;
    height: 2.1rem;
    margin-bottom: 0.75rem;
  }

  .cs-feature-card__icon-svg {
    width: 1.05rem;
    height: 1.05rem;
  }

  .cs-feature-card__title {
    margin: 0 0 0.45rem;
    font-size: 1rem;
  }

  .cs-feature-card__text {
    font-size: 0.83rem;
    line-height: 1.6;
    display: block;
    -webkit-line-clamp: unset;
    line-clamp: unset;
    overflow: visible;
  }

  .cs-feature-card__visual {
    position: absolute;
    left: 0.75rem;
    right: 0.75rem;
    bottom: 0;
    height: 7rem;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 1rem 1rem 0 0;
    background: linear-gradient(180deg, rgba(234, 241, 253, 0.25), rgba(234, 241, 253, 0.8));
    color: #78a3e8;
  }

  .cs-exam-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (min-width: 1024px) {
  .cs-nav__links {
    display: flex;
  }

  .cs-hero__vertical {
    display: inline-flex;
  }

  .cs-timeline {
    display: flex;
  }

  .cs-badge {
    display: inline-flex;
  }

  .cs-feature-float {
    display: block;
  }

  .cs-sim-float {
    display: flex;
  }

  .cs-feature-grid {
    grid-template-columns: repeat(4, 1fr);
  }

  .cs-exam-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

/* Mobile: hero becomes stacked, snap relaxed */
@media (max-width: 1023px) {
  .cs-viewport {
    scroll-snap-type: y proximity;
  }

  .cs-slide {
    height: auto;
    min-height: 100vh;
    min-height: 100dvh;
    overflow: visible;
  }

  .cs-slide--hero {
    display: flex;
    flex-direction: column;
    padding-bottom: 3rem;
  }

  .cs-hero__dark {
    top: auto;
    left: 0;
    right: 0;
    bottom: 0;
    width: 100%;
    height: clamp(16rem, 42vh, 24rem);
  }

  .cs-nav {
    position: relative;
    background: var(--paper);
  }

  .cs-nav__burger {
    color: var(--ink);
    border: 1px solid var(--line);
  }

  .cs-hero__copy {
    position: relative;
    left: auto;
    top: auto;
    transform: none;
    padding: 1.5rem clamp(1.25rem, 5vw, 2.5rem) 0;
    max-width: 32rem;
  }

  .cs-circle {
    position: relative;
    top: auto;
    right: auto;
    transform: none;
    margin: 3.5rem auto 0;
    width: min(20rem, 78vw);
  }

  .cs-circle__wire {
    display: none;
  }
}
</style>
