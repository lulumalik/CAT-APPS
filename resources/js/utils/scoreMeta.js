/**
 * Tailwind classes for score tags on a 0–100 scale.
 * < 50 red, 50–74 yellow, >= 75 green.
 */
export function scoreTagClass(score) {
  const value = Number(score)
  if (Number.isNaN(value)) {
    return 'bg-gray-100 text-gray-600'
  }
  if (value < 50) {
    return 'bg-red-100 text-red-800'
  }
  if (value < 75) {
    return 'bg-yellow-100 text-yellow-800'
  }
  return 'bg-emerald-100 text-emerald-800'
}
