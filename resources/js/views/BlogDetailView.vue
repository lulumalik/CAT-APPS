<template>
  <div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <!-- Back Button -->
      <router-link :to="{ name: 'blog' }" class="inline-flex items-center text-gray-500 hover:text-[#9DB359] font-medium transition-colors mb-8">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali ke Materi
      </router-link>

      <!-- Loading State -->
      <div v-if="loading" class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-[#9DB359] border-t-transparent"></div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-white rounded-3xl p-12 text-center shadow-sm border border-gray-100">
        <div class="text-6xl mb-4">😕</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Materi Tidak Ditemukan</h2>
        <p class="text-gray-500 mb-6">Materi yang Anda cari mungkin telah dihapus atau URL tidak valid.</p>
        <router-link :to="{ name: 'blog' }" class="px-6 py-3 rounded-full bg-[#1A1A1A] text-white font-medium inline-block hover:bg-black transition-colors">
          Lihat Semua Materi
        </router-link>
      </div>

      <!-- Article Content -->
      <article v-else class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 overflow-hidden">
        <div class="p-8 md:p-12">
          
          <!-- Header -->
          <header class="mb-10 text-center">
            <div v-if="material.category" class="mb-6">
              <span class="px-4 py-1.5 bg-[#9DB359]/10 text-[#9DB359] text-sm font-bold uppercase tracking-wider rounded-full">
                {{ material.category }}
              </span>
            </div>
            
            <h1 class="text-xl md:text-2xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">
              {{ material.title }}
            </h1>
            
            <div class="flex items-center justify-center gap-4 text-gray-500">
              <div class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-[#9DB359] to-emerald-400 flex items-center justify-center text-white font-bold">
                  {{ material.creator?.name?.charAt(0).toUpperCase() || 'U' }}
                </div>
                <span class="font-medium text-gray-900">{{ material.creator?.name || 'Unknown' }}</span>
              </div>
              <span>&bull;</span>
              <time :datetime="material.created_at">{{ formatDate(material.created_at) }}</time>
            </div>
          </header>

          <!-- Divider -->
          <hr class="border-gray-100 mb-10" />

          <!-- Quill Content (Prose) -->
          <div class="prose prose-lg max-w-none prose-headings:font-bold prose-a:text-[#9DB359] hover:prose-a:text-[#8ca34b] prose-img:rounded-xl ql-editor" v-html="material.content">
          </div>

        </div>
      </article>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import '@vueup/vue-quill/dist/vue-quill.snow.css' // Import to ensure formatting like bold/italic lists work

const route = useRoute()
const material = ref(null)
const loading = ref(true)
const error = ref(false)

const fetchMaterial = async () => {
  loading.value = true
  error.value = false
  try {
    const { data } = await window.axios.get(`/api/materials/public/${route.params.slug}`)
    material.value = data
    
    // SEO Meta tags injection
    document.title = `${data.title} | CAT-APPS`
    updateMeta('description', stripHtml(data.content).substring(0, 160) + '...')
    updateMeta('og:title', data.title)
    updateMeta('og:description', stripHtml(data.content).substring(0, 160) + '...')
  } catch (err) {
    error.value = true
    document.title = 'Materi Tidak Ditemukan | CAT-APPS'
  } finally {
    loading.value = false
  }
}

const formatDate = (dateStr) => {
  const dt = new Date(dateStr)
  return dt.toLocaleDateString('id-ID', { month: 'long', day: 'numeric', year: 'numeric' })
}

const stripHtml = (html) => {
  if (!html) return ''
  const tmp = document.createElement('DIV')
  tmp.innerHTML = html
  return tmp.textContent || tmp.innerText || ''
}

const updateMeta = (name, content) => {
  // Update standard meta
  let meta = document.querySelector(`meta[name="${name}"]`) || document.querySelector(`meta[property="${name}"]`)
  if (!meta) {
    meta = document.createElement('meta')
    if (name.startsWith('og:')) {
      meta.setAttribute('property', name)
    } else {
      meta.setAttribute('name', name)
    }
    document.head.appendChild(meta)
  }
  meta.setAttribute('content', content)
}

onMounted(() => {
  fetchMaterial()
})

watch(() => route.params.slug, () => {
  if (route.name === 'blog-detail') {
    fetchMaterial()
  }
})
</script>

<style>
/* Adjust Quill editor content padding since we use our own padding */
.ql-editor {
  padding: 0;
  min-height: auto;
}
</style>
