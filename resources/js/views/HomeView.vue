<template>
  <main class="min-h-screen text-[#1A1A1A]">
    <div
      v-if="!isAuthenticated"
      class="fixed top-12 left-[70.5%] md:flex hidden -translate-x-1/2 sm:w-[calc(100%-1rem)] md:w-[590px] z-30 rounded-xl bg-white/95 backdrop-blur border border-gray-100 shadow-xl shadow-black/10 p-2 sm:p-3"
    >
      <div class="flex flex-wrap items-center justify-center gap-1 w-full">
        <button
          v-for="item in quickNavItems"
          :key="item.id"
          type="button"
          class="text-xs sm:text-sm px-2.5 cursor-pointer py-2 rounded-lg hover:bg-[#f5f8ec] hover:text-[#4f6123] transition-colors whitespace-nowrap"
          @click="scrollToSection(item.id)"
        >
          {{ item.label }}
        </button>
      </div>
    </div>

    <section id="hero" class="relative overflow-hidden px-5 py-10 md:px-10 rounded-none md:rounded-2xl">
      <div class="absolute top-0 right-0 w-[360px] sm:w-[240px] md:w-[320px] lg:w-[390px] xl:w-[720px] pointer-events-none z-0 banner-wrap">
        <img :src="bannerUrl" alt="Banner Akademi Polisi" class="w-full h-auto banner-image rounded-none md:rounded-2xl" />
      </div>

      <div class="relative max-w-6xl mx-auto grid lg:grid-cols-2 gap-10 items-end">
        <div class="fade-up">
          <p class="text-sm uppercase tracking-[0.2em] text-[#6c7c3f] font-semibold mb-4">Bimbel Online Akademi Polisi</p>
          <h1 class="text-2xl md:text-6xl font-extrabold leading-tight tracking-tight text-[#111111]">
            <span class="inline">
              <span class="sm:hidden">
                <div class="rounded-xl bg-white/75 px-2 py-1 shadow-sm sm:bg-transparent sm:p-0">Bimbingan Belajar Online</div>
                <div class="rounded-xl mt-1 bg-white/75 px-2 py-1 shadow-sm sm:bg-transparent sm:p-0">dan Persiapan Akademi Polisi</div>
                <div class="rounded-xl mt-1 bg-white/75 px-2 py-1 shadow-sm sm:bg-transparent sm:p-0">Terstruktur, Profesional,</div>
                <div class="rounded-xl mt-1 bg-white/75 px-2 py-1 shadow-sm sm:bg-transparent sm:p-0">dan Terukur.</div>
              </span>
              <span class="hidden sm:inline">
                Bimbingan Belajar Online Akademi Polisi Terstruktur, Profesional, dan Terukur.
              </span>
            </span>
          </h1>
          <p class="mt-6 text-gray-600 text-lg leading-relaxed max-w-xl">
            Program pembinaan terpadu untuk calon peserta Akademi Kepolisian: kelas bimbingan, simulasi CBT, evaluasi berkala, monitoring onboarding, dan manajemen kelas yang rapi untuk tim pembina.
          </p>
          <div class="mt-8 flex flex-wrap gap-3">
            <router-link to="/signup" class="px-6 py-3 rounded-full bg-[#9DB359] text-white font-semibold shadow-lg shadow-[#9DB359]/20 hover:bg-[#8da74c] transition-all">
              Daftar Sebagai Peserta
            </router-link>
            <router-link to="/login" class="px-6 py-3 rounded-full bg-white border border-gray-200 text-[#1A1A1A] font-semibold hover:bg-gray-50 transition-all">
              Masuk ke Platform
            </router-link>
          </div>
        </div>

        <div class="fade-up delay-1">
          <div class="rounded-[2rem] bg-white/80 backdrop-blur border border-gray-100 shadow-2xl shadow-black/10 p-7 md:p-8">
            <h3 class="text-lg font-bold mb-4">Mengapa platform ini efektif?</h3>
            <div class="space-y-3 text-sm text-gray-700">
              <div class="rounded-xl bg-[#f6f9ef] border border-[#dfe8c6] px-4 py-3">Monitoring progres onboarding peserta dari administrasi sampai psikologi.</div>
              <div class="rounded-xl bg-[#f6f9ef] border border-[#dfe8c6] px-4 py-3">Kelas bimbingan terpusat: materi, kuis, dan ujian dalam satu workspace.</div>
              <div class="rounded-xl bg-[#f6f9ef] border border-[#dfe8c6] px-4 py-3">Kontrol akses berbasis role untuk admin, mentor, dan peserta.</div>
              <div class="rounded-xl bg-[#f6f9ef] border border-[#dfe8c6] px-4 py-3">Pelaporan hasil tes terukur untuk evaluasi pembinaan berkelanjutan.</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="services" class="px-5 md:px-10 pb-8">
      <div class="max-w-6xl mx-auto fade-up delay-1 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Layanan Pembinaan</h2>
          <p class="text-gray-600 mb-8">Program seleksi dan pendampingan untuk kesiapan calon peserta.</p>
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article
              v-for="service in services"
              :key="service.title"
              class="rounded-2xl border border-gray-100 bg-[#fcfdfb] p-5 hover:-translate-y-1 hover:shadow-lg transition-all"
            >
              <div class="mb-3 inline-flex rounded-lg bg-[#f3f8e7] p-2 text-[#6c7c3f]">
                <component :is="service.icon" class="h-5 w-5" />
              </div>
              <h3 class="font-semibold text-[#1A1A1A]">{{ service.title }}</h3>
              <p class="text-sm text-gray-600 mt-1">{{ service.desc }}</p>
            </article>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute w-28 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section id="programs" class="px-5 md:px-10 pb-8">
      <div class="max-w-6xl mx-auto fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Keunggulan Program</h2>
          <p class="text-gray-600 mb-8">Ekosistem belajar yang didesain untuk disiplin, konsisten, dan terukur.</p>
          <div class="grid md:grid-cols-2 gap-5 relative z-10">
            <article
              v-for="feature in keyFeatures"
              :key="feature.title"
              class="rounded-2xl border border-gray-100 bg-gradient-to-br from-white to-[#f7faef] p-6"
            >
              <div class="mb-3 inline-flex rounded-lg bg-[#f3f8e7] p-2 text-[#6c7c3f]">
                <component :is="feature.icon" class="h-5 w-5" />
              </div>
              <h3 class="font-bold text-lg">{{ feature.title }}</h3>
              <p class="text-gray-600 text-sm mt-2 leading-relaxed">{{ feature.desc }}</p>
            </article>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-0 w-28 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section id="choices" class="px-5 md:px-10 pb-8 relative">
      <div class="max-w-6xl mx-auto fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Pilihan Bimbel Online</h2>
          <p class="text-gray-600 mb-8">Pilih jalur program sesuai kebutuhan pembinaan dan target persiapan.</p>
          <div class="grid md:grid-cols-2 gap-5 relative z-30">
            <article
              v-for="program in onlinePrograms"
              :key="program.name"
              class="rounded-2xl border border-gray-100 p-6 hover:shadow-lg transition-all"
            >
              <div class="flex items-center justify-between gap-3">
                <h3 class="font-bold text-lg">{{ program.name }}</h3>
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded-full border font-semibold" :class="programBadge(program).className">
                  <Crown v-if="programBadge(program).isVip" class="h-3.5 w-3.5" />
                  {{ programBadge(program).label }}
                </span>
              </div>
              <p class="text-gray-600 text-sm mt-2">{{ program.summary }}</p>
              <ul class="mt-3 text-sm text-gray-600 list-disc pl-5 space-y-1">
                <li v-for="point in program.points" :key="point">{{ point }}</li>
              </ul>
            </article>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-10 w-28 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section id="leaders" class="px-5 md:px-10 pb-8">
      <div class="max-w-6xl mx-auto fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Dewan Pembina & Pimpinan</h2>
          <p class="text-gray-600 mb-8">Profil pejabat dan pimpinan PT. Pratistha Training Center Indonesia.</p>

          <div class="grid lg:grid-cols-3 gap-6 relative z-30">
            <article v-for="leader in leaders" :key="leader.name" class="rounded-2xl border border-gray-100 bg-[#fcfdfb] overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
              <img :src="leader.image" :alt="leader.name" class="w-full h-72 object-cover object-top" />
              <div class="p-5">
                <h3 class="font-bold text-lg leading-tight">{{ leader.name }}</h3>
                <p class="text-sm text-[#6c7c3f] font-semibold mt-1">{{ leader.batch }}</p>
                <p class="mt-2 text-sm font-medium text-[#1A1A1A]">{{ leader.position }}</p>
                <p class="mt-2 text-sm font-medium text-[#1A1A1A]">Jabatan Terakhir</p>
                <ul class="mt-3 space-y-1.5 text-sm text-gray-600 list-disc pl-4">
                  <li v-for="line in leader.highlights" :key="line">{{ line }}</li>
                </ul>
              </div>
            </article>
          </div>
          <div class="mt-10 border-t border-gray-100 pt-8 relative z-30">
            <h3 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Anggota</h3>
            <p class="text-gray-600 mb-6">Tim anggota pendukung program pembinaan PT. Pratistha Training Center Indonesia.</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
              <article
                v-for="member in members"
                :key="member.name"
                class="rounded-2xl border border-gray-100 bg-[#fcfdfb] overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
              >
                <img :src="member.image" :alt="member.name" class="w-full h-72 object-cover object-top" />
                <div class="p-5">
                  <h4 class="font-semibold text-base leading-snug text-[#1A1A1A]">{{ member.name }}</h4>
                  <p class="text-sm text-[#6c7c3f] font-semibold mt-1">{{ member.jabatan }}</p>
                </div>
              </article>
            </div>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-10 w-72 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section class="px-5 md:px-10 pb-14">
      <div class="max-w-6xl mx-auto rounded-[2rem] bg-[#1A1A1A] text-white p-8 md:p-10 shadow-2xl shadow-black/20 fade-up delay-3">
        <h2 class="text-2xl md:text-3xl font-bold mb-3">Siap naik level untuk persiapan Akademi Polisi?</h2>
        <p class="text-white/80 max-w-3xl">
          Bergabung sebagai peserta, lengkapi onboarding secara bertahap, lalu ikuti program kelas bimbingan online dengan standar pembinaan profesional.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <router-link to="/signup" class="px-6 py-3 rounded-full bg-[#9DB359] text-white font-semibold hover:bg-[#8da74c] transition-all">Mulai Pendaftaran</router-link>
          <router-link to="/registration" class="px-6 py-3 rounded-full bg-white/10 text-white font-semibold border border-white/20 hover:bg-white/20 transition-all">Lihat Tahapan Onboarding</router-link>
        </div>
      </div>
    </section>

    <a
      href="https://wa.me/6285124156748"
      target="_blank"
      rel="noopener noreferrer"
      class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-xl shadow-[#25D366]/30 transition hover:scale-105 hover:bg-[#1ebe5d]"
      aria-label="Chat WhatsApp"
      title="Chat WhatsApp"
    >
      <MessageCircle class="h-7 w-7" />
    </a>
  </main>
</template>

<script setup>
import { onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { storeToRefs } from 'pinia'
import { BookOpenText, Brain, Crown, Dumbbell, LineChart, MessageCircle, NotebookPen, ShieldCheck, UserCheck, Warehouse } from 'lucide-vue-next'

const route = useRoute()
const store = useAppStore()
const { isAuthenticated } = storeToRefs(store)
const nanaUrl = new URL('../../assets/bpk_nana.jpeg', import.meta.url).href
const tubagusUrl = new URL('../../assets/bpk_tubagus.png', import.meta.url).href
const awangUrl = new URL('../../assets/bpk_awang.png', import.meta.url).href
const gilangUrl = new URL('../../assets/anggota/gilang.jpeg', import.meta.url).href
const winUrl = new URL('../../assets/anggota/win.jpeg', import.meta.url).href
const rinaUrl = new URL('../../assets/anggota/rina.jpeg', import.meta.url).href
const natashaUrl = new URL('../../assets/anggota/natasha.jpeg', import.meta.url).href
const tutikUrl = new URL('../../assets/anggota/tutik.jpeg', import.meta.url).href
const bannerUrl = new URL('../../assets/Banner.png', import.meta.url).href
const patternUrl = new URL('../../assets/Pattern.svg', import.meta.url).href
const quickNavItems = [
  { id: 'services', label: 'Layanan Pembinaan' },
  { id: 'programs', label: 'Keunggulan Program' },
  { id: 'choices', label: 'Pilihan Bimbel' },
  { id: 'leaders', label: 'Dewan Pimpinan' },
]

const leaders = [
  {
    name: 'Komjen Pol (P) Drs. H. Nana S. Permana',
    batch: 'Batalion Dharma Angkatan 1968',
    position: 'Ketua Pembina Yayasan Tribakti Yayasan Langlang Buana',
    highlights: [
      'Wakapolri',
      'Pembina strategis pendidikan dan pembinaan kepolisian',
    ],
    image: nanaUrl,
  },
  {
    name: 'Irjen Pol (P) Dr. H Tubagus Anis Angkawijaya, Drs., M.Si',
    batch: 'Bataliyon Anindhita Tahun 1981',
    position: 'Komisaris PT. Pratistha Training Center Indonesia',
    highlights: [
      'Kapolda Sultra',
      'Kapolda Jabar',
      'Ketua Persatuan Purnawirawan Daerah Jabar',
      'Wakil Ketua Pembina Yayasan Pendidikan Tribakti Langlang Buana',
    ],
    image: tubagusUrl,
  },
  {
    name: 'Brigjen Pol (P) Drs. H. Awang Anwarudin, MH',
    batch: 'Bataliyon Pratistha Angkatan 1982',
    position: 'Direktur Utama PT. Pratistha Training Center Indonesia',
    highlights: [
      'Wakapolda Jawa Tengah',
      'Pengarah operasional program pembinaan dan pelatihan',
    ],
    image: awangUrl,
  },
]

const members = [
  { name: 'Gilang Nurfahradz Syahni Fasya, S.T', image: gilangUrl, jabatan: 'Direktur' },
  { name: 'Win Tasajat, S.Pd.I., M.Si', image: winUrl, jabatan: 'Sekretaris' },
  { name: 'KBP (P) Dra.Rina Regina', image: rinaUrl, jabatan: 'Bendahara' },
  { name: 'AKBP (P) Dra.NATASHA YUNITA POSPOS, S.H. M.T.C.P', image: natashaUrl, jabatan: 'Bidang Internal' },
  { name: 'Kompol (P) Tutik', image: tutikUrl, jabatan: 'Bidang Eksternal' },
]

const services = [
  { icon: Brain, title: 'Tes Psikologi', desc: 'Pemetaan karakter, kestabilan emosi, dan kesiapan menghadapi seleksi.' },
  { icon: BookOpenText, title: 'Tes Akademik', desc: 'Latihan soal akademik terstruktur dengan simulasi CBT berkala.' },
  { icon: Dumbbell, title: 'Tes Fisik', desc: 'Panduan pembinaan fisik sesuai standar seleksi kepolisian.' },
  { icon: ShieldCheck, title: 'Mental & Ideologi', desc: 'Penguatan mental, disiplin, wawasan kebangsaan, dan integritas.' },
  { icon: NotebookPen, title: 'Materi Pembelajaran', desc: 'Modul belajar, bank soal, dan pembahasan eksklusif per kelas.' },
]

const keyFeatures = [
  {
    icon: UserCheck,
    title: 'Pengajar dari Ahli & Praktisi',
    desc: 'Tim pembina berpengalaman dari unsur purnawirawan, mentor akademik, dan pelatih kesiapan seleksi.',
  },
  {
    icon: BookOpenText,
    title: 'Materi Terbaru dan Eksklusif',
    desc: 'Materi disusun berkala, menyesuaikan pola seleksi terbaru, dan hanya dapat diakses peserta terdaftar.',
  },
  {
    icon: LineChart,
    title: 'Laporan ke Orang Tua Secara Online',
    desc: 'Perkembangan hasil latihan, nilai tes, dan progres onboarding peserta dapat dipantau secara digital.',
  },
  {
    icon: Warehouse,
    title: 'Tempat Belajar Eksklusif',
    desc: 'Ruang belajar terarah dengan sistem kelas, jadwal, dan evaluasi untuk menjaga fokus pembinaan.',
  },
]

const onlinePrograms = [
  {
    name: 'VIP Class (Online + Offline + Karantina)',
    mode: 'Premium',
    summary: 'Pendampingan intensif dengan kontrol progres harian dan strategi personal.',
    points: ['Mentoring prioritas', 'Monitoring ketat', 'Pendalaman psikologi & akademik'],
  },
  {
    name: 'Regular Class Online',
    mode: 'Reguler',
    summary: 'Program online fleksibel dengan kurikulum inti untuk persiapan seleksi.',
    points: ['Kelas terjadwal', 'Latihan CBT periodik', 'Diskusi materi dan evaluasi'],
  },
  {
    name: 'Program Bimbingan Online',
    mode: 'Full Online',
    summary: 'Fokus pada penguatan kompetensi akademik, psikologi, dan latihan soal.',
    points: ['Akses materi kelas', 'Kuis dan tugas', 'Rekap progres pembelajaran'],
  },
  {
    name: 'Try Out Class',
    mode: 'Ujian',
    summary: 'Program simulasi ujian dengan pembahasan jawaban untuk pemetaan kemampuan.',
    points: ['Simulasi tes', 'Analisis skor', 'Pembahasan hasil'],
  },
]

const programBadge = (program) => {
  if (program.name.includes('VIP')) {
    return {
      label: program.mode,
      isVip: true,
      className: 'bg-amber-100 text-amber-800 border-amber-300',
    }
  }

  if (program.name.includes('Regular')) {
    return {
      label: program.mode,
      isVip: false,
      className: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    }
  }

  if (program.name.includes('Bimbingan')) {
    return {
      label: program.mode,
      isVip: false,
      className: 'bg-slate-100 text-slate-700 border-slate-300',
    }
  }

  return {
    label: program.mode,
    isVip: false,
    className: 'bg-orange-100 text-orange-800 border-orange-300',
  }
}

const scrollToHash = () => {
  const id = String(route.hash || '').replace('#', '')
  if (!id) return
  const target = document.getElementById(id)
  if (target) {
    setTimeout(() => target.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80)
  }
}

const scrollToSection = (id) => {
  const target = document.getElementById(id)
  if (target) {
    target.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

watch(() => route.hash, scrollToHash)
onMounted(scrollToHash)
</script>

<style scoped>
.fade-up {
  opacity: 0;
  transform: translateY(20px);
  animation: fadeUp 0.8s ease forwards;
}

.delay-1 { animation-delay: 0.12s; }
.delay-2 { animation-delay: 0.22s; }
.delay-3 { animation-delay: 0.32s; }

@keyframes fadeUp {
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.floating-orb {
  animation: floatOrb 7s ease-in-out infinite;
}

@keyframes floatOrb {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(18px); }
}

.banner-wrap {
  opacity: 0.9;
  overflow: hidden;
  isolation: isolate;
}

.banner-image {
  display: block;
  transform: scale(1.03);
  transform-origin: top right;
  -webkit-mask-image: radial-gradient(ellipse 82% 74% at 72% 26%, rgba(0, 0, 0, 1) 44%, rgba(0, 0, 0, 0.86) 61%, rgba(0, 0, 0, 0) 100%);
  mask-image: radial-gradient(ellipse 65% 67% at 72% 26%, rgba(0, 0, 0, 1) 40%, rgba(0, 0, 0, 0.86) 61%, rgba(0, 0, 0, 0) 100%);
  -webkit-mask-size: 100% 100%;
  mask-size: 100% 100%;
  -webkit-mask-repeat: no-repeat;
  mask-repeat: no-repeat;
}
</style>
