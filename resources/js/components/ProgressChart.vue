<template>
  <div class="w-full">
    <!-- BAR CHART (vertical) -->
    <div v-if="type === 'bars'">
      <div v-if="!normalizedBars.length" class="text-sm text-gray-400 py-6 text-center">{{ emptyText }}</div>
      <div v-else class="flex items-end gap-2 sm:gap-3 h-44 px-1">
        <div v-for="(item, i) in normalizedBars" :key="i" class="flex-1 flex flex-col items-center justify-end h-full min-w-0">
          <span class="text-[11px] font-semibold text-gray-700 mb-1">{{ item.display }}</span>
          <div class="w-full rounded-t-lg transition-all duration-500"
            :style="{ height: item.height + '%', background: color }"
            :class="item.height === 0 ? 'opacity-30' : ''"></div>
          <span class="mt-2 text-[10px] text-gray-500 text-center leading-tight line-clamp-2">{{ item.label }}</span>
        </div>
      </div>
    </div>

    <!-- LINE CHART (timeline, percentage) -->
    <div v-else-if="type === 'line'">
      <div v-if="points.length < 1" class="text-sm text-gray-400 py-6 text-center">{{ emptyText }}</div>
      <svg v-else viewBox="0 0 320 160" class="w-full h-44" preserveAspectRatio="none">
        <line v-for="g in [0, 25, 50, 75, 100]" :key="g"
          :x1="0" :x2="320" :y1="yFor(g)" :y2="yFor(g)" stroke="#eef0f2" stroke-width="1" />
        <polyline :points="polyline" fill="none" :stroke="color" stroke-width="2.5"
          stroke-linejoin="round" stroke-linecap="round" />
        <circle v-for="(p, i) in svgPoints" :key="i" :cx="p.x" :cy="p.y" r="3.5" :fill="color" />
      </svg>
      <div v-if="points.length" class="flex justify-between mt-1 px-1 text-[10px] text-gray-400">
        <span>{{ points[0].date }}</span>
        <span>{{ points[points.length - 1].date }}</span>
      </div>
    </div>

    <!-- HORIZONTAL bars (subjects) -->
    <div v-else class="space-y-3">
      <div v-if="!normalizedBars.length" class="text-sm text-gray-400 py-6 text-center">{{ emptyText }}</div>
      <template v-else>
        <div v-for="(item, i) in normalizedBars" :key="i">
          <div class="flex justify-between text-xs mb-1 gap-3">
            <span class="text-gray-600">{{ item.label }}</span>
            <span class="font-semibold text-gray-800 shrink-0">{{ item.display }}</span>
          </div>
          <div class="h-2.5 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full rounded-full transition-all duration-500"
              :style="{ width: item.height + '%', background: color }"></div>
          </div>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: { type: String, default: 'hbars' }, // 'bars' | 'line' | 'hbars'
  data: { type: Array, default: () => [] },
  color: { type: String, default: '#9DB359' },
  emptyText: { type: String, default: 'Belum ada data.' },
  max: { type: Number, default: null },
})

const hasData = computed(() => props.data.some((d) => d.value != null || d.percent != null))

const maxValue = computed(() => {
  if (props.max) return props.max
  const vals = props.data.map((d) => Number(d.value ?? d.percent ?? 0)).filter((v) => !Number.isNaN(v))
  const m = Math.max(0, ...vals)
  return m > 0 ? m : 1
})

const normalizedBars = computed(() =>
  props.data.map((d) => {
    const raw = d.percent != null ? Number(d.percent) : (d.value != null ? Number(d.value) : null)
    const pct = raw == null ? 0 : Math.min(100, Math.round((raw / maxValue.value) * 100))
    let display = '—'
    if (raw != null) {
      if (d.percent != null) display = `${raw}%`
      else display = d.unit ? `${raw} ${d.unit}` : String(raw)
    }
    return { label: d.label, height: pct, display }
  }),
)

const points = computed(() =>
  props.data
    .filter((d) => d.percent != null)
    .map((d) => ({ percent: Number(d.percent), date: d.date || '' })),
)

const svgPoints = computed(() => {
  const n = points.value.length
  if (n === 0) return []
  return points.value.map((p, i) => ({
    x: n === 1 ? 160 : (i / (n - 1)) * 320,
    y: yFor(p.percent),
  }))
})

const polyline = computed(() => svgPoints.value.map((p) => `${p.x},${p.y}`).join(' '))

function yFor(percent) {
  return 160 - (Math.min(100, Math.max(0, percent)) / 100) * 150 - 5
}
</script>
