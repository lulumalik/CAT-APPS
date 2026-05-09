<template>
  <main class="min-h-screen bg-[#F9F9F7] font-sans text-[#1A1A1A] py-8 px-4 md:px-8">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-6 items-stretch min-h-[80vh]">
      <section class="rounded-[2rem] relative border-6 border-[#9DB359] border-dashed shadow-xl shadow-black/5 bg-white text-black p-8 md:p-10 flex flex-col justify-between">
        <div>
          <router-link to="/" class="inline-flex items-center gap-2 mb-8 group">
            <span class="text-2xl text-[#9DB359] font-bold tracking-tight">{{ t('app.name') }}</span>
          </router-link>
          <h1 class="text-3xl md:text-4xl font-bold leading-tight">Mulai onboarding calon peserta secara terarah</h1>
          <p class="mt-4 text-[#000]/80 text-sm font-semibold leading-relaxed">
            Pilih program pendaftaran, isi data awal, lalu lanjutkan verifikasi bertahap sampai siap masuk kelas.
          </p>
        </div>
        <img :src="patternUrl" alt="Pattern" class="absolute w-32 bottom-0 right-0" />
        <ul class="space-y-2 text-sm text-[#000]/85 font-semibold">
          <li>• Pilihan program VIP, Regular, Bimbingan, Try Out</li>
          <li>• Monitoring progress oleh admin</li>
          <li>• Akses kelas aktif setelah onboarding selesai</li>
        </ul>
      </section>

      <section class="bg-white py-8 px-4 shadow-xl shadow-black/5 rounded-[2rem] sm:px-10 border border-gray-100 flex flex-col justify-center">
        <h2 class="text-3xl font-bold tracking-tight text-[#1A1A1A] text-center">
          {{ t('auth.signup.title') }}
        </h2>
        <div v-if="error" class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm flex items-center gap-2">
          <CircleAlert class="h-4 w-4" />
          {{ error }}
        </div>

        <form class="space-y-6 mt-4" @submit.prevent="onSubmit">
          <div>
            <label for="program_category" class="block text-sm font-medium text-gray-700 ml-1 mb-1">{{ t('auth.signup.programLabel') }}</label>
            <div class="mt-1">
              <select
                id="program_category"
                v-model="programCategory"
                required
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 text-gray-900 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors bg-white"
              >
                <option value="vip_offline">VIP — offline</option>
                <option value="vip_online">VIP — online (karantina)</option>
                <option value="regular_offline">Regular — offline</option>
                <option value="regular_online">Regular — online</option>
                <option value="bimbingan_online">Program bimbingan — full online</option>
                <option value="try_out">Try out — ujian saja</option>
              </select>
            </div>
          </div>

          <div>
            <label for="name" class="block text-sm font-medium text-gray-700 ml-1 mb-1">{{ t('auth.signup.nameLabel') }}</label>
            <div class="mt-1">
              <input id="name" v-model="name" name="name" type="text" autocomplete="name" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="John Doe" />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 ml-1 mb-1">{{ t('auth.signup.emailLabel') }}</label>
            <div class="mt-1">
              <input id="email" v-model="email" name="email" type="email" autocomplete="email" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="you@example.com" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 ml-1 mb-1">{{ t('auth.signup.passwordLabel') }}</label>
            <div class="mt-1">
              <input id="password" v-model="password" name="password" type="password" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="••••••••" />
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading" 
              class="flex w-full justify-center rounded-full border border-transparent bg-[#9DB359] py-3 px-4 text-sm font-medium text-white shadow-lg shadow-[#9DB359]/20 hover:bg-[#8ca34b] focus:outline-none focus:ring-2 focus:ring-[#9DB359] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:scale-[1.02] active:scale-[0.98]">
              {{ loading ? t('auth.signup.submitting') : t('auth.signup.submit') }}
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="bg-white px-2 text-gray-500">{{ t('auth.signup.haveAccount') }}</span>
            </div>
          </div>

          <div class="mt-6">
            <router-link to="/login" 
              class="flex w-full justify-center items-center gap-2 rounded-full border border-gray-200 bg-white py-3 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#9DB359] focus:ring-offset-2 transition-all">
              {{ t('auth.signup.signIn') }}
            </router-link>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { CircleAlert } from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const logoUrl = new URL('../../assets/logo.png', import.meta.url).href
const patternUrl = new URL('../../assets/Pattern.svg', import.meta.url).href

const programCategory = ref('regular_online')
const name = ref('')
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()
const { t, locale, setLocale } = useI18n()

const onSubmit = async () => {
  loading.value = true
  error.value = ''
  
  const result = await store.register({
    program_category: programCategory.value,
    name: name.value,
    email: email.value,
    password: password.value,
  })
  
  loading.value = false
  
  if (result.success) {
    toast.success(t('auth.signup.toastSuccessTitle'), t('auth.signup.toastSuccessMessage'))
    setTimeout(() => {
      router.push('/registration')
    }, 1000)
  } else {
    error.value = result.message || t('auth.signup.genericFailedMessage')
    toast.error(t('auth.signup.toastFailedTitle'), result.message || t('auth.signup.genericFailedMessage'))
  }
}
</script>

<style scoped>
</style>
