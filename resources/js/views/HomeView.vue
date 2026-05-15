<template>
  <main class="min-h-screen text-text">
    <div v-if="!isAuthenticated" class="fixed top-4 inset-x-0 z-40 px-4 md:px-10">
      <div class="mx-auto max-w-7xl rounded-3xl md:rounded-full bg-white/95 backdrop-blur border border-border shadow-xl shadow-[#123B8F]/10 px-4 md:px-6 py-2 md:py-0">
        <div class="hidden md:grid h-16 grid-cols-[1fr_auto_1fr] items-center gap-4">
          <div class="flex items-center gap-1">
            <button
              v-for="item in quickNavItems"
              :key="item.id"
              type="button"
              class="text-[11px] lg:text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors whitespace-nowrap"
              @click="scrollToSection(item.id)"
            >
              {{ item.label }}
            </button>
          </div>

          <router-link
            to="/about-us"
            class="text-[11px] lg:text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors whitespace-nowrap"
          >
            Tentang Kami
          </router-link>

          <div class="flex items-center justify-end gap-3">
            <router-link
              to="/login"
              class="px-4 py-2 rounded-full bg-primary text-white text-[11px] lg:text-xs font-bold uppercase tracking-wide hover:bg-secondary transition-colors whitespace-nowrap"
            >
              Masuk Ke Platform
            </router-link>
          </div>
        </div>

        <div class="md:hidden">
          <div class="h-12 flex items-center justify-between gap-3">
            <router-link
              to="/about-us"
              class="text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors"
            >
              Tentang Kami
            </router-link>
            <button
              type="button"
              class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-border text-text hover:bg-sky transition-colors"
              aria-label="Toggle menu"
              @click="isMobileMenuOpen = !isMobileMenuOpen"
            >
              <span class="text-xl leading-none">{{ isMobileMenuOpen ? 'x' : '=' }}</span>
            </button>
          </div>

          <div v-if="isMobileMenuOpen" class="pb-3 pt-2 border-t border-border">
            <div class="grid gap-1">
              <button
                v-for="item in quickNavItems"
                :key="`mobile-${item.id}`"
                type="button"
                class="text-left text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors"
                @click="scrollToSection(item.id)"
              >
                {{ item.label }}
              </button>
              <router-link
                to="/signup"
                class="mt-2 px-4 py-2 rounded-md bg-primary text-white text-center text-xs font-bold uppercase tracking-wide hover:bg-secondary transition-colors"
                @click="isMobileMenuOpen = false"
              >
                Masuk Ke Platform
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section id="hero" class="relative overflow-hidden rounded-none md:rounded-2xl min-h-[560px] md:min-h-[700px]">
      <img
        :src="bannerUrl"
        :alt="heroSelayangPandang.bannerAlt"
        class="absolute inset-0 h-full w-full object-cover object-center rounded-none md:rounded-2xl hero-banner-fade"
      />
      <div class="absolute inset-0 rounded-none md:rounded-2xl bg-gradient-to-b from-white/88 via-white/60 to-black/40" />

      <div class="relative z-10 mx-auto min-h-[560px] md:min-h-[700px] flex items-start md:items-center justify-center px-5 pt-32 md:pt-32 pb-14 md:px-10">
        <div class="text-center max-w-4xl fade-up">
          <p class="text-[11px] md:text-4xl font-bold uppercase tracking-[0.2em] text-primary mb-3">
            Pratistha Training Center
          </p>
          <h1 class="text-2xl md:text-5xl lg:text-6xl font-black leading-[1.05] tracking-tight text-text uppercase">
            {{ heroSelayangPandang.title }}
          </h1>
          <div class="bg-white/40 mt-5 rounded-xl p-4 shadow-xl">
            <p class="text-base md:text-lg text-gray-700 max-w-3xl mx-auto leading-relaxed font-bold">
              {{ heroSelayangPandang.description }}
            </p>
            <div class="mt-6 flex flex-wrap font-semibold items-center justify-center gap-2 text-gray-700">
              Alamat kantor Jl. Sukamaju no. 142, Cipadung Kulon, Kec. Panyileukan, Kota Bandung - Jabar 40614
            </div>
          </div>
          <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <router-link to="/free-tryout" class="px-7 py-3 rounded-full bg-secondary text-white text-sm md:text-base font-bold shadow-lg shadow-[#2F6BFF]/25 hover:bg-primary transition-all">
              Coba Tryout Gratis
            </router-link>
            <router-link
              type="button"
              class="px-7 py-3 rounded-full cursor-pointer border-2 border-primary text-primary text-sm md:text-base font-bold bg-white/80 hover:bg-sky transition-all"
              to="/signup"
            >
              Daftar Peserta Bimbel
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <section id="selayang-pandang-2" class="px-5 md:px-10 py-10">
      <div class="max-w-7xl mx-auto fade-up delay-1 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10 relative overflow-hidden">
          <div class="relative z-30">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary mb-3">Selayang Pandang</p>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-text leading-tight">
              Fondasi Terarah untuk Melangkah Pasti Menuju Akpol
            </h2>
            <div class="mt-6 space-y-4 font-semibold text-gray-700 leading-relaxed">
              <p>
                Selamat datang di <span class="font-bold text-text">Pratistha Training Center</span>, tempat lahirnya calon taruna terbaik yang dipersiapkan secara terarah, disiplin, dan profesional untuk menghadapi seleksi Akademi Kepolisian.
              </p>
              <p>
                Kami berfokus pada pembinaan akademik, mental, fisik, dan karakter kepemimpinan. Dalam persaingan seleksi yang semakin ketat, kami meyakini keberhasilan tidak hanya ditentukan oleh kecerdasan, tetapi juga oleh strategi belajar yang tepat dan pembinaan yang konsisten.
              </p>
              <p>
                Melalui metode pelatihan yang terstruktur, simulasi seleksi yang realistis, serta pendampingan mentor berpengalaman, setiap peserta dibimbing agar siap menghadapi seluruh tahapan seleksi Akpol secara maksimal.
              </p>
              <p>
                Nama <span class="font-bold text-text">Pratistha</span> melambangkan kehormatan, keteguhan, dan fondasi kuat dalam meraih cita-cita. Nilai inilah yang kami tanamkan: semangat juang, integritas, kedisiplinan, dan mental pantang menyerah.
              </p>
              <p>
                Kami tidak hanya membina peserta untuk lulus seleksi, tetapi juga membentuk pribadi berkarakter yang siap menjadi generasi pemimpin bangsa.
              </p>
            </div>
            <p class="mt-6 text-lg font-bold text-primary">Bersama Pratistha Training Center, wujudkan langkah pasti menuju Akpol.</p>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-0 w-28 bottom-0 right-0" />
          <SectionWaveDivider class="absolute -bottom-30 left-0 right-0 z-10" />
        </div>
      </div>
    </section>

    
    <section id="leaders" class="my-10">
      <div class="max-w-7xl mx-auto fade-up fade-up-tight delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Dewan Pembina & Pimpinan</h2>
          <p class="text-gray-600 mb-8">Profil pejabat dan pimpinan PT. Pratistha Training Center Indonesia.</p>

          <div class="grid gap-6 relative z-30">
            <article
              v-for="(leader, index) in leaders"
              :key="leader.name"
              class="group rounded-3xl flex flex-col md:flex-row border border-border bg-gradient-to-br from-white via-background to-sky overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
            >
              <div
                class="relative md:w-[38%] lg:w-[34%] shrink-0 bg-sky"
                :class="index % 2 === 0 ? 'md:order-1' : 'md:order-2'"
              >
                <img
                  :src="leader.image"
                  :alt="leader.name"
                  class="w-full h-[400px] md:h-full object-cover object-top transition-transform duration-700 group-hover:scale-[1.02]"
                />
                <div class="absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-black/30 to-transparent" />
              </div>
              <div
                class="p-6 md:p-8 flex-1"
                :class="index % 2 === 0 ? 'md:order-2' : 'md:order-1'"
              >
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-primary">Profil Pimpinan</p>
                <h3 class="mt-2 font-extrabold text-2xl md:text-3xl leading-tight tracking-tight text-text">{{ leader.name }}</h3>
                <p class="text-base text-secondary font-bold mt-2">{{ leader.batch }}</p>

                <div class="mt-5 rounded-xl border border-border bg-white/90 px-4 py-3">
                  <p class="text-[11px] font-bold tracking-[0.14em] text-primary uppercase">Posisi Saat Ini</p>
                  <p class="mt-1 text-base md:text-lg font-semibold leading-relaxed text-text">{{ leader.position }}</p>
                </div>

                <div class="mt-5">
                  <p class="text-sm font-bold tracking-[0.14em] text-text uppercase">Jabatan Terakhir</p>
                  <ul class="mt-3 space-y-2.5 text-base font-semibold leading-relaxed text-gray-700">
                    <li v-for="line in leader.highlights" :key="line" class="flex items-start gap-2.5">
                      <span class="mt-2 h-2 w-2 rounded-full bg-secondary shrink-0" />
                      <span>{{ line }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </article>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-10 w-72 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section id="members" class="pb-8">
      <div class="max-w-7xl mx-auto fade-up fade-up-tight relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h3 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Staff</h3>
          <p class="text-gray-600 mb-6">Staff pendukung program pembinaan PT. Pratistha Training Center Indonesia.</p>
          <div class="relative z-20">
            <div class="min-h-[590px]">
              <Transition name="member-slide-fade" mode="out-in">
                <div :key="`member-slide-${activeMemberSlide}`" class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                  <article
                    v-for="member in (memberSlides[activeMemberSlide] || [])"
                    :key="member.name"
                    class="h-full rounded-2xl border border-border bg-background overflow-hidden hover:shadow-lg transition-all duration-300 hover:-translate-y-1"
                  >
                    <img :src="member.image" :alt="member.name" class="w-full h-[480px] object-cover object-top" />
                    <div class="p-5">
                      <h4 class="font-semibold text-base leading-snug text-text">{{ member.name }}</h4>
                      <p class="text-sm text-primary font-semibold mt-1">{{ member.jabatan }}</p>
                    </div>
                  </article>
                </div>
              </Transition>
            </div>
            <div v-if="memberSlides.length > 1" class="mt-5 flex items-center justify-center gap-2.5">
              <button
                v-for="(_, index) in memberSlides"
                :key="`member-slide-dot-${index}`"
                type="button"
                class="h-2.5 rounded-full transition-all duration-300 cursor-pointer"
                :class="index === activeMemberSlide ? 'w-8 bg-secondary' : 'w-2.5 bg-gray-300 hover:bg-gray-400'"
                @click="activeMemberSlide = index"
              />
            </div>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-10 w-28 bottom-0 right-0" />
        </div>
      </div>
    </section>
    
    <section id="programs" class="px-5 md:px-10 pb-8">
      <div class="max-w-7xl mx-auto fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Keunggulan Program</h2>
          <p class="text-gray-600 mb-8">Ekosistem belajar yang didesain untuk disiplin, konsisten, dan terukur.</p>
          <div class="grid md:grid-cols-2 gap-5 relative z-10">
            <article
              v-for="feature in keyFeatures"
              :key="feature.title"
              class="rounded-2xl border border-border bg-gradient-to-br from-white to-sky p-6"
            >
              <div class="mb-3 inline-flex rounded-lg bg-sky p-2 text-primary">
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

    <section id="services" class="px-5 md:px-10 pb-8">
      <div class="max-w-7xl mx-auto fade-up delay-1 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Layanan Pembinaan</h2>
          <p class="text-gray-600 mb-8">Program seleksi dan pendampingan untuk kesiapan calon peserta.</p>
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article
              v-for="service in services"
              :key="service.title"
              class="rounded-2xl border border-border bg-background p-5 hover:-translate-y-1 hover:shadow-lg transition-all"
            >
              <div class="mb-3 inline-flex rounded-lg bg-sky p-2 text-primary">
                <component :is="service.icon" class="h-5 w-5" />
              </div>
              <h3 class="font-semibold text-text">{{ service.title }}</h3>
              <p class="text-sm text-gray-600 mt-1">{{ service.desc }}</p>
            </article>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute w-28 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section id="choices" class="px-5 md:px-10 pb-8 relative">
      <div class="max-w-7xl mx-auto fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Pilihan Kelas BIMBEL dan Pelatihan</h2>
          <p class="text-gray-600 mb-8">Pilih jalur program sesuai kebutuhan pembinaan dan target persiapan.</p>
          <div class="grid md:grid-cols-2 gap-5 relative z-30">
            <article
              v-for="program in onlinePrograms"
              :key="program.value"
              class="rounded-2xl border border-gray-100 p-6 hover:shadow-lg transition-all"
            >
              <div class="flex items-center justify-between gap-3">
                <h3 class="font-bold text-lg whitespace-nowrap">{{ program.name }}</h3>
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

    <section id="comparison" class="px-5 md:px-10 pb-8">
      <div class="max-w-7xl mx-auto fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Tabel Perbandingan Fasilitas Kelas Bimbel</h2>
          <p class="text-gray-600 mb-6">Bandingkan fasilitas dan durasi akses setiap kelas untuk menentukan program paling sesuai.</p>

          <div class="overflow-x-auto rounded-2xl border border-border relative z-20">
            <table class="min-w-[980px] w-full border-collapse">
              <thead>
                <tr class="bg-sky/60">
                  <th class="text-left px-4 py-3 font-bold text-text border-b border-border min-w-[290px]">Fasilitas Program</th>
                  <th
                    v-for="col in classComparisonColumns"
                    :key="col.key"
                    class="px-4 py-3 border-b border-border text-center"
                  >
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide" :class="col.badgeClass">
                      {{ col.label }}
                    </span>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in classComparisonRows" :key="row.label" class="odd:bg-white even:bg-sky/20">
                  <th class="text-left px-4 py-3 text-sm md:text-base font-semibold text-text border-b border-border">{{ row.label }}</th>
                  <td
                    v-for="col in classComparisonColumns"
                    :key="`${row.label}-${col.key}`"
                    class="px-4 py-3 border-b border-border text-center"
                  >
                    <template v-if="typeof row.values[col.key] === 'boolean'">
                      <span
                        class="inline-flex h-7 w-7 items-center justify-center rounded-full border"
                        :class="row.values[col.key] ? 'bg-mint text-primary border-border' : 'bg-rose-50 text-rose-500 border-rose-100'"
                      >
                        <Check v-if="row.values[col.key]" class="h-4 w-4" />
                        <XIcon v-else class="h-4 w-4" />
                      </span>
                    </template>
                    <template v-else>
                      <span class="text-sm md:text-base font-bold text-text">{{ row.values[col.key] }}</span>
                    </template>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <p class="text-xs text-gray-500 mt-3">
            Catatan: rincian fasilitas dapat menyesuaikan kebijakan program dan periode pembinaan.
          </p>
          <img :src="patternUrl" alt="Pattern" class="absolute z-10 w-28 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section class="px-5 md:px-10 pb-14">
      <div class="max-w-7xl mx-auto rounded-[2rem] bg-primary text-white p-8 md:p-10 shadow-2xl shadow-[#123B8F]/30 fade-up delay-3">
        <h2 class="text-2xl md:text-3xl font-bold mb-3">Siap naik level untuk persiapan Akademi Kepolisian?</h2>
        <p class="text-white/80 max-w-3xl">
          Bergabung sebagai peserta, lengkapi pendaftaran secara bertahap, lalu ikuti program kelas bimbingan online dengan standar pembinaan profesional.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <router-link to="/signup" class="px-6 py-3 rounded-full bg-secondary text-white font-semibold hover:bg-primary transition-all">Mulai Pendaftaran</router-link>
          <router-link to="/registration" class="px-6 py-3 rounded-full bg-white/10 text-white font-semibold border border-white/20 hover:bg-white/20 transition-all">Lihat Tahapan Pendaftaran</router-link>
        </div>
      </div>
    </section>

    <a
      href="https://wa.me/6285124156748"
      target="_blank"
      rel="noopener noreferrer"
      class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-secondary text-white shadow-xl shadow-[#2F6BFF]/30 transition hover:scale-105 hover:bg-primary"
      aria-label="Chat WhatsApp"
      title="Chat WhatsApp"
    >
      <MessageCircle class="h-7 w-7" />
    </a>
  </main>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { storeToRefs } from 'pinia'
import { BookOpenText, Brain, Check, Crown, Dumbbell, LineChart, MessageCircle, NotebookPen, ShieldCheck, UserCheck, Warehouse, X as XIcon } from 'lucide-vue-next'
import { ONLINE_PROGRAMS } from '@/constants/onlinePrograms'
import SectionWaveDivider from '@/components/SectionWaveDivider.vue'

const route = useRoute()
const store = useAppStore()
const { isAuthenticated } = storeToRefs(store)
const nanaUrl = new URL('../../assets/bpk_nana.png', import.meta.url).href
const tubagusUrl = new URL('../../assets/bpk_tubagus.jpg', import.meta.url).href
const awangUrl = new URL('../../assets/bpk_awang.jpg', import.meta.url).href
const gilangUrl = new URL('../../assets/anggota/gilang.jpeg', import.meta.url).href
const winUrl = new URL('../../assets/anggota/win.jpeg', import.meta.url).href
const rinaUrl = new URL('../../assets/anggota/rina.jpeg', import.meta.url).href
const natashaUrl = new URL('../../assets/anggota/natasha.jpeg', import.meta.url).href
const tutikUrl = new URL('../../assets/anggota/tutik.jpeg', import.meta.url).href
const bannerUrl = new URL('../../assets/bg_1.png', import.meta.url).href
const patternUrl = new URL('../../assets/Pattern.svg', import.meta.url).href
const wallpaperModules = import.meta.glob('../../assets/wallpaper/*.{jpg,jpeg,png,webp}', {
  eager: true,
  import: 'default',
})
const wallpaperSlides = Object.entries(wallpaperModules)
  .sort(([pathA], [pathB]) => pathA.localeCompare(pathB, undefined, { numeric: true }))
  .map(([, src]) => src)
const activeWallpaperIndex = ref(0)
const activeMemberSlide = ref(0)
/** Below Tailwind `sm` (640px): one member per slide; sm+: three per slide. */
const membersMobileOnePerSlide = ref(
  typeof window !== 'undefined' ? window.matchMedia('(max-width: 639px)').matches : false,
)
const isMobileMenuOpen = ref(false)
let wallpaperInterval
let memberInterval
let membersMediaQuery = null

function syncMembersSlideLayout() {
  if (!membersMediaQuery) return
  membersMobileOnePerSlide.value = membersMediaQuery.matches
}

let fadeObserver
const quickNavItems = [
  { id: 'leaders', label: 'Dewan Pimpinan' },
  { id: 'programs', label: 'Keunggulan Program' },
  { id: 'services', label: 'Layanan Pembinaan' },
  { id: 'choices', label: 'Pilihan Bimbel' },
  { id: 'comparison', label: 'Perbandingan Kelas' },
]

const leaders = [
  {
    name: 'Komjen Pol (P) Drs. H. Nana S. Permana',
    batch: 'Batalion Dharma Angkatan 1968',
    position: 'Ketua Pembina Yayasan Pendidikan Tribakti Langlang Buana',
    highlights: [
      'Wakapolri tahun 1998 - 2000',
      'Pembina strategis pendidikan dan pembinaan kepolisian',
    ],
    image: nanaUrl,
  },
  {
    name: 'Irjen Pol (P) Dr. H Tubagus Anis Angkawijaya, Drs., M.Si',
    batch: 'Bataliyon Anindhita Tahun 1981',
    position: 'Komisaris PT. Pratistha Training Center Indonesia',
    highlights: [
      'Kapolda Jabar tahun 2012 - 2013',
      'Kapolda Sultra tahun 2012',
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
      'Wakapolda Jawa Tengah tahun 2016 - 2017',
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
const MEMBER_SLIDE_SIZE_DESKTOP = 3
const memberSlides = computed(() => {
  const size = membersMobileOnePerSlide.value ? 1 : MEMBER_SLIDE_SIZE_DESKTOP
  const chunks = []
  for (let i = 0; i < members.length; i += size) {
    chunks.push(members.slice(i, i + size))
  }
  return chunks
})

watch(memberSlides, (slides) => {
  if (!slides.length) return
  if (activeMemberSlide.value >= slides.length) {
    activeMemberSlide.value = 0
  }
})

const services = [
  { icon: Brain, title: 'Tes Psikologi', desc: 'Pemetaan karakter, kestabilan emosi, dan kesiapan menghadapi seleksi.' },
  { icon: BookOpenText, title: 'Tes Akademik', desc: 'Latihan soal akademik terstruktur dengan simulasi CBT berkala.' },
  { icon: Dumbbell, title: 'Tes Fisik', desc: 'Panduan pembinaan fisik sesuai standar seleksi kepolisian.' },
  { icon: ShieldCheck, title: 'Mental & Ideologi', desc: 'Penguatan mental, disiplin, wawasan kebangsaan, dan integritas.' },
  { icon: NotebookPen, title: 'Materi Pembelajaran', desc: 'Modul belajar, bank soal, dan pembahasan eksklusif per kelas.' },
]

/** Ringkasan hero: selaras dengan penyelenggara & layanan di halaman ini. */
const heroSelayangPandang = {
  eyebrow: 'Selayang Pandang',
  title: 'Bimbingan belajar dan pelatihan persiapan seleksi Akademi Kepolisian',
  description:
    'PT. Pratistha Training Center Indonesia menyelenggarakan program pendampingan untuk kesiapan calon peserta AKADEMI KEPOLISIAN, dengan fasilitas belajar eksklusif dan pembinaan profesional.',
  bannerAlt: 'Banner PT. Pratistha Training Center Indonesia — persiapan seleksi Akademi Kepolisian',
}

const heroOverviewPillLabels = computed(() => services.map((s) => s.title))

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
    desc: 'Perkembangan hasil latihan, nilai tes, dan progres pendaftaran peserta dapat dipantau secara digital.',
  },
  {
    icon: Warehouse,
    title: 'Tempat Belajar Eksklusif',
    desc: 'Ruang belajar terarah dengan sistem kelas, jadwal, dan evaluasi untuk menjaga fokus pembinaan.',
  },
]

const onlinePrograms = ONLINE_PROGRAMS

const classComparisonColumns = [
  { key: 'karantina', label: 'Kelas Karantina', badgeClass: 'bg-primary text-white' },
  { key: 'reguler', label: 'Kelas Reguler', badgeClass: 'bg-secondary text-white' },
  { key: 'online', label: 'Kelas Online', badgeClass: 'bg-sky text-primary' },
  { key: 'ujian', label: 'Kelas Ujian', badgeClass: 'bg-cream text-text' },
]

const classComparisonRows = [
  { label: 'Masa pelatihan', values: { karantina: '3 bulan', reguler: '3 bulan', online: '3 bulan', ujian: '3 bulan' } },
  { label: 'Konsultasi', values: { karantina: true, reguler: true, online: true, ujian: true } },
  { label: 'Penginapan siswa (1 kamar 2 orang)', values: { karantina: true, reguler: false, online: false, ujian: false } },
  { label: 'Makan (3 kali sehari)', values: { karantina: true, reguler: false, online: false, ujian: false } },
  { label: 'Seragam siswa (baju olahraga, batik, PDH)', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Psikotes', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Medical check up', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Pelatihan jasmani', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Pelatihan akademik', values: { karantina: true, reguler: true, online: true, ujian: false } },
  { label: 'Pelatihan renang', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Pelatihan ujian', values: { karantina: true, reguler: true, online: true, ujian: true } },
  { label: 'Materi online', values: { karantina: true, reguler: true, online: true, ujian: false } },
  { label: 'Transportasi selama program', values: { karantina: true, reguler: false, online: false, ujian: false } },
  { label: 'Tas dan topi', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Perlengkapan makan', values: { karantina: true, reguler: false, online: false, ujian: false } },
  { label: 'Masa akses aplikasi', values: { karantina: '1 tahun', reguler: '1 tahun', online: '6 bulan', ujian: '3 bulan' } },
]

const programBadge = (program) => {
  const mode = program.mode
  if (mode === 'Premium') {
    return {
      label: mode,
      isVip: true,
      className: 'bg-sky text-primary border-border',
    }
  }
  if (mode === 'Reguler') {
    return {
      label: mode,
      isVip: false,
      className: 'bg-mint text-primary border-border',
    }
  }
  if (mode === 'Full Online') {
    return {
      label: mode,
      isVip: false,
      className: 'bg-cream text-text border-border',
    }
  }
  return {
    label: mode,
    isVip: false,
    className: 'bg-background text-text border-border',
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
  isMobileMenuOpen.value = false
  const target = document.getElementById(id)
  if (target) {
    target.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }
}

const setupScrollFadeAnimations = async () => {
  await nextTick()
  const fadeTargets = document.querySelectorAll('.fade-up')
  if (!fadeTargets.length) return

  // Keep content visible by default; activate fade behavior only when observer is ready.
  fadeTargets.forEach((el) => el.classList.add('fade-ready'))

  if (typeof IntersectionObserver === 'undefined') {
    fadeTargets.forEach((el) => el.classList.add('in-view'))
    return
  }

  fadeObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        entry.target.classList.toggle('in-view', entry.isIntersecting)
      })
    },
    {
      threshold: 0.01,
      rootMargin: '0px 0px -4% 0px',
    },
  )

  fadeTargets.forEach((el) => fadeObserver.observe(el))
}

watch(() => route.hash, scrollToHash)
onMounted(() => {
  scrollToHash()
  setupScrollFadeAnimations()
  if (wallpaperSlides.length > 1) {
    wallpaperInterval = setInterval(() => {
      activeWallpaperIndex.value = (activeWallpaperIndex.value + 1) % wallpaperSlides.length
    }, 3200)
  }
  if (memberSlides.value.length > 1) {
    memberInterval = setInterval(() => {
      activeMemberSlide.value = (activeMemberSlide.value + 1) % memberSlides.value.length
    }, 4200)
  }
  membersMediaQuery = window.matchMedia('(max-width: 639px)')
  syncMembersSlideLayout()
  membersMediaQuery.addEventListener('change', syncMembersSlideLayout)
})

onUnmounted(() => {
  if (wallpaperInterval) {
    clearInterval(wallpaperInterval)
  }
  if (memberInterval) {
    clearInterval(memberInterval)
  }
  if (membersMediaQuery) {
    membersMediaQuery.removeEventListener('change', syncMembersSlideLayout)
  }
  if (fadeObserver) {
    fadeObserver.disconnect()
  }
})
</script>

<style scoped>
.fade-up {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.fade-up.fade-ready {
  opacity: 0;
  transform: translateY(20px) scale(0.985);
  transition: opacity 0.55s ease, transform 0.55s ease;
}

.fade-up.fade-ready.in-view {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.fade-up-tight.fade-ready {
  transform: translateY(10px) scale(0.992);
  transition: opacity 0.38s ease, transform 0.38s ease;
}

.member-slide-fade-enter-active,
.member-slide-fade-leave-active {
  transition: opacity 0.35s ease, transform 0.35s ease;
}

.member-slide-fade-enter-from,
.member-slide-fade-leave-to {
  opacity: 0;
  transform: translateY(8px);
}

.floating-orb {
  animation: floatOrb 7s ease-in-out infinite;
}

@keyframes floatOrb {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(18px); }
}

.hero-banner-fade {
  animation: heroFadeIn 1.2s ease-out both;
}

@keyframes heroFadeIn {
  0% {
    opacity: 0;
    transform: scale(1.04);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

</style>
