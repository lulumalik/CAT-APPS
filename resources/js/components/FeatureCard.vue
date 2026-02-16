<template>
  <div class="rounded-xl border border-gray-200 shadow-sm p-6 md:p-8 bg-white/80 backdrop-blur">
    <div class="w-10 h-10 grid place-items-center rounded-lg" :class="iconClass">{{ icon }}</div>
    <h3 class="mt-4 text-xl font-medium">{{ title }}</h3>
    <p class="mt-2 text-muted">{{ desc }}</p>
  </div>
</template>

<script setup>
defineProps({
  icon: { type: String, required: true },
  iconClass: { type: String, default: '' },
  title: { type: String, required: true },
  desc: { type: String, required: true },
})
</script>

<style scoped>
</style>
