/**
 * Cookie helpers (per-browser). Used for the email-verification resend cooldown
 * so the countdown survives reloads and naturally clears when the cookie expires.
 */

export function setCookie(name, value, expiresAtMs) {
  if (typeof document === 'undefined') return
  let cookie = `${encodeURIComponent(name)}=${encodeURIComponent(value)}; path=/; SameSite=Lax`
  if (expiresAtMs) {
    cookie += `; expires=${new Date(expiresAtMs).toUTCString()}`
  }
  document.cookie = cookie
}

export function getCookie(name) {
  if (typeof document === 'undefined') return null
  const escaped = name.replace(/([.$?*|{}()[\]\\/+^])/g, '\\$1')
  const match = document.cookie.match(new RegExp('(?:^|; )' + escaped + '=([^;]*)'))
  return match ? decodeURIComponent(match[1]) : null
}

export function deleteCookie(name) {
  if (typeof document === 'undefined') return
  document.cookie = `${encodeURIComponent(name)}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`
}
