<template>
  <main class="home-main-gradient-animated min-h-screen text-text">
    <!-- <div v-if="!isAuthenticated" class="fixed top-4 inset-x-0 z-40 px-4 md:px-10">
      <div
        class="mx-auto max-w-7xl rounded-3xl md:rounded-full bg-white/95 backdrop-blur border border-border shadow-xl shadow-[#123B8F]/10 px-4 md:px-6 py-2 md:py-0">
        <div class="hidden md:grid h-16 grid-cols-[1fr_auto_1fr] items-center gap-4">
          <div class="flex items-center gap-1">
            <button v-for="item in quickNavItems" :key="item.id" type="button"
              class="text-[11px] lg:text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors whitespace-nowrap"
              @click="scrollToSection(item.id)">
              {{ item.label }}
            </button>
          </div>

          <router-link to="/about-us"
            class="text-[11px] lg:text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors whitespace-nowrap">
            Tentang Kami
          </router-link>


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
              <button v-for="item in quickNavItems" :key="`mobile-${item.id}`" type="button"
                class="text-left text-xs font-bold uppercase tracking-wide px-3 py-2 rounded-md cursor-pointer hover:bg-sky hover:text-primary transition-colors"
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
    </div> -->

    <section id="hero" class="relative overflow-hidden rounded-none min-h-[560px] md:min-h-[700px]">
      <div class="absolute inset-0 overflow-hidden">
        <iframe
          class="hero-banner-fade pointer-events-none absolute left-1/2 top-1/2 h-[56.25vw] min-h-full w-[177.78vh] scale-200 min-w-full -translate-x-1/2 -translate-y-1/2"
          :src="heroVideoEmbedUrl" title="Video profil Pratistha Cendekia Prestasi"
          allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
          referrerpolicy="strict-origin-when-cross-origin" allowfullscreen />
      </div>
      <div class="absolute inset-0 rounded-none bg-gradient-to-b from-white/20 via-white/60 to-black/40" />
      <div
        class="relative z-10 mx-auto min-h-[560px] md:min-h-[700px] flex items-start md:items-center justify-center px-5 pt-10 pb-10 md:px-10">
        <div class="text-center max-w-5xl fade-up">
          <div
            class="rounded-full border-10 h-32 w-32 md:h-56 md:w-56 mx-auto border-primary/50 flex items-center bg-white/90 p-2 shadow-lg">
            <img :src="brandLogoUrl" alt="Logo Pratistha Cendekia Prestasi"
              class="mx-auto mb-4 h-16 w-16 md:h-36 md:w-36" />
          </div>
          <p class="text-[11px] md:text-4xl font-bold uppercase tracking-[0.2em] text-primary my-4">
            Pratistha Cendekia Prestasi
          </p>
          <h1 class="text-2xl md:text-5xl lg:text-6xl font-black leading-[1.05] tracking-tight text-text uppercase">
            {{ heroSelayangPandang.title }}
          </h1>
          <div class="bg-white/40 mt-5 rounded-xl p-4 shadow-xl">
            <p class="text-base md:text-lg text-gray-700 max-w-5xl mx-auto leading-relaxed font-bold">
              {{ heroSelayangPandang.description }}
            </p>
            <div class="mt-6 flex flex-wrap font-semibold items-center justify-center gap-2 text-gray-700">
              Alamat kantor Jl. Sukamaju no. 142, Cipadung Kulon, Kec. Panyileukan, Kota Bandung - Jabar 40614
            </div>
          </div>
          <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
            <router-link to="/free-tryout"
              class="cta-tryout-animated px-7 py-3 rounded-full text-white text-sm md:text-base font-bold transition-all">
              Coba Tryout Gratis
            </router-link>
            <router-link type="button"
              class="px-7 py-3 rounded-full cursor-pointer border-2 border-primary text-primary text-sm md:text-base font-bold bg-white/80 hover:bg-sky transition-all"
              to="/signup">
              Daftar Peserta Kursus
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <section id="selayang-pandang-2" class="px-5 md:px-10 py-10">
      <div class="max-w-7xl mx-auto fade-up delay-1 relative">
        <div class="selayang-card-container rounded-[2rem] shadow-xl shadow-primary/8 relative overflow-hidden">
          <div class="absolute inset-0 mobile-card-gradient md:hidden"></div>
          <img :src="selayangCard" alt="Selayang Pandang"
            class="selayang-card-bg selayang-card-breathe hidden md:block w-full h-full absolute top-0 left-0 object-cover" />
          <div class="relative z-30 p-8 md:p-10">
            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary mb-3">Selayang Pandang</p>
            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-text leading-tight">
              Fondasi Terarah untuk Melangkah Pasti Menuju AKPOL
            </h2>
            <div class="mt-6 space-y-4 font-semibold text-gray-700 leading-relaxed">
              <p>
                Selamat datang di <span class="font-bold text-text">Pratistha Cendekia Prestasi</span>, tempat lahirnya
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
                berpengalaman, setiap peserta dipersiapkan agar <br> siap menghadapi seluruh tahapan seleksi AKPOL
                secara
                maksimal.
              </p>
              <p>
                Nama <span class="font-bold text-text">Pratistha</span> melambangkan kehormatan, keteguhan, dan fondasi
                kuat dalam meraih cita-cita. Nilai inilah yang kami tanamkan: semangat juang, <br> integritas,
                kedisiplinan,
                dan mental pantang menyerah.
              </p>
              <p>
                Kami tidak hanya membina peserta untuk lulus seleksi, tetapi juga membentuk pribadi berkarakter yang
                siap menjadi generasi pemimpin bangsa.
              </p>
            </div>
            <p class="mt-6 text-lg font-bold text-primary text-center">Bersama Pratistha Cendekia Prestasi, wujudkan
              langkah pasti
              menuju AKPOL.</p>
          </div>
        </div>
      </div>
    </section>


    <section id="leaders" class="my-10">
      <div class="max-w-7xl mx-auto fade-up fade-up-tight delay-2 relative">
        <div>
          <div class="text-2xl md:text-4xl font-bold tracking-tight text-white text-center">
            Profil pejabat dan pimpinan Pratistha Cendekia Prestasi
          </div>
          <hr class="border-white/60 my-10 w-44 border-b-2 mx-auto" />
          <div class="relative z-30 p-10 md:p-0 grid gap-5 lg:grid-cols-[330px_1fr] lg:items-start mt-10">
            <div>
              <Transition :name="leaderTransitionName" mode="out-in">
                <img :key="`leader-main-image-${activeLeaderIndex}`" :src="leaders[activeLeaderIndex].image"
                  :alt="leaders[activeLeaderIndex].name"
                  class="h-[450px] w-full rounded-[2rem] object-cover shadow-xl" />
              </Transition>
              <div ref="leaderCarouselRef"
                class="leader-vertical-carousel mt-4 grid grid-cols-3 max-h-[585px] gap-4 overflow-y-auto pr-2 snap-y snap-mandatory scroll-smooth"
                @scroll.passive="onLeaderScroll">
                <article v-for="(leader, index) in leaders" :key="leader.name"
                  :ref="(el) => setLeaderSlideRef(el, index)"
                  class="shrink-0 snap-center cursor-pointer overflow-hidden rounded-2xl border border-border bg-gradient-to-br from-white to-sky/40 p-3 shadow transition-all duration-300"
                  :class="activeLeaderIndex === index
                    ? 'scale-[1.01] border-primary/50 shadow-lg shadow-primary/20'
                    : 'opacity-70 hover:opacity-100'" @click="activeLeaderIndex = index">
                  <img :src="leader.image" :alt="leader.name" class="h-24 w-full rounded-[2rem] object-cover" />
                </article>
              </div>

              <div class="mt-4 flex items-center justify-center gap-2">
                <button v-for="(leader, index) in leaders" :key="`leader-dot-${leader.name}`" type="button"
                  class="h-2.5 rounded-full transition-all duration-300"
                  :class="activeLeaderIndex === index ? 'w-7 bg-primary' : 'w-2.5 bg-primary/30 hover:bg-primary/60'"
                  :aria-label="`Pilih pimpinan ${index + 1}`" @click="goToLeader(index)" />
              </div>
            </div>

            <article
              class="rounded-3xl relative bg-gradient-to-br from-primary to-secondary overflow-hidden border border-blue-100/20 p-6 md:p-8 shadow-xl">
              <div class="absolute inset-0 mobile-card-gradient md:hidden rounded-[2rem]"></div>
              <img :src="card2Url" alt="Card 2"
                class="selayang-card-breathe hidden md:block w-full h-full md:h-[590px] object-cover z-10 rounded-[2rem] absolute top-0 left-0" />
              <div class="relative min-h-[390px] md:min-h-[430px]">
                <div v-if="isLeaderInfoSwitching"
                  class="absolute inset-0 z-30 rounded-2xl">
                  <div class="loader2 block mx-auto mt-45"></div>
                </div>
                <Transition :name="leaderTransitionName" mode="out-in" @before-leave="onLeaderInfoBeforeLeave"
                  @after-enter="onLeaderInfoAfterEnter" @enter-cancelled="onLeaderInfoAfterEnter"
                  @leave-cancelled="onLeaderInfoAfterEnter">
                  <div :key="`leader-info-${activeLeaderIndex}`" class="md:mt-10">
                  <p class="text-[14px] font-bold uppercase tracking-[0.18em] relative z-20 text-center">
                    Profil {{ leaders[activeLeaderIndex].jabatan }}
                  </p>
                  <h3
                    class="mt-2 text-2xl font-extrabold leading-tight tracking-tight md:text-3xl relative z-20 text-center">
                    {{ leaders[activeLeaderIndex].name }}
                  </h3>
                  <p class="mt-2 text-md font-bold relative z-20 text-center">{{ leaders[activeLeaderIndex].batch }}
                  </p>

                  <div
                    class="mt-5 rounded-xl backdrop-blur-[1px] text-center relative z-20">
                    <p class="text-lg font-bold uppercase tracking-[0.14em]">Posisi Saat Ini</p>
                    <p class="mt-1 text-base font-semibold leading-relaxed md:text-lg">
                      {{ leaders[activeLeaderIndex].position }}
                    </p>
                  </div>

                  <div
                    class="md:mt-5 rounded-xl backdrop-blur-[1px] relative z-20">
                    <p
                      class="text-lg font-bold uppercase tracking-[0.14em] text-center border-b border-gray-300/20 pb-2">
                      Jabatan Terakhir</p>
                    <ul class="mt-3 space-y-2.5 text-lg font-semibold leading-relaxed">
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
        </div>
      </div>
    </section>

    <section id="members" class="pb-8 mt-10">
      <div class="max-w-7xl mx-auto fade-up fade-up-tight">
        <div class="text-2xl md:text-4xl font-bold tracking-tight text-white text-center">
          Staff pendukung program pembinaan Pratistha Cendekia Prestasi.
        </div>
        <hr class="border-white/60 my-10 w-44 border-b-2 mx-auto" />
        <div class=" relative overflow-hidden mt-10">
          <div class="relative z-20 p-4 grid grid-cols-2 gap-4 md:flex md:flex-wrap md:justify-center md:gap-12">
            <article v-for="member in members" :key="member.name"
              class="group flex flex-col items-center text-center rounded-2xl border border-border bg-gradient-to-b from-white to-sky/80 p-5 w-full sm:w-[360px] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary/40"
              role="button" tabindex="0" @click="openMemberModal(member)"
              @keydown.enter.prevent="openMemberModal(member)" @keydown.space.prevent="openMemberModal(member)">
              <div class="relative mb-4">
                <div
                  class="w-32 h-32 md:w-64 md:h-64 rounded-full overflow-hidden ring-4 ring-white shadow-lg border-2 border-primary/15 group-hover:ring-primary/30 group-hover:border-primary/30 transition-all duration-300">
                  <img :src="member.image" :alt="member.name"
                    class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500" />
                </div>
                <div class="absolute bottom-0 right-0 flex items-center justify-center">
                  <span class="block loader" />
                </div>
              </div>
              <h4 class="font-bold text-sm md:text-lg leading-snug text-text">{{ member.name }}</h4>
              <p class="text-xs md:text-base text-primary font-semibold mt-1 uppercase tracking-wide">{{ member.jabatan
              }}</p>
              <span class="mt-3 inline-flex items-center text-[11px] md:text-xs font-semibold text-primary">
                Lihat profil
              </span>
            </article>
          </div>
        </div>
      </div>
    </section>

    <section id="programs" class="px-5 md:px-10 pb-8 mt-10">
      <div class="max-w-7xl mx-auto fade-up delay-2 relative">
        <div>
          <div class="text-3xl md:text-4xl font-bold tracking-tight text-white text-center">
            Keunggulan Program
          </div>
          <hr class="border-white/60 my-10 w-44 border-b-2 mx-auto" />
          <div class="grid md:grid-cols-2 gap-8 relative z-10 mt-10">
            <div v-for="feature in keyFeatures" :key="feature.title" class="relative">
              <div
                class="absolute inset-0 top-3 -left-3 rounded-2xl bg-white/60 backdrop-blur-sm -z-10 pointer-events-none">
              </div>
              <button type="button"
                class="feature-gradient-animated rounded-2xl relative z-10 p-6 text-left cursor-pointer transition-all translate-y-2 hover:-translate-y-2 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/40"
                @click="openFeatureModal(feature)">
                <div class="mb-3 inline-flex rounded-lg bg-[gold] p-2 text-primary relative z-20">
                  <component :is="feature.icon" class="h-5 w-5" />
                </div>
                <h3 class="font-bold text-lg relative text-xl z-20">{{ feature.title }}</h3>
                <p class="text-gray-600 text-sm mt-2 text-lg leading-relaxed relative z-20 font-semibold">{{ feature.desc }}</p>
                <span class="mt-4 inline-flex items-center text-md font-semibold text-primary relative z-20">
                  Lihat detail
                </span>
              </button>
            </div>
          </div>
          <img :src="patternUrl" alt="Pattern" class="absolute z-0 w-28 bottom-0 right-0" />
        </div>
      </div>
    </section>

    <section id="services" class="px-5 md:px-10 pb-8 mt-10">
      <div class="max-w-7xl mx-auto fade-up delay-1 relative">
        <div>
          <div class="text-3xl md:text-4xl font-bold tracking-tight text-white text-center">
            Layanan Pembinaan
          </div>
          <hr class="border-white/60 my-10 w-44 border-b-2 mx-auto" />
          <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-10 relative z-10">
            <div v-for="service in services" :key="service.title" class="relative">
              <div
                class="absolute inset-0 top-3 -left-3 rounded-2xl bg-white/30 backdrop-blur-sm -z-10 pointer-events-none">
              </div>
              <article
                class="service-gradient-animated rounded-2xl border border-border p-5 relative z-10 hover:-translate-y-1 hover:shadow-lg transition-all">
                <div class="mb-3 inline-flex rounded-lg bg-sky p-2 text-primary">
                  <component :is="service.icon" class="h-5 w-5" />
                </div>
                <h3 class="font-semibold text-xl text-text">{{ service.title }}</h3>
                <p class="text-lg text-gray-600 mt-1">{{ service.desc }}</p>
              </article>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="choices" class="px-5 md:px-10 pb-8 relative mt-10">
      <div class="max-w-7xl mx-auto fade-up delay-2 relative">
        <div>
          <div class="text-3xl md:text-4xl font-bold tracking-tight text-white text-center">
            Pilihan Kelas Kursus
          </div>
          <hr class="border-white/60 my-10 w-44 border-b-2 mx-auto" />
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

    <section id="comparison" class="px-5 md:px-10 pb-8 mt-10">
      <div class="max-w-7xl mx-auto fade-up delay-2 relative">
        <div class="rounded-[2rem] bg-white border border-blue-100/60 shadow-xl shadow-primary/6 p-8 md:p-10">
          <h2 class="text-3xl md:text-4xl font-bold tracking-tight mb-2">Tabel Perbandingan Fasilitas Kelas Kursus</h2>
          <p class="text-gray-600 mb-6">Bandingkan fasilitas dan durasi akses setiap kelas untuk menentukan program
            paling sesuai.</p>

          <div class="overflow-x-auto rounded-2xl border border-border relative z-20">
            <table class="min-w-[980px] w-full border-collapse">
              <thead>
                <tr class="bg-sky/60">
                  <th class="text-left px-4 py-3 font-bold text-text border-b border-border min-w-[290px]">Fasilitas
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
                <tr v-for="row in classComparisonRows" :key="row.label" class="odd:bg-white even:bg-sky/20">
                  <th class="text-left px-4 py-3 text-sm md:text-base font-semibold text-text border-b border-border">{{
                    row.label }}</th>
                  <td v-for="col in classComparisonColumns" :key="`${row.label}-${col.key}`"
                    class="px-4 py-3 border-b border-border text-center"
                    :class="isBiayaRow(row) ? 'cursor-pointer hover:bg-primary/5 transition-colors' : ''"
                    @click="handleComparisonCellClick(row)">
                    <template v-if="typeof row.values[col.key] === 'boolean'">
                      <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border"
                        :class="row.values[col.key] ? 'bg-mint text-primary border-border' : 'bg-rose-50 text-rose-500 border-rose-100'">
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
      <div
        class="max-w-7xl mx-auto rounded-[2rem] bg-primary text-white p-8 md:p-10 shadow-2xl shadow-[#123B8F]/30 fade-up delay-3">
        <h2 class="text-2xl md:text-3xl font-bold mb-3">Siap naik level untuk persiapan Akademi Kepolisian?</h2>
        <p class="text-white/80 max-w-3xl">
          Bergabung sebagai peserta, lengkapi pendaftaran secara bertahap, lalu ikuti program kelas kursus online dengan
          standar pembelajaran profesional.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <router-link to="/signup"
            class="px-6 py-3 rounded-full bg-secondary text-white font-semibold cta-tryout-animated transition-all">Mulai
            Pendaftaran</router-link>
          <router-link to="/about-us"
            class="px-6 py-3 rounded-full bg-white/10 text-white font-semibold border border-white/20 hover:bg-white/20 transition-all">Tentang Kami</router-link>
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
            <div class="overflow-y-auto bg-cover bg-center absolute z-30 px-5 py-5 md:px-6 md:py-6">
              <div class="rounded-2xl border border-primary/15 p-4 shadow-lg backdrop-blur-[1px] md:p-6">
                <div class="grid gap-4 md:grid-cols-[220px_1fr] md:items-start">
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

                <div class="mt-6 grid gap-4 grid-cols-1 md:grid-cols-2">
                  <div class="rounded-xl border border-border bg-background/90 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">Riwayat Pendidikan</p>
                    <ul class="mt-3 space-y-2 text-sm text-gray-700">
                      <li v-for="item in activeMemberModal.profile.education" :key="item"
                        class="flex items-start gap-2">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-secondary" />
                        <span>{{ item }}</span>
                      </li>
                    </ul>
                  </div>

                  <div class="rounded-xl border border-border bg-background/90 p-4">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">Riwayat Organisasi</p>
                    <ul class="mt-3 space-y-2 text-sm text-gray-700">
                      <li v-for="item in activeMemberModal.profile.organization" :key="item"
                        class="flex items-start gap-2">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-secondary" />
                        <span>{{ item }}</span>
                      </li>
                    </ul>
                  </div>

                  <div class="rounded-xl border border-border bg-background/90 p-4 col-span-2">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">Riwayat Pekerjaan</p>
                    <ul class="mt-3 space-y-2 text-sm text-gray-700">
                      <li v-for="item in activeMemberModal.profile.work" :key="item" class="flex items-start gap-2">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-secondary" />
                        <span>{{ item }}</span>
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
import { BookOpenText, Brain, Check, Crown, Dumbbell, LineChart, MessageCircle, NotebookPen, ShieldCheck, UserCheck, Warehouse, X as XIcon } from 'lucide-vue-next'
import { ONLINE_PROGRAMS } from '@/constants/onlinePrograms'
import SectionWaveDivider from '@/components/SectionWaveDivider.vue'

const route = useRoute()
const router = useRouter()
const store = useAppStore()
const { isAuthenticated } = storeToRefs(store)
const nanaUrl = new URL('../../assets/bpk_nana.png', import.meta.url).href
const tubagusUrl = new URL('../../assets/bpk_tubagus.jpg', import.meta.url).href
const awangUrl = new URL('../../assets/bpk_awang.jpg', import.meta.url).href
const gilangUrl = new URL('../../assets/anggota/gilang.jpeg', import.meta.url).href
const wahyuUrl = new URL('../../assets/anggota/wahyu.jpeg', import.meta.url).href
const rinaUrl = new URL('../../assets/anggota/rina.jpeg', import.meta.url).href
const natashaUrl = new URL('../../assets/anggota/natasha.png', import.meta.url).href
const tutikUrl = new URL('../../assets/anggota/tutik.jpeg', import.meta.url).href
const cvTemplateUrl = new URL('../../assets/cv.png', import.meta.url).href
const card2Url = new URL('../../assets/card2.png', import.meta.url).href
const selayangCard = new URL('../../assets/selayangcard.png', import.meta.url).href
const brandLogoUrl = new URL('../../assets/logo.png', import.meta.url).href
const bannerUrl = new URL('../../assets/bg_1.png', import.meta.url).href
const heroVideoEmbedUrl = 'https://www.youtube.com/embed/t2k3uwS2zyA?autoplay=1&mute=1&controls=0&loop=1&playlist=t2k3uwS2zyA&modestbranding=1&rel=0&playsinline=1'
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
const activeMemberModal = ref(null)
const leaderCarouselRef = ref(null)
const leaderSlideRefs = ref([])
const activeLeaderIndex = ref(0)
const isLeaderInfoSwitching = ref(false)
const leaderTransitionName = computed(() => {
  const variants = ['leader-swap-slide', 'leader-swap-pop', 'leader-swap-tilt']
  return variants[activeLeaderIndex.value % variants.length]
})
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

const setLeaderSlideRef = (element, index) => {
  if (!element) return
  leaderSlideRefs.value[index] = element
}

const goToLeader = (index, behavior = 'smooth') => {
  const container = leaderCarouselRef.value
  const target = leaderSlideRefs.value[index]
  if (!container || !target) return

  const targetTop = target.offsetTop - (container.clientHeight / 2) + (target.clientHeight / 2)
  const maxScrollTop = Math.max(0, container.scrollHeight - container.clientHeight)
  const boundedTop = Math.min(Math.max(targetTop, 0), maxScrollTop)

  container.scrollTo({
    top: boundedTop,
    behavior,
  })
  activeLeaderIndex.value = index
}

const getNearestLeaderIndex = () => {
  const container = leaderCarouselRef.value
  if (!container || !leaderSlideRefs.value.length) return activeLeaderIndex.value

  const containerCenter = container.scrollTop + container.clientHeight / 2
  let closestIndex = 0
  let smallestDistance = Number.POSITIVE_INFINITY

  leaderSlideRefs.value.forEach((slide, index) => {
    if (!slide) return
    const slideCenter = slide.offsetTop + slide.clientHeight / 2
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

const onLeaderResize = () => {
  if (window.innerWidth < 1024) return
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
      birthPlaceDate: 'Bandung, 12 Februari 1993',
      education: ['S1 Teknik Industri', 'Pelatihan Manajemen Pendidikan'],
      organization: ['Pengurus Yayasan Pendidikan', 'Koordinator Pembinaan Internal'],
      work: ['Direktur Pratistha Cendekia Prestasi', 'Koordinator Strategi Program Pembinaan'],
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
      work: ['Kasat Binmas Polresta Malang', 'Kaur Regident Sat Lantas Polwiltabes Bdg', 'Kaur Jianma Sat Lantas Polwiltabes Bdg', 'Kasetum Polda Jabar', 'Kabag Binamitra Polwiltabes Bdg','Gadik SPN Cisarua', 'Kasubdit Kerma Dit Binmas Polda Jbr'],
    },
  },
  {
    name: 'AKBP (P) Dra.NATASHA YUNITA POSPOS, S.H. M.T.C.P',
    image: natashaUrl,
    jabatan: 'Bidang Internal',
    profile: {
      birthPlaceDate: 'Bandung, 18 November 1974',
      education: ['S1 Hukum (S.H.)', 'Pelatihan Tata Kelola Internal'],
      organization: ['Pengurus Bidang Internal Lembaga', 'Tim Evaluasi SOP Pembinaan'],
      work: ['Koordinator Bidang Internal', 'Auditor Pelaksanaan Program Pembinaan'],
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

.selayang-card-container {
  min-height: 100%;
}

.selayang-card-bg {
  object-position: center 32%;
}

.selayang-card-breathe {
  animation: selayangCardBreathe 8s ease-in-out infinite;
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
  background-image: linear-gradient(120deg, #2f6bff 0%, #5f0ad7 45%, #13c1fc 100%);
  background-size: 220% 220%;
  box-shadow: 0 12px 30px -14px rgba(47, 107, 255, 0.8);
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

.home-main-gradient-animated {
  background-color: #2f2a30;
  background-image:
    conic-gradient(from 22deg at 13% 12%,
      rgba(246, 225, 106, 0.52) 0deg 48deg,
      rgba(244, 188, 111, 0.44) 48deg 108deg,
      rgba(240, 157, 128, 0.36) 108deg 192deg,
      rgba(207, 123, 145, 0.3) 192deg 278deg,
      rgba(171, 103, 155, 0.24) 278deg 360deg),
    conic-gradient(from 210deg at 78% 26%,
      rgba(167, 112, 154, 0.4) 0deg 58deg,
      rgba(143, 91, 150, 0.36) 58deg 146deg,
      rgba(126, 78, 144, 0.32) 146deg 235deg,
      rgba(103, 70, 140, 0.28) 235deg 320deg,
      rgba(89, 66, 133, 0.24) 320deg 360deg),
    conic-gradient(from 318deg at 52% 82%,
      rgba(246, 143, 116, 0.34) 0deg 70deg,
      rgba(220, 95, 137, 0.32) 70deg 150deg,
      rgba(165, 74, 146, 0.3) 150deg 238deg,
      rgba(94, 62, 132, 0.34) 238deg 360deg),
    linear-gradient(160deg, #6c8ff7 0%, #561aef 26%, #1000f0 48%, #008cff 72%, #0019a8 100%);
  background-size: 190% 190%, 190% 190%, 220% 220%, 135% 135%;
  background-blend-mode: soft-light, overlay, multiply, normal;
  animation: homeMainGradientFlow 12s ease-in-out infinite;
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
    linear-gradient(150deg, #45b1ff 0%, #0766ff 42%, #0d64e5 100%);
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
  -ms-overflow-style: none;
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

.hero-banner-fade {
  animation: heroFadeIn 1.2s ease-out both;
}

.program-gradient-animated {
  background-size: 220% 220%;
  animation: programGradientFlow 9s ease-in-out infinite;
}

.feature-gradient-animated {
  background-image:
    radial-gradient(circle at 16% 22%, rgba(255, 255, 255, 0.92) 0%, rgba(255, 255, 255, 0) 42%),
    linear-gradient(120deg, #ebcc00 0%, #f37500 32%, #ffdd00 58%, #c09900 78%, #ffffff 100%);
  background-size: 240% 240%;
  animation: featureGradientFlow 5.8s linear infinite;
  will-change: background-position;
}

.service-gradient-animated {
  background-image:
    radial-gradient(circle at 84% 18%, rgba(255, 255, 255, 0.85) 0%, rgba(255, 255, 255, 0) 38%),
    linear-gradient(138deg, #ffffff 0%, #ecf8ff 28%, #cfefff 54%, #a8e6ff 74%, #e7f7ff 100%);
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
