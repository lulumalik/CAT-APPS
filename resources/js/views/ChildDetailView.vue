<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <router-link to="/dashboard" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-[#1A1A1A] mb-6">
      <ArrowLeft class="h-4 w-4" /> Kembali ke Dashboard
    </router-link>

    <div class="mb-6">
      <h1 class="text-2xl md:text-3xl font-bold text-[#1A1A1A]">Perkembangan {{ childName || 'Ananda' }}</h1>
      <p class="text-gray-500 mt-1 text-sm">Laporan & progress peserta — bersifat privat untuk Anda.</p>
    </div>

    <StudentProgressPanel :student-id="studentId" />
  </main>
</template>

<script setup>
import { computed, ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { ArrowLeft } from 'lucide-vue-next'
import axios from 'axios'
import StudentProgressPanel from '@/components/StudentProgressPanel.vue'

const route = useRoute()
const studentId = computed(() => route.params.id)
const childName = ref('')

onMounted(async () => {
  try {
    const { data } = await axios.get('/api/parent/children')
    const match = (data.items || []).find((c) => String(c.student.id) === String(studentId.value))
    childName.value = match?.student?.name || ''
  } catch (e) {
    childName.value = ''
  }
})
</script>
