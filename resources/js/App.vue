<template>
  <div
    :class="{ 'container-default': !isHomePage && !isFreeTryoutPage }"
    class="bg-background rounded-3xl min-h-screen border-2 border-border font-sans text-text"
  >
    <StickyHeader v-if="showSidebarNav" />
    <div :class="showSidebarNav ? 'pt-20 md:pt-0 md:pl-72' : ''" style="overflow-x: hidden !important;">
      <router-view />
    </div>
    
    <!-- Global Modal -->
    <Modal
      :show="modalState.show"
      :type="modalState.type"
      :title="modalState.title"
      :message="modalState.message"
      :confirmText="modalState.confirmText"
      :cancelText="modalState.cancelText"
      @confirm="modalState.onConfirm"
      @cancel="modalState.onCancel"
      @close="modalState.show = false"
    />

    <!-- Global Toast Notifications -->
    <div class="fixed top-4 right-4 z-50 space-y-3">
      <Toast
        v-for="toast in toasts"
        :key="toast.id"
        :show="toast.show"
        :type="toast.type"
        :title="toast.title"
        :message="toast.message"
        :duration="toast.duration"
        @close="removeToast(toast.id)"
      />
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, watch } from 'vue'
import { storeToRefs } from 'pinia'
import { useRoute } from 'vue-router'
import StickyHeader from '@/components/StickyHeader.vue'
import Modal from '@/components/Modal.vue'
import Toast from '@/components/Toast.vue'
import { useAppStore } from '@/stores/app'
import { useModal, useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const store = useAppStore()
const { isAuthenticated } = storeToRefs(store)
const route = useRoute()
const { modalState } = useModal()
const { toasts, removeToast } = useToast()
const { t, locale } = useI18n()

const isTestRunnerPage = computed(() => route.name === 'quick-test')
const isLoginPage = computed(() => route.name === 'login')
const isSignupPage = computed(() => route.name === 'signup')
const isHomePage = computed(() => route.name === 'home')
const isFreeTryoutPage = computed(() => route.name === 'free-tryout')
const isClassRoomPage = computed(() => route.name === 'bimble-class-room')
const isRankingPage = computed(() => route.name === 'rankings')
const showSidebarNav = computed(
  () =>
    isAuthenticated.value &&
    !isTestRunnerPage.value &&
    !isLoginPage.value &&
    !isSignupPage.value &&
    !isClassRoomPage.value &&
    !isFreeTryoutPage.value,
)

onMounted(() => {
  // Try to fetch user on app load
  store.fetchUser()
})

const updateDocumentMeta = () => {
  if (typeof document === 'undefined') return

  const routeTitleKeyByName = {
    home: 'seo.title',
    rankings: 'nav.rankings',
    login: 'nav.login',
    signup: 'auth.signup.title',
    dashboard: 'nav.dashboard',
    'question-bank': 'nav.questionBank',
    tests: 'nav.tests',
    users: 'nav.users',
  }

  const key = routeTitleKeyByName[route.name] || 'app.name'
  const baseTitle = t('app.name')
  const pageTitle = key === 'seo.title' ? t('seo.title') : `${baseTitle} - ${t(key)}`

  document.title = pageTitle

  const description = t('seo.description')
  const descriptionMeta = document.querySelector('meta[name="description"]')
  if (descriptionMeta) descriptionMeta.setAttribute('content', description)

  const ogTitle = document.querySelector('meta[property="og:title"]')
  if (ogTitle) ogTitle.setAttribute('content', pageTitle)

  const ogDescription = document.querySelector('meta[property="og:description"]')
  if (ogDescription) ogDescription.setAttribute('content', description)

  const twitterTitle = document.querySelector('meta[name="twitter:title"]')
  if (twitterTitle) twitterTitle.setAttribute('content', pageTitle)

  const twitterDescription = document.querySelector('meta[name="twitter:description"]')
  if (twitterDescription) twitterDescription.setAttribute('content', description)

  const currentUrl = typeof window !== 'undefined' ? window.location.href : ''

  let canonicalLink = document.querySelector('link[rel="canonical"]')
  if (!canonicalLink) {
    canonicalLink = document.createElement('link')
    canonicalLink.setAttribute('rel', 'canonical')
    document.head.appendChild(canonicalLink)
  }
  canonicalLink.setAttribute('href', currentUrl)

  const ogUrl = document.querySelector('meta[property="og:url"]')
  if (ogUrl) ogUrl.setAttribute('content', currentUrl)

  const shouldNoIndex =
    (route.meta && (route.meta.requiresAuth || route.meta.requiresAdmin)) ||
    isLoginPage.value ||
    isSignupPage.value ||
    isTestRunnerPage.value
  const robotsMeta = document.querySelector('meta[name="robots"]')
  if (robotsMeta) {
    robotsMeta.setAttribute('content', shouldNoIndex ? 'noindex,nofollow' : 'index,follow')
  } else {
    const newRobots = document.createElement('meta')
    newRobots.setAttribute('name', 'robots')
    newRobots.setAttribute('content', shouldNoIndex ? 'noindex,nofollow' : 'index,follow')
    document.head.appendChild(newRobots)
  }

  const ldId = 'ld-json-website'
  let ldScript = document.getElementById(ldId)
  const ldData = {
    '@context': 'https://schema.org',
    '@type': 'WebSite',
    url: typeof window !== 'undefined' ? window.location.origin : '',
    name: baseTitle,
    inLanguage: locale.value,
  }
  if (!ldScript) {
    ldScript = document.createElement('script')
    ldScript.setAttribute('type', 'application/ld+json')
    ldScript.setAttribute('id', ldId)
    document.head.appendChild(ldScript)
  }
  ldScript.textContent = JSON.stringify(ldData)

  if (document.documentElement) {
    document.documentElement.lang = locale.value
  }
}

watch([() => route.name, locale], updateDocumentMeta, { immediate: true })
</script>

<style>
</style>
