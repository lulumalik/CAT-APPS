export const normalizeProgramCategory = (program) => {
  if (program === 'vip_online' || program === 'vip_offline') {
    return 'vip'
  }
  if (program === 'regular_online' || program === 'regular_offline' || !program) {
    return 'regular'
  }
  return program
}

export const programCategoryLabel = (program) => {
  const normalized = normalizeProgramCategory(program)
  if (normalized === 'vip') return 'VIP (Online + Offline + Karantina)'
  if (normalized === 'regular') return 'Regular (Offline + Online)'
  if (normalized === 'bimbingan_online') return 'Program Bimbingan Online'
  if (normalized === 'try_out') return 'Try Out'
  return normalized
}

export const supportsProgramQuarantine = (program) => {
  return normalizeProgramCategory(program) === 'vip'
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

  if (program.startsWith('vip')) {
    return { label: 'Premium VIP', className: 'bg-purple-100 text-purple-700 border border-purple-200' }
  }

  if (program === 'try_out') {
    return { label: 'Try Out', className: 'bg-amber-100 text-amber-700 border border-amber-200' }
  }

  if (program === 'bimbingan_online') {
    return { label: 'Bimbingan', className: 'bg-cyan-100 text-cyan-700 border border-cyan-200' }
  }

  return { label: 'Regular', className: 'bg-emerald-100 text-emerald-700 border border-emerald-200' }
}

export const registrationCompleted = (user) => {
  return Boolean(user?.registration?.fully_completed)
}
