import { ref } from 'vue'

// Toast notifications
const toasts = ref([])
let toastId = 0

export function useToast() {
  const showToast = (type, title, message, duration = 3000) => {
    const id = toastId++
    toasts.value.push({
      id,
      type,
      title,
      message,
      duration,
      show: true
    })

    if (duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, duration)
    }
  }

  const removeToast = (id) => {
    const index = toasts.value.findIndex(t => t.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  return {
    toasts,
    showToast,
    removeToast,
    success: (title, message, duration) => showToast('success', title, message, duration),
    error: (title, message, duration) => showToast('error', title, message, duration),
    warning: (title, message, duration) => showToast('warning', title, message, duration),
    info: (title, message, duration) => showToast('info', title, message, duration),
  }
}

// Modal dialogs
const modalState = ref({
  show: false,
  type: 'alert',
  title: '',
  message: '',
  confirmText: 'OK',
  cancelText: 'Cancel',
  onConfirm: null,
  onCancel: null,
})

export function useModal() {
  const showModal = (options) => {
    return new Promise((resolve) => {
      modalState.value = {
        show: true,
        type: options.type || 'alert',
        title: options.title || 'Notification',
        message: options.message || '',
        confirmText: options.confirmText || 'OK',
        cancelText: options.cancelText || 'Cancel',
        onConfirm: () => {
          resolve(true)
          modalState.value.show = false
        },
        onCancel: () => {
          resolve(false)
          modalState.value.show = false
        },
      }
    })
  }

  const alert = (title, message, confirmText = 'OK') => {
    return showModal({
      type: 'alert',
      title,
      message,
      confirmText,
    })
  }

  const confirm = (title, message, confirmText = 'Confirm', cancelText = 'Cancel') => {
    return showModal({
      type: 'confirm',
      title,
      message,
      confirmText,
      cancelText,
    })
  }

  const success = (title, message, confirmText = 'OK') => {
    return showModal({
      type: 'success',
      title,
      message,
      confirmText,
    })
  }

  const error = (title, message, confirmText = 'OK') => {
    return showModal({
      type: 'error',
      title,
      message,
      confirmText,
    })
  }

  const warning = (title, message, confirmText = 'OK', cancelText = 'Cancel') => {
    return showModal({
      type: 'warning',
      title,
      message,
      confirmText,
      cancelText,
    })
  }

  return {
    modalState,
    showModal,
    alert,
    confirm,
    success,
    error,
    warning,
  }
}
