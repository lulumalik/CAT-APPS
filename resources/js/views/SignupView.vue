<template>
  <main class="min-h-screen bg-background font-sans text-text py-8 px-4 md:px-8">
    <div class="max-w-6xl mx-auto grid lg:grid-cols-2 gap-6 items-stretch min-h-[80vh]">
      <section
        class="rounded-[2rem] p-[2px] bg-gradient-to-br from-secondary via-secondary to-primary shadow-xl shadow-secondary/15"
      >
        <div
          class="rounded-[calc(2rem-2px)] relative bg-white text-text p-8 md:p-10 flex flex-col justify-between h-full min-h-[280px]"
        >
          <div>
            <router-link to="/" class="inline-flex items-center gap-2 mb-8 group">
              <span class="text-2xl text-secondary font-bold tracking-tight">{{ t('app.name') }}</span>
            </router-link>
            <h1 class="text-3xl md:text-4xl font-bold leading-tight">Mulai pendaftaran calon peserta</h1>
            <p class="mt-4 text-text/80 text-sm font-semibold leading-relaxed">
              Pilih program pendaftaran, isi data awal, lalu lanjutkan verifikasi bertahap sampai siap masuk kelas.
            </p>
          </div>
          <img :src="patternUrl" alt="" class="absolute w-32 bottom-0 right-0 opacity-80 pointer-events-none" />
          <ul class="space-y-2 text-sm text-text/85 font-semibold">
            <li v-for="p in ONLINE_PROGRAMS" :key="p.value">• {{ p.name }} — {{ p.mode }}</li>
            <li>• Monitoring progress oleh admin</li>
            <li>• Akses kelas aktif setelah pendaftaran selesai</li>
          </ul>
        </div>
      </section>

      <section
        class="rounded-[2rem] p-[2px] bg-gradient-to-br from-secondary/90 via-secondary to-primary shadow-xl shadow-primary/10"
      >
        <div
          class="rounded-[calc(2rem-2px)] bg-white py-8 px-4 sm:px-10 flex flex-col justify-center border border-transparent"
        >
          <h2 class="text-3xl font-bold tracking-tight text-text text-center">
            {{ t('auth.signup.title') }}
          </h2>
          <div v-if="error" class="mb-4 p-4 rounded-xl bg-red-50 border border-red-100 text-red-600 text-sm flex items-center gap-2">
            <CircleAlert class="h-4 w-4" />
            {{ error }}
          </div>

          <form class="space-y-6 mt-4" @submit.prevent="onSubmit">
            <div>
              <label for="program_category" class="block text-sm font-medium text-text ml-1 mb-1">{{ t('auth.signup.programLabel') }}</label>
              <div class="mt-1 rounded-xl p-px bg-gradient-to-br from-secondary to-primary focus-within:ring-2 focus-within:ring-secondary/35 focus-within:ring-offset-0">
                <select
                  id="program_category"
                  v-model="programCategory"
                  required
                  class="block w-full appearance-none rounded-[11px] border-0 bg-white px-4 py-3 text-sm text-text shadow-inner outline-none transition-shadow"
                >
                  <option v-for="p in ONLINE_PROGRAMS" :key="p.value" :value="p.value">
                    {{ programSignupOptionLabel(p) }}
                  </option>
                </select>
              </div>
            </div>

            <div>
              <label for="name" class="block text-sm font-medium text-text ml-1 mb-1">{{ t('auth.signup.nameLabel') }}</label>
              <div class="mt-1 rounded-xl p-px bg-gradient-to-br from-secondary to-primary focus-within:ring-2 focus-within:ring-secondary/35 focus-within:ring-offset-0">
                <input
                  id="name"
                  v-model="name"
                  name="name"
                  type="text"
                  autocomplete="name"
                  required
                  class="block w-full appearance-none rounded-[11px] border-0 bg-white px-4 py-3 placeholder:text-muted/70 text-sm text-text shadow-inner outline-none transition-shadow"
                  placeholder="John Doe"
                />
              </div>
            </div>

            <div>
              <label for="email" class="block text-sm font-medium text-text ml-1 mb-1">{{ t('auth.signup.emailLabel') }}</label>
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
              <label for="password" class="block text-sm font-medium text-text ml-1 mb-1">{{ t('auth.signup.passwordLabel') }}</label>
              <div class="mt-1 rounded-xl p-px bg-gradient-to-br from-secondary to-primary focus-within:ring-2 focus-within:ring-secondary/35 focus-within:ring-offset-0">
                <input
                  id="password"
                  v-model="password"
                  name="password"
                  type="password"
                  required
                  class="block w-full appearance-none rounded-[11px] border-0 bg-white px-4 py-3 placeholder:text-muted/70 text-sm text-text shadow-inner outline-none transition-shadow"
                  placeholder="••••••••"
                />
              </div>
            </div>

            <div>
              <button
                type="submit"
                :disabled="loading"
                class="flex w-full justify-center rounded-full border border-transparent bg-secondary py-3 px-4 text-sm font-medium text-white shadow-lg shadow-secondary/25 hover:bg-primary focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-all hover:scale-[1.02] active:scale-[0.98]"
              >
                {{ loading ? t('auth.signup.submitting') : t('auth.signup.submit') }}
              </button>
            </div>
          </form>

          <div class="mt-6">
            <div class="relative">
              <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-border" />
              </div>
              <div class="relative flex justify-center text-sm">
                <span class="bg-white px-2 text-muted">{{ t('auth.signup.haveAccount') }}</span>
              </div>
            </div>

            <div class="mt-6">
              <router-link
                to="/login"
                class="flex w-full justify-center items-center gap-2 rounded-full border border-border bg-white py-3 px-4 text-sm font-medium text-text shadow-sm hover:bg-background focus:outline-none focus:ring-2 focus:ring-secondary focus:ring-offset-2 transition-all"
              >
                {{ t('auth.signup.signIn') }}
              </router-link>
            </div>
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
import { ONLINE_PROGRAMS, programSignupOptionLabel } from '@/constants/onlinePrograms'

const patternUrl = new URL('../../assets/Pattern.svg', import.meta.url).href

const programCategory = ref('regular')
const name = ref('')
const email = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const router = useRouter()
const store = useAppStore()
const toast = useToast()
const { t } = useI18n()

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
