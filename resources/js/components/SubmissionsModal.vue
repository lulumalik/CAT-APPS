<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30" @click="$emit('close')"></div>
    <div class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-xl bg-white p-6 shadow-xl">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Submissions for {{ test.name }}</h2>
        <button class="px-3 py-1 rounded-md" @click="$emit('close')">✕</button>
      </div>

      <div v-if="loading" class="text-center py-4">Loading...</div>
      
      <div v-else>
        <table class="min-w-full divide-y divide-gray-200">
          <thead>
            <tr>
              <th class="px-4 py-2 text-left">User</th>
              <th class="px-4 py-2 text-left">Submitted At</th>
              <th class="px-4 py-2 text-left">Score</th>
              <th class="px-4 py-2 text-left">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <tr v-for="sub in submissions" :key="sub.id">
              <td class="px-4 py-2">{{ sub.user.name }} ({{ sub.user.email }})</td>
              <td class="px-4 py-2">{{ new Date(sub.submitted_at).toLocaleString() }}</td>
              <td class="px-4 py-2">{{ sub.score }}</td>
              <td class="px-4 py-2">
                 <button @click="selectedSubmission = sub" class="text-blue-600 hover:underline">Review</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Review Section -->
      <div v-if="selectedSubmission" class="mt-6 border-t pt-4">
        <h3 class="font-semibold text-lg mb-2">Review Submission: {{ selectedSubmission.user.name }}</h3>
        
        <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
            <div v-for="(answer, qId) in selectedSubmission.answers" :key="qId" class="border p-3 rounded bg-gray-50">
                <div class="font-medium text-gray-700 mb-1">
                    <span class="text-xs text-gray-500">ID: {{ qId }}</span>
                    {{ getQuestionText(qId) }}
                </div>
                <div class="mt-1 p-2 bg-white rounded border border-gray-200">
                    <span class="text-sm font-semibold text-gray-500">Answer:</span> {{ answer }}
                </div>
                <div v-if="getQuestionType(qId) === 'essay'" class="mt-1 text-xs text-blue-600">
                    Essay Question
                </div>
            </div>
            
            <div class="flex items-center gap-4 bg-blue-50 p-4 rounded">
                <label class="font-semibold">Final Score:</label>
                <input v-model.number="selectedSubmission.score" type="number" class="border rounded px-2 py-1 w-24" />
                <button @click="updateScore(selectedSubmission)" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Update Score</button>
            </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from '@/composables/useNotification'

const props = defineProps({ 
    test: Object,
    questions: Array // Pass all questions to look up text
})
const emit = defineEmits(['close'])
const toast = useToast()

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
        toast.success('Success', 'Score updated')
    } catch (e) {
        toast.error('Error', 'Failed to update score')
    }
}

const getQuestionText = (id) => {
    const q = props.questions.find(x => x.id == id)
    return q ? q.question : 'Question not found'
}

const getQuestionType = (id) => {
    const q = props.questions.find(x => x.id == id)
    return q ? q.type : ''
}

onMounted(loadSubmissions)
</script>
