<template>
  <Teleport to="body">
    <div
      v-if="imageUrl"
      class="fixed inset-0 z-[99999] flex items-center justify-center p-4 sm:p-6 bg-black/80 backdrop-blur-md transition-all duration-300 animate-fadeIn"
      @click="close"
    >
      <div class="relative max-w-5xl max-h-[90vh] flex flex-col items-center justify-center" @click.stop>
        <button
          type="button"
          class="absolute -top-12 right-0 md:-top-4 md:-right-12 p-2.5 rounded-full bg-white/20 text-white hover:bg-white/40 transition-colors focus:outline-none shadow-lg cursor-pointer"
          title="Tutup (Esc)"
          @click="close"
        >
          <X class="w-6 h-6" />
        </button>

        <img
          :src="imageUrl"
          :alt="alt || 'Soal Gambar'"
          class="max-h-[85vh] max-w-[90vw] w-auto h-auto object-contain rounded-2xl shadow-2xl border border-white/10"
        />
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue'
import { X } from 'lucide-vue-next'

const props = defineProps({
  imageUrl: { type: String, default: '' },
  alt: { type: String, default: '' },
})

const emit = defineEmits(['close'])

const close = () => {
  emit('close')
}

const handleKeyDown = (e) => {
  if (e.key === 'Escape') {
    close()
  }
}

onMounted(() => {
  window.addEventListener('keydown', handleKeyDown)
})

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeyDown)
})
</script>

<style scoped>
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.97); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fadeIn {
  animation: fadeIn 0.2s ease-out forwards;
}
</style>
