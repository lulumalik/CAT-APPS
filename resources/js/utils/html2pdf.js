import html2pdf from 'html2pdf.js'

/**
 * Capture a DOM element and save it as a multi-page PDF (client-side).
 */
export async function downloadElementAsPdf(element, filename, options = {}) {
  if (!element) {
    throw new Error('Elemen PDF tidak ditemukan.')
  }

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
    },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
    pagebreak: { mode: ['css', 'legacy'] },
    ...options,
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
