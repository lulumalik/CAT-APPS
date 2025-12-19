<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="handleBackdropClick"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all">
            <!-- Icon -->
            <div class="pt-8 px-8 text-center">
              <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center text-3xl" :class="iconBgClass">
                {{ icon }}
              </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-6 text-center">
              <h3 class="text-2xl font-semibold text-gray-900 mb-3">{{ title }}</h3>
              <p class="text-gray-600 leading-relaxed">{{ message }}</p>
            </div>

            <!-- Actions -->
            <div class="px-8 pb-8 flex gap-3" :class="type === 'confirm' ? 'flex-row' : 'flex-col'">
              <button
                v-if="type === 'confirm'"
                @click="handleCancel"
                class="flex-1 px-6 py-3 rounded-xl border-2 border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors cursor-pointer"
              >
                {{ cancelText }}
              </button>
              <button
                @click="handleConfirm"
                class="flex-1 px-6 py-3 rounded-xl font-medium text-white transition-all transform hover:scale-105 cursor-pointer"
                :class="confirmButtonClass"
              >
                {{ confirmText }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'alert', // 'alert', 'confirm', 'success', 'error', 'warning'
    validator: (value) => ['alert', 'confirm', 'success', 'error', 'warning'].includes(value)
  },
  title: {
    type: String,
    default: 'Notification'
  },
  message: {
    type: String,
    default: ''
  },
  confirmText: {
    type: String,
    default: 'OK'
  },
  cancelText: {
    type: String,
    default: 'Cancel'
  },
  closeOnBackdrop: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['confirm', 'cancel', 'close'])

const icon = computed(() => {
  switch (props.type) {
    case 'success': return '✓'
    case 'error': return '✕'
    case 'warning': return '⚠'
    case 'confirm': return '?'
    default: return 'ℹ'
  }
})

const iconBgClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bg-green-100 text-green-600'
    case 'error': return 'bg-red-100 text-red-600'
    case 'warning': return 'bg-yellow-100 text-yellow-600'
    case 'confirm': return 'bg-blue-100 text-blue-600'
    default: return 'bg-gray-100 text-gray-600'
  }
})

const confirmButtonClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bg-green-600 hover:bg-green-700 shadow-lg shadow-green-600/30'
    case 'error': return 'bg-red-600 hover:bg-red-700 shadow-lg shadow-red-600/30'
    case 'warning': return 'bg-yellow-600 hover:bg-yellow-700 shadow-lg shadow-yellow-600/30'
    case 'confirm': return 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/30'
    default: return 'bg-gray-600 hover:bg-gray-700 shadow-lg shadow-gray-600/30'
  }
})

const handleConfirm = () => {
  emit('confirm')
  emit('close')
}

const handleCancel = () => {
  emit('cancel')
  emit('close')
}

const handleBackdropClick = () => {
  if (props.closeOnBackdrop) {
    emit('cancel')
    emit('close')
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.3s ease, opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-from .relative,
.modal-leave-to .relative {
  transform: scale(0.9);
  opacity: 0;
}
</style>
