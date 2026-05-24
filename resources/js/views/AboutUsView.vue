<template>
  <main class="min-h-screen text-text md:px-10 md:py-12 about-us-gradient-animated">
    <div class="mx-auto space-y-8">
      <section
        class="fade-up relative overflow-hidden text-center md:text-left md:rounded-[2rem] text-primary shadow-2xl shadow-[#123B8F]/35">
        <img :src="backgroundAboutUrl" alt="Background About Us"
          class="about-hero-bg absolute inset-0 z-0 h-full w-full object-cover md:block hidden" />
        <img :src="accesoriseUrl" alt="Accessorise"
          class="about-hero-accesorise absolute top-0 right-0 h-32 w-32 object-cover md:hidden block" />
        <img :src="accesoriseUrl" alt="Accessorise"
          class="about-hero-accesorise absolute bottom-0 left-0 h-32 w-32 object-cover md:hidden block rotate-180" />
        <!-- <div class="absolute inset-0 z-0 bg-gradient-to-b from-white/40 via-white/55 to-white/70" /> -->
        <div class="relative z-10 p-7 md:p-10 pb-16">
          <router-link to="/"
            class="inline-flex absolute left-5 top-2 items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-semibold hover:bg-white/25 transition-colors">
            <ArrowLeft class="h-4 w-4" />
            Kembali ke Beranda
          </router-link>
          <div class="md:flex md:items-center md:justify-center md:gap-4">
            <div class="md:w-4/12 mt-8 md:mt-0">
              <div
                class="rounded-full border-10 h-32 w-32 md:h-56 md:w-56 mx-auto border-primary/50 flex items-center bg-white/90 p-2 shadow-lg">
                <img :src="brandLogoUrl" alt="Logo Pratistha Cendekia Prestasi"
                  class="mx-auto mb-4 h-16 w-16 md:h-36 md:w-36" />
              </div>
            </div>
            <div>
              <p class="mt-5 text-xs font-bold uppercase tracking-[0.2em] text-primary/80">Tentang Kami</p>
              <h1 class="mt-2 text-2xl md:text-5xl font-black leading-tight">
                Pratistha Cendekia Prestasi dibawah naungan <br /> PT. Pratistha Training Center Indonesia
              </h1>
              <p class="mt-5 text-md md:text-2xl pb-4 font-bold text-primary/90 leading-relaxed">
                Sebagai lembaga yang mengedepankan kualitas serta kepercayaan, Pratistha Cendekia Prestasi didukung oleh
                legalitas usaha yang jelas dan terdaftar secara resmi. Kami percaya bahwa transparansi dan
                profesionalisme
                merupakan fondasi penting dalam membangun kepercayaan peserta didik maupun orang tua.
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="fade-up relative overflow-hidden mt-10 p-4 md:p-0">
        <div class="relative z-20">
          <div class="flex items-center justify-center gap-3">
            <FileBadge class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl text-primary font-bold tracking-tight">Legalitas Perusahaan</h2>
          </div>
          <p class="mt-2 text-primary text-lg md:text-xl text-center">Berikut adalah nomor dokumen resmi perusahaan
            kami:</p>

          <div class="mt-6 grid gap-6 md:grid-cols-3">
            <article v-for="doc in legalDocuments" :key="doc.label"
              class="rounded-xl border border-border bg-background shadow-lg overflow-hidden flex flex-col">
              <div
                class="p-4 md:p-5 border-t border-border bg-gradient-to-br from-white to-sky flex-1 legal-gradient-animated">
                <p class="text-xs md:text-lg uppercase tracking-[0.15em] font-bold text-primary text-center">{{
                  doc.label }}</p>
                <p v-if="doc.subtitle"
                  class="mt-1 text-xs md:text-sm text-gray-600 font-bold text-center leading-relaxed text-md">{{
                  doc.subtitle }}
                </p>
                <p class="mt-3 text-sm md:text-lg font-bold text-text text-center break-words">{{ doc.value }}</p>
              </div>
            </article>
          </div>
        </div>
      </section>

      <section class="fade-up relative overflow-hidden mt-10 p-4 md:p-0">
        <div class="relative z-20">
          <h2
            class="mt-2 text-2xl md:text-4xl font-black text-center tracking-tight text-primary flex items-center justify-center gap-3">
            <Crown class="h-6 w-6 md:h-7 md:w-7 text-primary" />
            Pimpinan Pratistha Cendekia Prestasi
          </h2>

          <div class="mt-8 md:mt-16">
            <div ref="leaderCarouselRef"
              class="leader-carousel flex gap-5 md:gap-8 overflow-x-auto px-[11%] md:px-[16%] py-6 snap-x snap-mandatory scroll-smooth">
              <article v-for="(leader, index) in heroLeaders" :key="leader.name" @click="goToLeader(index)"
                :ref="(el) => setLeaderSlideRef(el, index)"
                class="shrink-0 basis-[78%] md:basis-[52%] lg:basis-[42%] snap-center rounded-2xl border border-border shadow-xl bg-background overflow-hidden transition-all duration-300"
                :class="activeLeaderIndex === index
                  ? 'scale-100 md:scale-[1.03] md:-translate-y-2 shadow-xl shadow-[#123B8F]/20 opacity-100'
                  : 'scale-[0.88] md:scale-[0.9] opacity-70'">
                <div class="relative">
                  <img :src="leader.image" :alt="leader.name" class="h-[360px] md:h-[420px] w-full object-cover object-top" />
                  <div class="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-center p-2">
                    <div>
                      {{ leader.name }}
                    </div>
                    <button type="button"
                      class="text-xs text-white/80 mt-2 underline decoration-white/50 underline-offset-2 md:hidden"
                      @click.stop="openLeaderDetailModal(index)">
                      lihat detail
                    </button>
                    <div class="text-xs text-white/80 mt-2 hidden md:block">
                      lihat detail
                    </div>
                  </div>
                </div>
              </article>
            </div>

            <div class="mt-3 flex items-center justify-center gap-2">
              <button v-for="(leader, index) in heroLeaders" :key="`dot-${leader.name}`"
                class="h-2.5 rounded-full transition-all duration-300" :class="activeLeaderIndex === index
                  ? 'w-7 bg-primary'
                  : 'w-2.5 bg-primary/30 hover:bg-primary/60'" :aria-label="`Pilih slide ${index + 1}`"
                @click="goToLeader(index)" />
            </div>
          </div>
        </div>
      </section>

      <section class="fade-up relative overflow-hidden mt-10 p-4 md:p-0">
        <div class="relative z-20">
          <div class="flex items-center justify-center gap-3">
            <Users class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-primary">Struktur Organisasi</h2>
          </div>

          <div ref="orgChartViewportRef" class="org-chart-viewport my-12">
            <div class="org-chart">
            <div class="org-node org-node-animated">
              <p class="org-node__role">{{ orgChart.penasehat.role }}</p>
              <p class="org-node__name">{{ orgChart.penasehat.name }}</p>
            </div>

            <div class="org-stem" />

            <div class="org-node org-node-animated">
              <p class="org-node__role">{{ orgChart.komisaris.role }}</p>
              <p class="org-node__name">{{ orgChart.komisaris.name }}</p>
            </div>

            <div class="org-stem" />

            <div class="org-node org-node-animated">
              <p class="org-node__role">{{ orgChart.direkturUtama.role }}</p>
              <p class="org-node__name">{{ orgChart.direkturUtama.name }}</p>
            </div>

            <div class="org-stem" />

            <div class="org-node org-node-animated">
              <p class="org-node__role">{{ orgChart.direktur.role }}</p>
              <p class="org-node__name">{{ orgChart.direktur.name }}</p>
            </div>

            <div class="org-split">
              <div class="org-stem" />
              <div class="org-split__cols">
                <div class="org-split__col">
                  <div class="org-split__drop" />
                  <div class="org-node org-node-animated">
                    <p class="org-node__role">{{ orgChart.sekretaris.role }}</p>
                    <p class="org-node__name">{{ orgChart.sekretaris.name }}</p>
                  </div>
                </div>
                <div class="org-split__col">
                  <div class="org-split__drop" />
                  <div class="org-node org-node-animated">
                    <p class="org-node__role">{{ orgChart.bendahara.role }}</p>
                    <p class="org-node__name">{{ orgChart.bendahara.name }}</p>
                  </div>
                </div>
              </div>

              <div class="org-split__through">
                <div class="org-stem org-stem--bridge" />
                <div class="org-split__cols org-split__cols--three">
                  <div class="org-split__col">
                    <div class="org-split__drop" />
                    <div class="org-node org-node-animated">
                      <p class="org-node__role">{{ orgChart.internal.role }}</p>
                      <p class="org-node__name">{{ orgChart.internal.name }}</p>
                    </div>
                  </div>
                  <div class="org-split__col">
                    <div class="org-split__drop" />
                    <div class="org-node org-node-animated">
                      <p class="org-node__role">{{ orgChart.eksternal.role }}</p>
                      <p class="org-node__name">{{ orgChart.eksternal.name }}</p>
                    </div>
                  </div>
                  <div class="org-split__col">
                    <div class="org-split__drop" />
                    <div class="org-node org-node-animated">
                      <p class="org-node__role">{{ orgChart.digitalMarketing.role }}</p>
                      <p class="org-node__name">{{ orgChart.digitalMarketing.name }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
          </div>
        </div>
      </section>

      <section class="fade-up relative overflow-hidden mt-10 p-4 md:p-0">
        <div class="relative z-20">
          <div class="flex items-center justify-center gap-3">
            <Phone class="h-6 w-6 text-white" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-white">Kontak Kami</h2>
          </div>
          <p class="mt-2 text-white text-center text-lg md:text-xl">Kanal komunikasi resmi Pratistha Cendekia
            Prestasi.</p>
          <div class="mt-6 flex flex-wrap justify-center gap-4">
            <article v-for="contact in contactChannels" :key="contact.label"
              class="rounded-xl border border-border legal-gradient-animated shadow-lg p-4 w-full md:w-1/3 contact-channel-animated">
              <div class="flex items-center justify-center gap-2 text-primary">
                <component :is="contact.icon" class="h-4 w-4" />
                <p class="text-sm md:text-xl font-bold text-center">{{ contact.label }}</p>
              </div>
              <p class="mt-2 text-sm font-semibold text-text break-all text-center">{{ contact.value || '-' }}</p>
            </article>
          </div>
        </div>
      </section>

      <section class="fade-up relative overflow-hidden mt-16 p-4 md:p-0">
        <div class="relative z-20">
          <div class="flex items-center text-center gap-3">
            <Landmark class="h-6 w-6 text-white left-12 relative md:left-0" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-white">Rekening Resmi Pembayaran</h2>
          </div>
          <p class="mt-2 text-white text-lg md:text-xl text-center max-w-3xl mx-auto">
            Segala transaksi Lembaga Kursus Pratistha Cendekia Prestasi hanya dilakukan melalui rekening resmi
            perusahaan di Bank BCA dan Bank BRI.
          </p>

          <div
            class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm md:text-xl text-amber-900 text-center max-w-5xl mx-auto">
            Demi keamanan, mohon pastikan pembayaran hanya ditransfer ke rekening resmi di bawah ini. Kami tidak
            menerima pembayaran melalui rekening pribadi, e-wallet, atau metode lain di luar rekening resmi perusahaan.
          </div>

          <div class="mt-6 grid sm:grid-cols-2 gap-4 max-w-5xl mx-auto">
            <article v-for="account in officialBankAccounts" :key="account.bank"
              class="rounded-xl shadow-xl p-5 bank-card-animated" :class="account.themeClass">
              <div><img :src="account.logo" :alt="account.logoAlt" class="h-24 w-32 mx-auto object-contain" />
              </div>
              <p class="mt-3 text-sm font-semibold md:text-xl text-gray-600 text-center">Nomor Rekening</p>
              <p class="mt-1 text-lg md:text-xl font-black text-text text-center tracking-wide">{{ account.accountNumber
                || '-' }}</p>
              <p class="mt-4 text-sm font-semibold md:text-xl text-gray-600 text-center">Atas Nama</p>
              <p class="mt-1 text-sm font-bold md:text-xl text-text text-center leading-relaxed">{{ account.accountName
                }}</p>
            </article>
          </div>
        </div>
      </section>

      <section class="fade-up relative overflow-hidden mt-16 p-4 md:p-0">

        <div class="relative z-20">
          <div class="flex items-center justify-center gap-3">
            <Image class="h-6 w-6 text-white" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-white">Galeri Kegiatan</h2>
          </div>
          <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article v-for="photo in galleryPhotos" :key="photo.src"
              class="rounded-2xl overflow-hidden border border-border bg-background shadow-lg">
              <img :src="photo.src" :alt="photo.alt" class="h-56 w-full object-cover" />
            </article>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-5 md:-bottom-30 left-0 right-0 z-10" />
      </section>
    <Teleport to="body">
      <Transition name="member-slide-fade">
        <div v-if="isLeaderDetailModalOpen"
          class="fixed inset-0 z-[120] flex items-center justify-center bg-black/55 p-4 backdrop-blur-[2px]"
          role="dialog" aria-modal="true" aria-labelledby="leader-detail-modal-title" @click.self="closeLeaderDetailModal">
          <article
            class="w-full max-w-2xl rounded-3xl relative bg-gradient-to-br from-primary to-secondary overflow-hidden border border-blue-100/20 p-6 shadow-xl">
            <div class="absolute inset-0 mobile-card-gradient rounded-[2rem]"></div>
            <div class="relative min-h-[390px]">
              <div class="flex items-center justify-end">
                <button type="button"
                  class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/35 bg-white/12 text-white hover:bg-white/20 transition-colors"
                  aria-label="Tutup detail pimpinan" @click="closeLeaderDetailModal">
                  <XIcon class="h-4 w-4" />
                </button>
              </div>
              <div class="mt-4">
                <p id="leader-detail-modal-title"
                  class="text-[14px] font-bold uppercase tracking-[0.18em] relative z-20 text-center">
                  Profil {{ activeLeaderDetail.role }}
                </p>
                <h3 class="mt-2 text-2xl font-extrabold leading-tight tracking-tight relative z-20 text-center">
                  {{ activeLeaderDetail.name }}
                </h3>
                <p class="mt-2 text-md font-bold relative z-20 text-center">{{ activeLeaderDetail.batch }}</p>

                <div class="mt-5 rounded-xl backdrop-blur-[1px] text-center relative z-20">
                  <p class="text-lg font-bold uppercase tracking-[0.14em]">Posisi Saat Ini</p>
                  <p class="mt-1 text-base font-semibold leading-relaxed">
                    {{ activeLeaderDetail.position }}
                  </p>
                </div>

                <div class="mt-5 rounded-xl backdrop-blur-[1px] relative z-20">
                  <p class="text-lg font-bold uppercase tracking-[0.14em] text-center border-b border-gray-300 pb-2">
                    Jabatan Terakhir</p>
                  <ul class="mt-3 space-y-2.5 text-lg font-semibold leading-relaxed">
                    <li v-for="line in activeLeaderDetail.highlights" :key="line" class="text-center">
                      <span>{{ line }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </article>
        </div>
      </Transition>
    </Teleport>
    </div>
  </main>
</template>

<script setup>
import SectionWaveDivider from '@/components/SectionWaveDivider.vue'
import { ArrowLeft, Crown, FileBadge, Globe, Image, Instagram, Landmark, Mail, Music2, Phone, Users, X as XIcon } from 'lucide-vue-next'
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'

const patternUrl = new URL('../../assets/Pattern.svg', import.meta.url).href
const accesoriseUrl = new URL('../../assets/accessorise.png', import.meta.url).href
const brandLogoUrl = new URL('../../assets/logo.png', import.meta.url).href
const backgroundAboutUrl = new URL('../../assets/about.png', import.meta.url).href
const wallpaper1Url = new URL('../../assets/wallpaper/1.webp', import.meta.url).href
const wallpaper2Url = new URL('../../assets/wallpaper/2.jpeg', import.meta.url).href
const wallpaper3Url = new URL('../../assets/wallpaper/3.webp', import.meta.url).href
const wallpaper4Url = new URL('../../assets/wallpaper/4.jpeg', import.meta.url).href
const bannerUrl = new URL('../../assets/Banner.png', import.meta.url).href
const activityBookUrl = new URL('../../assets/book.jpg', import.meta.url).href
const nanaUrl = new URL('../../assets/bpk_nana.png', import.meta.url).href
const tubagusUrl = new URL('../../assets/bpk_tubagus.jpg', import.meta.url).href
const awangUrl = new URL('../../assets/bpk_awang.jpg', import.meta.url).href
const skKemenkumhamUrl = new URL('../../assets/legal/sk-kemenkumham.png', import.meta.url).href
const npwpUrl = new URL('../../assets/legal/npwp.png', import.meta.url).href
const nibUrl = new URL('../../assets/legal/nib.png', import.meta.url).href
const bcaLogoUrl = new URL('../../assets/logotransaksi/Logo BCA_Biru.png', import.meta.url).href
const briLogoUrl = new URL('../../assets/logotransaksi/BRI_2025.png', import.meta.url).href

const heroLeaders = [
  {
    name: 'Irjen Pol (P) Dr. H Tubagus Anis Angkawijaya, Drs., M.Si',
    role: 'Komisaris',
    batch: 'Bataliyon Anindhita Tahun 1981',
    position: 'Komisaris Pratistha Cendekia Prestasi',
    highlights: [
      'Kapolda Jabar tahun 2012 - 2013',
      'Kapolda Sultra tahun 2012',
      'Ketua Persatuan Purnawirawan Daerah Jabar',
      'Wakil Ketua Pembina Yayasan Pendidikan Tribakti Langlang Buana',
    ],
    image: tubagusUrl,
    featured: false,
  },
  {
    name: 'Komjen Pol (P) Drs. H. Nana S. Permana',
    role: 'Penasehat',
    batch: 'Batalion Dharma Angkatan 1968',
    position: 'Ketua Pembina Yayasan Pendidikan Tribakti Langlang Buana',
    highlights: [
      'Wakapolri tahun 1998 - 2000',
      'Pembina strategis pendidikan dan pembinaan kepolisian',
    ],
    image: nanaUrl,
    featured: true,
  },

  {
    name: 'Brigjen Pol (P) Drs. H. Awang Anwarudin, MH',
    role: 'Direktur Utama',
    batch: 'Bataliyon Pratistha Angkatan 1982',
    position: 'Direktur Utama Pratistha Cendekia Prestasi',
    highlights: [
      'Wakapolda Jawa Tengah tahun 2016 - 2017',
      'Pengarah operasional program kursus',
    ],
    image: awangUrl,
    featured: false,
  },
]

const orgChart = {
  penasehat: { role: 'Penasehat', name: 'Komjen Pol (P) Drs. H. Nana S. Permana' },
  komisaris: { role: 'Komisaris', name: 'Irjen Pol (P) Dr. H Tubagus Anis Angkawijaya, Drs., M.Si' },
  direkturUtama: { role: 'Direktur Utama', name: 'Brigjen Pol (P) Drs. H. Awang Anwarudin, MH' },
  direktur: { role: 'Direktur', name: 'Gilang Nurfahradz Syahni Fasya, S.T' },
  sekretaris: { role: 'Sekretaris', name: 'AKBP (P) Wahyu suhardini, S.IP' },
  bendahara: { role: 'Bendahara', name: 'KBP (P) Dra.Rina Regina' },
  internal: { role: 'Internal', name: 'AKBP (P) Dra.NATASHA YUNITA POSPOS, S.H. M.T.C.P' },
  eksternal: { role: 'Eksternal', name: 'Kompol (P) Tutik' },
  digitalMarketing: { role: 'Digital Marketing', name: '-' },
}

const legalDocuments = [
  {
    label: 'Nomor SK Kemenkumham',
    subtitle: 'Keputusan Menteri Hukum Republik Indonesia',
    value: 'AHU-0037173.AH.01.01.TAHUN 2026',
    image: skKemenkumhamUrl,
    alt: 'SK Kemenkumham Pratistha Cendekia Prestasi',
  },
  {
    label: 'NPWP Perusahaan',
    subtitle: 'Nomor Pokok Wajib Pajak',
    value: '1000 0000 0958 6609',
    details: [
      'Pratistha Cendekia Prestasi',
      'Jalan Sukamaju Nomor 142 RT. 003 RW. 005, Cipadung Kulon, Panyileukan, Kota Bandung, Jawa Barat',
      'Tanggal Terdaftar: 11/05/2026',
      'KPP Pratama Bandung Cicadas',
    ],
    image: npwpUrl,
    alt: 'NPWP Pratistha Cendekia Prestasi',
  },
  {
    label: 'Nomor Induk Berusaha (NIB)',
    subtitle: 'Perizinan Berusaha Berbasis Risiko — Pemerintah Republik Indonesia',
    value: '1805260036293',
    image: nibUrl,
    alt: 'NIB Pratistha Cendekia Prestasi',
  },
]

const officialBankAccounts = [
  {
    bank: 'Bank BCA',
    accountNumber: '-',
    accountName: 'PT. Pratistha Training Center Indonesia',
    logo: bcaLogoUrl,
    logoAlt: 'Logo Bank BCA',
    themeClass: 'bank-card-bca',
  },
  {
    bank: 'Bank BRI',
    accountNumber: '-',
    accountName: 'PT. Pratistha Training Center Indonesia',
    logo: briLogoUrl,
    logoAlt: 'Logo Bank BRI',
    themeClass: 'bank-card-bri',
  },
]

const contactChannels = [
  { label: 'Website', value: '-', icon: Globe },
  { label: 'Email', value: '-', icon: Mail },
  { label: 'Telepon / WhatsApp', value: '-', icon: Phone },
  { label: 'Instagram', value: '-', icon: Instagram },
  { label: 'TikTok', value: '-', icon: Music2 },
]

const galleryPhotos = [
  { src: bannerUrl, alt: 'Banner Pratistha Cendekia Prestasi', caption: 'Program kursus terarah dan profesional.' },
  { src: wallpaper1Url, alt: 'Kegiatan kursus 1', caption: 'Suasana pembelajaran akademik dan kesiapan seleksi.' },
  { src: wallpaper2Url, alt: 'Kegiatan kursus 2', caption: 'Latihan terukur untuk meningkatkan konsistensi peserta.' },
  { src: wallpaper3Url, alt: 'Kegiatan kursus 3', caption: 'Pendampingan intensif oleh mentor berpengalaman.' },
  { src: wallpaper4Url, alt: 'Kegiatan kursus 4', caption: 'Pembelajaran fisik, mental, dan karakter kepemimpinan.' },
  { src: activityBookUrl, alt: 'Materi belajar', caption: 'Dokumentasi materi belajar dan simulasi seleksi.' },
]

const leaderCarouselRef = ref(null)
const orgChartViewportRef = ref(null)
const leaderSlideRefs = ref([])
const activeLeaderIndex = ref(Math.max(0, heroLeaders.findIndex((leader) => leader.featured)))
const isLeaderDetailModalOpen = ref(false)
const activeLeaderDetailIndex = ref(activeLeaderIndex.value)
const touchStartX = ref(null)
const pointerStartX = ref(null)
const swipeThreshold = 45
let fadeObserver

const activeLeaderDetail = computed(() => heroLeaders[activeLeaderDetailIndex.value] ?? heroLeaders[0])

const setLeaderSlideRef = (element, index) => {
  if (!element) return
  leaderSlideRefs.value[index] = element
}

const goToLeader = (index, behavior = 'smooth') => {
  const container = leaderCarouselRef.value
  const target = leaderSlideRefs.value[index]
  if (!container || !target) return

  const targetLeft = target.offsetLeft - (container.clientWidth / 2) + (target.clientWidth / 2)
  const maxScrollLeft = Math.max(0, container.scrollWidth - container.clientWidth)
  const boundedLeft = Math.min(Math.max(targetLeft, 0), maxScrollLeft)

  container.scrollTo({
    left: boundedLeft,
    behavior,
  })
  activeLeaderIndex.value = index
}

const getNearestLeaderIndex = () => {
  const container = leaderCarouselRef.value
  if (!container || !leaderSlideRefs.value.length) return activeLeaderIndex.value

  const containerCenter = container.scrollLeft + container.clientWidth / 2
  let closestIndex = 0
  let smallestDistance = Number.POSITIVE_INFINITY

  leaderSlideRefs.value.forEach((slide, index) => {
    if (!slide) return
    const slideCenter = slide.offsetLeft + slide.clientWidth / 2
    const distance = Math.abs(slideCenter - containerCenter)
    if (distance < smallestDistance) {
      smallestDistance = distance
      closestIndex = index
    }
  })

  return closestIndex
}

const onLeaderScroll = () => {
  activeLeaderIndex.value = getNearestLeaderIndex()
}

const moveLeaderBySwipe = (deltaX) => {
  if (Math.abs(deltaX) < swipeThreshold) {
    goToLeader(activeLeaderIndex.value)
    return
  }

  const direction = deltaX > 0 ? 1 : -1
  const nextIndex = Math.min(
    Math.max(activeLeaderIndex.value + direction, 0),
    heroLeaders.length - 1,
  )

  goToLeader(nextIndex)
}

const onLeaderTouchStart = (event) => {
  touchStartX.value = event.changedTouches[0]?.clientX ?? null
}

const onLeaderTouchEnd = (event) => {
  if (touchStartX.value === null) return
  const touchEndX = event.changedTouches[0]?.clientX ?? touchStartX.value
  const deltaX = touchStartX.value - touchEndX
  moveLeaderBySwipe(deltaX)
  touchStartX.value = null
}

const onLeaderPointerStart = (event) => {
  pointerStartX.value = event.clientX
}

const onLeaderPointerEnd = (event) => {
  if (pointerStartX.value === null) return
  const deltaX = pointerStartX.value - event.clientX
  moveLeaderBySwipe(deltaX)
  pointerStartX.value = null
}

const onLeaderResize = () => {
  if (window.innerWidth >= 1024) {
    goToLeader(activeLeaderIndex.value, 'auto')
  }
  centerOrgChartViewport()
}

const openLeaderDetailModal = (index = activeLeaderIndex.value) => {
  activeLeaderDetailIndex.value = index
  isLeaderDetailModalOpen.value = true
}

const closeLeaderDetailModal = () => {
  isLeaderDetailModalOpen.value = false
}

const centerOrgChartViewport = () => {
  if (window.innerWidth > 640) return
  const viewport = orgChartViewportRef.value
  if (!viewport) return

  const maxScrollLeft = viewport.scrollWidth - viewport.clientWidth
  if (maxScrollLeft <= 0) return
  viewport.scrollLeft = maxScrollLeft / 2
}

const setupScrollFadeAnimations = async () => {
  await nextTick()
  const fadeTargets = document.querySelectorAll('.fade-up')
  if (!fadeTargets.length) return

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

onMounted(async () => {
  await nextTick()
  goToLeader(activeLeaderIndex.value, 'auto')
  setupScrollFadeAnimations()
  centerOrgChartViewport()
  window.addEventListener('resize', onLeaderResize)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', onLeaderResize)
  if (fadeObserver) {
    fadeObserver.disconnect()
  }
})
</script>

<style scoped>
.about-us-gradient-animated {
  background-color: #262626;
  background-image:
    conic-gradient(from 22deg at 13% 12%,
      rgba(255, 255, 255, 0.38) 0deg 48deg,
      rgba(230, 230, 230, 0.32) 48deg 108deg,
      rgba(185, 185, 185, 0.28) 108deg 192deg,
      rgba(130, 130, 130, 0.24) 192deg 278deg,
      rgba(85, 85, 85, 0.22) 278deg 360deg),
    conic-gradient(from 210deg at 78% 26%,
      rgba(242, 242, 242, 0.28) 0deg 58deg,
      rgba(196, 196, 196, 0.26) 58deg 146deg,
      rgba(148, 148, 148, 0.24) 146deg 235deg,
      rgba(98, 98, 98, 0.22) 235deg 320deg,
      rgba(58, 58, 58, 0.2) 320deg 360deg),
    conic-gradient(from 318deg at 52% 82%,
      rgba(255, 255, 255, 0.2) 0deg 70deg,
      rgba(210, 210, 210, 0.22) 70deg 150deg,
      rgba(134, 134, 134, 0.24) 150deg 238deg,
      rgba(60, 60, 60, 0.28) 238deg 360deg),
    linear-gradient(160deg, #ffffff 0%, #d6d6d6 26%, #9b9b9b 48%, #525252 72%, #0f0f0f 100%);
  background-size: 190% 190%, 190% 190%, 220% 220%, 135% 135%;
  background-blend-mode: soft-light, overlay, multiply, normal;
  animation: homeMainGradientFlow 5s ease-in-out infinite;
  will-change: background-position;
}

.about-hero-section {
  min-height: 560px;
}

.about-hero-bg {
  min-height: 100%;
}

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

.about-bg {
  background-color: #eef6ff;
  background-image:
    radial-gradient(ellipse 75% 45% at 0% 0%, rgba(0, 174, 255, 0.95) 0%, transparent 100%),
    radial-gradient(ellipse 55% 90% at 100% 8%, rgba(47, 107, 255, 0.09) 0%, transparent 100%),
    radial-gradient(ellipse 65% 50% at 55% 100%, rgba(18, 59, 143, 0.07) 0%, transparent 100%),
    radial-gradient(ellipse 40% 35% at 85% 75%, rgba(0, 114, 166, 0.7) 0%, transparent 100%);
}

.legal-gradient-animated {
  background-image:
    radial-gradient(circle at 18% 20%, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0) 40%),
    linear-gradient(130deg, #ffffff 0%, #f0f9ff 30%, #d8f0ff 55%, #bfe6ff 78%, #ffffff 100%);
  background-size: 230% 230%;
  animation: legalGradientFlow 6.6s linear infinite;
  will-change: background-position;
}

@keyframes legalGradientFlow {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}

.leader-carousel {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.leader-carousel::-webkit-scrollbar {
  display: none;
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

.mobile-card-gradient {
  background-color: #b8e3bc;
  background-image:
    conic-gradient(from 24deg at 14% 14%,
      rgba(196, 239, 199, 0.7) 0deg 56deg,
      rgba(167, 231, 202, 0.52) 56deg 132deg,
      rgba(114, 199, 214, 0.36) 132deg 222deg,
      rgba(58, 126, 225, 0.34) 222deg 318deg,
      rgba(196, 239, 199, 0.56) 318deg 360deg),
    conic-gradient(from 210deg at 82% 26%,
      rgba(173, 235, 206, 0.5) 0deg 70deg,
      rgba(128, 216, 212, 0.38) 70deg 170deg,
      rgba(63, 147, 228, 0.36) 170deg 285deg,
      rgba(151, 226, 209, 0.48) 285deg 360deg),
    linear-gradient(150deg, #bee9bf 0%, #92e5cc 42%, #2f79e9 100%);
  background-size: 170% 170%, 185% 185%, 125% 125%;
}

.org-chart-viewport {
  width: 100%;
}

.org-chart {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 80rem;
  margin-inline: auto;
  padding: 0 0.5rem 1rem;
}

.org-node {
  width: min(100%, 34rem);
  padding: 1.4rem;
  border-radius: 0.75rem;
  border: 1px solid var(--color-border, #dce6f2);
  background: linear-gradient(to bottom right, #fff, var(--color-sky, #ddf4ff));
  text-align: center;
  box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.08);
}

.org-node-animated {
  background-image:
    radial-gradient(circle at 18% 20%, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0) 40%),
    linear-gradient(130deg, #ffffff 0%, #f0f9ff 30%, #d8f0ff 55%, #bfe6ff 78%, #ffffff 100%);
  background-size: 230% 230%;
  animation: legalGradientFlow 6.6s linear infinite;
  will-change: background-position;
}

.bank-card-animated {
  background-size: 230% 230%;
  animation: bankCardGradientFlow 7.2s linear infinite;
  will-change: background-position;
}

.bank-card-bca {
  background-image:
    radial-gradient(circle at 18% 18%, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0) 42%),
    linear-gradient(132deg, #0066ae 0%, #6095ff 34%, #00eaff 62%, #388dc9 82%, #0066ae 100%);
}

.bank-card-bri {
  background-image:
    radial-gradient(circle at 18% 18%, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0) 42%),
    linear-gradient(132deg, #00529c 0%, #6095ff 34%, #00eaff 62%, #388dc9 82%, #00529c 100%);
}

@keyframes homeMainGradientFlow {
  0% {
    background-position: 0% 0%, 100% 0%, 50% 100%, 0% 0%;
  }

  35% {
    background-position: 42% 24%, 70% 30%, 34% 78%, 30% 22%;
  }

  70% {
    background-position: 18% 58%, 94% 46%, 62% 56%, 78% 74%;
  }

  100% {
    background-position: 0% 0%, 100% 0%, 50% 100%, 0% 0%;
  }
}

@keyframes bankCardGradientFlow {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}

.org-node__role {
  font-size: 1.25rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--color-primary, #123b8f);
}

.org-node__name {
  margin-top: 0.5rem;
  font-size: 1rem;
  font-weight: 600;
  line-height: 1.45;
  color: var(--color-text, #1d2b4f);
}

.org-stem {
  width: 2px;
  height: 1.75rem;
  background: var(--color-border, #dce6f2);
  flex-shrink: 0;
}

.org-split {
  --org-through-offset: 8rem;
  --org-through-stem: 10rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  max-width: 44rem;
}

.org-split__through {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: 100%;
  margin-top: calc(-1 * var(--org-through-offset));
}

.org-stem--bridge {
  height: var(--org-through-stem);
}

.org-split:has(.org-split__cols--three) {
  max-width: min(100%, 72rem);
  width: 100%;
}

.org-split__cols--three {
  --org-gap: clamp(1rem, 2.5vw, 1.5rem);
  width: 100%;
}

.org-split__cols--three::before {
  content: '';
  position: absolute;
  top: 0;
  left: calc((100% - 2 * var(--org-gap)) / 6);
  width: calc(100% - (100% - 2 * var(--org-gap)) / 3);
  height: 2px;
  background: var(--color-border, #dce6f2);
}

.org-split__cols--three .org-split__col {
  flex: 1 1 0;
  min-width: 0;
  max-width: none;
  align-items: center;
}

.org-split__cols--three .org-split__drop {
  align-self: center;
}

.org-split__cols--three .org-node {
  width: 100%;
  align-self: stretch;
  min-height: 100%;
  min-width: 14rem;
  padding: 1.4rem;
  box-sizing: border-box;
}

.org-split__cols {
  --org-gap: clamp(1.5rem, 6vw, 4rem);
  position: relative;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  gap: var(--org-gap);
  width: 100%;
}

.org-split__col {
  position: relative;
  flex: 1;
  max-width: 32rem;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.org-split__cols:not(.org-split__cols--three) .org-split__col:first-child::after,
.org-split__cols:not(.org-split__cols--three) .org-split__col:last-child::after {
  content: '';
  position: absolute;
  top: 0;
  height: 2px;
  background: var(--color-border, #dce6f2);
  width: calc(100% + var(--org-gap));
}

.org-split__cols:not(.org-split__cols--three) .org-split__col:first-child::after {
  left: 50%;
}

.org-split__cols:not(.org-split__cols--three) .org-split__col:last-child::after {
  right: 50%;
}

.org-split__cols--three .org-split__col::after {
  display: none;
}

.org-split__drop {
  width: 2px;
  height: 1.25rem;
  background: var(--color-border, #dce6f2);
  flex-shrink: 0;
}

@media (max-width: 640px) {
  .about-hero-section {
    min-height: 620px;
  }

  .org-chart-viewport {
    overflow: auto;
    -webkit-overflow-scrolling: touch;
    touch-action: pan-x pan-y pinch-zoom;
    border-radius: 1rem;
  }

  .org-chart {
    width: 920px;
    max-width: 920px;
    min-height: 1080px;
    margin-inline: 0;
    padding: 0.2rem 0.5rem 1rem;
    zoom: 0.72;
  }

  .org-split__through {
    margin-top: 0;
  }

  .org-stem--bridge {
    height: 1.75rem;
  }
}
</style>
