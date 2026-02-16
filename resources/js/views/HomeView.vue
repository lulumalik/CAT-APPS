<template>
  <main>
    <section class="relative flex items-center" style="height:calc(100vh - 100px);" :style="heroStyle">
      <div class="absolute inset-0"></div>
      <div class="mx-auto w-full text-center max-w-3xl">
        <div
          class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-gray-200 bg-white text-sm text-brand/90">
          <span class="w-4 h-4 grid place-items-center rounded-full bg-brand/15 text-brand"><span class="relative bottom-0.5">◎</span></span>
          <span>Next-Gen Assessment Platform</span>
        </div>
        <h1 class="mt-6 text-4xl md:text-5xl font-bold text-white">
          Master Your Skills with Computer Assisted Testing
        </h1>
        <p class="mt-4 text-lg text-white max-w-3xl mx-auto">
          Experience adaptive testing that adjusts to your skill level in real-time. Track progress, compete with peers,
          and achieve excellence.
        </p>
      </div>
    </section>

    <section id="features" class="relative p-24">
      <img :src="arrowUrl" alt="arrow" class="absolute -top-64 -right-12 w-4/12 z-10 -rotate-60" />
      <div class="mx-auto relative z-20" style="max-width: 60vw;">
        <h2 class="text-3xl font-semibold text-center">Why Choose Our Platform?</h2>
        <div class="mt-10 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <FeatureCard icon="🧠" iconClass="bg-blue-100 text-blue-600" title="Adaptive Testing"
            desc="Questions adapt to your skill level, providing optimal challenge and accurate assessment." />
          <FeatureCard icon="📊" iconClass="bg-purple-100 text-purple-600" title="Detailed Analytics"
            desc="Track your performance with comprehensive analytics and insights into your strengths." />
          <FeatureCard icon="🏆" iconClass="bg-green-100 text-green-600" title="Competitive Rankings"
            desc="Compete with peers on global leaderboards and earn achievements for your progress." />
          <FeatureCard icon="🕒" iconClass="bg-orange-100 text-orange-600" title="Flexible Scheduling"
            desc="Take tests on your schedule with flexible timing and instant results." />
          <FeatureCard icon="✅" iconClass="bg-indigo-100 text-indigo-600" title="Instant Feedback"
            desc="Get immediate feedback on your performance with detailed explanations." />
          <FeatureCard icon="🎓" iconClass="bg-pink-100 text-pink-600" title="Expert Content"
            desc="Questions designed by subject matter experts ensuring quality and relevance." />
        </div>
      </div>
    </section>

    <section id="contact" class="py-24 bg-white">
      <div class="mx-auto" style="max-width: 60vw;">
        <div class="border-t border-gray-200 pt-24 mx-auto max-w-3xl text-center">
          <h2 class="text-3xl font-semibold">Contact Us</h2>
          <p class="mt-3 text-muted">
            Punya pertanyaan tentang akun, jadwal ujian, atau kerja sama institusi? Tim CAT Platform siap membantu.
            Hubungi kami melalui WhatsApp untuk respons cepat.
          </p>
          <div class="mt-6">
            <a href="https://wa.me/6285124156748" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-3 rounded-md bg-green-600 text-white hover:bg-green-700">
              <span class="w-6 h-6 grid place-items-center rounded-full bg-white">
                <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" class="w-4 h-4" />
              </span>
              <span>WhatsApp</span>
              <span class="opacity-90">(+62) 851-2415-6748</span>
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>
</template>

<script setup>
import FeatureCard from '@/components/FeatureCard.vue'
const heroUrl = 'https://images.unsplash.com/photo-1614607034248-0c121b3ab350?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D'
const arrowUrl = new URL('../../assets/arrow.png', import.meta.url).href
const heroStyle = {
  backgroundImage: `url('${heroUrl}')`,
  backgroundSize: 'cover',
  backgroundPosition: 'center',
  backgroundAttachment: 'fixed',
}
</script>

<style scoped></style>
