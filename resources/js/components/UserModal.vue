<template>
  <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="$emit('close')"></div>
    <div class="relative w-full max-w-lg max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ isEdit ? t('modals.user.editTitle') : t('modals.user.addTitle') }}</h2>
          <p class="text-gray-500 mt-1">{{ t('modals.user.subtitle') }}</p>
        </div>
        <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="$emit('close')">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <form class="space-y-6" @submit.prevent="submit">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.user.nameLabel') }}</label>
          <input v-model="form.name" type="text" :placeholder="t('modals.user.namePlaceholder')" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.user.emailLabel') }}</label>
          <input v-model="form.email" type="email" :placeholder="t('modals.user.emailPlaceholder')" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" required />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ t('modals.user.roleLabel') }}</label>
          <select v-model="form.role" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
            <option value="user">{{ t('modals.user.roleUser') }}</option>
            <option value="mentor">{{ t('modals.user.roleMentor') }}</option>
            <option value="admin">{{ t('modals.user.roleAdmin') }}</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Program Siswa</label>
          <select v-model="form.program_category" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
            <option value="vip_offline">VIP - offline</option>
            <option value="vip_online">VIP - online (karantina)</option>
            <option value="regular_offline">Regular - offline</option>
            <option value="regular_online">Regular - online</option>
            <option value="bimbingan_online">Bimbingan - online</option>
            <option value="try_out">Try Out</option>
          </select>
        </div>

        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input v-model="form.in_quarantine" type="checkbox" class="rounded border-gray-300" />
          Status karantina
        </label>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">{{ isEdit ? t('modals.user.passwordLabelEdit') : t('modals.user.passwordLabelAdd') }}</label>
          <input v-model="form.password" type="password" :placeholder="t('modals.user.passwordPlaceholder')" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" :required="!isEdit" />
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
          <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="$emit('close')">{{ t('modals.user.cancel') }}</button>
          <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform active:scale-95 transition-all">{{ isEdit ? t('modals.user.submitEdit') : t('modals.user.submitAdd') }}</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'
import { useI18n } from '@/composables/useI18n'

const props = defineProps({
  initial: { type: Object, default: null }
})
const emit = defineEmits(['close', 'submit'])
const { t } = useI18n()

const isEdit = computed(() => !!props.initial)

const form = reactive({
  name: '',
  email: '',
  role: 'user',
  password: '',
  program_category: 'regular_online',
  in_quarantine: false,
})

watch(() => props.initial, (val) => {
  if (val) {
    form.name = val.name
    form.email = val.email
    form.role = val.role
    form.password = ''
    form.program_category = val.program_category || 'regular_online'
    form.in_quarantine = !!val.in_quarantine
  } else {
    form.name = ''
    form.email = ''
    form.role = 'user'
    form.password = ''
    form.program_category = 'regular_online'
    form.in_quarantine = false
  }
}, { immediate: true })

const submit = () => {
  emit('submit', JSON.parse(JSON.stringify(form)))
}
</script>
