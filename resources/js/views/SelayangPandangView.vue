<template>
  <main class="min-h-screen overflow-x-hidden overflow-y-auto text-text md:px-10 md:py-12 programs-themed-bg">
    <div class="fixed top-4 inset-x-0 z-40 px-4 md:px-10">
      <div
        class="mx-auto max-w-7xl rounded-3xl md:rounded-full bg-white/95 backdrop-blur border border-border shadow-xl shadow-[#123B8F]/10 px-4 md:px-6 py-2 md:py-0">
        <div class="hidden md:grid h-16 grid-cols-[1fr_auto_1fr] items-center gap-4">
          <div class="flex items-center gap-1">
            <button v-for="item in quickNavItems" :key="item.id" type="button"
              class="text-[11px] lg:text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors whitespace-nowrap"
              @click="goToHomeSection(item.id)">
              {{ item.label }}
            </button>
          </div>
          <div class="flex items-center justify-end gap-2">
            <router-link to="/selayang-pandang"
              class="text-[11px] lg:text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors whitespace-nowrap">
              Selayang Pandang
            </router-link>
            <router-link to="/about-us"
              class="text-[11px] lg:text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors whitespace-nowrap">
              Tentang Kami
            </router-link>
          </div>
        </div>

        <div class="md:hidden">
          <div class="h-12 flex items-center justify-between gap-3">
            <router-link to="/about-us"
              class="text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors">
              Tentang Kami
            </router-link>
            <button type="button"
              class="inline-flex h-10 w-10 items-center justify-center rounded-md border border-border text-text hover:bg-sky transition-colors"
              aria-label="Toggle menu" @click="isMobileMenuOpen = !isMobileMenuOpen">
              <span class="text-xl leading-none">{{ isMobileMenuOpen ? 'x' : '=' }}</span>
            </button>
          </div>

          <div v-if="isMobileMenuOpen" class="pb-3 pt-2 border-t border-border">
            <div class="grid gap-1">
              <router-link to="/selayang-pandang"
                class="text-left text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors"
                @click="isMobileMenuOpen = false">
                Selayang Pandang
              </router-link>
              <button v-for="item in quickNavItems" :key="`mobile-${item.id}`" type="button"
                class="text-left text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors"
                @click="goToHomeSection(item.id)">
                {{ item.label }}
              </button>
              <router-link to="/signup"
                class="mt-2 px-4 py-2 rounded-md bg-primary text-white text-center text-xs font-bold uppercase tracking-wide hover:bg-secondary transition-colors"
                @click="isMobileMenuOpen = false">
                Masuk Ke Platform
              </router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="mx-auto space-y-8 pb-8">
      <section
        class="fade-up relative overflow-hidden text-center md:text-left md:rounded-[2rem] text-primary shadow-2xl shadow-[#123B8F]/35">
        <div class="relative z-10 p-7 md:p-10 pb-16">
          <div class="max-w-7xl mx-auto flex flex-col md:flex-row mt-16 md:mt-10">
            <div class="w-full md:w-4/12 flex justify-center mb-4 md:mb-0 relative">
              <img :src="taruna2" alt="Selayang Pandang"
                class="md:block hidden w-56 sm:w-64 md:w-8/12 md:fixed md:-left-48 md:-bottom-10 object-cover drop-shadow-2xl" />
            </div>
            <div class="w-full md:w-8/12 py-6 md:py-8 text-left">
              <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white mb-3">Selayang Pandang</p>
              <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-white leading-tight">
                Fondasi Terarah untuk Melangkah Menuju AKPOL
              </h2>
              <div class="mt-6 space-y-4 font-semibold text-white leading-relaxed">
                <p>
                  Selamat datang di <span class="font-bold">Pratistha Cendekia Prestasi</span>, tempat lahirnya
                  calon taruna terbaik yang dipersiapkan secara terarah, disiplin, dan profesional untuk menghadapi
                  seleksi Akademi Kepolisian.
                </p>
                <p>
                  Kami berfokus pada pembinaan akademik, mental, fisik, dan karakter kepemimpinan. Dalam persaingan
                  seleksi yang semakin ketat, kami meyakini keberhasilan tidak hanya ditentukan oleh kecerdasan, tetapi
                  juga oleh strategi belajar yang tepat dan pembinaan yang konsisten.
                </p>
                <p>
                  Melalui metode kursus yang terstruktur, simulasi seleksi yang realistis, serta pendampingan mentor
                  berpengalaman, setiap peserta dipersiapkan agar siap menghadapi seluruh tahapan seleksi AKPOL secara
                  maksimal.
                </p>
                <p>
                  Nama <span class="font-bold">Pratistha</span> melambangkan kehormatan, keteguhan, dan fondasi
                  kuat dalam meraih cita-cita. Nilai inilah yang kami tanamkan: semangat juang, integritas,
                  kedisiplinan,
                  dan mental pantang menyerah.
                </p>
                <p>
                  Kami tidak hanya membina peserta untuk lulus seleksi, tetapi juga membentuk pribadi berkarakter yang
                  siap menjadi generasi pemimpin bangsa.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { ref } from 'vue'

const router = useRouter()
const isMobileMenuOpen = ref(false)

const taruna2 = new URL('../../assets/anggota/taruna2.png', import.meta.url).href
const quickNavItems = [
  { id: 'leaders', label: 'Dewan Pimpinan' },
  { id: 'programs', label: 'Keunggulan Program' },
  { id: 'services', label: 'Layanan Pembinaan' },
  { id: 'choices', label: 'Pilihan Kursus' },
  { id: 'comparison', label: 'Perbandingan Kelas' },
]

const goToHomeSection = (id) => {
  isMobileMenuOpen.value = false
  router.push({ path: '/', hash: `#${id}` })
}
</script>

<style scoped>
.programs-themed-bg {
  background-image:
    radial-gradient(circle at 8% 10%, rgba(255, 255, 255, 0.1) 0 2px, transparent 2px 100%),
    linear-gradient(130deg, #1a1a2d 0%, #202239 55%, #262743 100%);
  background-size: 28px 28px, 100% 100%;
}
</style>
