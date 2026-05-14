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
  return Boolean(user?.registration?.fully_completed)
}
