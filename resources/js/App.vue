<template>
  <div class="min-h-screen bg-bg text-text overflow-x-hidden">
    <StickyHeader v-if="!isTestRunnerPage && !isLoginPage && !isSignupPage" />
    <router-view />
    
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
import { computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import StickyHeader from '@/components/StickyHeader.vue'
import Modal from '@/components/Modal.vue'
import Toast from '@/components/Toast.vue'
import { useAppStore } from '@/stores/app'
import { useModal, useToast } from '@/composables/useNotification'

const store = useAppStore()
const route = useRoute()
const { modalState } = useModal()
const { toasts, removeToast } = useToast()

const isTestRunnerPage = computed(() => route.name === 'quick-test')
const isLoginPage = computed(() => route.name === 'login')
const isSignupPage = computed(() => route.name === 'signup')

onMounted(() => {
  // Try to fetch user on app load
  store.fetchUser()
  if (typeof document !== 'undefined') {
    document.title = 'CAT - APPS'
  }
})
</script>

<style>
</style>
