<template>
  <main>
    <section class="container-main pt-16 pb-24">
      <div class="mx-auto w-full text-center">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-gray-200 bg-white text-sm text-brand/90">
          <span class="w-4 h-4 grid place-items-center rounded-full bg-brand/15 text-brand">◎</span>
          <span>Next-Gen Assessment Platform</span>
        </div>
        <h1 class="mt-6 text-4xl md:text-5xl font-semibold bg-gradient-to-r from-brand to-accent bg-clip-text text-transparent">
          Master Your Skills with Computer Assisted Testing
        </h1>
        <p class="mt-4 text-lg text-muted max-w-3xl mx-auto">
          Experience adaptive testing that adjusts to your skill level in real-time. Track progress, compete with peers, and achieve excellence.
        </p>
        <div class="mt-8 flex items-center justify-center gap-4">
          <button class="px-5 py-2 rounded-md bg-navy text-white">Start Testing Now</button>
          <button class="px-5 py-2 rounded-md border border-gray-200 bg-white">View Rankings</button>
        </div>
      </div>
    </section>

    <section id="features" class="container-main pb-24">
      <h2 class="text-3xl font-semibold text-center">Why Choose Our Platform?</h2>
      <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <FeatureCard icon="🧠" iconClass="bg-blue-100 text-blue-600" title="Adaptive Testing" desc="Questions adapt to your skill level, providing optimal challenge and accurate assessment." />
        <FeatureCard icon="📊" iconClass="bg-purple-100 text-purple-600" title="Detailed Analytics" desc="Track your performance with comprehensive analytics and insights into your strengths." />
        <FeatureCard icon="🏆" iconClass="bg-green-100 text-green-600" title="Competitive Rankings" desc="Compete with peers on global leaderboards and earn achievements for your progress." />
        <FeatureCard icon="🕒" iconClass="bg-orange-100 text-orange-600" title="Flexible Scheduling" desc="Take tests on your schedule with flexible timing and instant results." />
        <FeatureCard icon="✅" iconClass="bg-indigo-100 text-indigo-600" title="Instant Feedback" desc="Get immediate feedback on your performance with detailed explanations." />
        <FeatureCard icon="🎓" iconClass="bg-pink-100 text-pink-600" title="Expert Content" desc="Questions designed by subject matter experts ensuring quality and relevance." />
      </div>
    </section>
  </main>
</template>

<script setup>
import FeatureCard from '@/components/FeatureCard.vue'
</script>

<style scoped>
</style>