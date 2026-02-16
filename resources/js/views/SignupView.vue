<template>
  <main class="min-h-screen bg-bg">
    <div class="container-main py-6">
      <router-link to="/" class="text-sm text-muted hover:text-text">← Back</router-link>
    </div>
    <div class="container-main">
      <div class="ui-card mx-auto max-w-md">
        <div class="w-12 h-12 mx-auto grid place-items-center rounded-full bg-brand/15">
          <img :src="logoUrl" alt="CAT Apps" class="w-8 h-8 object-cover" />
        </div>
        <h1 class="mt-4 text-center text-2xl font-semibold">Create Account</h1>
        <p class="mt-1 text-center text-muted">Sign up to start your assessments</p>

        <div v-if="error" class="mt-4 p-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm">
          {{ error }}
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="onSubmit">
          <div>
            <label class="text-sm">Full Name</label>
            <input v-model="name" type="text" placeholder="Your name" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" required />
          </div>
          <div>
            <label class="text-sm">Email Address</label>
            <input v-model="email" type="email" placeholder="you@example.com" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" required />
          </div>
          <div>
            <label class="text-sm">Password</label>
            <input v-model="password" type="password" placeholder="••••••••" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" required />
          </div>
          <div>
            <label class="text-sm">Confirm Password</label>
            <input v-model="passwordConfirmation" type="password" placeholder="••••••••" class="mt-1 w-full rounded-md border border-gray-200 bg-white px-3 py-2" required />
          </div>
          <button type="submit" :disabled="loading" class="w-full px-4 py-2 rounded-md bg-navy text-white disabled:opacity-50 cursor-pointer disabled:cursor-not-allowed">
            {{ loading ? 'Creating...' : 'Sign Up' }}
          </button>
        </form>

        <div class="mt-6 text-center text-sm">
          <span>Already have an account?</span>
          <router-link to="/login" class="ml-1 text-brand">Login</router-link>
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
     setTimeout(() => { router.push('/dashboard') }, 1000)
   } else {
     error.value = result.message || 'Sign up gagal. Silakan coba lagi.'
     toast.error('Sign Up Failed', result.message || 'Please check your inputs.')
   }
 }
 </script>

 <style scoped>
 </style>
