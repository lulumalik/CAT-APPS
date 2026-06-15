/**
 * Public disk path as returned by Laravel `Storage::disk('public')->store(...)`.
 * Served via Laravel route GET /storage/{path} (see routes/web.php).
 */
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

export function isStoragePublicPath(val) {
  return typeof val === 'string' && val.length > 0 && !/^https?:\/\//i.test(val) && val.includes('/')
}

/**
 * Prefer stored path → relative /storage/ URL (reliable on any domain).
 * @param {Record<string, unknown>|null|undefined} row Registration progress row from API
 * @param {string} pathKey e.g. id_document_path
 */
export function registrationFileHref(row, pathKey) {
  if (!row || !pathKey) return ''

  const storedPath = row.administration_data?.[pathKey]
  if (typeof storedPath === 'string' && storedPath.length) {
    if (/^https?:\/\//i.test(storedPath)) {
      return normalizeUrlSlashes(storedPath)
    }
    return storagePublicUrl(storedPath)
  }

  const fromApi = row.administration_file_urls?.[pathKey]
  if (typeof fromApi === 'string' && fromApi.length) {
    if (fromApi.startsWith('/')) {
      return normalizeUrlSlashes(fromApi)
    }
    try {
      const parsed = new URL(fromApi, typeof window !== 'undefined' ? window.location.origin : undefined)
      if (parsed.pathname.startsWith('/storage/')) {
        return normalizeUrlSlashes(parsed.pathname)
      }
    } catch {
      // fall through
    }
    return normalizeUrlSlashes(fromApi)
  }

  return ''
}
