const FIELD_BY_PATH_KEY = {
  id_document_path: 'id_document',
  kk_path: 'kk',
  report_card_path: 'report_card',
  passport_photo_path: 'passport_photo',
  full_body_photo_path: 'full_body_photo',
}

const REGISTRATION_STORAGE_PREFIX = 'registration/'

export function normalizeUrlSlashes(url) {
  if (!url || typeof url !== 'string') return ''
  if (url.startsWith('/')) {
    return url.replace(/\/{2,}/g, '/')
  }
  return url.replace(/([^:]\/)\/+/g, '$1')
}

export function storagePublicUrl(path) {
  if (!path || typeof path !== 'string') return ''
  const clean = path.replace(/^\/+/, '')
  return normalizeUrlSlashes(`/storage/${clean}`)
}

export function registrationFileApiUrl(row, pathKey) {
  if (!row || !pathKey) return ''
  const field = FIELD_BY_PATH_KEY[pathKey]
  const userId = row.user_id
  if (!field || userId == null) return ''
  return `/api/registration-files/${userId}/${field}`
}

export function isStoragePublicPath(val) {
  return typeof val === 'string' && val.length > 0 && !/^https?:\/\//i.test(val) && val.includes('/')
}

function isRegistrationStoragePath(path) {
  return typeof path === 'string' && path.replace(/^\/+/, '').startsWith(REGISTRATION_STORAGE_PREFIX)
}

/**
 * Authenticated API URL only for registration documents (owner or admin session).
 * @param {Record<string, unknown>|null|undefined} row Registration progress row from API
 * @param {string} pathKey e.g. id_document_path
 */
export function registrationFileHref(row, pathKey) {
  if (!row || !pathKey) return ''

  const fromApi = row.administration_file_urls?.[pathKey]
  if (typeof fromApi === 'string' && fromApi.startsWith('/api/registration-files/')) {
    return normalizeUrlSlashes(fromApi)
  }

  const apiUrl = registrationFileApiUrl(row, pathKey)
  if (apiUrl) return apiUrl

  const storedPath = row.administration_data?.[pathKey]
  if (isRegistrationStoragePath(storedPath)) {
    return registrationFileApiUrl(row, pathKey)
  }

  if (typeof storedPath === 'string' && storedPath.length && /^https?:\/\//i.test(storedPath)) {
    return normalizeUrlSlashes(storedPath)
  }

  if (typeof storedPath === 'string' && storedPath.length && !isRegistrationStoragePath(storedPath)) {
    return storagePublicUrl(storedPath)
  }

  return ''
}
