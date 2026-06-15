import html2pdf from 'html2pdf.js'

const UNSUPPORTED_COLOR_RE = /oklch|oklab|color-mix|lch\(/i

/** Replace color functions html2canvas cannot parse (handles nested parentheses). */
export function scrubModernColorFunctions(cssText) {
  if (!cssText) return cssText

  let result = cssText
  const tokens = ['oklch(', 'oklab(', 'color-mix(', 'lch(']

  for (const token of tokens) {
    let searchFrom = 0
    while (true) {
      const start = result.toLowerCase().indexOf(token, searchFrom)
      if (start === -1) break

      let depth = 0
      let end = start
      for (let i = start; i < result.length; i++) {
        if (result[i] === '(') depth += 1
        if (result[i] === ')') {
          depth -= 1
          if (depth === 0) {
            end = i + 1
            break
          }
        }
      }

      const replacement = token.startsWith('color-mix') ? 'rgb(107, 114, 128)' : '#6b7280'
      result = result.slice(0, start) + replacement + result.slice(end)
      searchFrom = start + replacement.length
    }
  }

  return result
}

function stripAllStylesheets(doc) {
  doc.querySelectorAll('style, link[rel="stylesheet"]').forEach((node) => node.remove())
}

function scrubInlineStyles(root) {
  const nodes = [root, ...root.querySelectorAll('*')]
  nodes.forEach((node) => {
    const inline = node.getAttribute?.('style')
    if (inline && UNSUPPORTED_COLOR_RE.test(inline)) {
      node.setAttribute('style', scrubModernColorFunctions(inline))
    }
  })
}

function mirrorAllComputedStyles(sourceRoot, cloneRoot) {
  const sourceNodes = [sourceRoot, ...sourceRoot.querySelectorAll('*')]
  const cloneNodes = [cloneRoot, ...cloneRoot.querySelectorAll('*')]

  sourceNodes.forEach((source, index) => {
    const target = cloneNodes[index]
    if (!target || !source) return

    const computed = window.getComputedStyle(source)
    for (let i = 0; i < computed.length; i++) {
      const prop = computed[i]
      const value = computed.getPropertyValue(prop)
      if (!value) continue
      if (UNSUPPORTED_COLOR_RE.test(value)) continue

      try {
        target.style.setProperty(prop, value, computed.getPropertyPriority(prop))
      } catch {
        // Some properties cannot be set as inline styles.
      }
    }
  })
}

export const PDF_EXPORT_LAYOUT_CSS = `
.dashboard-pdf-export {
  width: 720px !important;
  max-width: 720px !important;
  margin: 0 auto !important;
  background: #ffffff !important;
  color: #1a1a1a !important;
  font-family: ui-sans-serif, system-ui, -apple-system, sans-serif !important;
  box-sizing: border-box !important;
}
.dashboard-pdf-export *,
.dashboard-pdf-export *::before,
.dashboard-pdf-export *::after {
  box-sizing: border-box !important;
}
.dashboard-pdf-export .pdf-grid-2 {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  gap: 1.5rem !important;
}
.dashboard-pdf-export .pdf-section {
  background: #ffffff !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 1rem !important;
  padding: 1.25rem !important;
  break-inside: avoid !important;
  page-break-inside: avoid !important;
}
.dashboard-pdf-export .pdf-card {
  border: 1px solid #e5e7eb !important;
  border-radius: 0.75rem !important;
  padding: 0.75rem !important;
  margin-bottom: 0.75rem !important;
  background: #ffffff !important;
}
.dashboard-pdf-export .pdf-card:last-child {
  margin-bottom: 0 !important;
}
.dashboard-pdf-export .pdf-header {
  border-bottom: 1px solid #e5e7eb !important;
  margin-bottom: 1.5rem !important;
  padding-bottom: 1rem !important;
}
.dashboard-pdf-export .pdf-section-title {
  font-size: 1.125rem !important;
  font-weight: 700 !important;
  color: #1a1a1a !important;
  margin-bottom: 1rem !important;
}
.dashboard-pdf-export .pdf-subsection-title {
  font-size: 1rem !important;
  font-weight: 700 !important;
  color: #1a1a1a !important;
  margin-bottom: 0.75rem !important;
}
.dashboard-pdf-export .pdf-muted {
  color: #6b7280 !important;
  font-size: 0.75rem !important;
}
.dashboard-pdf-export .pdf-text-sm {
  font-size: 0.875rem !important;
  color: #374151 !important;
}
.dashboard-pdf-export .pdf-weekly-card {
  border: 1px solid rgba(157, 179, 89, 0.3) !important;
  background: rgba(157, 179, 89, 0.05) !important;
  border-radius: 0.75rem !important;
  padding: 1rem !important;
  margin-bottom: 0.75rem !important;
}
.dashboard-pdf-export .pdf-tag {
  display: inline-block !important;
  background: #f3f4f6 !important;
  color: #4b5563 !important;
  font-size: 11px !important;
  border-radius: 9999px !important;
  padding: 0.25rem 0.625rem !important;
  margin: 0.125rem !important;
}
.dashboard-pdf-export .pdf-progress-section {
  background: #ffffff !important;
  border: 1px solid #e5e7eb !important;
  border-radius: 1rem !important;
  padding: 1.25rem !important;
  margin-bottom: 1.5rem !important;
  break-inside: avoid !important;
  page-break-inside: avoid !important;
}
.dashboard-pdf-export .pdf-hide {
  display: none !important;
}
.dashboard-pdf-export svg {
  max-width: 100% !important;
  height: auto !important;
}
.dashboard-pdf-export .space-y-6 > * + * {
  margin-top: 1.5rem !important;
}
.dashboard-pdf-export .space-y-3 > * + * {
  margin-top: 0.75rem !important;
}
.dashboard-pdf-export .flex {
  display: flex !important;
}
.dashboard-pdf-export .flex-wrap {
  flex-wrap: wrap !important;
}
.dashboard-pdf-export .items-center {
  align-items: center !important;
}
.dashboard-pdf-export .justify-between {
  justify-content: space-between !important;
}
.dashboard-pdf-export .gap-2 {
  gap: 0.5rem !important;
}
.dashboard-pdf-export .font-semibold {
  font-weight: 600 !important;
}
.dashboard-pdf-export .font-bold {
  font-weight: 700 !important;
}
.dashboard-pdf-export .text-sm {
  font-size: 0.875rem !important;
}
.dashboard-pdf-export .text-xs {
  font-size: 0.75rem !important;
}
.dashboard-pdf-export .text-xl {
  font-size: 1.25rem !important;
}
.dashboard-pdf-export .mt-1 {
  margin-top: 0.25rem !important;
}
.dashboard-pdf-export .mt-8 {
  margin-top: 2rem !important;
}
.dashboard-pdf-export .mb-4 {
  margin-bottom: 1rem !important;
}
.dashboard-pdf-export .shrink-0 {
  flex-shrink: 0 !important;
}
.dashboard-pdf-export .capitalize {
  text-transform: capitalize !important;
}
.dashboard-pdf-export .whitespace-pre-line {
  white-space: pre-line !important;
}
`

function injectPdfLayoutStyles(doc) {
  const style = doc.createElement('style')
  style.setAttribute('data-pdf-export', 'true')
  style.textContent = PDF_EXPORT_LAYOUT_CSS
  doc.head.appendChild(style)
}

function createIsolatedPrintRoot(sourceElement) {
  const iframe = document.createElement('iframe')
  iframe.setAttribute('aria-hidden', 'true')
  iframe.setAttribute('tabindex', '-1')
  Object.assign(iframe.style, {
    position: 'fixed',
    left: '-10000px',
    top: '0',
    width: '820px',
    height: '1px',
    border: '0',
    visibility: 'hidden',
  })
  document.body.appendChild(iframe)

  const iwin = iframe.contentWindow
  const idoc = iframe.contentDocument
  if (!iwin || !idoc) {
    iframe.remove()
    throw new Error('Gagal menyiapkan area cetak PDF.')
  }

  idoc.open()
  idoc.write('<!DOCTYPE html><html><head></head><body style="margin:0;background:#ffffff;"></body></html>')
  idoc.close()

  const clone = sourceElement.cloneNode(true)
  mirrorAllComputedStyles(sourceElement, clone)
  scrubInlineStyles(clone)
  idoc.body.appendChild(clone)
  injectPdfLayoutStyles(idoc)

  return { iframe, iwin, idoc, clone }
}

export function prepareClonedDocumentForCanvas(doc, sourceRoot, cloneRoot) {
  stripAllStylesheets(doc)
  mirrorAllComputedStyles(sourceRoot, cloneRoot)
  scrubInlineStyles(cloneRoot)
  injectPdfLayoutStyles(doc)
}

/**
 * Capture a DOM element and save it as a multi-page PDF (client-side).
 * Renders inside an isolated iframe so Tailwind oklch styles are never parsed.
 */
export async function downloadElementAsPdf(element, filename, options = {}) {
  if (!element) {
    throw new Error('Elemen PDF tidak ditemukan.')
  }

  const { html2canvas: html2canvasOpts = {}, ...restOptions } = options
  const { onclone: userOnClone, ...restHtml2canvas } = html2canvasOpts

  const { iframe, iwin, clone } = createIsolatedPrintRoot(element)

  try {
    await new Promise((resolve) => requestAnimationFrame(() => requestAnimationFrame(resolve)))

    const opt = {
      margin: [10, 10, 10, 10],
      filename,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: {
        scale: 2,
        useCORS: true,
        logging: false,
        scrollY: 0,
        scrollX: 0,
        windowWidth: 1280,
        backgroundColor: '#ffffff',
        window: iwin,
        width: clone.scrollWidth || 720,
        height: clone.scrollHeight || element.scrollHeight,
        ...restHtml2canvas,
        onclone: (clonedDoc, clonedElement) => {
          stripAllStylesheets(clonedDoc)
          injectPdfLayoutStyles(clonedDoc)
          userOnClone?.(clonedDoc, clonedElement)
        },
      },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
      pagebreak: { mode: ['css', 'legacy'], avoid: ['.pdf-section', '.pdf-progress-section', '.pdf-card'] },
      ...restOptions,
    }

    await html2pdf().set(opt).from(clone).save()
  } finally {
    iframe.remove()
  }
}

export function sanitizePdfFilename(name) {
  return String(name || 'laporan')
    .replace(/[^\w\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .slice(0, 80) || 'laporan'
}
