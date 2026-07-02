<template>
  <div class="w-full">
    <!-- VERTICAL BAR CHART (single series timeline) -->
    <div v-if="type === 'line'">
      <div v-if="!timelineBars.length" class="text-sm text-gray-400 py-6 text-center">{{ emptyText }}</div>
      <template v-else>
        <div class="flex gap-2">
          <div class="flex flex-col justify-between h-44 py-1 text-[9px] text-gray-400 text-right shrink-0 w-8">
            <span>{{ yLabelSingle(1) }}</span>
            <span>{{ yLabelSingle(0.5) }}</span>
            <span>{{ yLabelSingle(0) }}</span>
          </div>
          <div class="flex-1 min-w-0">
            <div class="flex items-end gap-2 sm:gap-3 h-44 px-1 border-b border-gray-100">
              <div
                v-for="(item, i) in timelineBars"
                :key="i"
                class="flex-1 flex flex-col items-center justify-end h-full min-w-0"
              >
                <span class="text-[11px] font-semibold text-gray-700 mb-1">{{ item.display }}</span>
                <div
                  class="w-full max-w-10 rounded-t-lg transition-all duration-500"
                  :style="{ height: item.height + '%', background: color }"
                  :class="item.height === 0 ? 'opacity-30' : ''"
                ></div>
                <span class="mt-2 text-[10px] text-gray-500 text-center leading-tight line-clamp-2">{{ item.label }}</span>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    <!-- GROUPED VERTICAL BAR CHART (multi series timeline) -->
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
            <div class="flex items-end justify-around gap-1 sm:gap-2 h-44 px-1 border-b border-gray-100">
              <div
                v-for="date in allDates"
                :key="date"
                class="flex-1 flex flex-col items-center justify-end h-full min-w-0"
              >
                <div class="flex items-end justify-center gap-1 w-full h-full px-0.5">
                  <div
                    v-for="s in activeSeries"
                    :key="s.label"
                    class="flex flex-col items-center justify-end h-full flex-1 min-w-0 max-w-[18px]"
                  >
                    <span v-if="valueForSeriesDate(s, date) != null" class="text-[9px] font-semibold text-gray-700 mb-0.5 leading-none">
                      {{ formatSeriesValue(s, date) }}
                    </span>
                    <div
                      v-if="valueForSeriesDate(s, date) != null"
                      class="w-full rounded-t-md transition-all duration-500"
                      :style="{ height: barHeightFor(s, date) + '%', background: s.color }"
                    ></div>
                    <div v-else class="w-full h-1 rounded bg-gray-100 opacity-40"></div>
                  </div>
                </div>
                <span class="mt-2 text-[10px] text-gray-500 text-center leading-tight">{{ fmtDate(date) }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="flex flex-wrap gap-x-3 gap-y-1 mt-3">
          <span v-for="(s, i) in activeSeries" :key="i" class="inline-flex items-center gap-1.5 text-[11px] text-gray-600">
            <span class="inline-block w-3 h-3 rounded-sm" :style="{ background: s.color }"></span>
            {{ s.label }}<span v-if="s.unit && valueMode !== 'percent'" class="text-gray-400"> ({{ s.unit }})</span>
          </span>
        </div>
      </template>
    </div>

    <!-- VERTICAL BAR CHART (simple categories) -->
    <div v-else-if="type === 'bars'">
      <div v-if="!normalizedBars.length" class="text-sm text-gray-400 py-6 text-center">{{ emptyText }}</div>
      <div v-else class="flex items-end gap-2 sm:gap-3 h-44 px-1">
        <div v-for="(item, i) in normalizedBars" :key="i" class="flex-1 flex flex-col items-center justify-end h-full min-w-0">
          <span class="text-[11px] font-semibold text-gray-700 mb-1">{{ item.display }}</span>
          <div
            class="w-full rounded-t-lg transition-all duration-500"
            :style="{ height: item.height + '%', background: color }"
            :class="item.height === 0 ? 'opacity-30' : ''"
          ></div>
          <span class="mt-2 text-[10px] text-gray-500 text-center leading-tight line-clamp-2">{{ item.label }}</span>
        </div>
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
            <div
              class="h-full rounded-full transition-all duration-500"
              :style="{ width: item.height + '%', background: color }"
            ></div>
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
  series: { type: Array, default: () => [] },
  valueMode: { type: String, default: 'percent' },
  color: { type: String, default: '#9DB359' },
  emptyText: { type: String, default: 'Belum ada data.' },
  max: { type: Number, default: null },
})

const palette = ['#2F6BFF', '#9DB359', '#E8833A', '#8B5CF6', '#EC4899', '#14B8A6', '#F59E0B', '#0EA5E9']

const singleMax = computed(() => {
  if (props.max) return props.max
  const vals = props.data.map((d) => Number(d.percent ?? d.value ?? 0)).filter((v) => !Number.isNaN(v))
  const m = Math.max(0, ...vals)
  return m > 0 ? m : 1
})

const timelineBars = computed(() =>
  [...props.data]
    .filter((d) => d.percent != null || d.value != null)
    .sort((a, b) => compareDates(a.date, b.date))
    .map((d) => {
      const raw = d.percent != null ? Number(d.percent) : Number(d.value)
      const pct = Number.isNaN(raw) ? 0 : Math.min(100, Math.round((raw / singleMax.value) * 100))
      const display = props.valueMode === 'percent' ? `${raw}%` : (d.unit ? `${raw} ${d.unit}` : String(raw))
      const label = d.date ? fmtDate(d.date) : (d.label || '')
      return { label, height: pct, display }
    }),
)

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

const cleanSeries = computed(() =>
  (props.series || []).map((s, idx) => ({
    label: s.label,
    unit: s.unit || null,
    color: s.color || palette[idx % palette.length],
    points: (s.points || [])
      .map((p) => ({ date: p.date || '', value: Number(p.percent != null ? p.percent : p.value) }))
      .filter((p) => p.date && !Number.isNaN(p.value))
      .sort((a, b) => compareDates(a.date, b.date)),
  })),
)

const activeSeries = computed(() => cleanSeries.value.filter((s) => s.points.length))

const allDates = computed(() => {
  const set = new Set()
  activeSeries.value.forEach((s) => s.points.forEach((p) => set.add(p.date)))
  return Array.from(set).sort(compareDates)
})

const multiMax = computed(() => {
  if (props.valueMode === 'percent') return 100
  if (props.max) return props.max
  let m = 0
  activeSeries.value.forEach((s) => s.points.forEach((p) => { if (p.value > m) m = p.value }))
  return m > 0 ? m : 1
})

function valueForSeriesDate(series, date) {
  const point = series.points.find((p) => p.date === date)
  return point ? point.value : null
}

function barHeightFor(series, date) {
  const val = valueForSeriesDate(series, date)
  if (val == null) return 0
  return Math.min(100, Math.round((val / multiMax.value) * 100))
}

function formatSeriesValue(series, date) {
  const val = valueForSeriesDate(series, date)
  if (val == null) return '—'
  if (props.valueMode === 'percent') return `${val}%`
  return series.unit ? `${val}` : String(val)
}

function yLabel(frac) {
  const v = Math.round(multiMax.value * frac)
  return props.valueMode === 'percent' ? `${v}%` : `${v}`
}

function yLabelSingle(frac) {
  const v = Math.round(singleMax.value * frac)
  return props.valueMode === 'percent' ? `${v}%` : `${v}`
}

function compareDates(a, b) {
  return String(a || '').localeCompare(String(b || ''))
}

function fmtDate(d) {
  if (!d) return ''
  const parts = String(d).split('-')
  return parts.length === 3 ? `${parts[2]}/${parts[1]}` : d
}
</script>
