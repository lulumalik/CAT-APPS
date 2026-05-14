/**
 * Public disk path as returned by Laravel `Storage::disk('public')->store(...)`.
 * Requires `php artisan storage:link` so `/storage/...` is served.
 */
export function storagePublicUrl(path) {
  if (!path || typeof path !== 'string') return ''
  const clean = path.replace(/^\/+/, '')
  return `/storage/${clean}`
}

export function isStoragePublicPath(val) {
  return typeof val === 'string' && val.length > 0 && !/^https?:\/\//i.test(val) && val.includes('/')
}

/**
 * Prefer API-provided URLs (S3/R2/public disk); fall back to /storage/ for local public paths.
 * @param {Record<string, unknown>|null|undefined} row Registration progress row from API
 * @param {string} pathKey e.g. id_document_path
 */
export function registrationFileHref(row, pathKey) {
  if (!row || !pathKey) return ''
  const fromApi = row.administration_file_urls?.[pathKey]
  if (typeof fromApi === 'string' && fromApi.length) return fromApi
  return storagePublicUrl(row.administration_data?.[pathKey])
}
