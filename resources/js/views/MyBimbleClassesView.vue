<template>
  <main class="max-w-7xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-[#1A1A1A] mb-6">{{ t('bimble.myClassesTitle') }}</h1>
    <section
      v-if="isLocked"
      class="rounded-[2rem] border border-amber-200 bg-amber-50 p-8 text-amber-900"
    >
      <h2 class="text-xl font-bold flex items-center gap-2">
        <LockKeyhole class="h-5 w-5" />
        Kelas masih terkunci
      </h2>
      <p class="text-sm mt-2">
        Selesaikan onboarding hingga tahap fisik selesai disetujui admin untuk membuka akses kelas.
      </p>
      <router-link to="/registration" class="inline-flex mt-5 rounded-full bg-[#1A1A1A] px-5 py-2.5 text-sm font-semibold text-white">
        Buka halaman onboarding
      </router-link>
    </section>
    <div v-if="!isLocked && loading" class="py-16 text-center text-gray-500">{{ t('common.refresh') }}…</div>
    <div
      v-else-if="!isLocked && errorMessage"
      class="rounded-2xl border border-red-100 bg-red-50 p-6 text-sm text-red-700"
    >
      {{ errorMessage }}
    </div>
    <div v-else-if="!isLocked && !classes.length" class="rounded-2xl border border-gray-100 bg-white p-10 text-center text-gray-500 text-sm">
      {{ t('bimble.myClassesEmpty') }}
    </div>
    <div v-else-if="!isLocked" class="grid gap-4 md:grid-cols-2">
      <router-link
        v-for="c in classes"
        :key="c.id"
        :to="{ name: 'bimble-class-room', params: { id: c.id } }"
        class="rounded-[2rem] border border-gray-100 bg-white p-6 shadow-lg shadow-black/5 hover:border-[#9DB359]/40 transition-colors block"
      >
        <div class="font-bold text-lg text-[#1A1A1A]">{{ c.name }}</div>
        <div class="text-sm text-gray-500 mt-1">{{ c.class_code }} · {{ c.program_type }}</div>
      </router-link>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import { LockKeyhole } from 'lucide-vue-next'
import { useI18n } from '@/composables/useI18n'
import { useAppStore } from '@/stores/app'
import { storeToRefs } from 'pinia'
import { registrationCompleted } from '@/utils/userMeta'

const { t } = useI18n()
const store = useAppStore()
const { user } = storeToRefs(store)
const loading = ref(true)
const classes = ref([])
const errorMessage = ref('')
const isLocked = computed(() => user.value?.role === 'user' && !registrationCompleted(user.value))

onMounted(async () => {
  if (isLocked.value) {
    loading.value = false
    classes.value = []
    return
  }
  try {
    const { data } = await axios.get('/api/bimble-classes/mine')
    classes.value = (Array.isArray(data) ? data : []).filter((item) => item && item.id)
    errorMessage.value = ''
  } catch (error) {
    classes.value = []
    errorMessage.value =
      error?.response?.data?.message ||
      'Gagal memuat kelas. Pastikan database migration terbaru sudah dijalankan.'
  } finally {
    loading.value = false
  }
})
</script>
