<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="handleBackdropClick"></div>
        
        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
          <div class="relative bg-white rounded-[2rem] shadow-2xl shadow-black/10 max-w-md w-full transform transition-all border border-gray-100">
            <!-- Icon -->
            <div class="pt-8 px-8 text-center">
              <div class="mx-auto w-16 h-16 rounded-full flex items-center justify-center text-3xl" :class="iconBgClass">
                {{ icon }}
              </div>
            </div>

            <!-- Content -->
            <div class="px-8 py-6 text-center">
              <h3 class="text-2xl font-bold text-[#1A1A1A] mb-3">{{ title }}</h3>
              <p class="text-gray-500 leading-relaxed">{{ message }}</p>
            </div>

            <!-- Actions -->
            <div class="px-8 pb-8 flex gap-3" :class="type === 'confirm' ? 'flex-row' : 'flex-col'">
              <button
                v-if="type === 'confirm'"
                @click="handleCancel"
                class="flex-1 px-6 py-3 rounded-full border border-gray-200 text-gray-700 font-medium hover:bg-gray-50 transition-colors cursor-pointer"
              >
                {{ cancelText }}
              </button>
              <button
                @click="handleConfirm"
                class="flex-1 px-6 py-3 rounded-full font-medium text-white transition-all transform hover:scale-[1.02] cursor-pointer shadow-lg"
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
    default: 'alert', // 'alert', 'confirm', 'success', 'error', 'warning', 'danger'
    validator: (value) => ['alert', 'confirm', 'success', 'error', 'warning', 'danger'].includes(value)
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
    case 'danger': return '⚠'
    default: return 'ℹ'
  }
})

const iconBgClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bg-green-100 text-green-600'
    case 'error': return 'bg-red-100 text-red-600'
    case 'warning': return 'bg-yellow-100 text-yellow-600'
    case 'confirm': return 'bg-blue-100 text-blue-600'
    case 'danger': return 'bg-red-100 text-red-600'
    default: return 'bg-gray-100 text-gray-600'
  }
})

const confirmButtonClass = computed(() => {
  switch (props.type) {
    case 'success': return 'bg-[#9DB359] shadow-[#9DB359]/20 hover:bg-[#8ca34b]'
    case 'error': return 'bg-red-600 shadow-red-600/20 hover:bg-red-700'
    case 'warning': return 'bg-yellow-500 shadow-yellow-500/20 hover:bg-yellow-600'
    case 'confirm': return 'bg-[#1A1A1A] shadow-black/20 hover:bg-black'
    case 'danger': return 'bg-red-600 shadow-red-600/20 hover:bg-red-700'
    default: return 'bg-[#1A1A1A] shadow-black/20 hover:bg-black'
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
    emit('close')
  }
}
</script>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

.modal-enter-active .transform,
.modal-leave-active .transform {
  transition: all 0.3s ease;
}

.modal-enter-from .transform,
.modal-leave-to .transform {
  opacity: 0;
  transform: scale(0.95);
}
</style>
