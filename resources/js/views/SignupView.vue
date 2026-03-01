<template>
  <main class="min-h-screen bg-[#F9F9F7] font-sans text-[#1A1A1A] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <router-link to="/" class="flex justify-center items-center gap-2 mb-6 group">
        <img :src="logoUrl" alt="CAT Platform" class="h-10 w-auto transition-transform group-hover:scale-105" />
        <span class="text-2xl font-bold tracking-tight text-[#1A1A1A]">CAT Platform</span>
      </router-link>
      <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-[#1A1A1A]">
        Create an account
      </h2>
      <p class="mt-2 text-center text-sm text-gray-600">
        Sign up to start your assessments
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
            <label for="name" class="block text-sm font-medium text-gray-700 ml-1 mb-1">Full Name</label>
            <div class="mt-1">
              <input id="name" v-model="name" name="name" type="text" autocomplete="name" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="John Doe" />
            </div>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 ml-1 mb-1">Email address</label>
            <div class="mt-1">
              <input id="email" v-model="email" name="email" type="email" autocomplete="email" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="you@example.com" />
            </div>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 ml-1 mb-1">Password</label>
            <div class="mt-1">
              <input id="password" v-model="password" name="password" type="password" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="••••••••" />
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 ml-1 mb-1">Confirm Password</label>
            <div class="mt-1">
              <input id="password_confirmation" v-model="passwordConfirmation" name="password_confirmation" type="password" required 
                class="block w-full appearance-none rounded-xl border border-gray-200 px-4 py-3 placeholder-gray-400 shadow-sm focus:border-[#9DB359] focus:outline-none focus:ring-[#9DB359] sm:text-sm transition-colors" 
                placeholder="••••••••" />
            </div>
          </div>

          <div>
            <button type="submit" :disabled="loading" 
              class="flex w-full justify-center rounded-full border border-transparent bg-[#9DB359] py-3 px-4 text-sm font-medium text-white shadow-lg shadow-[#9DB359]/20 hover:bg-[#8ca34b] focus:outline-none focus:ring-2 focus:ring-[#9DB359] focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:scale-[1.02] active:scale-[0.98]">
              {{ loading ? 'Creating account...' : 'Sign up' }}
            </button>
          </div>
        </form>

        <div class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-gray-200"></div>
            </div>
            <div class="relative flex justify-center text-sm">
              <span class="bg-white px-2 text-gray-500">Already have an account?</span>
            </div>
          </div>

          <div class="mt-6">
            <router-link to="/login" 
              class="flex w-full justify-center items-center gap-2 rounded-full border border-gray-200 bg-white py-3 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-[#9DB359] focus:ring-offset-2 transition-all">
              Sign in
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

const logoUrl = new URL('../../assets/logo.png', import.meta.url).href

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()

const onSubmit = async () => {
  loading.value = true
  error.value = ''
  
  const result = await store.register({
    name: name.value,
    email: email.value,
    password: password.value,
    password_confirmation: passwordConfirmation.value,
  })
  
  loading.value = false
  
  if (result.success) {
    toast.success('Account Created', 'Welcome! Redirecting to dashboard...')
    setTimeout(() => {
      router.push('/dashboard')
    }, 1000)
  } else {
    error.value = result.message || 'Sign up failed. Please check your inputs.'
    toast.error('Sign Up Failed', result.message || 'Please check your inputs.')
  }
}
</script>

<style scoped>
</style>
