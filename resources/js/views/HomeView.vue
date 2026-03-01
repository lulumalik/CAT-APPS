<template>
  <div class="bg-[#F9F9F7] min-h-screen font-sans text-[#1A1A1A]">
    <!-- Header -->
    <header class="w-full py-6 px-4 md:px-12 flex justify-between items-center max-w-7xl mx-auto">
      <div class="flex items-center gap-2">
        <img :src="logoUrl" alt="CAT Platform Logo" class="h-8 w-auto" />
        <span class="text-xl font-bold tracking-tight">CAT Platform</span>
      </div>

      <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
        <router-link to="/" class="text-black font-semibold">Home</router-link>
        <router-link to="/rankings" class="hover:text-black transition-colors">Rankings</router-link>
        
        <template v-if="isAuthenticated">
          <router-link to="/dashboard" class="hover:text-black transition-colors">Dashboard</router-link>
          <div class="flex items-center gap-4 ml-4 pl-4 border-l border-gray-200">
            <span class="text-sm font-medium text-[#1A1A1A]">{{ user?.name }}</span>
            <button @click="handleLogout" class="text-red-600 hover:text-red-700 transition-colors">Logout</button>
          </div>
        </template>
        <template v-else>
          <router-link to="/login" class="hover:text-black transition-colors">Login</router-link>
        </template>
      </nav>

      <div v-if="!isAuthenticated">
        <router-link to="/login" class="bg-black text-white px-5 py-2.5 rounded-full text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition-colors group">
          Get Started
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform">
            <line x1="7" y1="17" x2="17" y2="7"></line>
            <polyline points="7 7 17 7 17 17"></polyline>
          </svg>
        </router-link>
      </div>
      <div v-else class="md:hidden">
        <!-- Mobile menu placeholder or similar if needed, though StickyHeader usually handles logged-in pages -->
        <router-link to="/dashboard" class="bg-black text-white px-5 py-2.5 rounded-full text-sm font-medium flex items-center gap-2 hover:bg-gray-800 transition-colors">
          Dashboard
        </router-link>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 md:px-12 py-8 md:py-16">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
        <!-- Left Content -->
        <div class="space-y-8">
          <h1 class="text-4xl md:text-6xl font-bold leading-[1.1] tracking-tight">
            Master Your Skills with Computer Assisted Testing
          </h1>

          <p class="text-gray-600 text-lg max-w-md leading-relaxed">
            Experience adaptive testing that adjusts to your skill level in real-time. Track progress, compete with
            peers, and achieve excellence.
          </p>

          <router-link to="/login"
            class="inline-flex bg-[#9DB359] text-white px-8 py-4 rounded-full font-medium text-lg items-center gap-2 hover:bg-[#8ca34b] transition-colors group shadow-lg shadow-[#9DB359]/20">
            Get a Free Quote
            <span class="bg-white/20 rounded-full p-1 group-hover:bg-white/30 transition-colors">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="7" y1="17" x2="17" y2="7"></line>
                <polyline points="7 7 17 7 17 17"></polyline>
              </svg>
            </span>
          </router-link>
        </div>

        <!-- Right Image -->
        <div class="relative">
          <div class="rounded-[2.5rem] overflow-hidden shadow-2xl relative group">
            <img src="https://images.unsplash.com/photo-1594818379496-da1e345b0ded?q=80&w=2070&auto=format&fit=crop"
              alt="Solar Engineers"
              class="w-full h-[500px] object-cover object-center group-hover:scale-105 transition-transform duration-700" />
            <!-- Badge -->
            <div
              class="absolute top-8 right-8 bg-white/20 backdrop-blur-md border border-white/30 text-white px-4 py-2 rounded-full flex items-center gap-2 font-medium">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current text-yellow-300" viewBox="0 0 20 20"
                fill="currentColor">
                <path
                  d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
              </svg>
              4.5
            </div>
          </div>
        </div>
      </div>

      <!-- Services Section -->
      <section class="mt-32">
        <div class="text-center max-w-2xl mx-auto mb-16">
          <h2 class="text-3xl md:text-5xl font-bold mb-6">Our Services</h2>
          <p class="text-gray-600 leading-relaxed text-lg">
            Comprehensive assessment solutions designed to elevate your learning and evaluation process with
            cutting-edge technology.
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
            <h3 class="text-xl font-bold mb-3 text-[#1A1A1A]">Lifetime Support</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
              Continuous technical assistance and updates to ensure your testing platform runs smoothly forever.
            </p>
            <div class="flex items-center gap-2 text-sm font-medium text-[#9DB359]">
              Learn More
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
            <h3 class="text-xl font-bold mb-3 text-[#1A1A1A]">Ranking Tests</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
              Real-time leaderboards and comparative analytics to benchmark performance against peers globally.
            </p>
            <div class="flex items-center gap-2 text-sm font-medium text-[#9DB359]">
              Learn More
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
            <h3 class="text-xl font-bold mb-3 text-[#1A1A1A]">Question Management</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
              Effortlessly create, organize, and update your assessment content with our intuitive management tools.
            </p>
            <div class="flex items-center gap-2 text-sm font-medium text-[#9DB359]">
              Learn More
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
            <h3 class="text-xl font-bold mb-3 text-[#1A1A1A]">Question Bank</h3>
            <p class="text-gray-500 text-sm leading-relaxed mb-6">
              Access a vast repository of verified questions across various subjects to jumpstart your evaluations.
            </p>
            <div class="flex items-center gap-2 text-sm font-medium text-[#9DB359]">
              Learn More
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
            View all services
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="7" y1="17" x2="17" y2="7"></line>
              <polyline points="7 7 17 7 17 17"></polyline>
            </svg>
          </router-link>
        </div>
      </section>
    </main>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'

const store = useAppStore()
const router = useRouter()
const { user, isAuthenticated } = storeToRefs(store)
const logoUrl = new URL('../../assets/logo.png', import.meta.url).href

const handleLogout = async () => {
  await store.logout()
  router.push('/')
}
</script>

<style scoped>
/* Custom font if needed, otherwise using system sans */
</style>
