<template>
  <div class="section-wave-divider pointer-events-none select-none" aria-hidden="true">
    <img
      :src="vecteezyWaveUrl"
      alt=""
      class="block w-full h-14 sm:h-16 md:h-[320px]"
      :class="imageClass"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'inverse'].includes(value),
  },
})

const vecteezyWaveUrl = new URL('../../assets/Pattern.svg', import.meta.url).href
const imageClass = computed(() => (props.variant === 'inverse' ? 'brightness-0 invert opacity-85' : 'opacity-100'))
</script>
