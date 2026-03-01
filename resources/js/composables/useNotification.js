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

  const alert = (titleOrOptions, message, confirmText = 'OK') => {
    if (typeof titleOrOptions === 'object' && titleOrOptions !== null) {
      return showModal({
        type: 'alert',
        confirmText: 'OK',
        ...titleOrOptions
      })
    }
    return showModal({
      type: 'alert',
      title: titleOrOptions,
      message,
      confirmText,
    })
  }

  const confirm = (titleOrOptions, message, confirmText = 'Confirm', cancelText = 'Cancel') => {
    if (typeof titleOrOptions === 'object' && titleOrOptions !== null) {
      return showModal({
        type: 'confirm',
        confirmText: 'Confirm',
        cancelText: 'Cancel',
        ...titleOrOptions
      })
    }
    return showModal({
      type: 'confirm',
      title: titleOrOptions,
      message,
      confirmText,
      cancelText,
    })
  }

  const success = (titleOrOptions, message, confirmText = 'OK') => {
    if (typeof titleOrOptions === 'object' && titleOrOptions !== null) {
      return showModal({
        type: 'success',
        confirmText: 'OK',
        ...titleOrOptions
      })
    }
    return showModal({
      type: 'success',
      title: titleOrOptions,
      message,
      confirmText,
    })
  }

  const error = (titleOrOptions, message, confirmText = 'OK') => {
    if (typeof titleOrOptions === 'object' && titleOrOptions !== null) {
      return showModal({
        type: 'error',
        confirmText: 'OK',
        ...titleOrOptions
      })
    }
    return showModal({
      type: 'error',
      title: titleOrOptions,
      message,
      confirmText,
    })
  }

  const warning = (titleOrOptions, message, confirmText = 'OK', cancelText = 'Cancel') => {
    if (typeof titleOrOptions === 'object' && titleOrOptions !== null) {
      return showModal({
        type: 'warning',
        confirmText: 'OK',
        cancelText: 'Cancel',
        ...titleOrOptions
      })
    }
    return showModal({
      type: 'warning',
      title: titleOrOptions,
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
