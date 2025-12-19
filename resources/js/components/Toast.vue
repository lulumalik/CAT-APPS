<template>
  <Teleport to="body">
    <Transition name="toast">
      <div
        v-if="show"
        class="fixed top-4 right-4 z-50 max-w-sm w-full"
      >
        <div class="bg-white rounded-xl shadow-2xl border-2 overflow-hidden transform transition-all" :class="borderClass">
          <div class="flex items-start gap-4 p-4">
            <!-- Icon -->
            <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-xl" :class="iconBgClass">
              {{ icon }}
            </div>

            <!-- Content -->
            <div class="flex-1 min-w-0">
              <h4 class="font-semibold text-gray-900 mb-1">{{ title }}</h4>
              <p class="text-sm text-gray-600">{{ message }}</p>
            </div>

            <!-- Close Button -->
            <button
              @click="$emit('close')"
              class="flex-shrink-0 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
              </svg>
            </button>
          </div>

          <!-- Progress Bar -->
          <div v-if="duration" class="h-1 bg-gray-100">
            <div class="h-full transition-all ease-linear" :class="progressBarClass" :style="{ width: progress + '%' }"></div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, computed, watch, onUnmounted } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'info',
    validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
  },
  title: {
    type: String,
    default: 'Notification'
  },
  message: {
    type: String,
    default: ''
  },
  duration: {
    type: Number,
    default: 3000
  }
})

const emit = defineEmits(['close'])

const progress = ref(100)
let timer = null
let progressTimer = null

const icon = computed(() => {
  switch (props.type) {
    case 'success': return '✓'
    case 'error': return '✕'
    case 'warning': return '⚠'
    default: return 'ℹ'
  }
})

const iconBgClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bg-green-100 text-green-600'
    case 'error': return 'bg-red-100 text-red-600'
    case 'warning': return 'bg-yellow-100 text-yellow-600'
    default: return 'bg-blue-100 text-blue-600'
  }
})

const borderClass = computed(() => {
  switch (props.type) {
    case 'success': return 'border-green-200'
    case 'error': return 'border-red-200'
    case 'warning': return 'border-yellow-200'
    default: return 'border-blue-200'
  }
})

const progressBarClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bg-green-500'
    case 'error': return 'bg-red-500'
    case 'warning': return 'bg-yellow-500'
    default: return 'bg-blue-500'
  }
})

const startTimer = () => {
  if (!props.duration) return
  
  progress.value = 100
  
  const interval = 50
  const step = (interval / props.duration) * 100
  
  progressTimer = setInterval(() => {
    progress.value -= step
    if (progress.value <= 0) {
      clearInterval(progressTimer)
    }
  }, interval)
  
  timer = setTimeout(() => {
    emit('close')
  }, props.duration)
}

const stopTimer = () => {
  if (timer) clearTimeout(timer)
  if (progressTimer) clearInterval(progressTimer)
}

watch(() => props.show, (newVal) => {
  if (newVal) {
    startTimer()
  } else {
    stopTimer()
  }
})

onUnmounted(() => {
  stopTimer()
})
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateY(-20px);
}
</style>
