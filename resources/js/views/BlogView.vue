<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center max-w-3xl mx-auto mb-16">
      <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-4">Materi Pembelajaran</h1>
      <p class="text-xl text-gray-500">Kumpulan materi, artikel, dan panduan belajar untuk membantu Anda sukses.</p>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-[#9DB359] border-t-transparent"></div>
    </div>

    <!-- Empty State -->
    <div v-else-if="materials.length === 0" class="text-center py-16">
      <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#9DB359]/10 text-[#9DB359]">
        <NotebookPen class="h-8 w-8" />
      </div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada materi</h3>
      <p class="text-gray-500">Materi pembelajaran akan segera ditambahkan.</p>
    </div>

    <!-- Blog Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <router-link 
        v-for="material in materials" 
        :key="material.id" 
        :to="{ name: 'blog-detail', params: { slug: material.slug } }"
        class="bg-white rounded-[2rem] shadow-lg shadow-black/5 border border-gray-100 overflow-hidden hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 group flex flex-col"
      >
        <div class="p-8 flex-1 flex flex-col">
          <div v-if="material.category" class="mb-4">
            <span class="px-3 py-1 bg-[#9DB359]/10 text-[#9DB359] text-xs font-bold uppercase tracking-wider rounded-full">
              {{ material.category }}
            </span>
          </div>
          <h2 class="text-2xl font-bold text-gray-900 mb-3 group-hover:text-[#9DB359] transition-colors line-clamp-3">
            {{ material.title }}
          </h2>
          <!-- Extract text content from HTML for preview -->
          <p class="text-gray-500 mb-6 line-clamp-3 flex-1">
            {{ stripHtml(material.content) }}
          </p>
          
          <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-100">
            <div class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#9DB359] to-emerald-400 flex items-center justify-center text-white font-bold text-xs">
                {{ material.creator?.name?.charAt(0).toUpperCase() || 'U' }}
              </div>
              <div class="text-sm font-medium text-gray-900">{{ material.creator?.name || 'Unknown' }}</div>
            </div>
            <div class="text-sm text-gray-500">
              {{ formatDate(material.created_at) }}
            </div>
          </div>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { NotebookPen } from 'lucide-vue-next'

const materials = ref([])
const loading = ref(true)

const fetchMaterials = async () => {
  try {
    const { data } = await window.axios.get('/api/materials/public')
    materials.value = data
  } catch (error) {
    console.error('Failed to fetch materials', error)
  } finally {
    loading.value = false
  }
}

const stripHtml = (html) => {
  if (!html) return ''
  const tmp = document.createElement('DIV')
  tmp.innerHTML = html
  return tmp.textContent || tmp.innerText || ''
}

const formatDate = (dateStr) => {
  const dt = new Date(dateStr)
  return dt.toLocaleDateString('id-ID', { month: 'short', day: 'numeric', year: 'numeric' })
}

onMounted(() => {
  // Set meta title for SEO
  document.title = 'Materi Pembelajaran | CAT-APPS'
  fetchMaterials()
})
</script>
