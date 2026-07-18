<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      title="Kategori Ujian"
      subtitle="Kelola kategori bisnis dan ujian spesifik (JLPT, JFT, IELTS, TOEFL, SNMPTN, SBMPTN, dst)."
      theme="blue"
      :icon="Layers"
    >
      <template #actions>
        <button
          class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 flex items-center gap-2"
          @click="openCategoryModal()"
        >
          <Plus class="h-[18px] w-[18px]" />
          Tambah Kategori
        </button>
      </template>
    </PageHeroHeader>

    <div v-if="loading" class="mt-8 space-y-4">
      <div v-for="n in 3" :key="n" class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm animate-pulse">
        <div class="h-6 w-56 bg-gray-100 rounded mb-3"></div>
        <div class="h-4 w-80 bg-gray-100 rounded mb-6"></div>
        <div class="grid grid-cols-2 gap-3 md:gap-4">
          <div class="h-20 bg-gray-100 rounded-xl"></div>
          <div class="h-20 bg-gray-100 rounded-xl"></div>
        </div>
      </div>
    </div>

    <div v-else class="mt-8 space-y-6">
      <p v-if="categories.length === 0" class="text-center text-gray-500 py-12">
        Belum ada kategori. Tambahkan kategori pertama untuk memulai.
      </p>

      <section
        v-for="cat in categories"
        :key="cat.id"
        class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-8"
      >
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div class="min-w-0">
            <div class="flex items-center gap-3 flex-wrap">
              <h2 class="text-xl font-bold text-[#1A1A1A]">{{ cat.name }}</h2>
              <span
                class="text-xs font-semibold px-3 py-1 rounded-full"
                :class="cat.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
              >
                {{ cat.is_active ? 'Aktif' : 'Nonaktif' }}
              </span>
            </div>
            <p class="text-sm text-gray-500 mt-1 max-w-2xl">{{ cat.description }}</p>
          </div>
          <div class="flex items-center gap-2">
            <button
              class="px-4 py-2 rounded-full border border-gray-200 text-sm font-medium hover:bg-gray-50 transition-colors text-gray-600"
              @click="openTrackModal(cat)"
            >
              + Track
            </button>
            <button
              class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors"
              title="Edit kategori"
              @click="openCategoryModal(cat)"
            >
              <Pencil class="h-4 w-4" />
            </button>
            <button
              class="inline-flex items-center justify-center w-9 h-9 rounded-full border border-red-100 text-red-600 hover:bg-red-50 transition-colors"
              title="Hapus kategori"
              @click="removeCategory(cat)"
            >
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-3 md:gap-4">
          <p v-if="!cat.tracks?.length" class="text-sm text-gray-400 italic col-span-2">
            Belum ada track ujian pada kategori ini.
          </p>
          <article
            v-for="tr in cat.tracks"
            :key="tr.id"
            class="rounded-2xl border border-gray-100 bg-gray-50/50 p-5 flex items-start justify-between gap-3"
          >
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <span class="font-bold text-[#1A1A1A]">{{ tr.name }}</span>
                <span
                  class="text-[11px] font-semibold px-2 py-0.5 rounded-full"
                  :class="tr.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'"
                >
                  {{ tr.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </div>
              <p class="text-xs text-gray-500 mt-1">{{ tr.description }}</p>
              <div class="flex items-center gap-3 mt-2 text-[11px] text-gray-400 font-medium">
                <span>{{ tr.questions_count ?? 0 }} soal</span>
                <span>{{ tr.users_count ?? 0 }} peserta</span>
              </div>
            </div>
            <div class="flex items-center gap-1.5 shrink-0">
              <button
                class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-gray-200 text-gray-600 hover:bg-white transition-colors"
                title="Edit track"
                @click="openTrackModal(cat, tr)"
              >
                <Pencil class="h-3.5 w-3.5" />
              </button>
              <button
                class="inline-flex items-center justify-center w-8 h-8 rounded-full border border-red-100 text-red-600 hover:bg-red-50 transition-colors"
                title="Hapus track"
                @click="removeTrack(tr)"
              >
                <Trash2 class="h-3.5 w-3.5" />
              </button>
            </div>
          </article>
        </div>
      </section>
    </div>

    <!-- Modal kategori -->
    <div v-if="showCategoryModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showCategoryModal = false"></div>
      <div class="relative w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-2xl border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ editingCategory ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
        <form class="space-y-5" @submit.prevent="saveCategory">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori</label>
            <input v-model="categoryForm.name" required placeholder="cth: Bahasa Korea" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
            <textarea v-model="categoryForm.description" rows="2" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all resize-none"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
              <input v-model.number="categoryForm.sort_order" type="number" min="0" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
            </div>
            <div class="flex items-end pb-3">
              <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                <input v-model="categoryForm.is_active" type="checkbox" class="rounded border-gray-300" />
                Aktif (tampil ke user)
              </label>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="showCategoryModal = false">Batal</button>
            <button type="submit" :disabled="saving" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium hover:bg-black transition-all disabled:opacity-50">
              {{ saving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal track -->
    <div v-if="showTrackModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm" @click="showTrackModal = false"></div>
      <div class="relative w-full max-w-lg rounded-[2rem] bg-white p-8 shadow-2xl border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ editingTrack ? 'Edit Track' : 'Tambah Track' }}</h2>
        <p class="text-gray-500 mb-6 text-sm">Ujian spesifik di dalam sebuah kategori.</p>
        <form class="space-y-5" @submit.prevent="saveTrack">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
            <select v-model.number="trackForm.exam_category_id" required class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all">
              <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Track</label>
            <input v-model="trackForm.name" required placeholder="cth: TOPIK" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
            <textarea v-model="trackForm.description" rows="2" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all resize-none"></textarea>
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
              <input v-model.number="trackForm.sort_order" type="number" min="0" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
            </div>
            <div class="flex items-end pb-3">
              <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                <input v-model="trackForm.is_active" type="checkbox" class="rounded border-gray-300" />
                Aktif (tampil ke user)
              </label>
            </div>
          </div>
          <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="showTrackModal = false">Batal</button>
            <button type="submit" :disabled="saving" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium hover:bg-black transition-all disabled:opacity-50">
              {{ saving ? 'Menyimpan...' : 'Simpan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue'
import { Layers, Pencil, Plus, Trash2 } from 'lucide-vue-next'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useModal, useToast } from '@/composables/useNotification'

const { confirm } = useModal()
const toast = useToast()

const categories = ref([])
const loading = ref(false)
const saving = ref(false)

const showCategoryModal = ref(false)
const editingCategory = ref(null)
const categoryForm = reactive({ name: '', description: '', sort_order: 0, is_active: true })

const showTrackModal = ref(false)
const editingTrack = ref(null)
const trackForm = reactive({ exam_category_id: null, name: '', description: '', sort_order: 0, is_active: true })

const load = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/admin/exam-categories')
    categories.value = data || []
  } catch (e) {
    toast.error('Error', 'Gagal memuat kategori ujian.')
  } finally {
    loading.value = false
  }
}

const openCategoryModal = (cat = null) => {
  editingCategory.value = cat
  categoryForm.name = cat?.name || ''
  categoryForm.description = cat?.description || ''
  categoryForm.sort_order = cat?.sort_order ?? 0
  categoryForm.is_active = cat ? !!cat.is_active : true
  showCategoryModal.value = true
}

const saveCategory = async () => {
  saving.value = true
  try {
    if (editingCategory.value) {
      await window.axios.put(`/api/admin/exam-categories/${editingCategory.value.id}`, { ...categoryForm })
      toast.success('Success', 'Kategori diperbarui.')
    } else {
      await window.axios.post('/api/admin/exam-categories', { ...categoryForm })
      toast.success('Success', 'Kategori ditambahkan.')
    }
    showCategoryModal.value = false
    await load()
  } catch (e) {
    toast.error('Error', e.response?.data?.message || 'Gagal menyimpan kategori.')
  } finally {
    saving.value = false
  }
}

const removeCategory = async (cat) => {
  const ok = await confirm({
    title: 'Hapus Kategori',
    message: `Hapus kategori "${cat.name}" beserta seluruh track di dalamnya?`,
    confirmText: 'Hapus',
    type: 'danger',
  })
  if (!ok) return
  try {
    await window.axios.delete(`/api/admin/exam-categories/${cat.id}`)
    toast.success('Success', 'Kategori dihapus.')
    await load()
  } catch (e) {
    toast.error('Error', e.response?.data?.message || 'Gagal menghapus kategori.')
  }
}

const openTrackModal = (cat, track = null) => {
  editingTrack.value = track
  trackForm.exam_category_id = track?.exam_category_id ?? cat?.id ?? categories.value[0]?.id ?? null
  trackForm.name = track?.name || ''
  trackForm.description = track?.description || ''
  trackForm.sort_order = track?.sort_order ?? 0
  trackForm.is_active = track ? !!track.is_active : true
  showTrackModal.value = true
}

const saveTrack = async () => {
  saving.value = true
  try {
    if (editingTrack.value) {
      await window.axios.put(`/api/admin/exam-tracks/${editingTrack.value.id}`, { ...trackForm })
      toast.success('Success', 'Track diperbarui.')
    } else {
      await window.axios.post('/api/admin/exam-tracks', { ...trackForm })
      toast.success('Success', 'Track ditambahkan.')
    }
    showTrackModal.value = false
    await load()
  } catch (e) {
    toast.error('Error', e.response?.data?.message || 'Gagal menyimpan track.')
  } finally {
    saving.value = false
  }
}

const removeTrack = async (track) => {
  const ok = await confirm({
    title: 'Hapus Track',
    message: `Hapus track "${track.name}"? Soal yang terkait akan kehilangan asosiasi track.`,
    confirmText: 'Hapus',
    type: 'danger',
  })
  if (!ok) return
  try {
    await window.axios.delete(`/api/admin/exam-tracks/${track.id}`)
    toast.success('Success', 'Track dihapus.')
    await load()
  } catch (e) {
    toast.error('Error', e.response?.data?.message || 'Gagal menghapus track.')
  }
}

onMounted(load)
</script>
