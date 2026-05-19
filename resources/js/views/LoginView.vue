<template>
  <main class="bg-background font-sans text-text py-8 px-4 md:px-8">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-6 items-stretch">
      <section
        class="fade-up rounded-[2rem] p-[2px] bg-gradient-to-br from-secondary via-secondary to-primary shadow-xl shadow-secondary/15"
      >
        <div
          class="rounded-[calc(2rem-2px)] relative bg-white text-text p-8 md:p-10 flex flex-col justify-between h-full min-h-[280px]"
        >
          <div>
            <router-link to="/" class="inline-flex items-center gap-2 mb-8 group">
              <span class="text-2xl text-secondary font-bold tracking-tight">{{ t('app.name') }}</span>
            </router-link>
            <h1 class="text-3xl md:text-4xl font-bold leading-tight">Lembaga Kursus persiapan untuk mengikuti seleksi AKADEMI KEPOLISIAN</h1>
          </div>
          <img :src="patternUrl" alt="" class="absolute w-32 bottom-0 right-0 opacity-80 pointer-events-none" />
          <ul class="space-y-2 text-sm text-text/85 font-semibold">
            <li>• Alur Pendaftaran 4 tahap terstruktur</li>
            <li>• Dashboard dan akses kelas terkendali</li>
            <li>• Notifikasi realtime status peserta</li>
          </ul>
        </div>
      </section>

      <section
        class="fade-up rounded-[2rem] p-[2px] bg-gradient-to-br from-secondary/90 via-secondary to-primary shadow-xl shadow-primary/10"
      >
        <div
          class="rounded-[calc(2rem-2px)] bg-white py-8 px-4 sm:px-10 flex flex-col justify-center border border-transparent"
        >
          <h2 class="text-3xl font-bold tracking-tight text-text text-center">
            {{ t('auth.login.title') }}
          </h2>
          <p class="mt-2 text-center text-sm text-muted">
            {{ t('auth.login.subtitle') }}
          </p>
          <div v-if="error" class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm flex items-center gap-2">
            <CircleAlert class="h-4 w-4" />
            {{ error }}
          </div>

          <form class="space-y-6" @submit.prevent="onSubmit">
            <div>
              <label for="email" class="block text-sm font-medium text-text ml-1 mb-1">{{ t('auth.login.emailLabel') }}</label>
              <div class="mt-1 rounded-xl p-px bg-gradient-to-br from-secondary to-primary focus-within:ring-2 focus-within:ring-secondary/35 focus-within:ring-offset-0">
                <input
                  id="email"
                  v-model="email"
                  name="email"
                  type="email"
                  autocomplete="email"
                  required
                  class="block w-full appearance-none rounded-[11px] border-0 bg-white px-4 py-3 placeholder:text-muted/70 text-sm text-text shadow-inner outline-none transition-shadow"
                  placeholder="you@example.com"
                />
              </div>
            </div>

            <div>
              <label for="password" class="block text-sm font-medium text-text ml-1 mb-1">{{ t('auth.login.passwordLabel') }}</label>
              <div class="mt-1 rounded-xl p-px bg-gradient-to-br from-secondary to-primary focus-within:ring-2 focus-within:ring-secondary/35 focus-within:ring-offset-0">
                <input
                  id="password"
                  v-model="password"
                  name="password"
                  type="password"
                  autocomplete="current-password"
                  required
                  class="block w-full appearance-none rounded-[11px] border-0 bg-white px-4 py-3 placeholder:text-muted/70 text-sm text-text shadow-inner outline-none transition-shadow"
                  placeholder="••••••••"
                />
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input
                  id="remember-me"
                  v-model="remember"
                  name="remember-me"
                  type="checkbox"
                  class="h-4 w-4 rounded border-border text-secondary focus:ring-secondary"
                />
                <label for="remember-me" class="ml-2 block text-sm text-text">{{ t('auth.login.rememberMe') }}</label>
              </div>

              <div class="text-sm">
                <a href="#" class="font-medium text-secondary hover:text-primary transition-colors">{{ t('auth.login.forgotPassword') }}</a>
              </div>
            </div>

            <div>
              <button
                type="submit"
                :disabled="loading"
                class="flex w-full justify-center rounded-full border border-transparent bg-secondary py-3 px-4 text-sm font-medium text-white shadow-lg shadow-secondary/25 hover:bg-primary focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:scale-[1.02] active:scale-[0.98]"
              >
                {{ loading ? t('auth.login.submitting') : t('auth.login.submit') }}
              </button>
            </div>
          </form>

          <div class="mt-6">
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-border" />
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="bg-white px-2 text-muted">{{ t('auth.login.noAccount') }}</span>
              </div>
            </div>

            <div class="mt-6">
              <router-link
                to="/signup"
                class="flex w-full justify-center items-center gap-2 rounded-full border border-border bg-white py-3 px-4 text-sm font-medium text-text shadow-sm hover:bg-background focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-all"
              >
                {{ t('auth.login.createAccount') }}
              </router-link>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main>
</template>

<script setup>
import { nextTick, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import { CircleAlert } from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const patternUrl = new URL('../../assets/Pattern.svg', import.meta.url).href

const email = ref('')
const password = ref('')
const remember = ref(false)
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()
const { t } = useI18n()
let fadeObserver

const setupScrollFadeAnimations = async () => {
  await nextTick()
  const fadeTargets = document.querySelectorAll('.fade-up')
  if (!fadeTargets.length) return

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

const onSubmit = async () => {
  loading.value = true
  error.value = ''

  const result = await store.login({
    email: email.value,
    password: password.value,
    remember: remember.value,
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

onMounted(() => {
  setupScrollFadeAnimations()
})

onBeforeUnmount(() => {
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

input[type='checkbox']:checked {
  background-color: var(--color-secondary);
  border-color: var(--color-secondary);
}
</style>
