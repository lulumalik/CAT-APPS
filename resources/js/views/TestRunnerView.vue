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
const isExam = route.name === 'quick-exam'
const detailApi = isExam ? `/api/exams/${testId}` : `/api/tests/${testId}`
const submitApi = isExam ? `/api/exams/${testId}/submit` : `/api/tests/${testId}/submit`

const fetchTest = async () => {
  try {
    const { data } = await window.axios.get(detailApi)

    if (data.has_submitted) {
      toast.error('Error', 'You have already submitted this test')
      router.push('/dashboard')
      return
    }

    if (!data.can_submit) {
      const message = data?.status === 'upcoming'
        ? `${isExam ? 'Ujian' : 'Test'} belum dimulai sesuai jadwal`
        : `${isExam ? 'Ujian' : 'Test'} sudah berakhir atau tidak tersedia`
      toast.error('Error', message)
      router.push('/dashboard')
      return
    }

    testData.value = data
    questions.value = data.questions || []
  } catch (error) {
    const message = error?.response?.data?.message || `Failed to start ${isExam ? 'exam' : 'test'} or data not found`
    toast.error('Error', message)
    router.push('/dashboard')
  }
}

const handleSubmit = async ({ answers }) => {
  if (isSubmitting.value) return
  isSubmitting.value = true

  try {
    await window.axios.post(submitApi, { answers })
    toast.success(t('testRunner.toastSubmittedTitle'), t('testRunner.toastSubmittedMessage'))
    router.push('/dashboard')
  } catch {
    toast.error('Error', `Failed to submit ${isExam ? 'exam' : 'test'}`)
    isSubmitting.value = false
  }
}

onMounted(fetchTest)
</script>
