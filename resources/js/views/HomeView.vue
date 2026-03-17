<template>
  <div class="bg-[#F9F9F7] min-h-screen font-sans text-[#1A1A1A]">
    <!-- Header -->
    <header class="w-full py-6 px-4 md:px-12 flex justify-between items-center max-w-7xl mx-auto">
      <div class="flex items-center gap-2">
        <img :src="logoUrl" alt="CAT Platform Logo" class="h-8 w-auto" />
        <span class="text-xl font-bold tracking-tight">{{ t('app.name') }}</span>
      </div>

      <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
        <router-link to="/" class="text-black font-semibold">{{ t('nav.home') }}</router-link>
        <router-link to="/rankings" class="hover:text-black transition-colors">{{ t('nav.rankings') }}</router-link>
        
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="hover:text-black transition-colors">{{ t('nav.dashboard') }}</router-link>
          <div class="flex items-center gap-4 ml-4 pl-4 border-l border-gray-200">
            <span class="text-sm font-medium text-[#1A1A1A]">{{ user?.name }}</span>
            <button @click="handleLogout" class="text-red-600 hover:text-red-700 transition-colors">{{ t('nav.logout') }}</button>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="hover:text-black transition-colors">{{ t('nav.login') }}</router-link>
        </template>
      </nav>

      <div class="flex items-center gap-3">
        <div class="flex items-center rounded-full border border-gray-200 bg-white shadow-sm overflow-hidden">
          <button
            type="button"
            class="px-3 py-2 text-xs font-semibold transition-colors"
            :class="locale === 'id' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
            @click="setLocale('id')"
          >
            ID
          </button>
          <button
            type="button"
            class="px-3 py-2 text-xs font-semibold transition-colors"
            :class="locale === 'en' ? 'bg-black text-white' : 'text-gray-700 hover:bg-gray-50'"
            @click="setLocale('en')"
          >
            EN
          </button>
        </div>

        <div v-if="!isAuthenticated">
          <router-link to="/login" class="bg-black text-white px-5 py-2.5 rounded-full text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition-colors group">
            {{ t('nav.getStarted') }}
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform">
            <line x1="7" y1="17" x2="17" y2="7"></line>
            <polyline points="7 7 17 7 17 17"></polyline>
          </svg>
        </router-link>
        </div>
        <div v-else class="md:hidden">
          <router-link to="/dashboard" class="bg-black text-white px-5 py-2.5 rounded-full text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition-colors">
            {{ t('nav.dashboard') }}
          </router-link>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 md:px-12 py-8 md:py-16">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
        <!-- Left Content -->
        <div class="space-y-8">
          <h1 class="text-4xl md:text-6xl font-bold leading-[1.1] tracking-tight">
            {{ t('home.hero.title') }}
          </h1>

          <p class="text-gray-600 text-lg max-w-md leading-relaxed">
            {{ t('home.hero.description') }}
          </p>

          <div class="flex flex-col sm:flex-row gap-3">
            <router-link to="/login"
              class="inline-flex bg-[#9DB359] text-white px-8 py-4 rounded-full font-medium text-lg items-center justify-center gap-2 hover:bg-[#8ca34b] transition-colors group shadow-lg shadow-[#9DB359]/20">
              {{ t('home.hero.ctaPrimary') }}
              <span class="bg-white/20 rounded-full p-1 group-hover:bg-white/30 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <line x1="7" y1="17" x2="17" y2="7"></line>
                  <polyline points="7 7 17 7 17 17"></polyline>
                </svg>
              </span>
            </router-link>
            <router-link to="/rankings"
              class="inline-flex bg-white border border-gray-200 text-[#1A1A1A] px-8 py-4 rounded-full font-medium text-lg items-center justify-center gap-2 hover:bg-gray-50 transition-colors shadow-sm">
              {{ t('home.hero.ctaSecondary') }}
            </router-link>
          </div>
        </div>

        <!-- Right Image -->
        <div class="relative">
          <div class="rounded-[2.5rem] overflow-hidden shadow-2xl relative group">
            <img src="https://images.pexels.com/photos/6683392/pexels-photo-6683392.jpeg"
              alt="Platform ujian online dan penilaian"
              class="w-full h-[500px] object-cover object-center group-hover:scale-105 transition-transform duration-700" />
          </div>
        </div>
      </div>

      <!-- Services Section -->
      <section class="mt-32">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-5xl font-bold mb-6">{{ t('home.services.title') }}</h2>
          <p class="text-gray-600 leading-relaxed text-lg">
            {{ t('home.services.description') }}
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- Card 1 -->
          <div
            class="bg-white rounded-[2rem] p-8 shadow-xl shadow-black/5 hover:shadow-2xl hover:shadow-[#9DB359]/10 transition-all duration-300 border border-gray-100 group">
            <div
              class="w-14 h-14 bg-[#9DB359]/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#9DB359] transition-colors duration-300">
              <svg xmlns="http://www.w3.org/2000/svg"
                class="w-7 h-7 text-[#9DB359] group-hover:text-white transition-colors" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold mb-3 text-[#1A1A1A]">{{ t('home.services.cards.secureTesting.title') }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
              {{ t('home.services.cards.secureTesting.description') }}
            </p>
            <div class="flex items-center gap-2 text-sm font-medium text-[#9DB359]">
              {{ t('home.services.learnMore') }}
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="group-hover:translate-x-1 transition-transform">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </div>
          </div>

          <!-- Card 2 -->
          <div
            class="bg-white rounded-[2rem] p-8 shadow-xl shadow-black/5 hover:shadow-2xl hover:shadow-[#9DB359]/10 transition-all duration-300 border border-gray-100 group">
            <div
              class="w-14 h-14 bg-[#9DB359]/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#9DB359] transition-colors duration-300">
              <svg xmlns="http://www.w3.org/2000/svg"
                class="w-7 h-7 text-[#9DB359] group-hover:text-white transition-colors" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20v-6M6 20V10M18 20V4"></path>
              </svg>
            </div>
            <h3 class="text-xl font-bold mb-3 text-[#1A1A1A]">{{ t('home.services.cards.rankings.title') }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
              {{ t('home.services.cards.rankings.description') }}
            </p>
            <div class="flex items-center gap-2 text-sm font-medium text-[#9DB359]">
              {{ t('home.services.learnMore') }}
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="group-hover:translate-x-1 transition-transform">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </div>
          </div>

          <!-- Card 3 -->
          <div
            class="bg-white rounded-[2rem] p-8 shadow-xl shadow-black/5 hover:shadow-2xl hover:shadow-[#9DB359]/10 transition-all duration-300 border border-gray-100 group">
            <div
              class="w-14 h-14 bg-[#9DB359]/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#9DB359] transition-colors duration-300">
              <svg xmlns="http://www.w3.org/2000/svg"
                class="w-7 h-7 text-[#9DB359] group-hover:text-white transition-colors" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
              </svg>
            </div>
            <h3 class="text-xl font-bold mb-3 text-[#1A1A1A]">{{ t('home.services.cards.questionManagement.title') }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
              {{ t('home.services.cards.questionManagement.description') }}
            </p>
            <div class="flex items-center gap-2 text-sm font-medium text-[#9DB359]">
              {{ t('home.services.learnMore') }}
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="group-hover:translate-x-1 transition-transform">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </div>
          </div>

          <!-- Card 4 -->
          <div
            class="bg-white rounded-[2rem] p-8 shadow-xl shadow-black/5 hover:shadow-2xl hover:shadow-[#9DB359]/10 transition-all duration-300 border border-gray-100 group">
            <div
              class="w-14 h-14 bg-[#9DB359]/10 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#9DB359] transition-colors duration-300">
              <svg xmlns="http://www.w3.org/2000/svg"
                class="w-7 h-7 text-[#9DB359] group-hover:text-white transition-colors" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="4" y="4" width="16" height="16" rx="2" ry="2"></rect>
                <rect x="9" y="9" width="6" height="6"></rect>
                <line x1="9" y1="1" x2="9" y2="4"></line>
                <line x1="15" y1="1" x2="15" y2="4"></line>
                <line x1="9" y1="20" x2="9" y2="23"></line>
                <line x1="15" y1="20" x2="15" y2="23"></line>
                <line x1="20" y1="9" x2="23" y2="9"></line>
                <line x1="20" y1="14" x2="23" y2="14"></line>
                <line x1="1" y1="9" x2="4" y2="9"></line>
                <line x1="1" y1="14" x2="4" y2="14"></line>
              </svg>
            </div>
            <h3 class="text-xl font-bold mb-3 text-[#1A1A1A]">{{ t('home.services.cards.questionBank.title') }}</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
              {{ t('home.services.cards.questionBank.description') }}
            </p>
            <div class="flex items-center gap-2 text-sm font-medium text-[#9DB359]">
              {{ t('home.services.learnMore') }}
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="group-hover:translate-x-1 transition-transform">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
              </svg>
            </div>
          </div>
        </div>

        <div class="mt-16 text-center">
          <router-link to="/login"
            class="inline-flex bg-white border border-gray-200 text-[#1A1A1A] px-8 py-3 rounded-full font-medium text-sm items-center gap-2 hover:bg-gray-50 transition-colors shadow-sm">
            {{ t('home.services.viewAll') }}
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="7" y1="17" x2="17" y2="7"></line>
              <polyline points="7 7 17 7 17 17"></polyline>
            </svg>
          </router-link>
        </div>
      </section>

      <footer class="mt-24 pb-10">
        <div class="rounded-[2rem] bg-white border border-gray-100 shadow-xl shadow-black/5 px-8 py-10 md:px-12 md:py-12">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
              <div class="text-xl md:text-2xl font-bold tracking-tight text-[#1A1A1A]">
                {{ t('home.footer.title') }}
              </div>
              <div class="mt-2 text-gray-600">
                {{ t('home.footer.description') }}
              </div>
            </div>

            <a
              href="mailto:maliklulu098@gmail.com"
              class="inline-flex items-center justify-center rounded-full bg-[#1A1A1A] text-white px-6 py-3 font-medium hover:bg-black transition-colors"
            >
              maliklulu098@gmail.com
            </a>
          </div>
        </div>
      </footer>
    </main>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { useI18n } from '@/composables/useI18n'

const store = useAppStore()
const router = useRouter()
const { user, isAuthenticated } = storeToRefs(store)
const { t, locale, setLocale } = useI18n()
const logoUrl = new URL('../../assets/logo.png', import.meta.url).href

const handleLogout = async () => {
  await store.logout()
  router.push('/')
}
</script>

<style scoped>
/* Custom font if needed, otherwise using system sans */
</style>
