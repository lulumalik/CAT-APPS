<template>
  <main class="min-h-screen text-text bg-gradient-to-b from-[#52a2ed] via-[#260da7] via-[#5f0ad7] to-[#13c1fc]">
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
            Pratistha Cendekia Prestasi
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
              Daftar Peserta Kursus
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <section id="selayang-pandang-2" class="px-5 md:px-10 py-10">
      <div class="max-w-7xl mx-auto fade-up delay-1 relative">
        <div class="rounded-[2rem] bg-white border border-blue-100/80 shadow-xl shadow-primary/8 p-8 md:p-10 relative overflow-hidden">
          <div class="relative z-30">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary mb-3">Selayang Pandang</p>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-text leading-tight">
              Fondasi Terarah untuk Melangkah Pasti Menuju AKPOL
            </h2>
            <div class="mt-6 space-y-4 font-semibold text-gray-700 leading-relaxed">
              <p>
                Selamat datang di <span class="font-bold text-text">Pratistha Cendekia Prestasi</span>, tempat lahirnya calon taruna terbaik yang dipersiapkan secara terarah, disiplin, dan profesional untuk menghadapi seleksi Akademi Kepolisian.
              </p>
              <p>
                Kami berfokus pada pembinaan akademik, mental, fisik, dan karakter kepemimpinan. Dalam persaingan seleksi yang semakin ketat, kami meyakini keberhasilan tidak hanya ditentukan oleh kecerdasan, tetapi juga oleh strategi belajar yang tepat dan pembinaan yang konsisten.
              </p>
              <p>
                Melalui metode kursus yang terstruktur, simulasi seleksi yang realistis, serta pendampingan mentor berpengalaman, setiap peserta dipersiapkan agar siap menghadapi seluruh tahapan seleksi AKPOL secara maksimal.
              </p>
              <p>
                Nama <span class="font-bold text-text">Pratistha</span> melambangkan kehormatan, keteguhan, dan fondasi kuat dalam meraih cita-cita. Nilai inilah yang kami tanamkan: semangat juang, integritas, kedisiplinan, dan mental pantang menyerah.
              </p>
              <p>
                Kami tidak hanya membina peserta untuk lulus seleksi, tetapi juga membentuk pribadi berkarakter yang siap menjadi generasi pemimpin bangsa.
              </p>
            </div>
            <p class="mt-6 text-lg font-bold text-primary">Bersama Pratistha Cendekia Prestasi, wujudkan langkah pasti menuju AKPOL.</p>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-0 w-28 bottom-0 right-0" />
          <SectionWaveDivider class="absolute -bottom-4 md:-bottom-30 left-0 right-0 z-10" />
        </div>
      </div>
    </section>

    
    <section id="leaders" class="my-10">
      <div class="max-w-7xl mx-auto fade-up fade-up-tight delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-blue-100/70 shadow-xl shadow-primary/8 p-8 md:p-10">
          <!-- <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Dewan Pimpinan</h2> -->
          <p class="text-3xl md:text-4xl font-bold tracking-tight mb-2 mb-8">Profil pejabat dan pimpinan Pratistha Cendekia Prestasi.</p>

          <div class="grid gap-6 relative z-30">
            <article
              v-for="(leader, index) in leaders"
              :key="leader.name"
              class="group rounded-3xl relative bg-gradient-to-br from-[#b0862e] via-[#bea053] to-[#dac887] flex flex-col md:flex-row overflow-hidden border border-blue-100/20 transition-all duration-300 hover:-translate-y-1"
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
                <p class="text-[14px] font-bold uppercase tracking-[0.18em] text-white">Profil {{ leader.jabatan }}</p>
                <h3 class="mt-2 font-extrabold text-2xl md:text-3xl leading-tight tracking-tight text-white">{{ leader.name }}</h3>
                <p class="text-base text-cyan-200 font-bold mt-2">{{ leader.batch }}</p>

                <div class="mt-5 rounded-xl border border-white/20 bg-white/12 px-4 py-6 backdrop-blur-[1px]">
                  <p class="text-[14px] font-bold tracking-[0.14em] text-white uppercase">Posisi Saat Ini</p>
                  <p class="mt-1 text-base md:text-lg font-semibold leading-relaxed text-white">{{ leader.position }}</p>
                </div>

                <div class="mt-5 rounded-xl border border-white/20 bg-white/12 px-4 py-6 backdrop-blur-[1px]">
                  <p class="text-sm font-bold tracking-[0.14em] text-blue-100 uppercase">Jabatan Terakhir</p>
                  <ul class="mt-3 space-y-2.5 text-base font-semibold leading-relaxed text-blue-50">
                    <li v-for="line in leader.highlights" :key="line" class="flex items-start gap-2.5">
                      <span class="mt-2 h-2 w-2 rounded-full bg-cyan-300 shrink-0" />
                      <span>{{ line }}</span>
                    </li>
                  </ul>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section id="members" class="pb-8">
      <div class="max-w-7xl mx-auto fade-up fade-up-tight">
        <div class="rounded-[2rem] bg-gradient-to-br from-white to-secondary border border-gray-100 shadow-xl shadow-black/5 p-8 md:p-10 relative overflow-hidden">
          <h3 class="text-2xl md:text-3xl font-bold tracking-tight mb-2">Staff</h3>
          <p class="text-gray-600 mb-6">Staff pendukung program pembinaan Pratistha Cendekia Prestasi.</p>
          <div class="relative z-20 flex flex-wrap justify-center gap-5">
            <article
              v-for="member in members"
              :key="member.name"
              class="group flex flex-col items-center text-center rounded-2xl border border-border bg-gradient-to-b from-white to-sky/80 p-5 w-[calc(50%-10px)] sm:w-[320px] hover:shadow-lg hover:-translate-y-1 transition-all duration-300"
            >
              <div class="relative mb-4">
                <div class="w-28 h-28 md:w-56 md:h-56 rounded-full overflow-hidden ring-4 ring-white shadow-lg border-2 border-primary/15 group-hover:ring-primary/30 group-hover:border-primary/30 transition-all duration-300">
                  <img :src="member.image" :alt="member.name" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" />
                </div>
                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-secondary flex items-center justify-center shadow-md">
                  <span class="block w-2.5 h-2.5 rounded-full bg-white" />
                </div>
              </div>
              <h4 class="font-bold text-sm md:text-lg leading-snug text-text">{{ member.name }}</h4>
              <p class="text-xs md:text-base text-primary font-semibold mt-1 uppercase tracking-wide">{{ member.jabatan }}</p>
            </article>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-10 w-28 bottom-0 right-0" />
          <SectionWaveDivider class="absolute -bottom-4 md:-bottom-30 left-0 right-0 z-10" />
        </div>
      </div>
    </section>
    
    <section id="programs" class="px-5 md:px-10 pb-8">
      <div class="max-w-7xl mx-auto fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-primary/10 shadow-xl shadow-primary/8 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Keunggulan Program</h2>
          <p class="text-gray-600 mb-8">Ekosistem belajar yang didesain untuk disiplin, konsisten, dan terukur.</p>
          <div class="grid md:grid-cols-2 gap-5 relative z-10">
            <button
              v-for="feature in keyFeatures"
              :key="feature.title"
              type="button"
              class="rounded-2xl border border-border bg-gradient-to-br from-white to-sky p-6 text-left cursor-pointer transition-all hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/40"
              @click="openFeatureModal(feature)"
            >
              <div class="mb-3 inline-flex rounded-lg bg-sky p-2 text-primary">
                <component :is="feature.icon" class="h-5 w-5" />
              </div>
              <h3 class="font-bold text-lg">{{ feature.title }}</h3>
              <p class="text-gray-600 text-sm mt-2 leading-relaxed">{{ feature.desc }}</p>
              <span class="mt-4 inline-flex items-center text-xs font-semibold text-primary">
                Lihat detail
              </span>
            </button>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-0 w-28 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section id="services" class="px-5 md:px-10 pb-8">
      <div class="max-w-7xl mx-auto fade-up delay-1 relative">
        <div class="rounded-[2rem] bg-white border border-green-100/60 shadow-xl shadow-black/5 p-8 md:p-10">
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
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 p-2 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight p-5 md:p-0">Pilihan Kelas Kursus</h2>
          <p class="text-gray-600 p-5 md:p-0">Pilih jalur program sesuai kebutuhan kursus dan target persiapan.</p>
          <div class="grid md:grid-cols-2 gap-5 relative z-30 mt-8">
            <article
              v-for="program in onlinePrograms"
              :key="program.value"
              class="rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1"
              :style="{
                backgroundImage: program.backgroundColor,
                color: program.textColor,
                boxShadow: program.boxShadow,
                border: '1px solid rgba(255,255,255,0.18)',
              }"
            >
              <div class="flex items-center justify-between gap-3">
                <h3 class="font-bold text-lg md:whitespace-nowrap">{{ program.name }}</h3>
                <span
                  class="inline-flex shrink-0 items-center gap-1 text-xs px-2.5 py-1 rounded-full font-bold backdrop-blur-sm"
                  :style="programBadgeStyle(program)"
                >
                  <Crown v-if="program.mode === 'Premium'" class="h-3.5 w-3.5" />
                  {{ program.mode }}
                </span>
              </div>
              <p class="text-sm mt-2 opacity-90 font-semibold">{{ program.summary }}</p>
              <ul class="mt-3 text-sm list-disc pl-5 space-y-1 opacity-85 font-semibold">
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
        <div class="rounded-[2rem] bg-white border border-blue-100/60 shadow-xl shadow-primary/6 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Tabel Perbandingan Fasilitas Kelas Kursus</h2>
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
          Bergabung sebagai peserta, lengkapi pendaftaran secara bertahap, lalu ikuti program kelas kursus online dengan standar pembelajaran profesional.
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

    <Teleport to="body">
      <Transition name="member-slide-fade">
        <div
          v-if="activeFeatureModal"
          class="fixed inset-0 z-[120] flex items-center justify-center bg-black/50 p-4 backdrop-blur-[2px]"
          role="dialog"
          aria-modal="true"
          aria-labelledby="feature-modal-title"
          @click.self="closeFeatureModal"
        >
          <div class="w-full max-w-2xl overflow-hidden rounded-3xl border border-border bg-white shadow-2xl shadow-black/20">
            <div class="flex items-start justify-between gap-4 border-b border-border bg-gradient-to-r from-sky to-white px-5 py-4">
              <div class="flex items-center gap-3">
                <div class="inline-flex rounded-lg bg-sky p-2 text-primary">
                  <component :is="activeFeatureModal.icon" class="h-5 w-5" />
                </div>
                <div>
                  <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">Detail Keunggulan</p>
                  <h3 id="feature-modal-title" class="text-lg md:text-xl font-bold text-text">{{ activeFeatureModal.title }}</h3>
                </div>
              </div>
              <button
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-border text-gray-500 hover:bg-background hover:text-text transition-colors"
                aria-label="Tutup"
                @click="closeFeatureModal"
              >
                <XIcon class="h-4 w-4" />
              </button>
            </div>

            <div class="px-5 py-5 md:px-6 md:py-6">
              <p class="text-sm text-gray-700 leading-relaxed">
                {{ activeFeatureModal.longDesc }}
              </p>
              <ul class="mt-4 space-y-2.5">
                <li v-for="point in activeFeatureModal.points" :key="point" class="flex items-start gap-2.5 text-sm text-gray-700">
                  <span class="mt-1.5 h-2 w-2 rounded-full bg-secondary shrink-0" />
                  <span>{{ point }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </main>
</template>

<script setup>
import { nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
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
const wahyuUrl = new URL('../../assets/anggota/wahyu.jpeg', import.meta.url).href
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
const isMobileMenuOpen = ref(false)
const activeFeatureModal = ref(null)
let wallpaperInterval

let fadeObserver
const quickNavItems = [
  { id: 'leaders', label: 'Dewan Pimpinan' },
  { id: 'programs', label: 'Keunggulan Program' },
  { id: 'services', label: 'Layanan Pembinaan' },
  { id: 'choices', label: 'Pilihan Kursus' },
  { id: 'comparison', label: 'Perbandingan Kelas' },
]

const leaders = [
  {
    name: 'Komjen Pol (P) Drs. H. Nana S. Permana',
    batch: 'Batalion Dharma Angkatan 1968',
    position: 'Ketua Pembina Yayasan Pendidikan Tribakti Langlang Buana',
    jabatan: 'Penasehat',
    highlights: [
      'Wakapolri tahun 1998 - 2000',
      'Pembina strategis pendidikan dan pembinaan kepolisian',
    ],
    image: nanaUrl,
  },
  {
    name: 'Irjen Pol (P) Dr. H Tubagus Anis Angkawijaya, Drs., M.Si',
    batch: 'Bataliyon Anindhita Tahun 1981',
    position: 'Komisaris Pratistha Cendekia Prestasi',
    jabatan: 'Komisaris',
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
    position: 'Direktur Utama Pratistha Cendekia Prestasi',
    jabatan: 'Direktur Utama',
    highlights: [
      'Wakapolda Jawa Tengah tahun 2016 - 2017',
      'Pengarah operasional program kursus',
    ],
    image: awangUrl,
  },
]

function leaderCardStyle(index) {
  const premiumTexture = [
    'radial-gradient(ellipse at 20% 16%, rgba(255,255,255,0.5) 0%, rgba(255,255,255,0.08) 28%, transparent 55%)',
    'radial-gradient(ellipse at 85% 85%, rgba(0,0,0,0.45) 0%, transparent 50%)',
    'linear-gradient(145deg, rgba(255,255,255,0.18) 0%, transparent 34%)',
    'linear-gradient(235deg, rgba(255,255,255,0.08) 0%, transparent 28%)',
    'linear-gradient(328deg, rgba(0,0,0,0.15) 0%, transparent 42%)',
  ]

  const baseGradient =
    index === 0
      ? 'linear-gradient(112deg, #9a0000 0%, #d12a2a 34%, #7a0000 57%, #b01616 78%, #4d0000 100%)'
      : 'linear-gradient(112deg, #00acb2 0%, #33ced2 34%, #008d92 57%, #1abec2 78%, #00666a 100%)'

  const shadowColor = index === 0 ? 'rgba(120, 0, 0, 0.4)' : 'rgba(0, 120, 125, 0.38)'

  return {
    backgroundImage: [...premiumTexture, baseGradient].join(', '),
    boxShadow: `0 14px 36px ${shadowColor}, 0 4px 12px rgba(0,0,0,0.18), inset 0 1px 0 rgba(255,255,255,0.3)`,
  }
}

const members = [
  { name: 'Gilang Nurfahradz Syahni Fasya, S.T', image: gilangUrl, jabatan: 'Direktur' },
  { name: 'AKBP (P) Wahyu suhardini, S.IP', image: wahyuUrl, jabatan: 'Sekretaris' },
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

/** Ringkasan hero: selaras dengan penyelenggara & layanan di halaman ini. */
const heroSelayangPandang = {
  eyebrow: 'Selayang Pandang',
  title: 'Lembaga Kursus Persiapan Seleksi Akademi Kepolisian',
  description:
    'Pratistha Cendekia Prestasi adalah lembaga kursus yang menyelenggarakan program persiapan calon peserta AKADEMI KEPOLISIAN, dengan fasilitas belajar eksklusif dan pembelajaran profesional.',
  bannerAlt: 'Banner Pratistha Cendekia Prestasi — persiapan seleksi Akademi Kepolisian',
}


const keyFeatures = [
  {
    icon: UserCheck,
    title: 'Pengajar dari Ahli & Praktisi',
    desc: 'Tim pembina berpengalaman dari unsur purnawirawan, mentor akademik, dan pelatih kesiapan seleksi.',
    longDesc: 'Pendampingan peserta dilakukan oleh tim lintas bidang agar persiapan berjalan terarah, disiplin, dan sesuai kebutuhan seleksi terkini.',
    points: [
      'Sesi pembinaan dipandu mentor akademik dan pelatih berpengalaman.',
      'Peserta mendapat arahan belajar mingguan yang terukur.',
      'Evaluasi dilakukan berkala untuk menentukan fokus latihan berikutnya.',
    ],
  },
  {
    icon: BookOpenText,
    title: 'Materi Terbaru dan Eksklusif',
    desc: 'Materi disusun berkala, menyesuaikan pola seleksi terbaru, dan hanya dapat diakses peserta terdaftar.',
    longDesc: 'Konten pembelajaran diperbarui secara periodik agar tetap relevan dengan standar seleksi, disertai pembahasan yang mudah dipahami.',
    points: [
      'Bank soal latihan ditata berdasarkan tingkat kesulitan.',
      'Ringkasan materi disiapkan untuk mempercepat pengulangan.',
      'Pembahasan contoh soal membantu peserta memahami pola jawaban.',
    ],
  },
  {
    icon: LineChart,
    title: 'Laporan ke Orang Tua Secara Online',
    desc: 'Perkembangan hasil latihan, nilai tes, dan progres pendaftaran peserta dapat dipantau secara digital.',
    longDesc: 'Informasi perkembangan peserta ditampilkan dalam laporan ringkas agar orang tua bisa memantau progres belajar secara berkala.',
    points: [
      'Rekap latihan dan capaian nilai ditampilkan per periode.',
      'Perubahan progres dapat dipantau tanpa harus datang ke lokasi.',
      'Komunikasi pendampingan menjadi lebih cepat dan transparan.',
    ],
  },
  {
    icon: Warehouse,
    title: 'Tempat Belajar Eksklusif',
    desc: 'Ruang belajar terarah dengan sistem kelas, jadwal, dan evaluasi untuk menjaga fokus pembinaan.',
    longDesc: 'Lingkungan belajar dirancang kondusif untuk menjaga ritme latihan, meningkatkan fokus, dan membangun konsistensi peserta.',
    points: [
      'Jadwal pembinaan disusun dengan alur yang jelas.',
      'Sistem kelas membantu peserta belajar sesuai target program.',
      'Monitoring rutin menjaga kedisiplinan selama masa kursus.',
    ],
  },
]

function openFeatureModal(feature) {
  activeFeatureModal.value = feature
}

function closeFeatureModal() {
  activeFeatureModal.value = null
}

const onlinePrograms = ONLINE_PROGRAMS

const classComparisonColumns = [
  { key: 'karantina', label: 'Kelas Karantina', badgeClass: 'bg-primary text-white' },
  { key: 'reguler', label: 'Kelas Reguler', badgeClass: 'bg-secondary text-white' },
  { key: 'online', label: 'Kelas Online', badgeClass: 'bg-sky text-primary' },
  { key: 'ujian', label: 'Kelas Ujian', badgeClass: 'bg-cream text-text' },
]

const classComparisonRows = [
  { label: 'Masa kursus', values: { karantina: '3 bulan', reguler: '3 bulan', online: '3 bulan', ujian: '3 bulan' } },
  { label: 'Konsultasi', values: { karantina: true, reguler: true, online: true, ujian: true } },
  { label: 'Penginapan siswa (1 kamar 2 orang)', values: { karantina: true, reguler: false, online: false, ujian: false } },
  { label: 'Makan (3 kali sehari)', values: { karantina: true, reguler: false, online: false, ujian: false } },
  { label: 'Seragam siswa (baju olahraga, batik, PDH)', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Psikotes', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Medical check up', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Program jasmani', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Program akademik', values: { karantina: true, reguler: true, online: true, ujian: false } },
  { label: 'Program renang', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Program ujian', values: { karantina: true, reguler: true, online: true, ujian: true } },
  { label: 'Materi online', values: { karantina: true, reguler: true, online: true, ujian: false } },
  { label: 'Transportasi selama program', values: { karantina: true, reguler: false, online: false, ujian: false } },
  { label: 'Tas dan topi', values: { karantina: true, reguler: true, online: false, ujian: false } },
  { label: 'Perlengkapan makan', values: { karantina: true, reguler: false, online: false, ujian: false } },
  { label: 'Masa akses aplikasi', values: { karantina: '1 tahun', reguler: '1 tahun', online: '6 bulan', ujian: '3 bulan' } },
  { label: 'Biaya kursus', values: { karantina: 'Hubungi kami', reguler: 'Hubungi kami', online: 'Rp. 6.500.000', ujian: 'Rp. 500.000' } },
]

const programBadge = (program) => {
  const mode = program.mode
  if (mode === 'Premium') {
    return { label: mode, isVip: true, className: 'bg-sky text-primary border-border' }
  }
  if (mode === 'Reguler') {
    return { label: mode, isVip: false, className: 'bg-mint text-primary border-border' }
  }
  if (mode === 'Full Online') {
    return { label: mode, isVip: false, className: 'bg-cream text-text border-border' }
  }
  return { label: mode, isVip: false, className: 'bg-background text-text border-border' }
}

const programBadgeStyle = (program) => {
  if (program.mode === 'Premium') {
    return {
      background: 'rgba(255,255,255,0.88)',
      color: '#7a4a00',
      border: '1px solid rgba(180,140,30,0.5)',
    }
  }
  return {
    background: 'rgba(255,255,255,0.18)',
    color: program.textColor,
    border: '1px solid rgba(255,255,255,0.35)',
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
})

onUnmounted(() => {
  if (wallpaperInterval) {
    clearInterval(wallpaperInterval)
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
