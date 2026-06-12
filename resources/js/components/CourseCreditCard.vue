<template>
  <article
    class="course-credit-card program-gradient-animated transition-all duration-300 hover:-translate-y-1"
    :style="{
      backgroundImage: program.backgroundColor,
      color: program.textColor,
      boxShadow: program.boxShadow,
    }"
  >
    <svg class="course-credit-poly" viewBox="0 0 400 250" preserveAspectRatio="none" aria-hidden="true">
      <polygon
        v-for="(shape, index) in theme.polygons"
        :key="index"
        :points="shape.points"
        :fill="shape.fill"
        :opacity="shape.opacity ?? 0.55"
      />
    </svg>

    <div class="course-credit-content">
      <div class="course-credit-header">
        <div class="course-credit-brand">
          <div class="course-credit-chip" aria-hidden="true">
            <span class="course-credit-chip-line" />
            <span class="course-credit-chip-line" />
            <span class="course-credit-chip-line" />
          </div>
          <p class="course-credit-bank">{{ program.cardBank }}</p>
        </div>

        <span
          class="course-credit-mode"
          :style="badgeStyle"
        >
          <Crown v-if="program.mode === 'Premium'" class="h-3.5 w-3.5" />
          {{ program.mode }}
        </span>
      </div>

      <h3 class="course-credit-title">{{ program.name }}</h3>
      <p class="course-credit-summary">{{ program.summary }}</p>
      <ul class="course-credit-points">
        <li v-for="point in program.points" :key="point">{{ point }}</li>
      </ul>
    </div>
  </article>
</template>

<script setup>
import { computed } from 'vue'
import { Crown } from 'lucide-vue-next'

const props = defineProps({
  program: { type: Object, required: true },
})

const CARD_THEMES = {
  aurora: {
    polygons: [
      { points: '0,0 180,0 90,120', fill: '#166534', opacity: 0.45 },
      { points: '120,0 400,0 260,90', fill: '#14532d', opacity: 0.4 },
      { points: '0,80 160,40 120,180 0,200', fill: '#15803d', opacity: 0.38 },
      { points: '180,60 400,30 400,150 220,170', fill: '#0f5132', opacity: 0.35 },
      { points: '40,150 220,130 400,250 0,250', fill: '#134e2a', opacity: 0.32 },
      { points: '260,140 400,120 400,250 180,250', fill: '#0a3d26', opacity: 0.3 },
    ],
  },
  midnight: {
    polygons: [
      { points: '0,0 220,0 120,110', fill: '#1d4ed8', opacity: 0.75 },
      { points: '160,0 400,0 400,100 240,80', fill: '#312e81', opacity: 0.7 },
      { points: '0,70 150,40 100,190 0,210', fill: '#4338ca', opacity: 0.55 },
      { points: '200,70 400,50 400,170 250,160', fill: '#1e3a8a', opacity: 0.5 },
      { points: '60,160 260,140 400,250 0,250', fill: '#5b21b6', opacity: 0.45 },
      { points: '300,120 400,100 400,250 220,250', fill: '#2563eb', opacity: 0.4 },
    ],
  },
  blossom: {
    polygons: [
      { points: '0,0 190,0 100,100', fill: '#f9a8d4', opacity: 0.7 },
      { points: '140,0 400,0 280,80', fill: '#c084fc', opacity: 0.55 },
      { points: '0,90 170,50 130,180 0,200', fill: '#fda4af', opacity: 0.5 },
      { points: '210,60 400,20 400,150 230,150', fill: '#a78bfa', opacity: 0.45 },
      { points: '30,150 240,130 400,250 0,250', fill: '#f0abfc', opacity: 0.42 },
      { points: '280,130 400,110 400,250 190,250', fill: '#93c5fd', opacity: 0.38 },
    ],
  },
  sunset: {
    polygons: [
      { points: '0,0 200,0 110,100', fill: '#a16207', opacity: 0.5 },
      { points: '150,0 400,0 300,90', fill: '#854d0e', opacity: 0.45 },
      { points: '0,80 160,50 120,180 0,200', fill: '#92400e', opacity: 0.4 },
      { points: '190,70 400,40 400,160 240,150', fill: '#78350f', opacity: 0.38 },
      { points: '50,150 250,130 400,250 0,250', fill: '#713f12', opacity: 0.35 },
      { points: '290,120 400,100 400,250 210,250', fill: '#5c3d0a', opacity: 0.32 },
    ],
  },
}

const theme = computed(() => CARD_THEMES[props.program.cardTheme] || CARD_THEMES.aurora)

const badgeStyle = computed(() => {
  if (props.program.mode === 'Premium') {
    return {
      background: 'rgba(255,255,255,0.88)',
      color: '#7a4a00',
      border: '1px solid rgba(180,140,30,0.5)',
    }
  }
  return {
    background: 'rgba(255,255,255,0.18)',
    color: props.program.textColor,
    border: '1px solid rgba(255,255,255,0.35)',
  }
})
</script>

<style scoped>
.course-credit-card {
  position: relative;
  width: 100%;
  border-radius: 1rem;
  overflow: hidden;
  border: 1px solid rgba(255, 255, 255, 0.18);
}

.course-credit-poly {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
}

.course-credit-content {
  position: relative;
  z-index: 1;
  padding: 1.5rem;
}

.course-credit-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin-bottom: 1rem;
}

.course-credit-brand {
  display: flex;
  align-items: center;
  gap: 0.65rem;
  min-width: 0;
}

.course-credit-chip {
  flex-shrink: 0;
  width: 2.2rem;
  height: 1.65rem;
  border-radius: 0.3rem;
  background: linear-gradient(135deg, #f5d76e 0%, #d4af37 45%, #f0e68c 100%);
  border: 1px solid rgba(120, 90, 10, 0.35);
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.18rem;
  padding: 0.3rem 0.35rem;
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.45);
}

.course-credit-chip-line {
  display: block;
  height: 1px;
  background: rgba(90, 65, 8, 0.55);
}

.course-credit-chip-line:nth-child(2) {
  width: 72%;
}

.course-credit-bank {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  opacity: 0.9;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.course-credit-mode {
  flex-shrink: 0;
  display: inline-flex;
  align-items: center;
  gap: 0.25rem;
  padding: 0.25rem 0.625rem;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  backdrop-filter: blur(4px);
}

.course-credit-title {
  font-size: 1.125rem;
  font-weight: 700;
  line-height: 1.35;
}

@media (min-width: 768px) {
  .course-credit-title {
    white-space: nowrap;
  }
}

.course-credit-summary {
  margin-top: 0.5rem;
  font-size: 0.875rem;
  font-weight: 600;
  line-height: 1.45;
  opacity: 0.9;
}

.course-credit-points {
  margin-top: 0.75rem;
  padding-left: 1.25rem;
  font-size: 0.875rem;
  font-weight: 600;
  line-height: 1.5;
  opacity: 0.85;
  list-style-type: disc;
}

.course-credit-card.program-gradient-animated {
  background-size: 220% 220%;
  animation: programGradientFlow 9s ease-in-out infinite;
}

@keyframes programGradientFlow {
  0% {
    background-position: 0% 50%;
  }

  50% {
    background-position: 100% 50%;
  }

  100% {
    background-position: 0% 50%;
  }
}
</style>
