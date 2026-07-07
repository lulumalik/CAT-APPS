export const PROGRAM_ADMIN_CONTACT = {
  name: 'Admin Pratistha Cendekia Prestasi',
  whatsapp: '628138964488',
  whatsappDisplay: '+62 813-8964-4888',
  emails: [
    'administrator@pratisthaindonesia.com',
    'admin.pratistha@gmail.com',
  ],
  bank: {
    name: 'Bank BRI',
    accountNumber: '1107-01-000931-56-9',
    accountHolder: 'PT. Pratistha Training Center Indonesia',
  },
  paymentNote:
    'Setelah mendaftar, hubungi admin via WhatsApp untuk konfirmasi biaya program dan instruksi transfer pembayaran.',
}

export function programAdminWhatsAppUrl(message = '') {
  const base = `https://wa.me/${PROGRAM_ADMIN_CONTACT.whatsapp}`
  if (!message) return base
  return `${base}?text=${encodeURIComponent(message)}`
}
