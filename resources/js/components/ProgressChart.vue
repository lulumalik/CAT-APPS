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

    <!-- MULTI-LINE CHART (timeline per series) -->
    <div v-else-if="type === 'multiline'">
      <div v-if="!activeSeries.length" class="text-sm text-gray-400 py-6 text-center">{{ emptyText }}</div>
      <template v-else>
        <div class="flex gap-2">
          <div class="flex flex-col justify-between h-44 py-1 text-[9px] text-gray-400 text-right shrink-0 w-8">
            <span>{{ yLabel(1) }}</span>
            <span>{{ yLabel(0.5) }}</span>
            <span>{{ yLabel(0) }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <svg viewBox="0 0 320 160" class="w-full h-44" preserveAspectRatio="none">
              <line v-for="(g, gi) in [0, 0.25, 0.5, 0.75, 1]" :key="gi"
                :x1="0" :x2="320" :y1="yForFrac(g)" :y2="yForFrac(g)" stroke="#eef0f2" stroke-width="1" />
              <g v-for="(s, si) in seriesPaths" :key="si">
                <polyline v-if="s.dots.length > 1" :points="s.polyline" fill="none" :stroke="s.color"
                  stroke-width="2.5" stroke-linejoin="round" stroke-linecap="round" />
                <circle v-for="(d, di) in s.dots" :key="di" :cx="d.x" :cy="d.y" r="3" :fill="s.color" />
              </g>
            </svg>
            <div class="flex justify-between mt-1 px-1 text-[10px] text-gray-400">
              <span>{{ fmtDate(allDates[0]) }}</span>
              <span v-if="allDates.length > 1">{{ fmtDate(allDates[allDates.length - 1]) }}</span>
            </div>
          </div>
        </div>
        <div class="flex flex-wrap gap-x-3 gap-y-1 mt-3">
          <span v-for="(s, i) in seriesPaths" :key="i" class="inline-flex items-center gap-1.5 text-[11px] text-gray-600">
            <span class="inline-block w-3 h-1.5 rounded-full" :style="{ background: s.color }"></span>
            {{ s.label }}<span v-if="s.unit && valueMode !== 'percent'" class="text-gray-400"> ({{ s.unit }})</span>
          </span>
        </div>
      </template>
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
  type: { type: String, default: 'hbars' }, // 'bars' | 'line' | 'multiline' | 'hbars'
  data: { type: Array, default: () => [] },
  series: { type: Array, default: () => [] }, // multiline: [{ label, unit, points: [{ date, value|percent }] }]
  valueMode: { type: String, default: 'percent' }, // 'percent' (0–100) | 'value' (auto max)
  color: { type: String, default: '#9DB359' },
  emptyText: { type: String, default: 'Belum ada data.' },
  max: { type: Number, default: null },
})

const palette = ['#2F6BFF', '#9DB359', '#E8833A', '#8B5CF6', '#EC4899', '#14B8A6', '#F59E0B', '#0EA5E9']

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

// ---- multiline ----
const cleanSeries = computed(() =>
  (props.series || []).map((s, idx) => ({
    label: s.label,
    unit: s.unit || null,
    color: s.color || palette[idx % palette.length],
    points: (s.points || [])
      .map((p) => ({ date: p.date || '', value: Number(p.percent != null ? p.percent : p.value) }))
      .filter((p) => p.date && !Number.isNaN(p.value)),
  })),
)

const activeSeries = computed(() => cleanSeries.value.filter((s) => s.points.length))

const allDates = computed(() => {
  const set = new Set()
  activeSeries.value.forEach((s) => s.points.forEach((p) => set.add(p.date)))
  return Array.from(set).sort()
})

const multiMax = computed(() => {
  if (props.valueMode === 'percent') return 100
  let m = 0
  activeSeries.value.forEach((s) => s.points.forEach((p) => { if (p.value > m) m = p.value }))
  return m > 0 ? m : 1
})

function xForDate(date) {
  const n = allDates.value.length
  if (n <= 1) return 160
  return (allDates.value.indexOf(date) / (n - 1)) * 320
}

function yForFrac(frac) {
  return 160 - Math.min(1, Math.max(0, frac)) * 150 - 5
}

function yForValue(v) {
  return yForFrac(v / multiMax.value)
}

function yLabel(frac) {
  const v = Math.round(multiMax.value * frac)
  return props.valueMode === 'percent' ? `${v}%` : `${v}`
}

function fmtDate(d) {
  if (!d) return ''
  const parts = String(d).split('-')
  return parts.length === 3 ? `${parts[2]}/${parts[1]}` : d
}

const seriesPaths = computed(() =>
  activeSeries.value.map((s) => {
    const dots = s.points.map((p) => ({ x: xForDate(p.date), y: yForValue(p.value) }))
    return {
      color: s.color,
      label: s.label,
      unit: s.unit,
      polyline: dots.map((d) => `${d.x},${d.y}`).join(' '),
      dots,
    }
  }),
)
</script>
