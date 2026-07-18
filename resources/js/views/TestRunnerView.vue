<template>
  <TestRunnerPanel
    v-if="testData && questions.length"
    :test-data="testData"
    :questions="questions"
    :submitting="isSubmitting"
    :is-exam="isExam"
    @submit="handleSubmit"
  />
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
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
const isExam = computed(() => route.name === 'quick-exam')
const detailApi = computed(() => (isExam.value ? `/api/exams/${testId}` : `/api/tests/${testId}`))
const submitApi = computed(() => (isExam.value ? `/api/exams/${testId}/submit` : `/api/tests/${testId}/submit`))

const fetchTest = async () => {
  try {
    const { data } = await window.axios.get(detailApi.value)

    // Quiz tetap sekali submit; ujian boleh diulang selama jadwal terbuka.
    if (!isExam.value && data.has_submitted) {
      toast.error('Error', 'You have already submitted this test')
      router.push('/dashboard')
      return
    }

    if (!data.can_submit) {
      const message = data?.status === 'upcoming'
        ? `${isExam.value ? 'Ujian' : 'Test'} belum dimulai sesuai jadwal`
        : `${isExam.value ? 'Ujian' : 'Test'} sudah berakhir atau tidak tersedia`
      toast.error('Error', message)
      router.push(isExam.value ? '/ujian' : '/dashboard')
      return
    }

    testData.value = data
    questions.value = data.questions || []
  } catch (error) {
    const message = error?.response?.data?.message || `Failed to start ${isExam.value ? 'exam' : 'test'} or data not found`
    toast.error('Error', message)
    router.push(isExam.value ? '/ujian' : '/dashboard')
  }
}

const handleSubmit = async ({ answers }) => {
  if (isSubmitting.value) return
  isSubmitting.value = true

  try {
    const { data } = await window.axios.post(submitApi.value, { answers })
    toast.success(t('testRunner.toastSubmittedTitle'), t('testRunner.toastSubmittedMessage'))

    if (isExam.value && data?.submission?.id) {
      router.push({
        name: 'exam-review',
        params: { id: testId, submissionId: data.submission.id },
      })
      return
    }

    router.push('/dashboard')
  } catch {
    toast.error('Error', `Failed to submit ${isExam.value ? 'exam' : 'test'}`)
    isSubmitting.value = false
  }
}

onMounted(fetchTest)
</script>
