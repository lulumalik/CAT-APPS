<template>
  <main class="interest-page">
    <div class="interest-shell">
      <div class="interest-brand">
        <span class="interest-brand-icon">
          <CheckSquare class="h-5 w-5" />
        </span>
        <span class="interest-brand-name">CAT<span>Lab</span></span>
      </div>

      <header class="interest-header">
        <h1>Pilih minat ujianmu</h1>
        <p>
          Pilih kategori yang ingin kamu taklukkan, lalu tentukan ujian spesifiknya.
          Kamu bisa mengubahnya kapan saja lewat halaman profil.
        </p>
      </header>

      <div v-if="loading" class="interest-loading">
        <span class="interest-spinner" />
        Memuat kategori...
      </div>

      <template v-else>
        <section class="interest-step">
          <div class="interest-step-label">
            <span class="interest-step-num">1</span>
            Kategori ujian
          </div>
          <div class="interest-category-grid">
            <button
              v-for="cat in categories"
              :key="cat.id"
              type="button"
              class="interest-category-card"
              :class="{ 'is-selected': selectedCategory?.id === cat.id }"
              @click="selectCategory(cat)"
            >
              <span class="interest-category-icon">
                <component :is="categoryIcon(cat)" class="h-6 w-6" />
              </span>
              <span class="interest-category-name">{{ cat.name }}</span>
              <span class="interest-category-desc">{{ cat.description }}</span>
              <span class="interest-category-tracks">
                <span v-for="tr in cat.tracks" :key="tr.id" class="interest-chip">{{ tr.name }}</span>
              </span>
            </button>
          </div>
        </section>

        <section v-if="selectedCategory" class="interest-step">
          <div class="interest-step-label">
            <span class="interest-step-num">2</span>
            Ujian spesifik di {{ selectedCategory.name }}
          </div>
          <div class="interest-track-grid">
            <button
              v-for="tr in selectedCategory.tracks"
              :key="tr.id"
              type="button"
              class="interest-track-card"
              :class="{ 'is-selected': selectedTrack?.id === tr.id }"
              @click="selectedTrack = tr"
            >
              <span class="interest-track-radio" aria-hidden="true">
                <Check v-if="selectedTrack?.id === tr.id" class="h-3.5 w-3.5" />
              </span>
              <span class="interest-track-body">
                <span class="interest-track-name">{{ tr.name }}</span>
                <span class="interest-track-desc">{{ tr.description }}</span>
              </span>
            </button>
          </div>
        </section>

        <div class="interest-actions">
          <button
            type="button"
            class="interest-submit"
            :disabled="!selectedTrack || submitting"
            @click="submit"
          >
            {{ submitting ? 'Menyimpan...' : 'Simpan & Mulai' }}
            <ArrowRight v-if="!submitting" class="h-4 w-4" />
          </button>
          <p v-if="!selectedTrack" class="interest-hint">Pilih kategori dan ujian spesifik dulu untuk melanjutkan.</p>
        </div>
      </template>
    </div>
  </main>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import {
  ArrowRight,
  BookOpen,
  Check,
  CheckSquare,
  GraduationCap,
  Languages,
  Target,
} from 'lucide-vue-next'
import { useAppStore } from '@/stores/app'
import { useToast } from '@/composables/useNotification'

const router = useRouter()
const store = useAppStore()
const toast = useToast()

const categories = ref([])
const loading = ref(true)
const submitting = ref(false)
const selectedCategory = ref(null)
const selectedTrack = ref(null)

const iconMap = {
  languages: Languages,
  'book-open': BookOpen,
  'graduation-cap': GraduationCap,
}

const categoryIcon = (cat) => iconMap[cat.icon] || Target

const currentTrackId = computed(() => store.user?.exam_track_id || null)

const selectCategory = (cat) => {
  selectedCategory.value = cat
  selectedTrack.value = cat.tracks?.length === 1 ? cat.tracks[0] : null
}

const load = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('/api/exam-categories')
    categories.value = data || []

    // Pre-select minat yang sudah tersimpan (jika ada).
    if (currentTrackId.value) {
      for (const cat of categories.value) {
        const tr = (cat.tracks || []).find((x) => x.id === currentTrackId.value)
        if (tr) {
          selectedCategory.value = cat
          selectedTrack.value = tr
          break
        }
      }
    }
  } catch (e) {
    toast.error('Error', 'Gagal memuat kategori ujian.')
  } finally {
    loading.value = false
  }
}

const submit = async () => {
  if (!selectedTrack.value) return
  submitting.value = true
  try {
    const { data } = await axios.post('/api/my-interest', {
      exam_track_id: selectedTrack.value.id,
    })
    if (data?.user) {
      store.setUser(data.user)
    }
    toast.success('Tersimpan', data?.message || 'Minat ujian berhasil disimpan.')
    router.push('/dashboard')
  } catch (e) {
    toast.error('Error', e.response?.data?.message || 'Gagal menyimpan minat ujian.')
  } finally {
    submitting.value = false
  }
}

onMounted(load)
</script>

<style scoped>
.interest-page {
  min-height: 100dvh;
  background:
    radial-gradient(60rem 30rem at 110% -10%, rgba(37, 99, 235, 0.1), transparent 60%),
    radial-gradient(50rem 26rem at -10% 110%, rgba(37, 99, 235, 0.08), transparent 60%),
    #f5f8fe;
  padding: 2.5rem 1.25rem 4rem;
}

.interest-shell {
  max-width: 60rem;
  margin: 0 auto;
}

.interest-brand {
  display: inline-flex;
  align-items: center;
  gap: 0.6rem;
  margin-bottom: 2rem;
}

.interest-brand-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.3rem;
  height: 2.3rem;
  border-radius: 0.7rem;
  background: #2563eb;
  color: #fff;
}

.interest-brand-name {
  font-size: 1.25rem;
  font-weight: 800;
  color: #0f172a;
}

.interest-brand-name span {
  color: #2563eb;
}

.interest-header h1 {
  font-size: clamp(1.6rem, 3.5vw, 2.2rem);
  font-weight: 800;
  color: #0f172a;
  margin: 0 0 0.5rem;
}

.interest-header p {
  color: #64748b;
  max-width: 36rem;
  margin: 0 0 2rem;
  line-height: 1.6;
}

.interest-loading {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  color: #64748b;
  padding: 3rem 0;
}

.interest-spinner {
  width: 1.4rem;
  height: 1.4rem;
  border-radius: 999px;
  border: 3px solid #dbe6fb;
  border-top-color: #2563eb;
  animation: interest-spin 0.8s linear infinite;
}

@keyframes interest-spin {
  to { transform: rotate(360deg); }
}

.interest-step {
  margin-bottom: 2rem;
}

.interest-step-label {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  font-weight: 700;
  color: #0f172a;
  margin-bottom: 1rem;
}

.interest-step-num {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.7rem;
  height: 1.7rem;
  border-radius: 999px;
  background: #2563eb;
  color: #fff;
  font-size: 0.85rem;
  font-weight: 700;
}

.interest-category-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(16rem, 1fr));
  gap: 1rem;
}

.interest-category-card {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.55rem;
  text-align: left;
  padding: 1.25rem;
  border-radius: 1.1rem;
  border: 2px solid #e6ebf4;
  background: #fff;
  cursor: pointer;
  transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.15s ease;
}

.interest-category-card:hover {
  border-color: #b8cdf5;
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(15, 23, 42, 0.07);
}

.interest-category-card.is-selected {
  border-color: #2563eb;
  box-shadow: 0 12px 26px rgba(37, 99, 235, 0.15);
}

.interest-category-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 2.8rem;
  height: 2.8rem;
  border-radius: 0.8rem;
  background: rgba(37, 99, 235, 0.1);
  color: #2563eb;
}

.interest-category-name {
  font-weight: 800;
  color: #0f172a;
  font-size: 1.05rem;
}

.interest-category-desc {
  font-size: 0.82rem;
  color: #64748b;
  line-height: 1.5;
}

.interest-category-tracks {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4rem;
  margin-top: 0.25rem;
}

.interest-chip {
  font-size: 0.72rem;
  font-weight: 700;
  color: #2563eb;
  background: rgba(37, 99, 235, 0.08);
  border-radius: 999px;
  padding: 0.2rem 0.6rem;
}

.interest-track-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(18rem, 1fr));
  gap: 1rem;
}

.interest-track-card {
  display: flex;
  align-items: flex-start;
  gap: 0.85rem;
  text-align: left;
  padding: 1.1rem 1.2rem;
  border-radius: 1rem;
  border: 2px solid #e6ebf4;
  background: #fff;
  cursor: pointer;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.interest-track-card:hover {
  border-color: #b8cdf5;
}

.interest-track-card.is-selected {
  border-color: #2563eb;
  background: rgba(37, 99, 235, 0.04);
}

.interest-track-radio {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 1.4rem;
  height: 1.4rem;
  flex-shrink: 0;
  margin-top: 0.15rem;
  border-radius: 999px;
  border: 2px solid #cbd5e1;
  color: #fff;
  transition: all 0.15s ease;
}

.interest-track-card.is-selected .interest-track-radio {
  border-color: #2563eb;
  background: #2563eb;
}

.interest-track-name {
  display: block;
  font-weight: 800;
  color: #0f172a;
}

.interest-track-desc {
  display: block;
  font-size: 0.8rem;
  color: #64748b;
  line-height: 1.5;
  margin-top: 0.2rem;
}

.interest-actions {
  margin-top: 2.25rem;
}

.interest-submit {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.85rem 2rem;
  border-radius: 999px;
  background: #2563eb;
  color: #fff;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: background 0.15s ease, transform 0.15s ease;
}

.interest-submit:hover:not(:disabled) {
  background: #1d4ed8;
  transform: translateY(-1px);
}

.interest-submit:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.interest-hint {
  margin-top: 0.75rem;
  font-size: 0.8rem;
  color: #94a3b8;
}
</style>
