<template>
  <main class="min-h-screen bg-[#F9F9F7] font-sans text-[#1A1A1A] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="absolute top-4 right-4">
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
    </div>
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <router-link to="/" class="flex justify-center items-center gap-2 mb-6 group">
        <img :src="logoUrl" alt="CAT Platform" class="h-10 w-auto transition-transform group-hover:scale-105" />
        <span class="text-2xl font-bold tracking-tight text-[#1A1A1A]">{{ t('app.name') }}</span>
      </router-link>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-[#1A1A1A]">
        {{ t('auth.login.title') }}
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        {{ t('auth.login.subtitle') }}
      </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
      <div class="bg-white py-8 px-4 shadow-xl shadow-black/5 sm:rounded-[2rem] sm:px-10 border border-gray-100">
        <div v-if="error" class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm flex items-center gap-2">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
          {{ error }}
        </div>

        <form class="space-y-6" @submit.prevent="onSubmit">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 ml-1 mb-1">{{ t('auth.login.emailLabel') }}</label>
            <div class="mt-1">
              <input id="email" v-model="email" name="email" type="email" autocomplete="email" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="you@example.com" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 ml-1 mb-1">{{ t('auth.login.passwordLabel') }}</label>
            <div class="mt-1">
              <input id="password" v-model="password" name="password" type="password" autocomplete="current-password" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="••••••••" />
            </div>
          </div>

          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember-me" v-model="remember" name="remember-me" type="checkbox" 
                class="h-4 w-4 rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359]" />
              <label for="remember-me" class="ml-2 block text-sm text-gray-900">{{ t('auth.login.rememberMe') }}</label>
            </div>

            <div class="text-sm">
              <a href="#" class="font-medium text-[#9DB359] hover:text-[#8ca34b] transition-colors">{{ t('auth.login.forgotPassword') }}</a>
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading" 
              class="flex w-full justify-center rounded-full border border-transparent bg-[#9DB359] py-3 px-4 text-sm font-medium text-white shadow-lg shadow-[#9DB359]/20 hover:bg-[#8ca34b] focus:outline-none focus:ring-2 focus:ring-[#9DB359] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:scale-[1.02] active:scale-[0.98]">
              {{ loading ? t('auth.login.submitting') : t('auth.login.submit') }}
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="bg-white px-2 text-gray-500">{{ t('auth.login.noAccount') }}</span>
            </div>
          </div>

          <div class="mt-6">
            <router-link to="/signup" 
              class="flex w-full justify-center items-center gap-2 rounded-full border border-gray-200 bg-white py-3 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#9DB359] focus:ring-offset-2 transition-all">
              {{ t('auth.login.createAccount') }}
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAppStore } from '@/stores/app'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const logoUrl = new URL('../../assets/logo.png', import.meta.url).href

const email = ref('')
const password = ref('')
const remember = ref(false)
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()
const { t, locale, setLocale } = useI18n()

const onSubmit = async () => {
  loading.value = true
  error.value = ''
  
  const result = await store.login({
    email: email.value,
    password: password.value,
    remember: remember.value
  })
  
  loading.value = false
  
  if (result.success) {
    toast.success(t('auth.login.toastSuccessTitle'), t('auth.login.toastSuccessMessage'))
    setTimeout(() => {
      router.push('/dashboard')
    }, 1000)
  } else {
    error.value = result.message || t('auth.login.genericFailedMessage')
    toast.error(t('auth.login.toastFailedTitle'), result.message || t('auth.login.invalidMessage'))
  }
}
</script>

<style scoped>
/* Custom checkbox color fix since Tailwind forms plugin might default to blue */
input[type="checkbox"]:checked {
  background-color: #9DB359;
  border-color: #9DB359;
}
</style>
