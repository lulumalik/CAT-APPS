<template>
  <main class="min-h-screen bg-background text-text px-5 md:px-10 py-8 md:py-12">
    <div class="mx-auto space-y-8">
      <section
        class="relative overflow-hidden rounded-[2rem] text-white shadow-2xl shadow-[#123B8F]/35 p-7 md:p-10 pb-16 md:pb-20 bg-cover bg-center"
        :style="{ backgroundImage: `url('${backgroundAboutUrl}')` }"
      >
        <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-br from-primary/20 via-[#1E4CA8]/48 to-secondary/90" />
        <img :src="patternUrl" alt="Pattern" class="absolute opacity-25 w-40 md:w-52 -bottom-8 -right-6 z-[1]" />
        <div class="relative z-10">
          <router-link to="/"
            class="inline-flex items-center gap-2 rounded-full bg-white/15 px-4 py-2 text-sm font-semibold hover:bg-white/25 transition-colors">
            <ArrowLeft class="h-4 w-4" />
            Kembali ke Beranda
          </router-link>
          <p class="mt-5 text-xs font-bold uppercase tracking-[0.2em] text-white/80">Tentang Kami</p>
          <h1 class="mt-2 text-3xl md:text-5xl font-black leading-tight">
            PT. Pratistha Training Center Indonesia
          </h1>
          <p class="mt-5 max-w-4xl text-sm font-bold md:text-base text-white/90 leading-relaxed">
            Pratistha Training Center hadir sebagai pusat pembinaan persiapan Akpol yang membentuk kemampuan akademik,
            mental, fisik, serta karakter kepemimpinan peserta secara terarah, disiplin, dan profesional.
          </p>
        </div>
      </section>

      <section
        class="relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-160 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 top-12 z-30 right-0 opacity-90" />
        
        <div class="relative z-20">
          <h2 class="mt-2 text-2xl md:text-4xl font-black text-center tracking-tight text-text">
            Pimpinan PT. Pratistha Training Center Indonesia
          </h2>

          <div class="my-24 grid md:grid-cols-3 gap-12 items-end">
            <article v-for="leader in heroLeaders" :key="leader.name"
              class="rounded-2xl border border-border shadow-xl bg-background overflow-hidden hover:shadow-lg transition-all duration-300"
              :class="leader.featured ? 'md:scale-[1.05] md:-translate-y-2 shadow-xl shadow-[#123B8F]/10' : 'md:scale-[0.8]'">
              <img :src="leader.image" :alt="leader.name"
                class="h-[360px] md:h-[420px] w-full object-cover object-top" />
              <div class="p-4">
                <p class="text-[11px] uppercase tracking-[0.16em] text-primary font-bold">{{ leader.role }}</p>
                <h3 class="mt-1 text-base md:text-lg font-bold leading-snug text-text">{{ leader.name }}</h3>
              </div>
            </article>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-30 left-0 right-0 z-10" />
      </section>

      <section
        class="relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-16 md:pb-20 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 top-12 right-0 opacity-90" />
        <div class="relative z-20">
          <div class="flex items-center gap-3">
            <Users class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Struktur Organisasi</h2>
          </div>
          <p class="mt-2 text-gray-600">Diagram jabatan inti PT. Pratistha Training Center Indonesia.</p>

          <div class="my-12 space-y-6">
            <div v-for="(row, rowIndex) in organizationRows" :key="rowIndex">
              <div class="grid gap-12" :class="row.gridClass">
                <article v-for="item in row.items" :key="item.role"
                  class="rounded-xl border border-border bg-gradient-to-br from-white to-sky p-4 text-center shadow-lg">
                  <p class="text-lg uppercase tracking-[0.15em] text-primary font-bold">{{ item.role }}</p>
                  <p class="mt-2 text-sm font-semibold text-text">{{ item.name || '-' }}</p>
                </article>
              </div>
              <div v-if="rowIndex < organizationRows.length - 1" class="flex justify-center py-1">
                <div class="h-6 w-px bg-border border-2 border-border mt-6" />
              </div>
            </div>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-30 left-0 right-0 z-10" />
      </section>

      <section
        class="relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-16 md:pb-20 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 bottom-12 right-0 opacity-90" />
        <div class="relative z-20">
          <div class="flex items-center gap-3">
            <FileBadge class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Legalitas Perusahaan</h2>
          </div>
          <p class="mt-2 text-gray-600">Silakan lengkapi nomor dokumen resmi perusahaan di bawah ini.</p>

          <div class="mt-6 grid sm:grid-cols-2 gap-4">
            <article v-for="doc in legalDocuments" :key="doc.label"
              class="rounded-xl border border-border bg-background p-4 shadow-lg">
              <p class="text-xs uppercase tracking-[0.15em] font-bold text-primary">{{ doc.label }}</p>
              <p class="mt-2 text-base font-semibold text-text">{{ doc.value || '-' }}</p>
            </article>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-30 left-0 right-0 z-10" />
      </section>

      <section
        class="relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-16 md:pb-20 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 bottom-12 right-0 opacity-90" />
        <div class="relative z-20">
          <div class="flex items-center gap-3">
            <Phone class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Kontak Kami</h2>
          </div>
          <p class="mt-2 text-gray-600">Kanal komunikasi resmi PT. Pratistha Training Center Indonesia.</p>

          <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article v-for="contact in contactChannels" :key="contact.label"
              class="rounded-xl border border-border bg-gradient-to-br from-white to-sky shadow-lg p-4">
              <div class="flex items-center gap-2 text-primary">
                <component :is="contact.icon" class="h-4 w-4" />
                <p class="text-sm font-bold">{{ contact.label }}</p>
              </div>
              <p class="mt-2 text-sm font-semibold text-text break-all">{{ contact.value || '-' }}</p>
            </article>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-30 left-0 right-0 z-10" />
      </section>

      <section
        class="relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-16 md:pb-20 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 bottom-12 right-0 opacity-90" />
        <div class="relative z-20">
          <div class="flex items-center gap-3">
            <Image class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Galeri Kegiatan</h2>
          </div>
          <p class="mt-2 text-gray-600">Dokumentasi suasana pembinaan dan aktivitas pelatihan peserta.</p>
          <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article v-for="photo in galleryPhotos" :key="photo.src"
              class="rounded-2xl overflow-hidden border border-border bg-background shadow-lg">
              <img :src="photo.src" :alt="photo.alt" class="h-56 w-full object-cover" />
            </article>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-30 left-0 right-0 z-10" />
      </section>
    </div>
  </main>
</template>

<script setup>
import SectionWaveDivider from '@/components/SectionWaveDivider.vue'
import { ArrowLeft, FileBadge, Globe, Image, Instagram, Mail, Music2, Phone, Users } from 'lucide-vue-next'

const patternUrl = new URL('../../assets/Pattern.svg', import.meta.url).href
const backgroundAboutUrl = new URL('../../assets/background-about.jpg', import.meta.url).href
const wallpaper1Url = new URL('../../assets/wallpaper/1.webp', import.meta.url).href
const wallpaper2Url = new URL('../../assets/wallpaper/2.jpeg', import.meta.url).href
const wallpaper3Url = new URL('../../assets/wallpaper/3.webp', import.meta.url).href
const wallpaper4Url = new URL('../../assets/wallpaper/4.jpeg', import.meta.url).href
const bannerUrl = new URL('../../assets/Banner.png', import.meta.url).href
const activityBookUrl = new URL('../../assets/book.jpg', import.meta.url).href
const nanaUrl = new URL('../../assets/bpk_nana.png', import.meta.url).href
const tubagusUrl = new URL('../../assets/bpk_tubagus.jpg', import.meta.url).href
const awangUrl = new URL('../../assets/bpk_awang.jpg', import.meta.url).href

const heroLeaders = [
  {
    name: 'Irjen Pol (P) Dr. H Tubagus Anis Angkawijaya, Drs., M.Si',
    role: 'Komisaris',
    image: tubagusUrl,
    featured: false,
  },
  {
    name: 'Komjen Pol (P) Drs. H. Nana S. Permana',
    role: 'Penasehat',
    image: nanaUrl,
    featured: true,
  },

  {
    name: 'Brigjen Pol (P) Drs. H. Awang Anwarudin, MH',
    role: 'Direktur Utama',
    image: awangUrl,
    featured: false,
  },
]

const organizationRows = [
  {
    gridClass: 'md:max-w-sm mx-auto',
    items: [{ role: 'Penasehat', name: 'Komjen Pol (P) Drs. H. Nana S. Permana' }],
  },
  {
    gridClass: 'md:max-w-2xl mx-auto sm:grid-cols-2',
    items: [
      { role: 'Komisaris', name: 'Irjen Pol (P) Dr. H Tubagus Anis Angkawijaya, Drs., M.Si' },
      { role: 'Direktur Utama', name: 'Brigjen Pol (P) Drs. H. Awang Anwarudin, MH' },
    ],
  },
  {
    gridClass: 'md:max-w-4xl mx-auto sm:grid-cols-3',
    items: [
      { role: 'Direktur', name: 'Gilang Nurfahradz Syahni Fasya, S.T' },
      { role: 'Sekretaris', name: 'Win Tasajat, S.Pd.I., M.Si' },
      { role: 'Bendahara', name: 'KBP (P) Dra.Rina Regina' },
    ],
  },
  {
    gridClass: 'md:max-w-2xl mx-auto sm:grid-cols-2',
    items: [
      { role: 'Internal', name: 'AKBP (P) Dra.NATASHA YUNITA POSPOS, S.H. M.T.C.P' },
      { role: 'Eksternal', name: 'Kompol (P) Tutik' },
    ],
  },
]

const legalDocuments = [
  { label: 'Nomor SK Kemenkumham', value: '-' },
  { label: 'NPWP Perusahaan', value: '-' },
  { label: 'Nomor Induk Berusaha (NIB)', value: '-' },
  { label: 'Nomor Izin Operasional', value: '-' },
]

const contactChannels = [
  { label: 'Website', value: '-', icon: Globe },
  { label: 'Email', value: '-', icon: Mail },
  { label: 'Telepon / WhatsApp', value: '-', icon: Phone },
  { label: 'Instagram', value: '-', icon: Instagram },
  { label: 'TikTok', value: '-', icon: Music2 },
]

const galleryPhotos = [
  { src: bannerUrl, alt: 'Banner Pratistha Training Center', caption: 'Program pembinaan terarah dan profesional.' },
  { src: wallpaper1Url, alt: 'Kegiatan pelatihan 1', caption: 'Suasana pembinaan akademik dan kesiapan seleksi.' },
  { src: wallpaper2Url, alt: 'Kegiatan pelatihan 2', caption: 'Latihan terukur untuk meningkatkan konsistensi peserta.' },
  { src: wallpaper3Url, alt: 'Kegiatan pelatihan 3', caption: 'Pendampingan intensif oleh mentor berpengalaman.' },
  { src: wallpaper4Url, alt: 'Kegiatan pelatihan 4', caption: 'Pembinaan fisik, mental, dan karakter kepemimpinan.' },
  { src: activityBookUrl, alt: 'Materi belajar', caption: 'Dokumentasi materi belajar dan simulasi seleksi.' },
]
</script>
