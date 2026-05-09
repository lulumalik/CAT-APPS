<template>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Kelola Materi</h1>
        <p class="text-gray-500 mt-2">Tambahkan dan kelola materi pembelajaran untuk siswa.</p>
      </div>
      <button @click="openCreate" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform hover:-translate-y-0.5 transition-all inline-flex items-center gap-2">
        <Plus class="h-4 w-4" />
        Tambah Materi
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-4 border-[#9DB359] border-t-transparent"></div>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 text-red-600 p-4 rounded-xl border border-red-100 text-center">
      {{ error }}
    </div>

    <!-- Empty State -->
    <div v-else-if="materials.length === 0" class="text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm">
      <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-full bg-[#9DB359]/10 text-[#9DB359]">
        <BookOpenText class="h-8 w-8" />
      </div>
      <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada materi</h3>
      <p class="text-gray-500 max-w-md mx-auto">Mulai tambahkan materi pembelajaran agar siswa dapat membacanya.</p>
    </div>

    <!-- Materials List -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="material in materials" :key="material.id" class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 relative group hover:shadow-2xl hover:shadow-black/5 transition-all duration-300">
        <div class="flex justify-between items-start mb-4">
          <span :class="material.status === 'published' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider">
            {{ material.status === 'published' ? 'Published' : 'Draft' }}
          </span>
          <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <button @click="edit(material)" class="p-2 bg-blue-50 text-blue-600 rounded-full hover:bg-blue-100 transition-colors">
              <Pencil class="h-4 w-4" />
            </button>
            <button @click="remove(material.id)" class="p-2 bg-red-50 text-red-600 rounded-full hover:bg-red-100 transition-colors">
              <Trash2 class="h-4 w-4" />
            </button>
          </div>
        </div>
        
        <h3 class="text-xl font-bold text-[#1A1A1A] mb-2 line-clamp-2">{{ material.title }}</h3>
        <div class="text-sm text-gray-500 mb-4 flex items-center gap-2">
          <span>By {{ material.creator?.name || 'Unknown' }}</span>
          <span>&bull;</span>
          <span>{{ new Date(material.created_at).toLocaleDateString('id-ID') }}</span>
        </div>
        
        <router-link :to="{ name: 'blog-detail', params: { slug: material.slug } }" target="_blank" class="text-[#9DB359] font-medium hover:underline text-sm">
          Lihat di halaman publik &rarr;
        </router-link>
      </div>
    </div>

    <!-- Material Form Modal -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/40 backdrop-blur-sm transition-opacity" @click="closeModal"></div>
      <div class="relative w-full max-w-4xl max-h-[90vh] flex flex-col rounded-[2rem] bg-white shadow-2xl overflow-hidden border border-gray-100">
        
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
          <h2 class="text-2xl font-bold text-gray-900">{{ isEdit ? 'Edit Materi' : 'Tambah Materi' }}</h2>
          <button @click="closeModal" class="p-2 rounded-full hover:bg-gray-200 transition-colors text-gray-500">
            <X class="h-6 w-6" />
          </button>
        </div>

        <div class="p-6 overflow-y-auto flex-1">
          <div class="space-y-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Judul Materi</label>
              <input v-model="form.title" type="text" class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 focus:ring-2 focus:ring-[#9DB359]/20 focus:border-[#9DB359] transition-all" placeholder="Masukkan judul..." />
            </div>

            <div class="grid grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kategori (Opsional)</label>
                <select v-model="form.category" class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 focus:ring-2 focus:ring-[#9DB359]/20 focus:border-[#9DB359] transition-all">
                  <option value="">Pilih Kategori</option>
                  <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select v-model="form.status" class="w-full rounded-xl border-gray-200 bg-white px-4 py-3 focus:ring-2 focus:ring-[#9DB359]/20 focus:border-[#9DB359] transition-all">
                  <option value="published">Published</option>
                  <option value="draft">Draft</option>
                </select>
              </div>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Konten</label>
              <div class="rounded-xl overflow-hidden border border-gray-200">
                <QuillEditor v-model:content="form.content" :toolbar="toolbarOptions" contentType="html" theme="snow" class="min-h-[300px] bg-white" />
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3">
          <button @click="closeModal" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-200 font-medium transition-colors">Batal</button>
          <button @click="save" :disabled="saving" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black transition-all disabled:opacity-50 flex items-center gap-2">
            <span v-if="saving" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
            {{ isEdit ? 'Simpan Perubahan' : 'Simpan Materi' }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, computed } from 'vue'
import { BookOpenText, Pencil, Plus, Trash2, X } from 'lucide-vue-next'
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css'
import { useToast, useModal } from '@/composables/useNotification'

const materials = ref([])
const loading = ref(false)
const error = ref(null)
const saving = ref(false)

const showModal = ref(false)
const isEdit = ref(false)
const editingId = ref(null)

const toast = useToast()
const { confirm } = useModal()

const categories = [
  'Pendidikan Agama dan Budi Pekerti',
  'Pendidikan Pancasila',
  'Bahasa Indonesia',
  'Matematika',
  'Fisika',
  'Kimia',
  'Biologi',
  'Sosiologi',
  'Ekonomi',
  'Sejarah',
  'Geografi',
  'Bahasa Inggris',
  'Pendidikan Jasmani, Olahraga, dan Kesehatan (PJOK)',
  'Informatika',
  'Seni dan Prakarya (Seni Musik, Rupa, Teater, atau Tari)',
  'Muatan Lokal',
  'Matematika Tingkat Lanjut',
  'Antropologi'
]

const form = reactive({
  title: '',
  category: '',
  status: 'published',
  content: ''
})

const toolbarOptions = [
  ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
  ['blockquote', 'code-block'],

  [{ 'header': 1 }, { 'header': 2 }],               // custom button values
  [{ 'list': 'ordered'}, { 'list': 'bullet' }],
  [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
  [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
  [{ 'direction': 'rtl' }],                         // text direction

  [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
  [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

  [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
  [{ 'font': [] }],
  [{ 'align': [] }],

  ['clean'],                                         // remove formatting button
  ['link', 'image', 'video']                         // link and image, video
]

const fetchMaterials = async () => {
  loading.value = true
  try {
    const { data } = await window.axios.get('/api/materials')
    materials.value = data
  } catch (err) {
    error.value = 'Gagal memuat materi'
    toast.error('Error', error.value)
  } finally {
    loading.value = false
  }
}

const openCreate = () => {
  isEdit.value = false
  editingId.value = null
  form.title = ''
  form.category = ''
  form.status = 'published'
  form.content = ''
  showModal.value = true
}

const edit = (material) => {
  isEdit.value = true
  editingId.value = material.id
  form.title = material.title
  form.category = material.category || ''
  form.status = material.status
  form.content = material.content
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const save = async () => {
  if (!form.title || !form.content) {
    toast.error('Error', 'Judul dan konten wajib diisi')
    return
  }

  saving.value = true
  try {
    if (isEdit.value) {
      const { data } = await window.axios.put(`/api/materials/${editingId.value}`, form)
      const index = materials.value.findIndex(m => m.id === editingId.value)
      if (index !== -1) {
        materials.value[index] = data
      }
      toast.success('Sukses', 'Materi berhasil diperbarui')
    } else {
      const { data } = await window.axios.post('/api/materials', form)
      materials.value.unshift(data)
      toast.success('Sukses', 'Materi berhasil ditambahkan')
    }
    closeModal()
  } catch (err) {
    toast.error('Error', 'Gagal menyimpan materi')
  } finally {
    saving.value = false
  }
}

const remove = async (id) => {
  const confirmed = await confirm({
    title: 'Hapus Materi',
    message: 'Apakah Anda yakin ingin menghapus materi ini? Tindakan ini tidak dapat dibatalkan.',
    confirmText: 'Hapus',
    type: 'danger'
  })

  if (confirmed) {
    try {
      await window.axios.delete(`/api/materials/${id}`)
      materials.value = materials.value.filter(m => m.id !== id)
      toast.success('Sukses', 'Materi berhasil dihapus')
    } catch (err) {
      toast.error('Error', 'Gagal menghapus materi')
    }
  }
}

onMounted(() => {
  fetchMaterials()
})
</script>

<style>
/* Customizing Quill editor slightly to fit the design */
.ql-toolbar.ql-snow {
  border-top-left-radius: 0.75rem;
  border-top-right-radius: 0.75rem;
  border-color: #e5e7eb !important;
  background-color: #f9fafb;
}
.ql-container.ql-snow {
  border-bottom-left-radius: 0.75rem;
  border-bottom-right-radius: 0.75rem;
  border-color: #e5e7eb !important;
  font-family: inherit;
  font-size: 1rem;
}
.ql-editor {
  min-height: 300px;
}
</style>
