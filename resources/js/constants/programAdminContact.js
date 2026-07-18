export const PROGRAM_ADMIN_CONTACT = {
  name: 'Admin CATLab',
  whatsapp: '628138964488',
  whatsappDisplay: '+62 813-8964-4888',
  emails: [
    'halo@catlab.id',
  ],
  bank: {
    name: 'Bank BRI',
    accountNumber: '1107-01-000931-56-9',
    accountHolder: 'CATLab',
  },
  paymentNote:
    'Setelah mendaftar, hubungi admin via WhatsApp untuk konfirmasi program dan instruksi lanjutan.',
}

export function programAdminWhatsAppUrl(message = '') {
  const base = `https://wa.me/${PROGRAM_ADMIN_CONTACT.whatsapp}`
  if (!message) return base
  return `${base}?text=${encodeURIComponent(message)}`
}
