import html2pdf from 'html2pdf.js'

/** Replace color functions html2canvas cannot parse. */
export function scrubModernColorFunctions(cssText) {
  if (!cssText) return cssText
  return cssText
    .replace(/oklch\([^)]*\)/gi, '#6b7280')
    .replace(/oklab\([^)]*\)/gi, '#6b7280')
    .replace(/color-mix\([^)]*\)/gi, 'rgb(107 114 128)')
}

function fixInlineStyleTags(doc) {
  doc.querySelectorAll('style').forEach((el) => {
    el.textContent = scrubModernColorFunctions(el.textContent)
  })
}

function fixStylesheetRules(doc) {
  for (const sheet of doc.styleSheets) {
    try {
      const rules = sheet.cssRules
      if (!rules) continue
      for (let i = rules.length - 1; i >= 0; i--) {
        const rule = rules[i]
        if (!rule?.cssText || !/oklch|oklab|color-mix/i.test(rule.cssText)) continue
        const fixed = scrubModernColorFunctions(rule.cssText)
        sheet.deleteRule(i)
        sheet.insertRule(fixed, i)
      }
    } catch {
      sheet.ownerNode?.remove?.()
    }
  }
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
`

function injectPdfLayoutStyles(doc) {
  const style = doc.createElement('style')
  style.setAttribute('data-pdf-export', 'true')
  style.textContent = PDF_EXPORT_LAYOUT_CSS
  doc.head.appendChild(style)
}

export function prepareClonedDocumentForCanvas(doc) {
  fixInlineStyleTags(doc)
  fixStylesheetRules(doc)
  injectPdfLayoutStyles(doc)
}

/**
 * Capture a DOM element and save it as a multi-page PDF (client-side).
 */
export async function downloadElementAsPdf(element, filename, options = {}) {
  if (!element) {
    throw new Error('Elemen PDF tidak ditemukan.')
  }

  const { html2canvas: html2canvasOpts = {}, ...restOptions } = options
  const { onclone: userOnClone, ...restHtml2canvas } = html2canvasOpts

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
      width: element.scrollWidth,
      height: element.scrollHeight,
      backgroundColor: '#ffffff',
      ...restHtml2canvas,
      onclone: (clonedDoc, clonedElement) => {
        prepareClonedDocumentForCanvas(clonedDoc)
        userOnClone?.(clonedDoc, clonedElement)
      },
    },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    pagebreak: { mode: ['css', 'legacy'], avoid: ['.pdf-section', '.pdf-progress-section', '.pdf-card'] },
    ...restOptions,
  }

  await html2pdf().set(opt).from(element).save()
}

export function sanitizePdfFilename(name) {
  return String(name || 'laporan')
    .replace(/[^\w\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .slice(0, 80) || 'laporan'
}
