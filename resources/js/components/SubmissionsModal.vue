<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ t('modals.submissions.title') }}</h2>
          <p class="text-gray-500 mt-1">{{ t('modals.submissions.subtitle', { test: test.name }) }}</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div v-if="loading" class="flex justify-center py-12">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#9DB359]"></div>
      </div>
      
      <div v-else>
        <div class="overflow-hidden rounded-xl border border-gray-100">
          <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ t('modals.submissions.tableUser') }}</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ t('modals.submissions.tableSubmittedAt') }}</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ t('modals.submissions.tableScore') }}</th>
                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ t('modals.submissions.tableActions') }}</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
              <tr v-for="sub in submissions" :key="sub.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-900">{{ sub.user.name }}</div>
                  <div class="text-sm text-gray-500">{{ sub.user.email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDateTime(sub.submitted_at) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" :class="sub.score >= 70 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                    {{ sub.score }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                   <button @click="selectedSubmission = sub" class="text-[#9DB359] hover:text-[#8ca34b] transition-colors">{{ t('modals.submissions.review') }}</button>
                </td>
              </tr>
              <tr v-if="submissions.length === 0">
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">{{ t('modals.submissions.noneFound') }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Review Section -->
      <div v-if="selectedSubmission" class="mt-8 border-t border-gray-100 pt-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="font-bold text-lg text-gray-900">{{ t('modals.submissions.reviewTitle', { name: selectedSubmission.user.name }) }}</h3>
          <button @click="selectedSubmission = null" class="text-sm text-gray-500 hover:text-gray-700">{{ t('modals.submissions.closeReview') }}</button>
        </div>
        
        <div class="space-y-4 max-h-[500px] overflow-y-auto custom-scrollbar pr-2">
            <div v-for="(answer, qId) in selectedSubmission.answers" :key="qId" class="rounded-xl border border-gray-100 bg-gray-50/50 p-5 hover:border-gray-200 transition-colors">
                <div class="font-medium text-gray-900 mb-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mr-2">Q{{ qId }}</span>
                    {{ getQuestionText(qId) }}
                </div>
                <div class="mt-2 p-3 bg-white rounded-lg border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">{{ t('modals.submissions.answer') }}</span>
                    <div class="text-gray-700">{{ answer }}</div>
                </div>
                <div v-if="getQuestionType(qId) === 'essay'" class="mt-2 flex items-center gap-2">
                    <span class="px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-medium">{{ t('modals.submissions.essay') }}</span>
                </div>
            </div>
            
            <div class="sticky bottom-0 bg-white p-4 rounded-xl border border-gray-100 shadow-lg mt-4 flex items-center justify-between">
                <div class="font-bold text-gray-900">{{ t('modals.submissions.finalScore') }}</div>
                <div class="flex items-center gap-3">
                  <input v-model.number="selectedSubmission.score" type="number" class="w-24 rounded-lg border-gray-200 focus:border-[#9DB359] focus:ring-[#9DB359] transition-all" />
                  <button @click="updateScore(selectedSubmission)" class="bg-[#1A1A1A] text-white px-4 py-2 rounded-full hover:bg-black shadow-lg shadow-black/20 transition-all text-sm font-medium">{{ t('modals.submissions.updateScore') }}</button>
                </div>
            </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'

const props = defineProps({ 
    test: Object,
    questions: Array // Pass all questions to look up text
})
const emit = defineEmits(['close'])
const toast = useToast()
const { t } = useI18n()

const submissions = ref([])
const loading = ref(true)
const selectedSubmission = ref(null)

const loadSubmissions = async () => {
    try {
        const { data } = await window.axios.get(`/api/tests/${props.test.id}/submissions`)
        submissions.value = data
    } catch (e) {
        toast.error('Error', 'Failed to load submissions')
    } finally {
        loading.value = false
    }
}

const updateScore = async (sub) => {
    try {
        await window.axios.put(`/api/submissions/${sub.id}`, { score: sub.score })
        toast.success('Success', t('modals.submissions.updateScore'))
    } catch (e) {
        toast.error('Error', 'Failed to update score')
    }
}

const getQuestionText = (id) => {
    const q = props.questions.find(x => x.id == id)
    return q ? q.question : t('modals.submissions.questionNotFound')
}

const getQuestionType = (id) => {
    const q = props.questions.find(x => x.id == id)
    return q ? q.type : ''
}

const formatDateTime = (dateStr) => {
    if (!dateStr) return ''
    const dt = new Date(dateStr)
    const opts = {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: false,
    }
    try {
      return dt.toLocaleString('id-ID', { ...opts, timeZone: 'Asia/Jakarta' })
    } catch (e) {
      return dt.toLocaleString('id-ID', opts)
    }
}

onMounted(loadSubmissions)
</script>
