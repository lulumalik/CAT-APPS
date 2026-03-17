<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <div class="mb-10 text-center">
      <h1 class="text-3xl font-bold text-[#1A1A1A]">{{ t('rankings.title') }}</h1>
      <p class="text-gray-500 mt-2">{{ t('rankings.subtitle') }}</p>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-12 text-center">
      <div class="w-24 h-24 bg-yellow-50 rounded-full flex items-center justify-center text-4xl mb-6 mx-auto">
        🏆
      </div>
      <h2 class="text-2xl font-bold text-[#1A1A1A] mb-2">{{ t('rankings.comingSoonTitle') }}</h2>
      <p class="text-gray-500 max-w-md mx-auto">
        {{ t('rankings.comingSoonDescription') }}
      </p>
    </div>
  </main>
</template>

<script setup>
import { useI18n } from '@/composables/useI18n'

const { t } = useI18n()
</script>

<style scoped>
</style>
