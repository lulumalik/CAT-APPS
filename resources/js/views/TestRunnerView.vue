<template>
  <TestRunnerPanel
    v-if="testData && questions.length"
    :test-data="testData"
    :questions="questions"
    :submitting="isSubmitting"
    @submit="handleSubmit"
  />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'
import TestRunnerPanel from '@/components/TestRunnerPanel.vue'

const route = useRoute()
const router = useRouter()
const toast = useToast()
const { t } = useI18n()

const testId = route.params.id
const questions = ref([])
const testData = ref(null)
const isSubmitting = ref(false)

const fetchTest = async () => {
  try {
    const { data } = await window.axios.get(`/api/tests/${testId}`)

    if (data.has_submitted) {
      toast.error('Error', 'You have already submitted this test')
      router.push('/dashboard')
      return
    }

    if (!data.can_submit) {
      const message = data?.status === 'upcoming'
        ? 'Test belum dimulai sesuai jadwal'
        : 'Test sudah berakhir atau tidak tersedia'
      toast.error('Error', message)
      router.push('/dashboard')
      return
    }

    testData.value = data
    questions.value = data.questions || []
  } catch (error) {
    const message = error?.response?.data?.message || 'Failed to start test or test not found'
    toast.error('Error', message)
    router.push('/dashboard')
  }
}

const handleSubmit = async ({ answers }) => {
  if (isSubmitting.value) return
  isSubmitting.value = true

  try {
    await window.axios.post(`/api/tests/${testId}/submit`, { answers })
    toast.success(t('testRunner.toastSubmittedTitle'), t('testRunner.toastSubmittedMessage'))
    router.push('/dashboard')
  } catch {
    toast.error('Error', 'Failed to submit test')
    isSubmitting.value = false
  }
}

onMounted(fetchTest)
</script>
