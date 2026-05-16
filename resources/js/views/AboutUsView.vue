<template>
  <main class="min-h-screen bg-background text-text md:px-10 md:py-12">
    <div class="mx-auto space-y-8">
      <section
        class="fade-up relative overflow-hidden text-center md:rounded-[2rem] text-white shadow-2xl shadow-[#123B8F]/35 p-7 md:p-10 pb-16 md:pb-20 bg-cover bg-center"
        :style="{ backgroundImage: `url('${backgroundAboutUrl}')` }">
        <div
          class="absolute inset-0 md:rounded-[2rem] bg-gradient-to-br from-primary/20 via-[#1E4CA8]/48 to-secondary/90" />
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
          <p class="mt-5 text-sm font-bold md:text-base text-white/90 leading-relaxed">
            Pratistha Training Center hadir sebagai pusat pembinaan persiapan Akpol yang membentuk kemampuan akademik,
            mental, fisik, serta karakter kepemimpinan peserta secara terarah, disiplin, dan profesional.
          </p>
        </div>
      </section>

      <section
        class="fade-up relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 top-12 z-30 right-0 opacity-90" />

        <div class="relative z-20">
          <h2 class="mt-2 text-2xl md:text-4xl font-black text-center tracking-tight text-text">
            Pimpinan PT. Pratistha Training Center Indonesia
          </h2>

          <div class="mt-8 md:mt-16">
            <div ref="leaderCarouselRef"
              class="leader-carousel flex gap-5 md:gap-8 overflow-x-auto px-[11%] md:px-[16%] py-6 snap-x snap-mandatory scroll-smooth" >
              <article v-for="(leader, index) in heroLeaders" :key="leader.name" @click="goToLeader(index)"
                :ref="(el) => setLeaderSlideRef(el, index)"
                class="shrink-0 basis-[78%] md:basis-[52%] lg:basis-[42%] snap-center rounded-2xl border border-border shadow-xl bg-background overflow-hidden transition-all duration-300"
                :class="activeLeaderIndex === index
                  ? 'scale-100 md:scale-[1.03] md:-translate-y-2 shadow-xl shadow-[#123B8F]/20 opacity-100'
                  : 'scale-[0.88] md:scale-[0.9] opacity-70'">
                <img :src="leader.image" :alt="leader.name"
                  class="h-[360px] md:h-[420px] w-full object-cover object-top" />
                <div class="p-4">
                  <p class="text-[11px] uppercase tracking-[0.16em] text-primary font-bold">{{ leader.role }}</p>
                  <h3 class="mt-1 text-base md:text-lg font-bold leading-snug text-text">{{ leader.name }}</h3>
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
        <SectionWaveDivider class="absolute -bottom-5 md:-bottom-30 left-0 right-0 z-10" />
      </section>

      <section
        class="fade-up relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-16 md:pb-20 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 top-12 right-0 opacity-90" />
        <div class="relative z-20">
          <div class="flex items-center justify-center gap-3">
            <Users class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Struktur Organisasi</h2>
          </div>
          <p class="mt-2 text-gray-600 text-center">Diagram jabatan inti PT. Pratistha Training Center Indonesia.</p>

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
        <SectionWaveDivider class="absolute -bottom-5 md:-bottom-30 left-0 right-0 z-10" />
      </section>

      <section
        class="fade-up relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-16 md:pb-20 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 bottom-12 right-0 opacity-90" />
        <div class="relative z-20">
          <div class="flex items-center justify-center gap-3">
            <FileBadge class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Legalitas Perusahaan</h2>
          </div>
          <p class="mt-2 text-gray-600 text-center">Silakan lengkapi nomor dokumen resmi perusahaan di bawah ini.</p>

          <div class="mt-6 grid sm:grid-cols-2 gap-4">
            <article v-for="doc in legalDocuments" :key="doc.label"
              class="rounded-xl border border-border bg-background p-4 shadow-lg">
              <p class="text-xs uppercase tracking-[0.15em] font-bold text-primary text-center">{{ doc.label }}</p>
              <p class="mt-2 text-base font-semibold text-text text-center">{{ doc.value || '-' }}</p>
            </article>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-5 md:-bottom-30 left-0 right-0 z-10" />
      </section>

      <section
        class="fade-up relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-16 md:pb-20 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 bottom-12 right-0 opacity-90" />
        <div class="relative z-20">
          <div class="flex items-center justify-center gap-3">
            <Phone class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Kontak Kami</h2>
          </div>
          <p class="mt-2 text-gray-600 text-center">Kanal komunikasi resmi PT. Pratistha Training Center Indonesia.</p>
          <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article v-for="contact in contactChannels" :key="contact.label"
              class="rounded-xl border border-border bg-gradient-to-br from-white to-sky shadow-lg p-4">
              <div class="flex items-center justify-center gap-2 text-primary">
                <component :is="contact.icon" class="h-4 w-4" />
                <p class="text-sm font-bold text-center">{{ contact.label }}</p>
              </div>
              <p class="mt-2 text-sm font-semibold text-text break-all text-center">{{ contact.value || '-' }}</p>
            </article>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-5 md:-bottom-30 left-0 right-0 z-10" />
      </section>

      <section
        class="fade-up relative rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-7 md:p-10 pb-16 md:pb-20 overflow-hidden">
        <img :src="patternUrl" alt="Pattern" class="absolute w-28 bottom-12 right-0 opacity-90" />
        <div class="relative z-20">
          <div class="flex items-center justify-center gap-3">
            <Image class="h-6 w-6 text-primary" />
            <h2 class="text-2xl md:text-3xl font-bold tracking-tight text-center">Galeri Kegiatan</h2>
          </div>
          <p class="mt-2 text-gray-600 text-center">Dokumentasi suasana pembinaan dan aktivitas pelatihan peserta.</p>
          <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <article v-for="photo in galleryPhotos" :key="photo.src"
              class="rounded-2xl overflow-hidden border border-border bg-background shadow-lg">
              <img :src="photo.src" :alt="photo.alt" class="h-56 w-full object-cover" />
            </article>
          </div>
        </div>
        <SectionWaveDivider class="absolute -bottom-5 md:-bottom-30 left-0 right-0 z-10" />
      </section>
    </div>
  </main>
</template>

<script setup>
import SectionWaveDivider from '@/components/SectionWaveDivider.vue'
import { ArrowLeft, FileBadge, Globe, Image, Instagram, Mail, Music2, Phone, Users } from 'lucide-vue-next'
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue'

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

const leaderCarouselRef = ref(null)
const leaderSlideRefs = ref([])
const activeLeaderIndex = ref(Math.max(0, heroLeaders.findIndex((leader) => leader.featured)))
const touchStartX = ref(null)
const pointerStartX = ref(null)
const swipeThreshold = 45
let fadeObserver

const setLeaderSlideRef = (element, index) => {
  if (!element) return
  leaderSlideRefs.value[index] = element
}

const goToLeader = (index, behavior = 'smooth') => {
  const target = leaderSlideRefs.value[index]
  if (!target) return
  target.scrollIntoView({
    behavior,
    block: 'nearest',
    inline: 'center',
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

const onLeaderResize = () => goToLeader(activeLeaderIndex.value, 'auto')

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

.leader-carousel {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

.leader-carousel::-webkit-scrollbar {
  display: none;
}
</style>
