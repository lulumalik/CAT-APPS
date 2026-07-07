import { ONLINE_PROGRAMS } from '@/constants/onlinePrograms'

export const normalizeProgramCategory = (program) => {
  if (program === 'vip_online' || program === 'vip_offline') {
    return 'vip'
  }
  if (program === 'regular_online' || program === 'regular_offline' || !program) {
    return 'regular'
  }
  return program
}

const programRow = (normalized) => ONLINE_PROGRAMS.find((p) => p.value === normalized)

export const programCategoryLabel = (program) => {
  const normalized = normalizeProgramCategory(program)
  const row = programRow(normalized)
  if (row) return row.name
  return normalized
}

export const supportsProgramQuarantine = (program) => {
  return normalizeProgramCategory(program) === 'vip'
}

export const usesSimplifiedOnboarding = (user) => {
  if (user?.uses_simplified_onboarding === true) return true
  const program = normalizeProgramCategory(user?.program_category)
  return program === 'bimbingan_online' || program === 'try_out'
}

export const isExamOnlyProgram = (user) => {
  if (user?.is_exam_only_program === true) return true
  return normalizeProgramCategory(user?.program_category) === 'try_out'
}

const badgeStyleByValue = {
  vip: { className: 'bg-sky text-primary border border-border' },
  regular: { className: 'bg-mint text-primary border border-border' },
  bimbingan_online: { className: 'bg-cream text-text border border-border' },
  try_out: { className: 'bg-background text-text border border-border' },
}

export const getProgramBadge = (user) => {
  const role = user?.role || 'guest'
  const program = normalizeProgramCategory(user?.program_category || '')

  if (role === 'admin') {
    return { label: 'Admin', className: 'bg-slate-100 text-slate-700 border border-slate-200' }
  }

  if (role === 'mentor') {
    return { label: 'Mentor', className: 'bg-blue-100 text-blue-700 border border-blue-200' }
  }

  const row = programRow(program)
  if (row) {
    const style = badgeStyleByValue[program] || badgeStyleByValue.regular
    return { label: row.mode, className: style.className }
  }

  return { label: program, className: 'bg-gray-100 text-gray-700 border border-gray-200' }
}

export const registrationCompleted = (user) => {
  if (user?.onboarding_completed === true) return true
  if (usesSimplifiedOnboarding(user)) {
    return Boolean(user?.email_verified_at)
  }
  return Boolean(user?.registration?.fully_completed)
}

export const isAppExpired = (user) => {
  if (user?.app_expired === true) return true
  if (!user?.app_expires_at) return false
  return new Date(user.app_expires_at) < new Date()
}

export const formatAppExpiresAt = (value) => {
  if (!value) return null
  try {
    return new Date(value).toLocaleString('id-ID', {
      day: '2-digit',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Asia/Jakarta',
    })
  } catch {
    return value
  }
}

export const toDateInputValue = (value) => {
  if (!value) return ''
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return ''
  const pad = (n) => String(n).padStart(2, '0')
  return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`
}

export const dateInputToExpiresAt = (value) => {
  const trimmed = String(value || '').trim()
  if (!trimmed) return null
  if (trimmed.includes('T')) return trimmed
  return `${trimmed}T23:59:59`
}
