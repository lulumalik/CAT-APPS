import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useAppStore } from '@/stores/app';
import { translate, supportedLocales } from '@/i18n/messages';

export const useI18n = () => {
  const store = useAppStore();
  const { locale } = storeToRefs(store);

  const t = (key, params) => translate(locale.value, key, params);

  const setLocale = (nextLocale) => {
    if (!supportedLocales.includes(nextLocale)) return;
    store.setLocale(nextLocale);
  };

  const toggleLocale = () => {
    store.toggleLocale();
  };

  const isIndonesian = computed(() => locale.value === 'id');

  return {
    locale,
    t,
    setLocale,
    toggleLocale,
    isIndonesian,
  };
};
