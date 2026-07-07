<template>
  <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-amber-950">
    <h3 class="font-bold text-sm">{{ title }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-amber-900/90">{{ contact.paymentNote }}</p>

    <div class="mt-4 space-y-2 text-sm">
      <p><span class="font-semibold">WhatsApp:</span>
        <a :href="waUrl" target="_blank" rel="noopener noreferrer" class="ml-1 font-semibold text-[#1c8a47] hover:underline">
          {{ contact.whatsappDisplay }}
        </a>
      </p>
      <p v-for="email in contact.emails" :key="email">
        <span class="font-semibold">Email:</span>
        <a :href="`mailto:${email}`" class="ml-1 hover:underline">{{ email }}</a>
      </p>
      <p>
        <span class="font-semibold">Rekening:</span>
        {{ contact.bank.name }} — {{ contact.bank.accountNumber }}
      </p>
      <p class="text-xs text-amber-800/80">a.n. {{ contact.bank.accountHolder }}</p>
    </div>

    <a
      :href="waUrl"
      target="_blank"
      rel="noopener noreferrer"
      class="mt-4 inline-flex items-center justify-center rounded-full bg-[#25D366] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#1eb558] transition-colors"
    >
      Hubungi Admin via WhatsApp
    </a>
  </section>
</template>

<script setup>
import { computed } from 'vue'
import { PROGRAM_ADMIN_CONTACT, programAdminWhatsAppUrl } from '@/constants/programAdminContact'

const props = defineProps({
  title: { type: String, default: 'Kontak Admin untuk Pembayaran' },
  whatsappMessage: { type: String, default: '' },
})

const contact = PROGRAM_ADMIN_CONTACT
const waUrl = computed(() => programAdminWhatsAppUrl(props.whatsappMessage))
</script>
