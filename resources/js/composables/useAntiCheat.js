import { ref } from 'vue'
import { useToast } from '@/composables/useNotification'

export function useAntiCheat({ maxViolations = 5, onMaxViolations, isActive } = {}) {
  const violations = ref(0)
  const antiCheatMessage = ref('')
  const limitReached = ref(false)
  const toast = useToast()

  const registerViolation = async (reason) => {
    if (typeof isActive === 'function' ? !isActive() : isActive?.value === false) return

    if (limitReached.value) {
      antiCheatMessage.value = 'Kamu sudah terlalu banyak hal yang melanggar aturan anti-cheat.'
      return
    }

    violations.value += 1
    antiCheatMessage.value = reason
    toast.error('Peringatan anti-cheat', reason)

    if (violations.value >= maxViolations) {
      limitReached.value = true
      antiCheatMessage.value = 'Kamu sudah terlalu banyak hal yang melanggar aturan anti-cheat.'
      toast.error(
        'Peringatan anti-cheat',
        'Kamu sudah terlalu banyak hal yang melanggar aturan anti-cheat. Tetap di halaman ujian dan lanjutkan mengerjakan soal.',
      )
      await onMaxViolations?.()
    }
  }

  const preventClipboardAction = (event) => {
    event.preventDefault()
    registerViolation('Copy/paste/cut tidak diizinkan selama ujian.')
  }

  const preventContextMenu = (event) => {
    event.preventDefault()
    registerViolation('Klik kanan dinonaktifkan selama ujian.')
  }

  const handleKeydown = (event) => {
    const key = String(event.key || '').toLowerCase()
    const ctrlOrMeta = event.ctrlKey || event.metaKey
    const blockedCtrlKeys = ['c', 'x', 'v', 'a', 'u', 'p', 's']
    const isDevToolsShortcut =
      key === 'f12' ||
      (ctrlOrMeta && event.shiftKey && ['i', 'j', 'c'].includes(key)) ||
      (ctrlOrMeta && blockedCtrlKeys.includes(key))

    if (isDevToolsShortcut) {
      event.preventDefault()
      registerViolation('Shortcut ini diblokir selama ujian.')
    }
  }

  const handleVisibilityChange = () => {
    if (document.hidden) {
      registerViolation('Anda berpindah tab/jendela. Tetap di halaman ujian.')
    }
  }

  const handleWindowBlur = () => {
    registerViolation('Fokus jendela terlepas dari halaman ujian.')
  }

  const requestFullscreen = async () => {
    const root = document.documentElement
    if (!document.fullscreenElement && root?.requestFullscreen) {
      try {
        await root.requestFullscreen()
      } catch {
        // Browser may require user gesture; keep test running.
      }
    }
  }

  const handleFullscreenChange = () => {
    if (!document.fullscreenElement) {
      registerViolation('Mode fullscreen ditutup selama ujian.')
      requestFullscreen()
    }
  }

  const attach = () => {
    document.addEventListener('copy', preventClipboardAction)
    document.addEventListener('cut', preventClipboardAction)
    document.addEventListener('paste', preventClipboardAction)
    document.addEventListener('contextmenu', preventContextMenu)
    document.addEventListener('keydown', handleKeydown)
    document.addEventListener('visibilitychange', handleVisibilityChange)
    window.addEventListener('blur', handleWindowBlur)
    document.addEventListener('fullscreenchange', handleFullscreenChange)
  }

  const detach = () => {
    document.removeEventListener('copy', preventClipboardAction)
    document.removeEventListener('cut', preventClipboardAction)
    document.removeEventListener('paste', preventClipboardAction)
    document.removeEventListener('contextmenu', preventContextMenu)
    document.removeEventListener('keydown', handleKeydown)
    document.removeEventListener('visibilitychange', handleVisibilityChange)
    window.removeEventListener('blur', handleWindowBlur)
    document.removeEventListener('fullscreenchange', handleFullscreenChange)
  }

  return {
    violations,
    antiCheatMessage,
    maxViolations,
    limitReached,
    attach,
    detach,
    requestFullscreen,
  }
}
