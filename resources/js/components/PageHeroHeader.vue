<template>
  <header class="mb-10 overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-4 md:gap-5">
      <div class="min-w-0 flex-1 flex flex-col justify-center py-0.5">
        <h1 class="text-4xl md:text-5xl font-bold tracking-tight leading-tight" :class="themeClasses.title">
          {{ title }}
        </h1>
        <p v-if="subtitle && !$slots.subtitle" class="text-gray-500 mt-2 text-sm md:text-base max-w-xl leading-relaxed">
          {{ subtitle }}
        </p>
        <div v-else-if="$slots.subtitle" class="text-gray-500 mt-2 text-sm md:text-base max-w-xl leading-relaxed">
          <slot name="subtitle" />
        </div>
      </div>

      <div
        v-if="icon"
        class="hidden sm:flex shrink-0 items-center justify-center w-[4.75rem] h-[4.75rem] md:w-[5.5rem] md:h-[5.5rem] rounded-[1.75rem] relative overflow-hidden"
        :class="themeClasses.box"
      >
        <div class="relative flex items-center justify-center">
          <component :is="icon" class="h-9 w-9 md:h-10 md:w-10 drop-shadow-sm" :class="themeClasses.icon" stroke-width="1.5" />
          <component
            v-if="secondaryIcon"
            :is="secondaryIcon"
            class="h-4 w-4 md:h-5 md:w-5 absolute -bottom-0.5 -right-2 rotate-[-24deg]"
            :class="themeClasses.secondary"
            stroke-width="2"
          />
        </div>
      </div>
    </div>

    <div v-if="$slots.actions" class="mt-4 flex flex-wrap items-center gap-2">
      <slot name="actions" />
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: { type: String, required: true },
  subtitle: { type: String, default: '' },
  theme: {
    type: String,
    default: 'blue',
  },
  icon: { type: [Object, Function], default: null },
  secondaryIcon: { type: [Object, Function], default: null },
})

const THEMES = {
  blue: {
    title: 'text-[#1E3A8A]',
    box: 'bg-gradient-to-br from-blue-50 to-blue-100/80',
    decoration: 'bg-blue-200',
    icon: 'text-blue-500',
    secondary: 'text-amber-400',
  },
  green: {
    title: 'text-[#1E3A8A]',
    box: 'bg-gradient-to-br from-[#9DB359]/20 to-[#9DB359]/5',
    decoration: 'bg-[#9DB359]/30',
    icon: 'text-[#5a6b2e]',
    secondary: 'text-amber-400',
  },
  purple: {
    title: 'text-[#1E3A8A]',
    box: 'bg-gradient-to-br from-purple-50 to-purple-100/80',
    decoration: 'bg-purple-200',
    icon: 'text-purple-500',
    secondary: 'text-amber-400',
  },
  amber: {
    title: 'text-[#1E3A8A]',
    box: 'bg-gradient-to-br from-amber-50 to-amber-100/80',
    decoration: 'bg-amber-200',
    icon: 'text-amber-600',
    secondary: 'text-blue-500',
  },
  rose: {
    title: 'text-[#1E3A8A]',
    box: 'bg-gradient-to-br from-rose-50 to-rose-100/80',
    decoration: 'bg-rose-200',
    icon: 'text-rose-500',
    secondary: 'text-amber-400',
  },
  slate: {
    title: 'text-[#1E3A8A]',
    box: 'bg-gradient-to-br from-slate-50 to-slate-100/80',
    decoration: 'bg-slate-200',
    icon: 'text-slate-600',
    secondary: 'text-amber-400',
  },
}

const themeClasses = computed(() => THEMES[props.theme] || THEMES.blue)
</script>
