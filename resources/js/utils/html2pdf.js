import html2pdf from 'html2pdf.js'

const CANVAS_STYLE_PROPS = [
  'display', 'position', 'top', 'right', 'bottom', 'left', 'z-index',
  'width', 'height', 'min-width', 'min-height', 'max-width', 'max-height',
  'margin', 'margin-top', 'margin-right', 'margin-bottom', 'margin-left',
  'padding', 'padding-top', 'padding-right', 'padding-bottom', 'padding-left',
  'border', 'border-width', 'border-style', 'border-color', 'border-radius',
  'border-top', 'border-right', 'border-bottom', 'border-left',
  'background', 'background-color', 'background-image', 'background-size', 'background-position',
  'color', 'fill', 'stroke', 'stroke-width',
  'font-family', 'font-size', 'font-weight', 'font-style', 'line-height',
  'letter-spacing', 'text-align', 'text-transform', 'text-decoration', 'white-space', 'word-break',
  'flex', 'flex-direction', 'flex-wrap', 'flex-grow', 'flex-shrink', 'flex-basis',
  'align-items', 'align-self', 'justify-content', 'justify-items', 'gap', 'row-gap', 'column-gap',
  'grid', 'grid-template-columns', 'grid-template-rows', 'grid-column', 'grid-row',
  'opacity', 'visibility', 'overflow', 'overflow-x', 'overflow-y',
  'box-shadow', 'outline', 'object-fit', 'vertical-align', 'list-style',
  'transform', 'transform-origin',
]

function stripStylesheets(doc) {
  doc.querySelectorAll('style, link[rel="stylesheet"]').forEach((node) => node.remove())
}

function safeCanvasValue(value) {
  if (!value || value === 'none' || value === 'normal' || value === 'auto') return value
  if (/oklch|oklab|color-mix|lch\(/i.test(value)) return null
  return value
}

function mirrorComputedStyles(sourceRoot, cloneRoot) {
  const sourceNodes = [sourceRoot, ...sourceRoot.querySelectorAll('*')]
  const cloneNodes = [cloneRoot, ...cloneRoot.querySelectorAll('*')]

  sourceNodes.forEach((source, index) => {
    const target = cloneNodes[index]
    if (!target) return

    const computed = window.getComputedStyle(source)
    CANVAS_STYLE_PROPS.forEach((prop) => {
      const value = safeCanvasValue(computed.getPropertyValue(prop))
      if (value) {
        target.style.setProperty(prop, value)
      }
    })
  })
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
    margin: [8, 8, 8, 8],
    filename,
    image: { type: 'jpeg', quality: 0.95 },
    html2canvas: {
      scale: 2,
      useCORS: true,
      logging: false,
      scrollY: -window.scrollY,
      windowWidth: element.scrollWidth,
      ...restHtml2canvas,
      onclone: (clonedDoc, clonedElement) => {
        stripStylesheets(clonedDoc)
        mirrorComputedStyles(element, clonedElement)
        userOnClone?.(clonedDoc, clonedElement)
      },
    },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    pagebreak: { mode: ['css', 'legacy'] },
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
