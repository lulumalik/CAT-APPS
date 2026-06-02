<template>
  <main class="auth-right-pane min-h-screen text-text">
    <span class="auth-visual-stripe fixed top-0 left-0 w-full h-full" style="z-index: -10;" />
    <div v-if="!isAuthenticated" class="fixed top-4 inset-x-0 z-40 px-4 md:px-10">
      <div
        class="page-shell rounded-3xl md:rounded-full bg-white/95 backdrop-blur border border-border shadow-xl shadow-[#123B8F]/10 px-4 md:px-6 py-2 md:py-0">
        <div class="hidden md:flex h-16 items-center justify-center gap-1 lg:gap-2 flex-wrap">
          <div class="flex items-center gap-1">
            <button v-for="item in quickNavItems" :key="item.id" type="button"
              class="text-[11px] lg:text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors whitespace-nowrap"
              @click="scrollToSection(item.id)">
              {{ item.label }}
            </button>
          </div>
          <div class="flex items-center gap-2">
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
          <div class="h-12 relative flex items-center justify-center gap-2">
            <router-link to="/about-us"
              class="text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors">
              Tentang Kami
            </router-link>
            <button type="button"
              class="absolute right-0 inline-flex h-10 w-10 items-center justify-center rounded-md border border-border text-text hover:bg-sky transition-colors"
              aria-label="Toggle menu" @click="isMobileMenuOpen = !isMobileMenuOpen">
              <span class="text-xl leading-none">{{ isMobileMenuOpen ? 'x' : '=' }}</span>
            </button>
          </div>

          <div v-if="isMobileMenuOpen" class="pb-3 pt-2 border-t border-border">
            <div class="grid gap-1 text-center">
              <router-link to="/selayang-pandang"
                class="text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors"
                @click="isMobileMenuOpen = false">
                Selayang Pandang
              </router-link>
              <button v-for="item in quickNavItems" :key="`mobile-${item.id}`" type="button"
                class="text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors"
                @click="scrollToSection(item.id)">
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

    <section id="hero" class="relative overflow-hidden rounded-none px-4 md:px-10 pt-24 md:pt-28 pb-8">
      <div
        class="page-shell relative overflow-hidden rounded-[2rem] border border-white/20 bg-gradient-to-r from-[#333333] via-[#636363] to-[#595959] px-5 py-8 md:px-10 md:py-12 shadow-2xl shadow-primary/30">
        <div
          class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_20%_15%,rgba(255,255,255,0.18),transparent_48%),radial-gradient(circle_at_85%_80%,rgba(255,255,255,0.2),transparent_52%)]" />
        <div class="relative z-10 grid gap-8 lg:grid-cols-[1.25fr_0.95fr] items-center">
          <div>
            <div
              class="inline-flex items-center gap-2 rounded-full border border-yellow-300/70 bg-yellow-300/10 px-4 py-1.5 text-[11px] font-extrabold uppercase tracking-[0.18em] text-yellow-200">
              Program Persiapan Taruna Akademi POLRI
            </div>
            <h1 class="mt-5 text-3xl md:text-5xl font-black leading-[1.1] tracking-tight text-white">
              Persiapan AKPOL yang Intensif, Eksklusif, dan Terarah.
            </h1>
            <p class="mt-4 max-w-2xl text-sm md:text-base text-blue-100 font-semibold leading-relaxed">
              ​Bingung memulai persiapan AKPOL dari mana? Serahkan pada ahlinya. Pratistha Cendekia Prestasi menyediakan ekosistem belajar profesional dengan fasilitas premium untuk memastikan setiap calon peserta tampil maksimal di seluruh tahapan seleksi.
            </p>
            <p class="mt-3 max-w-2xl text-xs md:text-sm text-blue-50 font-semibold">
              Alamat kantor Jl. Sukamaju no. 142, Cipadung Kulon, Kec. Panyileukan, Kota Bandung - Jabar 40614
            </p>

            <div class="mt-6 flex flex-wrap items-center gap-3">
              <router-link to="/free-tryout"
                class="cta-tryout-animated px-7 py-3 rounded-full text-white text-sm md:text-base font-bold transition-all">
                Konsultasi Sekarang
              </router-link>
              <router-link type="button"
                class="px-7 py-3 rounded-full cursor-pointer border-2 border-blue-100 text-white text-sm md:text-base font-bold bg-white/10 hover:bg-white/20 transition-all"
                to="/signup">
                Daftar Peserta Kursus
              </router-link>
            </div>

            <div class="mt-6 rounded-2xl border border-red-200/40 bg-orange-500/70 pr-4 pt-4 pb-4 pl-36 md:pl-40 backdrop-blur-sm relative">
              <img src="../../assets/promo.png" alt="Promo Early Bird" class=" absolute top-0 md:-top-8 -left-8 z-10 object-cover w-44" />
              <p class="text-[11px] md:text-sm font-extrabold uppercase tracking-[0.16em] text-white">Promo Early Bird
              </p>
              <p class="mt-1 text-sm md:text-base font-bold text-white">Dapatkan promo spesial untuk <b class="text-[gold]">10 Pendaftar
                Pertama.</b></p>
            </div>
          </div>

          <div class="relative mx-auto w-full max-w-md">
            <div class="grid grid-cols-2 gap-3 rounded-3xl border border-white/25 bg-white/10 p-3 shadow-2xl">
              <div class="overflow-hidden rounded-2xl bg-[#333]">
                <img :src="taruna" alt="Taruna" class="h-[300px] w-full object-cover object-top" />
              </div>
              <div class="overflow-hidden rounded-2xl bg-[#333]">
                <img :src="taruni" alt="Taruni" class="h-[300px] w-full object-cover object-top" />
              </div>
            </div>
            <div
              class="absolute -bottom-6 left-1/2 -translate-x-1/2 w-[92%] rounded-2xl border border-white/25 bg-[#333]/90 px-4 py-3 text-center shadow-xl">
              <p class="text-3xl font-black text-white">Garansi</p>
              <p class="text-sm font-semibold text-blue-100">100% membentuk calon-calon polisi terbaik</p>
            </div>
          </div>
        </div>

        <div class="relative z-10 mt-14 grid grid-cols-2 md:grid-cols-4 gap-3">
          <article class="rounded-2xl border border-white/25 bg-white/10 p-4 text-center">
            <div class="flex items-center justify-center gap-2">
              <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white">
                <Check class="h-5 w-5" />
              </div>
              <p class="text-3xl font-black text-white">92%</p>
            </div>
            <p class="mt-1 text-xs md:text-sm font-semibold text-blue-100">Tingkat Kelulusan</p>
          </article>
          <article class="rounded-2xl border border-white/25 bg-white/10 p-4 text-center">
            <div class="flex items-center justify-center gap-2">
              <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white">
                <NotebookPen class="h-5 w-5" />
              </div>
              <p class="text-3xl font-black text-white">99%</p>
            </div>
            <p class="mt-1 text-xs md:text-sm font-semibold text-blue-100">Materi Prediktif</p>
          </article>
          <article class="rounded-2xl border border-white/25 bg-white/10 p-4 text-center">
            <div class="flex items-center justify-center gap-2">
              <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white">
                <Crown class="h-5 w-5" />
              </div>
              <p class="text-3xl font-black text-white">20+</p>
            </div>
            <p class="mt-1 text-xs md:text-sm font-semibold text-blue-100">Tahun Pengalaman</p>
          </article>
          <article class="rounded-2xl border border-white/25 bg-white/10 p-4 text-center">
            <div class="flex items-center justify-center gap-2">
              <div class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-white">
                <GraduationCap class="h-5 w-5" />
              </div>
              <p class="text-3xl font-black text-white">100%</p>
            </div>
            <p class="mt-1 text-xs md:text-sm font-semibold text-blue-100">Pengajar Profesional</p>
          </article>
        </div>
      </div>
    </section>

    <div class="px-4 md:px-10 pb-6">
      <div
        class="page-shell rounded-2xl border border-amber-200/50 bg-gradient-to-r from-[#333333] to-[#595959] px-5 py-4 text-center shadow-lg shadow-primary/25">
        <p class="text-xl md:text-2xl font-black uppercase tracking-wide text-amber-200">
          Diawasi Langsung Oleh Purnawirawan Polri
        </p>
        <p class="mt-1 text-sm md:text-base text-blue-100 font-semibold">
          Dibina langsung oleh para Purnawirawan POLRI yang berpengalaman.
        </p>
      </div>
    </div>

    <section id="leaders">
      <div class="page-shell px-4 md:px-0">
        <div class="overflow-visible">
          <div class="leaders-demo-layout">
            <div class="leaders-demo-content order-2 xl:order-1">
              <p class="leaders-demo-eyebrow">Profil</p>
              <h3 class="leaders-demo-name">{{ leaders[activeLeaderIndex].name }}</h3>
              <p class="leaders-demo-batch">{{ leaders[activeLeaderIndex].batch }}</p>

              <p class="leaders-demo-heading mt-8">Posisi Saat Ini</p>
              <p class="leaders-demo-position">{{ leaders[activeLeaderIndex].position }}</p>

              <p class="leaders-demo-heading mt-8">Jabatan Terakhir</p>
              <ul class="leaders-demo-list">
                <li v-for="line in leaders[activeLeaderIndex].highlights" :key="line">{{ line }}</li>
              </ul>
            </div>

            <div class="leaders-demo-visual rounded-md relative order-1 xl:order-2 top-6 mb-24 xl:mb-0 xl:top-0">
              <Transition :name="leaderTransitionName" mode="out-in">
                <div class="relative xl:right-48 xl:top-10 pb-16 xl:pb-0">
                  <img :key="`leader-main-image-${activeLeaderIndex}`" :src="leaders[activeLeaderIndex].image"
                    :alt="leaders[activeLeaderIndex].name" class="leaders-demo-image rounded-md" />
                </div>
              </Transition>
              <div ref="leaderCarouselRef"
                class="leader-vertical-carousel absolute left-1/2 -translate-x-1/2 -bottom-12 mt-4 z-20 grid grid-flow-col auto-cols-[6.8rem] grid-rows-1 gap-3 w-[calc(100%-1rem)] max-w-xl overflow-x-auto overflow-y-hidden snap-x snap-mandatory scroll-smooth xl:left-auto xl:translate-x-0 xl:-right-16 xl:top-32 xl:bottom-auto xl:grid-flow-row xl:auto-cols-auto xl:grid-cols-2 xl:w-[16rem] xl:max-h-none xl:gap-2.5 xl:overflow-visible xl:snap-none"
                @scroll.passive="onLeaderScroll">
                <article v-for="(leader, index) in leaders" :key="leader.name"
                  :ref="(el) => setLeaderSlideRef(el, index)"
                  class="shrink-0 snap-center cursor-pointer overflow-hidden w-10/12 xl:w-full mx-auto rounded-md border border-border bg-[#1c1d2f] p-1 xl:p-1.5 shadow transition-all duration-300 aspect-[3/4]"
                  :class="activeLeaderIndex === index
                    ? 'scale-[1.01] border-3 border-white shadow-lg shadow-white/20'
                    : 'xl:opacity-70 hover:opacity-100'" @click="goToLeader(index)">
                  <img :src="leader.image" :alt="leader.name" class="h-24 md:h-28 xl:h-full w-full rounded-md object-cover" />
                </article>
              </div>
              <div class="xl:hidden absolute -bottom-24 left-1/2 -translate-x-1/2 z-30 flex items-center gap-3">
                <button type="button"
                  class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/40 bg-[#1c1d2f]/85 text-white shadow transition disabled:opacity-45 disabled:cursor-not-allowed"
                  :disabled="activeLeaderIndex <= 0" @click="stepLeader(-1)" aria-label="Leader sebelumnya">
                  <ChevronLeft class="h-4 w-4" />
                </button>
                <button type="button"
                  class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/40 bg-[#1c1d2f]/85 text-white shadow transition disabled:opacity-45 disabled:cursor-not-allowed"
                  :disabled="activeLeaderIndex >= leaders.length - 1" @click="stepLeader(1)" aria-label="Leader berikutnya">
                  <ChevronRight class="h-4 w-4" />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="programs">
      <div class="page-shell fade-up delay-2 relative px-4 md:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8 lg:items-center">
          <div class="px-1 md:px-8 gap-8 relative z-10 mt-10">
            <div class="text-2xl md:text-4xl font-bold tracking-tight text-white md:w-full">
              Mengapa Harus Pilih Pratistha Cendekia Prestasi ?
            </div>
            <hr class="border-white/60 my-4 w-44 border-b-2" />
            <div v-for="feature in keyFeatures" :key="feature.title" class="relative mt-4 md:mt-2 w-full">
              <div
                class="absolute inset-0 top-3 -left-3 rounded-2xl bg-white/30 backdrop-blur-sm -z-10 pointer-events-none">
              </div>
              <button type="button"
                class="service-gradient-animated rounded-2xl shadow-xl relative z-10 p-5 md:p-6 text-left cursor-pointer transition-all translate-y-2 hover:-translate-y-2 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/40"
                @click="openFeatureModal(feature)">
                <div class="flex items-center gap-2">
                  <div class="inline-flex rounded-lg bg-[gold] p-2 text-primary relative z-20">
                    <component :is="feature.icon" class="h-5 w-5" />
                  </div>
                  <h3 class="font-bold text-md relative text-xl z-20">{{ feature.title }}</h3>
                </div>
                <p class="text-gray-600 text-xs md:text-sm mt-2 text-lg leading-relaxed relative z-20 font-semibold">{{
                  feature.desc }}</p>
                <span class="mt-2 inline-flex underline items-center text-sm font-semibold text-primary relative z-20">
                  Lihat detail
                </span>
              </button>
            </div>
          </div>
          <div class="hidden xl:block absolute  md:w-10/12 xl:relative xl:top-8 xl:left-0">
            <img src="../../assets/group.png" alt="group" class="w-9/12 object-cover" />
            <img src="../../assets/group2.png" alt="group2" class="w-[73%] object-cover absolute bottom-0 left-56" />
          </div>
        </div>
      </div>
    </section>

    <section id="choices" class="px-5 md:px-10 pb-8 relative mt-10">
      <div class="page-shell fade-up delay-2 relative">
        <div>
          <div class="text-2xl md:text-4xl font-bold tracking-tight text-white text-left">
            Pilihan Kelas Kursus
          </div>
          <hr class="border-white/60 mt-4 w-44 border-b-2" />
          <div class="grid md:grid-cols-2 gap-5 relative z-30 mt-10">
            <article v-for="program in onlinePrograms" :key="program.value"
              class="program-gradient-animated rounded-2xl p-6 transition-all duration-300 hover:-translate-y-1" :style="{
                backgroundImage: program.backgroundColor,
                color: program.textColor,
                boxShadow: program.boxShadow,
                border: '1px solid rgba(255,255,255,0.18)',
              }">
              <div class="flex items-center justify-between gap-3">
                <h3 class="font-bold text-lg md:whitespace-nowrap">{{ program.name }}</h3>
                <span
                  class="inline-flex shrink-0 items-center gap-1 text-xs px-2.5 py-1 rounded-full font-bold backdrop-blur-sm"
                  :style="programBadgeStyle(program)">
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

    <section id="comparison" class="px-5 md:px-10 pb-8 mt-10 relative z-10">
      <div class="page-shell fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-blue-100/60 shadow-xl shadow-primary/6 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Tabel Perbandingan Fasilitas Kelas Kursus</h2>
          <p class="text-gray-600 mb-6">Bandingkan fasilitas dan durasi akses setiap kelas untuk menentukan program
            paling sesuai.</p>

          <div class="overflow-x-auto rounded-2xl border border-border relative z-20">
            <table class="min-w-[980px] w-full border-collapse">
              <thead>
                <tr class="bg-gray-500">
                  <th class="text-left px-4 py-3 font-bold text-white border-b border-border min-w-[290px]">Fasilitas
                    Program</th>
                  <th v-for="col in classComparisonColumns" :key="col.key"
                    class="px-4 py-3 border-b border-border text-center">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-bold uppercase tracking-wide"
                      :class="col.badgeClass">
                      {{ col.label }}
                    </span>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="row in classComparisonRows" :key="row.label" class="odd:bg-white even:bg-gray-400/40">
                  <th class="text-left px-4 py-3 text-sm md:text-base font-semibold text-text border-b border-border">{{
                    row.label }}</th>
                  <td v-for="col in classComparisonColumns" :key="`${row.label}-${col.key}`"
                    class="px-4 py-3 border-b border-border text-center"
                    :class="isBiayaRow(row) ? 'cursor-pointer hover:bg-primary/5 transition-colors' : ''"
                    @click="handleComparisonCellClick(row)">
                    <template v-if="typeof row.values[col.key] === 'boolean'">
                      <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border"
                        :class="row.values[col.key] ? 'bg-green-500 text-white border-border' : 'bg-red-500 text-white border-border'">
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

    <section class="px-5 md:px-10 pb-14 relative z-10">
      <div
        class="page-shell rounded-[2rem] bg-black text-white p-8 md:p-10 shadow-2xl shadow-[#000] fade-up delay-3">
        <h2 class="text-2xl md:text-3xl font-bold mb-3">Bagaimana Siap naik level untuk persiapan Akademi Kepolisian?</h2>
        <p class="text-white/80">
          Bergabung sebagai peserta, lengkapi pendaftaran secara bertahap, lalu ikuti program kelas kursus online dengan
          standar pembelajaran profesional.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <router-link to="/free-tryout"
            class="px-6 py-3 rounded-full bg-yellow-500 text-white font-semibold cta-tryout-animated transition-all">Coba Tryout Gratis</router-link>
        </div>
      </div>
    </section>

    <a href="https://wa.me/6285124156748" target="_blank" rel="noopener noreferrer"
      class="fixed bottom-6 right-6 z-50 inline-flex h-14 w-14 items-center justify-center rounded-full bg-secondary text-white shadow-xl shadow-[#2F6BFF]/30 transition hover:scale-105 hover:bg-primary"
      aria-label="Chat WhatsApp" title="Chat WhatsApp">
      <MessageCircle class="h-7 w-7" />
    </a>

    <Teleport to="body">
      <Transition name="member-slide-fade">
        <div v-if="isLeaderDetailModalOpen"
          class="fixed inset-0 z-[119] flex items-center justify-center bg-black/55 p-4 backdrop-blur-[2px]"
          role="dialog" aria-modal="true" aria-labelledby="leader-detail-modal-title"
          @click.self="closeLeaderDetailModal">
          <article
            class="w-full max-w-2xl rounded-3xl relative bg-gradient-to-br from-primary to-secondary overflow-hidden border border-blue-100/20 p-6 shadow-xl">
            <div class="absolute inset-0 mobile-card-gradient rounded-[2rem]"></div>
            <div class="relative min-h-[390px]">
              <div class="flex items-center justify-end">
                <button type="button"
                  class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-black bg-white/12 text-black hover:bg-white/20 transition-colors"
                  aria-label="Tutup detail pimpinan" @click="closeLeaderDetailModal">
                  <XIcon class="h-4 w-4" />
                </button>
              </div>
              <Transition :name="leaderTransitionName" mode="out-in" @before-leave="onLeaderInfoBeforeLeave"
                @after-enter="onLeaderInfoAfterEnter" @enter-cancelled="onLeaderInfoAfterEnter"
                @leave-cancelled="onLeaderInfoAfterEnter">
                <div :key="`leader-mobile-info-${activeLeaderIndex}`" class="mt-4">
                  <p id="leader-detail-modal-title"
                    class="text-[14px] font-bold uppercase tracking-[0.18em] relative z-20 text-center">
                    Profil {{ leaders[activeLeaderIndex].jabatan }}
                  </p>
                  <h3 class="mt-2 text-2xl font-extrabold leading-tight tracking-tight relative z-20 text-center">
                    {{ leaders[activeLeaderIndex].name }}
                  </h3>
                  <p class="mt-2 text-md font-bold relative z-20 text-center">{{ leaders[activeLeaderIndex].batch }}
                  </p>

                  <div class="mt-5 rounded-xl backdrop-blur-[1px] text-center relative z-20">
                    <p class="text-md font-bold uppercase tracking-[0.14em] border-b border-gray-400 pb-2">Posisi Saat
                      Ini
                    </p>
                    <p class="mt-1 text-sm font-semibold leading-relaxed">
                      {{ leaders[activeLeaderIndex].position }}
                    </p>
                  </div>

                  <div class="mt-5 rounded-xl backdrop-blur-[1px] relative z-20">
                    <p class="text-md font-bold uppercase tracking-[0.14em] text-center border-b border-gray-400 pb-2">
                      Jabatan Terakhir</p>
                    <ul class="mt-3 space-y-2.5 text-sm font-semibold leading-relaxed">
                      <li v-for="line in leaders[activeLeaderIndex].highlights" :key="line" class="text-center">
                        <span>{{ line }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </Transition>
            </div>
          </article>
        </div>
      </Transition>
    </Teleport>
    <Teleport to="body">
      <Transition name="member-slide-fade">
        <div v-if="activeFeatureModal"
          class="fixed inset-0 z-[120] flex items-center justify-center bg-black/50 p-4 backdrop-blur-[2px]"
          role="dialog" aria-modal="true" aria-labelledby="feature-modal-title" @click.self="closeFeatureModal">
          <div
            class="w-full max-w-2xl overflow-hidden rounded-3xl border border-border bg-white shadow-2xl shadow-black/20">
            <div
              class="flex items-start justify-between gap-4 border-b border-border bg-gradient-to-r from-sky to-white px-5 py-4">
              <div class="flex items-center gap-3">
                <div class="inline-flex rounded-lg bg-sky p-2 text-primary">
                  <component :is="activeFeatureModal.icon" class="h-5 w-5" />
                </div>
                <div>
                  <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">Detail Keunggulan</p>
                  <h3 id="feature-modal-title" class="text-lg md:text-xl font-bold text-text">{{
                    activeFeatureModal.title }}
                  </h3>
                </div>
              </div>
              <button type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-border text-gray-500 hover:bg-background hover:text-text transition-colors"
                aria-label="Tutup" @click="closeFeatureModal">
                <XIcon class="h-4 w-4" />
              </button>
            </div>

            <div class="px-5 py-5 md:px-6 md:py-6">
              <p class="text-sm text-gray-700 leading-relaxed">
                {{ activeFeatureModal.longDesc }}
              </p>
              <ul class="mt-4 space-y-2.5">
                <li v-for="point in activeFeatureModal.points" :key="point"
                  class="flex items-start gap-2.5 text-sm text-gray-700">
                  <span class="mt-1.5 h-2 w-2 rounded-full bg-secondary shrink-0" />
                  <span>{{ point }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
    <TeacherModal :is-open="isTeacherModalOpen" :teachers="teachers" :cv-template-url="cvTemplateUrl"
      @close="closeTeacherModal" />
    <Teleport to="body">
      <Transition name="member-slide-fade">
        <div v-if="activeMemberModal"
          class="fixed inset-0 z-[125] flex items-center justify-center bg-black/55 p-4 backdrop-blur-[2px]"
          role="dialog" aria-modal="true" aria-labelledby="member-modal-title" @click.self="closeMemberModal">
          <div
            class="w-full max-w-3xl overflow-hidden rounded-3xl relative border border-border bg-white shadow-2xl shadow-black/20">
            <button type="button" @click="closeMemberModal"
              class="absolute right-3 top-3 z-40 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/65 text-white shadow-lg transition hover:bg-black/80 focus:outline-none focus:ring-2 focus:ring-white/90"
              aria-label="Tutup detail anggota">
              <XIcon class="h-5 w-5" />
            </button>
            <div class="overflow-y-auto h-[700px] bg-cover bg-center absolute z-30 px-5 py-5 md:px-6 md:py-6">
              <div class="rounded-2xl p-4 md:p-6">
                <div class="grid gap-4 md:grid-cols-[220px_1fr] md:items-center">
                  <div class="relative mx-auto w-40 md:w-52">
                    <div class="h-40 w-40 overflow-hidden rounded-full border-4 border-white shadow-lg md:h-52 md:w-52">
                      <img :src="activeMemberModal.image" :alt="activeMemberModal.name"
                        class="h-full w-full object-cover object-top" />
                    </div>
                    <div class="absolute bottom-0 right-0 z-30 flex items-center justify-center">
                      <span class="block loader" />
                    </div>
                  </div>

                  <div class="space-y-2 text-left">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">Data Diri</p>
                    <h4 class="text-xl font-extrabold leading-tight text-text md:text-2xl">{{ activeMemberModal.name }}
                    </h4>
                    <p class="text-sm font-semibold uppercase tracking-wide text-primary">{{ activeMemberModal.jabatan
                    }}
                    </p>
                    <div class="mt-3 space-y-1.5 text-sm text-gray-700">
                      <p><span class="font-semibold text-text">Tempat, Tanggal Lahir:</span> {{
                        activeMemberModal.profile.birthPlaceDate }}</p>
                    </div>
                  </div>
                </div>

                <div class="mt-8 grid gap-4 grid-cols-1 md:grid-cols-2">
                  <div class="rounded-xl">
                    <p
                      class="text-[11px] font-bold uppercase tracking-[0.14em] text-white bg-gradient-to-r from-primary to-white px-4 py-1 rounded-full mb-2">
                      Riwayat Pendidikan</p>
                    <ul class="member-history-list mt-3 text-sm text-gray-700">
                      <li v-for="item in activeMemberModal.profile.education" :key="item"
                        class="member-history-item flex items-start gap-2">
                        <span class="member-history-dot mt-1.5 h-2 w-2 shrink-0 rounded-full bg-secondary" />
                        <span>{{ item }}</span>
                      </li>
                    </ul>
                  </div>

                  <div class="rounded-xl">
                    <p
                      class="text-[11px] font-bold uppercase tracking-[0.14em] text-white bg-gradient-to-r from-primary to-white px-4 py-1 rounded-full mb-2">
                      Riwayat Organisasi</p>
                    <ul class="member-history-list mt-3 text-sm text-gray-700">
                      <li v-for="item in activeMemberModal.profile.organization" :key="item"
                        class="member-history-item flex items-start gap-2">
                        <span class="member-history-dot mt-1.5 h-2 w-2 shrink-0 rounded-full bg-secondary" />
                        <span>{{ item }}</span>
                      </li>
                    </ul>
                  </div>

                  <div class="rounded-xl md:col-span-2 mt-4">
                    <p
                      class="text-[11px] font-bold uppercase tracking-[0.14em] text-white bg-gradient-to-r from-primary to-white/30 px-4 py-1 rounded-full mb-2">
                      Riwayat Pekerjaan</p>
                    <ul class="member-history-list mt-3 text-sm text-gray-700">
                      <li v-for="item in activeMemberModal.profile.work" :key="item"
                        class="member-history-item flex items-start gap-2">
                        <span class="member-history-dot mt-1.5 h-2 w-2 shrink-0 rounded-full bg-secondary" />
                        <span class="w-8/12">{{ item }}</span>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div>
              <img :src="cvTemplateUrl" alt="CV Template" class="selayang-card-breathe w-full relative z-10 h-screen" />
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </main>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { storeToRefs } from 'pinia'
import { BookOpenText, Brain, Check, ChevronLeft, ChevronRight, Crown, Dumbbell, LineChart, MessageCircle, NotebookPen, ShieldCheck, UserCheck, Warehouse, GraduationCap, X as XIcon } from 'lucide-vue-next'
import { ONLINE_PROGRAMS } from '@/constants/onlinePrograms'
import SectionWaveDivider from '@/components/SectionWaveDivider.vue'
import TeacherModal from '@/components/TeacherModal.vue'

const route = useRoute()
const router = useRouter()
const store = useAppStore()
const { isAuthenticated } = storeToRefs(store)
const nanaUrl = new URL('../../assets/bpk_nana.png', import.meta.url).href
const tubagusUrl = new URL('../../assets/bpk_tubagus.jpg', import.meta.url).href
const awangUrl = new URL('../../assets/bpk_awang.jpg', import.meta.url).href
const haitiUrl = new URL('../../assets/bpk_haiti.jpg', import.meta.url).href
const gilangUrl = new URL('../../assets/anggota/gilang.jpeg', import.meta.url).href
const wahyuUrl = new URL('../../assets/anggota/wahyu.jpeg', import.meta.url).href
const rinaUrl = new URL('../../assets/anggota/rina.jpeg', import.meta.url).href
const natashaUrl = new URL('../../assets/anggota/natasha.png', import.meta.url).href
const tutikUrl = new URL('../../assets/anggota/tutik.jpeg', import.meta.url).href
const natashateacherUrl = new URL('../../assets/pengajar/natashateacher.jpg', import.meta.url).href
const djatmikoUrl = new URL('../../assets/pengajar/Djatmiko.jpg', import.meta.url).href
const iketaUrl = new URL('../../assets/pengajar/Iketutadipurnama.jpg', import.meta.url).href
const yunusSufianUrl = new URL('../../assets/pengajar/YunusSufian.png', import.meta.url).href
const defaultTeacherUrl = new URL('../../assets/anggota/default.png', import.meta.url).href
const cvTemplateUrl = new URL('../../assets/cv.png', import.meta.url).href
const card2Url = new URL('../../assets/card2.png', import.meta.url).href
const selayangCard = new URL('../../assets/selayangcard.png', import.meta.url).href
const brandLogoUrl = new URL('../../assets/logo.png', import.meta.url).href
const bannerUrl = new URL('../../assets/bg_1.png', import.meta.url).href
const taruna = new URL('../../assets/anggota/taruna.png', import.meta.url).href
const taruna2 = new URL('../../assets/anggota/taruna2.png', import.meta.url).href
const taruni = new URL('../../assets/anggota/taruni.png', import.meta.url).href
const HERO_VIDEO_ID = 't2k3uwS2zyA'
const heroVideoEmbedUrl = `https://www.youtube.com/embed/${HERO_VIDEO_ID}?autoplay=1&mute=1&controls=0&loop=1&playlist=${HERO_VIDEO_ID}&modestbranding=1&rel=0&playsinline=1`
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
const isLeaderDetailModalOpen = ref(false)
const isSelayangModalOpen = ref(false)
const activeFeatureModal = ref(null)
const isTeacherModalOpen = ref(false)
const activeMemberModal = ref(null)
const leaderCarouselRef = ref(null)
const leaderSlideRefs = ref([])
const activeLeaderIndex = ref(0)
const isLeaderInfoSwitching = ref(false)
const isLeaderAutoScrolling = ref(false)
const leaderTransitionName = computed(() => {
  const variants = ['leader-swap-slide', 'leader-swap-pop', 'leader-swap-tilt']
  return variants[activeLeaderIndex.value % variants.length]
})
let wallpaperInterval
let leaderAutoScrollTimer

let fadeObserver
const quickNavItems = [
  { id: 'leaders', label: 'Dewan Pimpinan' },
  { id: 'programs', label: 'Program Layanan' },
  // { id: 'services', label: 'Layanan Pembinaan' },
  { id: 'choices', label: 'Pilihan Kursus' },
  { id: 'comparison', label: 'Perbandingan Kelas' },
]

const leaders = [
  {
    name: 'Jenderal Polisi (Purn.) Tan Sri Drs. Badrodin Haiti',
    batch: '',
    position: 'Pembina Pratistha Cendekia Prestasi',
    jabatan: 'Pembina',
    highlights: [
      'Kabaharkam Polri (2013–2014)',
      'Wakapolri (2014–2015)',
      'Kapolri (2015–2016)'
    ],
    image: haitiUrl,
  },
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

const setLeaderSlideRef = (element, index) => {
  if (!element) return
  leaderSlideRefs.value[index] = element
}

const goToLeader = (index, behavior = 'smooth') => {
  const container = leaderCarouselRef.value
  const target = leaderSlideRefs.value[index]
  if (!container || !target) return

  isLeaderAutoScrolling.value = true
  if (leaderAutoScrollTimer) {
    clearTimeout(leaderAutoScrollTimer)
  }

  const isDesktop = window.innerWidth >= 1280
  const hasScrollableArea =
    container.scrollHeight > container.clientHeight || container.scrollWidth > container.clientWidth

  if (hasScrollableArea) {
    if (isDesktop) {
      const targetTop = target.offsetTop - (container.clientHeight / 2) + (target.clientHeight / 2)
      const maxScrollTop = Math.max(0, container.scrollHeight - container.clientHeight)
      const boundedTop = Math.min(Math.max(targetTop, 0), maxScrollTop)
      container.scrollTo({
        top: boundedTop,
        behavior,
      })
    } else {
      const targetLeft = target.offsetLeft - (container.clientWidth / 2) + (target.clientWidth / 2)
      const maxScrollLeft = Math.max(0, container.scrollWidth - container.clientWidth)
      const boundedLeft = Math.min(Math.max(targetLeft, 0), maxScrollLeft)
      container.scrollTo({
        left: boundedLeft,
        behavior,
      })
    }
  }

  activeLeaderIndex.value = index
  leaderAutoScrollTimer = setTimeout(() => {
    isLeaderAutoScrolling.value = false
  }, behavior === 'auto' ? 0 : 260)
}

const stepLeader = (direction) => {
  const nextIndex = Math.min(
    Math.max(activeLeaderIndex.value + direction, 0),
    leaders.length - 1,
  )
  goToLeader(nextIndex)
}

const getNearestLeaderIndex = () => {
  const container = leaderCarouselRef.value
  if (!container || !leaderSlideRefs.value.length) return activeLeaderIndex.value

  const isDesktop = window.innerWidth >= 1280
  if (isDesktop) {
    const maxScrollTop = Math.max(0, container.scrollHeight - container.clientHeight)
    if (container.scrollTop <= 2) return 0
    if (Math.abs(container.scrollTop - maxScrollTop) <= 2) return leaders.length - 1
  } else {
    const maxScrollLeft = Math.max(0, container.scrollWidth - container.clientWidth)
    if (container.scrollLeft <= 2) return 0
    if (Math.abs(container.scrollLeft - maxScrollLeft) <= 2) return leaders.length - 1
  }

  const containerCenter = isDesktop
    ? container.scrollTop + container.clientHeight / 2
    : container.scrollLeft + container.clientWidth / 2
  let closestIndex = 0
  let smallestDistance = Number.POSITIVE_INFINITY

  leaderSlideRefs.value.forEach((slide, index) => {
    if (!slide) return
    const slideCenter = isDesktop
      ? slide.offsetTop + slide.clientHeight / 2
      : slide.offsetLeft + slide.clientWidth / 2
    const distance = Math.abs(slideCenter - containerCenter)
    if (distance < smallestDistance) {
      smallestDistance = distance
      closestIndex = index
    }
  })

  return closestIndex
}

const onLeaderScroll = () => {
  if (isLeaderAutoScrolling.value) return
  activeLeaderIndex.value = getNearestLeaderIndex()
}

const onLeaderResize = () => {
  if (window.innerWidth < 1280) return
  goToLeader(activeLeaderIndex.value, 'auto')
}

const onLeaderInfoBeforeLeave = () => {
  isLeaderInfoSwitching.value = true
}

const onLeaderInfoAfterEnter = () => {
  isLeaderInfoSwitching.value = false
}

const members = [
  {
    name: 'Gilang Nurfahradz Syahni Fasya, S.T',
    image: gilangUrl,
    jabatan: 'Direktur',
    profile: {
      birthPlaceDate: 'Bandung, 04 Agustus 1991',
      education: ['Kimia Industri – SMK Negeri 7 Bandung (2009)', 'S1 Teknologi Pangan – Universitas Pasundan (2009) '],
      organization: ['Ketua Bidang Regenerasi Ikatan Mahasiswa AMS JABAR (2009-2011)', 'Ketua Badan Eksekutif Mahasiswa Fakultas Teknik UNPAS (2012-2013)', 'Founder Badan Legislatif (DPM) Universitas Pasundan (2013) ', 'Ketua Bidang Organisasi IKA TP UNPAS (2018 - sekarang) '],
      work: ['R&D Specialist Chocolate PT. Mercolade Indonesia (2013-2015)', 'Konsultan Manufacture Kosmetik PT. Prapta Rekayasa Buana (2016) ', 'Co. Founder PT. Magnolium Mandiri Indonesia (2015)', 'Head Factory PT. Magnolium Mandiri Indonesa dan  PT. Nusantara Agro Horeca (2015-2021) ']
    },
  },
  {
    name: 'AKBP (P) Wahyu suhardini, S.IP',
    image: wahyuUrl,
    jabatan: 'Sekretaris',
    profile: {
      birthPlaceDate: 'Tasikmalaya, 20 Juni 1972',
      education: ['S1 Ilmu Pemerintahan (S.IP)', 'Pelatihan Administrasi dan Kearsipan'],
      organization: ['Pengurus Bidang Administrasi Yayasan', 'Tim Koordinasi Program'],
      work: ['Sekretaris Pratistha Cendekia Prestasi', 'Pendamping Operasional Administrasi Program'],
    },
  },
  {
    name: 'KBP (P) Dra.Rina Regina',
    image: rinaUrl,
    jabatan: 'Bendahara',
    profile: {
      birthPlaceDate: 'Bandung, 31 Oktober 1975',
      education: ['Sarjana Pendidikan Ikip Bdg', 'Sepamilksukwan Polri 1984', 'Selapa Polri 1997'],
      organization: ['Pengurus Keuangan Yayasan', 'Tim Pengawasan Anggaran Program'],
      work: ['Kasetum Polda Jabar', 'Kabag Binamitra Polwiltabes Bdg', 'Gadik SPN Cisarua', 'Kasubdit Kerma Dit Binmas Polda Jbr'],
    },
  },
  {
    name: 'AKBP (P) Dra.Natasha Yunita Pospos, S.H. M.T.C.P',
    image: natashaUrl,
    jabatan: 'Bidang Internal',
    profile: {
      birthPlaceDate: 'Palembang, 15 Juni 1964',
      education: ['IKIP N Jakarta, Fakultas Bahasa Inggris, 1987', 'SEPAMILSUKWAN VI, 1989', 'UNLA Bandung, Fakultas Hukum, 2007', 'Selapa Polri angkatan 39, 2008', 'Kuliah Jarak Jauh, jurusan Community Policing, Singapore, 2009.', 'Management Development Course (IPS, Problem Solving ), Akpol, London, 1989, 1999', 'Training on Police Reform, Japan, Singapore, 2002, 2009', 'Training Drugs and treatment, Australia, Thayland, 2003, 2004', 'Law Enforcement  Management  Program, Vietnam, 2012'],
      organization: ['Dosen Muda, Akpol Smrg, 1989-1994', 'Dosen Muda, PTIK Jakarta, 1994-1996', 'Kasubbag Min Ops Pengawasan Orang Asing Dit IPP Polda Jabar, 1996-1999', 'Kasubbag Was Jas Pam (Satpam), Biro Bina Mitra Polda Jabar, 2008-2011', 'Kasubdit Bhabinkamtibmas Polda Jabar, 2019-2022'],
      work: ['Pelatih Interpersonal Skill dan Service Excellent sejak 1990 s/d 2022 di Badan Usaha Jasa Pengamanan untuk Sekolah Gada Pratama, Gada Madya', 'Pelatih Service Excellent dan Manajemen Tanggap Darurat sejak 2008 s/d 2022 di Badan Usaha Jasa Pengamanan untuk Sekolah Gada Utama', 'Auditor Sistem Manajemen Pengamanan dan Sistem Manajemen Pengamanan Hotel sejak 2008 s/d 2015']
    },
  },
  {
    name: 'Kompol (P) Tutik',
    image: tutikUrl,
    jabatan: 'Bidang Eksternal',
    profile: {
      birthPlaceDate: 'Cirebon, 9 September 1971',
      education: ['S1 Ilmu Sosial', 'Pelatihan Public Relations'],
      organization: ['Pengurus Hubungan Eksternal', 'Tim Kemitraan Strategis'],
      work: ['Koordinator Bidang Eksternal', 'Pengembang Jejaring Kolaborasi Program'],
    },
  },
]

const teachers = [
  {
    id: 'iketutadipurnama',
    name: 'Dr. I ketut Adi Purnama, S.H., M.H., C.M.C.',
    role: 'Kewarganegaraan dan Undang-Undang Kepolisian',
    image: iketaUrl,
    birthPlaceDate: 'Denpasar, 27 November 1966',
    education: ['S1 UNWIR Fak. HK', 'S2 UNPAD HK. Bisnis', 'S3 UNPAR DIH'],
    teaching: ['Hukum Acara Pidana', 'Sistem Peradilan Pidana Indonesia', 'Keamanan Hukum Pidana'],
  },
  {
    id: 'djatmiko',
    name: 'Djatmiko, M.Pd',
    role: 'Pengajar Matematika',
    image: defaultTeacherUrl,
    birthPlaceDate: 'Bandung, 31 Mei 1968',
    education: ['S2 Pendidikan Matematika IKIP Siliwangi'],
    teaching: ['Mengajar di SMAN 23 Bandung'],
  },
  {
    id: 'teacher-3',
    name: 'AKBP (P) Dra.Natasha Yunita Pospos, S.H., M.T.C.P',
    role: 'Pengajar Bahasa Inggris',
    image: natashateacherUrl,
    birthPlaceDate: 'Palembang, 15 Juni 1964',
    education: ['SEPAMILSUKWAN VI, tamat 1989', 'UNLA Bandung, Fakultas Hukum, tamat 2007', 'Selapa Polri angkatan 39, tamat 2008', 'Kuliah Jarak Jauh, jurusan Community Policing, Singapore, inagurasi 2009'],
    teaching: ['Pelatih Interpersonal Skill dan Service Excellent sejak 1990 s/d 2022 di Badan Usaha Jasa Pengamanan untuk Sekolah Gada Pratama, Gada  Madya', 'Pelatih Service Excellent dan Manajemen Tanggap Darurat sejak 2008 s/d 2022 di Badan Usaha Jasa Pengamanan untuk Sekolah Gada Utama', 'Auditor Sistem Manajemen Pengamanan dan Sistem Manajemen Pengamanan Hotel sejak 2008 s/d 2015'],
  },
  {
    id: 'teacher-4',
    name: 'YUNUS SUFIAN, S.H',
    role: 'BRIPTU - BIDPROPAM POLDA JABAR',
    image: yunusSufianUrl,
    birthPlaceDate: '-',
    address: '-',
    education: [
      'Pangkat: BRIPTU',
      'Kesatuan: BIDPROPAM POLDA JABAR',
      'Juara 2 10km Jalan Cepat Porda 2022',
      'Juara 3 20km Jalan Cepat Porda 2022',
      'Juara 1 5km Bandung Neighbor Fun Race 2022',
    ],
    teaching: [
      'Lisensi Level 1 Kepelatihan Fisik Nasional',
      'Pelatih Komunitas RIOT BANDUNG 2022 - sekarang',
      'Pelatih Komunitas TEMAN SPORTY 2022 - 2024',
      'Pelatih BINJAS PADJAJARANBDG 2024 - sekarang',
    ],
  }
]

const services = [
  { icon: Brain, title: 'Tes Psikologi', desc: 'Pemetaan karakter, kestabilan emosi, dan kesiapan menghadapi seleksi.' },
  { icon: BookOpenText, title: 'Tes Akademik', desc: 'Latihan soal akademik terstruktur dengan simulasi CBT berkala.' },
  { icon: Dumbbell, title: 'Tes Fisik', desc: 'Panduan pembinaan fisik sesuai standar seleksi kepolisian.' },
  { icon: Check, title: 'Tes Kesehatan', desc: 'Pendampingan pemeriksaan kesehatan untuk memenuhi standar seleksi kepolisian.' },
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

const marketing5w1hCards = [
  {
    key: 'what',
    label: 'What',
    title: 'Program Karantina Intensif',
    description: 'Program khusus persiapan seleksi akademi dengan kurikulum akademik, psikologi, kesehatan, jasmani, dan mental dalam satu alur pembinaan.',
  },
  {
    key: 'why',
    label: 'Why',
    title: 'Peluang Lolos Lebih Besar',
    description: 'Pendekatan berbasis evaluasi dan pendampingan harian membantu peserta belajar terarah, disiplin, dan siap menghadapi standar seleksi terbaru.',
  },
  {
    key: 'who',
    label: 'Who',
    title: 'Untuk Calon Taruna Serius',
    description: 'Dirancang untuk siswa kelas akhir SMA/sederajat, gap year, serta orang tua yang ingin anaknya dibina oleh tim profesional dan berpengalaman.',
  },
  {
    key: 'where',
    label: 'Where',
    title: 'Lokasi Strategis Bandung',
    description: 'Pembinaan dilakukan di pusat program Bandung dengan akses konsultasi online agar peserta dari berbagai daerah tetap bisa terlayani.',
  },
  {
    key: 'when',
    label: 'When',
    title: 'Mulai dari Sekarang',
    description: 'Pendaftaran dibuka setiap periode. Semakin awal bergabung, semakin panjang waktu persiapan dan semakin matang kesiapan peserta.',
  },
  {
    key: 'how',
    label: 'How',
    title: 'Metode 5 Langkah Terukur',
    description: 'Assessment awal, mapping kemampuan, kelas terjadwal, simulasi berkala, dan review progres ke peserta serta orang tua secara periodik.',
  },
]


const keyFeatures = [
  {
    icon: UserCheck,
    modal: 'teachers',
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
  if (feature.modal === 'teachers') {
    isTeacherModalOpen.value = true
    return
  }
  activeFeatureModal.value = feature
}

function openSelayangModal() {
  router.push('/selayang-pandang')
}

function openLeaderDetailModal() {
  isLeaderDetailModalOpen.value = true
}

function closeLeaderDetailModal() {
  isLeaderDetailModalOpen.value = false
}

function closeSelayangModal() {
  isSelayangModalOpen.value = false
}

function closeFeatureModal() {
  activeFeatureModal.value = null
}

function closeTeacherModal() {
  isTeacherModalOpen.value = false
}

function openMemberModal(member) {
  activeMemberModal.value = member
}

function closeMemberModal() {
  activeMemberModal.value = null
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

function isBiayaRow(row) {
  return row.label === 'Biaya kursus'
}

function handleComparisonCellClick(row) {
  if (!isBiayaRow(row)) return
  router.push('/about-us')
}

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
onMounted(async () => {
  scrollToHash()
  setupScrollFadeAnimations()
  await nextTick()
  goToLeader(activeLeaderIndex.value, 'auto')
  window.addEventListener('resize', onLeaderResize)
  if (wallpaperSlides.length > 1) {
    wallpaperInterval = setInterval(() => {
      activeWallpaperIndex.value = (activeWallpaperIndex.value + 1) % wallpaperSlides.length
    }, 3200)
  }
})

onUnmounted(() => {
  window.removeEventListener('resize', onLeaderResize)
  if (leaderAutoScrollTimer) {
    clearTimeout(leaderAutoScrollTimer)
  }
  if (wallpaperInterval) {
    clearInterval(wallpaperInterval)
  }
  if (fadeObserver) {
    fadeObserver.disconnect()
  }
})
</script>

<style src="../../css/animate.css"></style>

<style scoped>
.fade-up {
  opacity: 1;
  transform: translateY(0) scale(1);
}

.page-shell {
  width: min(100%, 80rem);
  margin-inline: auto;
}

.leaders-demo-frame {
  border-radius: 1rem;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background-color: #1c1d2f;
  box-shadow: 0 18px 36px rgba(0, 0, 0, 0.25);
}

.leaders-demo-layout {
  display: grid;
  grid-template-columns: 2.5fr 1fr;
  min-height: 620px;
}

.leaders-demo-content {
  padding-left: 3rem;
  padding-top: 3rem;
  padding-right: 12rem;
  padding-bottom: 3rem;
  color: #f2f4fb;
  height: 500px;
}

.leaders-demo-eyebrow {
  font-size: 2rem;
  font-weight: 600;
  line-height: 1;
}

.leaders-demo-name {
  margin-top: 0.4rem;
  font-size: clamp(2.5rem, 4vw, 4rem);
  font-weight: 700;
  line-height: 0.92;
  text-transform: uppercase;
  letter-spacing: 0.02em;
}

.leaders-demo-label {
  font-size: 1.35rem;
  font-weight: 600;
  text-transform: uppercase;
  opacity: 0.9;
}

.leaders-demo-batch {
  font-size: 1.05rem;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  font-weight: 600;
  opacity: 0.95;
}

.leaders-demo-heading {
  font-size: 1.35rem;
  font-weight: 700;
  line-height: 0.95;
}

.leaders-demo-position {
  margin-top: 0.3rem;
  font-size: clamp(2.2rem, 3.6vw, 3.6rem);
  font-weight: 700;
  line-height: 0.9;
  text-transform: uppercase;
}

.leaders-demo-list {
  margin-top: 0.7rem;
  padding-left: 1rem;
  list-style: disc;
  font-size: 1rem;
  line-height: 1.5;
  font-family: 'Inter', 'Segoe UI', sans-serif;
  color: rgba(241, 245, 255, 0.92);
}

.leaders-demo-visual {
  background: #f2f2f5;
  padding: 1.25rem;
}

.leaders-demo-image {
  width: 100%;
  height: 500px;
  object-fit: cover;
  border-radius: 0.25rem;
  box-shadow: 0 12px 26px rgba(0, 0, 0, 0.28);
}

@media (max-width: 1024px) {
  .leaders-demo-layout {
    grid-template-columns: 1fr;
    min-height: auto;
  }

  .leaders-demo-content {
    padding: 1.8rem 1.3rem;
    height: 100%;
  }

  .leaders-demo-visual {
    padding: 1rem;
  }

  .leaders-demo-image {
    height: 550px;
    width: 50%;
    margin: 0px auto;
  }

  .leaders-demo-name {
    font-size: 2rem;
  }

  .leaders-demo-eyebrow {
    font-size: 1.5rem;
    border-bottom: 1px solid #f2f4fb;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
  }

  .leaders-demo-heading {
    font-size: 1rem;
    border-bottom: 1px solid #f2f4fb;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
  }

}

@media (max-width: 450px) {
  .leaders-demo-image {
    height: 450px;
    width: 100%;
  }
}

.selayang-card-container {
  min-height: 100%;
}

.selayang-card-bg {
  object-position: center 32%;
}

.selayang-card-breathe {
  animation: selayangCardBreathe 3s ease-in-out infinite;
  transform-origin: center;
  will-change: transform;
}

.cta-tryout-animated {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  isolation: isolate;
  background-image: linear-gradient(120deg, #ffee00 0%, #e0c200 45%, #dcc600 100%);
  background-size: 220% 220%;
  box-shadow: 0 12px 30px -14px rgba(168, 168, 168, 0.8);
  animation: ctaTryoutBgFlow 4.8s ease-in-out infinite, ctaTryoutPulse 1.9s ease-in-out infinite;
  will-change: transform, background-position, box-shadow;
}

.cta-tryout-animated::after {
  content: '';
  position: absolute;
  inset: 0;
  transform: translateX(-130%);
  background: linear-gradient(105deg, rgba(255, 255, 255, 0) 25%, rgba(255, 255, 255, 0.45) 50%, rgba(255, 255, 255, 0) 75%);
  animation: ctaTryoutShine 2.3s linear infinite;
  pointer-events: none;
}

.cta-tryout-animated:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 18px 36px -16px rgba(47, 107, 255, 0.92);
}

.cta-tryout-animated:active {
  transform: translateY(0) scale(0.99);
}

.cta-selayang-animated {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
  isolation: isolate;
  background-image: linear-gradient(125deg, #47fff6 0%, #01dbc8 46%, #14b8a6 100%);
  background-size: 230% 230%;
  box-shadow: 0 12px 30px -14px rgba(15, 118, 110, 0.82);
  animation: ctaSelayangBgFlow 5.8s ease-in-out infinite, ctaSelayangPulse 2.1s ease-in-out infinite;
  will-change: transform, background-position, box-shadow;
}

.cta-selayang-animated::after {
  content: '';
  position: absolute;
  inset: 0;
  transform: translateX(-130%);
  background: linear-gradient(105deg, rgba(255, 255, 255, 0) 24%, rgba(255, 255, 255, 0.42) 50%, rgba(255, 255, 255, 0) 76%);
  animation: ctaSelayangShine 2.8s linear infinite;
  pointer-events: none;
}

.cta-selayang-animated:hover {
  transform: translateY(-2px) scale(1.02);
  box-shadow: 0 18px 36px -16px rgba(15, 118, 110, 0.95);
}

.cta-selayang-animated:active {
  transform: translateY(0) scale(0.99);
}

.home-main-gradient-animated {
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
    linear-gradient(160deg, #878787 0%, #bababa 26%, #9b9b9b 48%, #525252 72%, #0f0f0f 100%);
  background-size: 190% 190%, 190% 190%, 220% 220%, 135% 135%;
  background-blend-mode: soft-light, overlay, multiply, normal;
  animation: homeMainGradientFlow 5s ease-in-out infinite;
  will-change: background-position;
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
    linear-gradient(150deg, #e6e6e6 0%, #cccccc 42%, #ededed 100%);
  background-size: 170% 170%, 185% 185%, 125% 125%;
  background-blend-mode: overlay, soft-light, normal;
  animation: mobileCardGradientFlow 9s ease-in-out infinite;
  will-change: background-position;
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

.leader-vertical-carousel {
  scrollbar-width: none;
}

.leader-vertical-carousel::-webkit-scrollbar {
  display: none;
}

.leader-swap-slide-enter-active,
.leader-swap-slide-leave-active {
  transition: transform 0.42s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.42s ease;
}

.leader-swap-slide-enter-from {
  opacity: 0;
  transform: translateY(34px) scale(0.985);
}

.leader-swap-slide-leave-to {
  opacity: 0;
  transform: translateY(-26px) scale(1.01);
}

.leader-swap-pop-enter-active,
.leader-swap-pop-leave-active {
  transition: transform 0.38s cubic-bezier(0.2, 0.8, 0.2, 1), filter 0.38s ease, opacity 0.38s ease;
}

.leader-swap-pop-enter-from {
  opacity: 0;
  transform: scale(0.9) translateX(14px);
  filter: blur(4px);
}

.leader-swap-pop-leave-to {
  opacity: 0;
  transform: scale(1.08) translateX(-10px);
  filter: blur(3px);
}

.leader-swap-tilt-enter-active,
.leader-swap-tilt-leave-active {
  transition: transform 0.45s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.45s ease;
  transform-origin: center;
}

.leader-swap-tilt-enter-from {
  opacity: 0;
  transform: perspective(900px) rotateX(-8deg) translateY(20px) scale(0.96);
}

.leader-swap-tilt-leave-to {
  opacity: 0;
  transform: perspective(900px) rotateX(7deg) translateY(-16px) scale(1.02);
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

.member-history-list {
  display: grid;
  gap: 0.55rem;
}

.member-history-item {
  position: relative;
}

.member-history-item:not(:last-child)::after {
  content: '';
  position: absolute;
  left: 0.21rem;
  top: 1.1rem;
  bottom: -0.65rem;
  width: 2px;
  background: rgba(47, 107, 255, 0.42);
}

.member-history-dot {
  position: relative;
  z-index: 1;
}

.floating-orb {
  animation: floatOrb 7s ease-in-out infinite;
}

@keyframes floatOrb {

  0%,
  100% {
    transform: translateY(0);
  }

  50% {
    transform: translateY(18px);
  }
}

@keyframes ctaTryoutBgFlow {
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

@keyframes ctaTryoutPulse {

  0%,
  100% {
    transform: translateY(0) scale(1);
    box-shadow: 0 12px 30px -14px rgba(47, 107, 255, 0.8);
  }

  50% {
    transform: translateY(-1px) scale(1.025);
    box-shadow: 0 16px 34px -14px rgba(47, 107, 255, 0.95);
  }
}

@keyframes ctaTryoutShine {
  0% {
    transform: translateX(-130%);
  }

  100% {
    transform: translateX(130%);
  }
}

@keyframes ctaSelayangBgFlow {
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

@keyframes ctaSelayangPulse {

  0%,
  100% {
    transform: translateY(0) scale(1);
    box-shadow: 0 12px 30px -14px rgba(15, 118, 110, 0.82);
  }

  50% {
    transform: translateY(-1px) scale(1.02);
    box-shadow: 0 16px 34px -14px rgba(15, 118, 110, 0.95);
  }
}

@keyframes ctaSelayangShine {
  0% {
    transform: translateX(-130%);
  }

  100% {
    transform: translateX(130%);
  }
}

.hero-banner-fade {
  animation: heroFadeIn 1.2s ease-out both;
}

.program-gradient-animated {
  background-size: 220% 220%;
  animation: programGradientFlow 9s ease-in-out infinite;
}

.feature-gradient-animated {
  background-image:
    radial-gradient(circle at 18% 22%, rgba(255, 255, 255, 0.88) 0%, rgba(255, 255, 255, 0) 42%),
    radial-gradient(circle at 82% 86%, rgba(110, 66, 8, 0.32) 0%, rgba(110, 66, 8, 0) 46%),
    linear-gradient(105deg, #bf953f 0%, #fcf6ba 30%, #b38728 50%, #fbf5b7 70%, #aa771c 100%);
  background-size: 240% 240%;
  animation: featureGradientFlow 5.8s linear infinite;
  will-change: background-position;
}

.service-gradient-animated {
  background-image:
    radial-gradient(circle at 84% 18%, rgba(255, 255, 255, 0.85) 0%, rgba(255, 255, 255, 0) 38%),
    linear-gradient(138deg, #a8a8a8 0%, #a5a5a5 28%, #dcdcdc 54%, #bdbdbd 74%, #c4c4c4 100%);
  background-size: 230% 230%;
  animation: serviceGradientFlow 6.2s linear infinite;
  will-change: background-position;
}

@keyframes programGradientFlow {
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

@keyframes serviceGradientFlow {
  0% {
    background-position: 100% 10%;
  }

  50% {
    background-position: 0% 90%;
  }

  100% {
    background-position: 100% 10%;
  }
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

@keyframes mobileCardGradientFlow {
  0% {
    background-position: 0% 0%, 100% 0%, 50% 50%;
  }

  50% {
    background-position: 100% 100%, 0% 100%, 55% 45%;
  }

  100% {
    background-position: 0% 0%, 100% 0%, 50% 50%;
  }
}

@keyframes featureGradientFlow {
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

@keyframes selayangCardBreathe {
  0% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.035);
  }

  100% {
    transform: scale(1);
  }
}

@media (max-width: 768px) {
  .selayang-card-bg {
    object-position: center 20%;
    transform: scale(1.06);
    transform-origin: center;
  }
}
</style>