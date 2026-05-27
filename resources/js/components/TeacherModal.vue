<template>
  <Teleport to="body">
    <Transition name="teacher-modal-fade">
      <div v-if="isOpen"
        class="fixed inset-0 z-[130] flex items-center justify-center bg-black/55 p-4 backdrop-blur-[2px] " role="dialog"
        aria-modal="true" aria-labelledby="teacher-modal-title" @click.self="closeModal">
        <div
          class="w-full max-w-5xl overflow-hidden rounded-3xl border border-border programs-themed-bg shadow-2xl shadow-black/20">
          <div v-if="!selectedTeacher" class="border-b border-border px-5 py-4 md:px-6 md:py-5">
            <div class="flex items-start justify-between gap-4">
              <div class="text-white">
                <h3 id="teacher-modal-title" class="text-lg md:text-xl font-bold">Daftar Pengajar</h3>
                <p class="mt-1 text-sm">Klik pengajar untuk melihat profil lengkap.</p>
              </div>
              <button type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-border text-white transition-colors hover:bg-background hover:text-text"
                aria-label="Tutup" @click="closeModal">
                <XIcon class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div v-if="!selectedTeacher" class="max-h-[80vh] overflow-y-auto p-5 md:p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <button v-for="teacher in teachers" :key="teacher.id" type="button"
                class="group flex h-full flex-col rounded-2xl border border-border bg-white p-3 text-left shadow transition-all duration-300 hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/35"
                @click="selectedTeacher = teacher">
                <div class="aspect-[9/12] w-full overflow-hidden rounded-xl bg-gray-100">
                  <img :src="teacher.image" :alt="teacher.name"
                    class="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-105" />
                </div>
                <h4 class="mt-3 text-sm font-bold leading-snug text-text">{{ teacher.name }}</h4>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-primary">{{ teacher.role }}</p>
              </button>
            </div>
          </div>

          <div v-else class="relative overflow-hidden">
            <button type="button"
              class="absolute right-3 top-3 z-40 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/65 text-white shadow-lg transition hover:bg-black/80 focus:outline-none focus:ring-2 focus:ring-white/90"
              aria-label="Tutup detail pengajar" @click="closeModal">
              <XIcon class="h-5 w-5" />
            </button>
            <div class="teacher-cv-layout">
              <aside class="teacher-cv-sidebar relative left-24">
                <div class="teacher-cv-photo-frame">
                  <img :src="selectedTeacher.image" :alt="selectedTeacher.name" class="teacher-cv-photo" />
                </div>

                <div class="teacher-cv-identity">
                  <h4 class="teacher-cv-name">{{ selectedTeacher.name }}</h4>
                  <p class="teacher-cv-role">{{ selectedTeacher.role }}</p>
                </div>

                <div class="teacher-cv-meta">
                  <p><span class="font-semibold">Tempat, Tanggal Lahir:</span></p>
                  <p>{{ selectedTeacher.birthPlaceDate || '-' }}</p>
                </div>

                <div class="mt-6">
                  <button type="button"
                    class="rounded-full border border-primary px-5 py-2 text-sm font-semibold text-primary transition-colors hover:bg-primary/5"
                    @click="selectedTeacher = null">
                    Kembali
                  </button>
                </div>
              </aside>

              <section class="teacher-cv-content">
                <div class="teacher-cv-section">
                  <h5 class="teacher-cv-heading">Profil</h5>
                  <p class="teacher-cv-paragraph">
                    {{ selectedTeacher.name }} merupakan pengajar {{ selectedTeacher.role }} di Pratistha Cendekia
                    Prestasi dan berperan dalam pembinaan peserta secara terarah.
                  </p>
                </div>

                <div class="teacher-cv-section">
                  <h5 class="teacher-cv-heading">Pendidikan</h5>
                  <ul class="teacher-cv-list">
                    <li v-for="item in selectedTeacher.education" :key="item">{{ item }}</li>
                  </ul>
                </div>

                <div class="teacher-cv-section">
                  <h5 class="teacher-cv-heading">Pengalaman Mengajar</h5>
                  <ul class="teacher-cv-list">
                    <li v-for="item in selectedTeacher.teaching" :key="item">{{ item }}</li>
                  </ul>
                </div>
              </section>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue'
import { X as XIcon } from 'lucide-vue-next'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  teachers: {
    type: Array,
    default: () => [],
  },
  cvTemplateUrl: {
    type: String,
    required: true,
  },
})

const emit = defineEmits(['close'])
const selectedTeacher = ref(null)

const closeModal = () => {
  selectedTeacher.value = null
  emit('close')
}

watch(
  () => props.isOpen,
  (isOpen) => {
    if (!isOpen) selectedTeacher.value = null
  },
)
</script>

<style scoped>
.programs-themed-bg {
  background-image:
    radial-gradient(circle at 8% 10%, rgba(255, 255, 255, 0.1) 0 2px, transparent 2px 100%),
    linear-gradient(130deg, #1a1a2d 0%, #202239 55%, #262743 100%);
  background-size: 28px 28px, 100% 100%;
}

.teacher-modal-fade-enter-active,
.teacher-modal-fade-leave-active {
  transition: all 0.28s ease;
}

.teacher-modal-fade-enter-from,
.teacher-modal-fade-leave-to {
  opacity: 0;
  transform: translateY(12px);
}

.teacher-history-list {
  list-style: none;
  padding: 0;
}

.teacher-history-item+.teacher-history-item {
  margin-top: 0.55rem;
}

.teacher-cv-layout {
  display: grid;
  grid-template-columns: minmax(260px, 320px) 1fr;
  height: min(86vh, 760px);
  max-height: 86vh;
  overflow: hidden;
}

.teacher-cv-sidebar {
  background: #fff;
  padding: 2rem 1.6rem;
  border-right: 1px solid rgba(0, 0, 0, 0.08);
  overflow-y: auto;
  min-height: 0;
}

.teacher-cv-photo-frame {
  background: #ffffff;
  padding: 0.75rem;
}

.teacher-cv-photo {
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  object-position: top;
}

.teacher-cv-identity {
  margin-top: 1.4rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.28);
}

.teacher-cv-name {
  font-size: 1.9rem;
  font-weight: 800;
  line-height: 1.1;
  letter-spacing: 0.02em;
  color: #1e2430;
  text-transform: uppercase;
}

.teacher-cv-role {
  margin-top: 0.45rem;
  font-size: 0.95rem;
  font-weight: 600;
  letter-spacing: 0.16em;
  color: #4b5563;
  text-transform: uppercase;
}

.teacher-cv-meta {
  margin-top: 1rem;
  font-size: 0.9rem;
  line-height: 1.45;
  color: #374151;
}

.teacher-cv-content {
  background-image:
    radial-gradient(circle at 8% 10%, rgba(255, 255, 255, 0.1) 0 2px, transparent 2px 100%),
    linear-gradient(130deg, #1a1a2d 0%, #202239 55%, #262743 100%);
  background-size: 28px 28px, 100% 100%;
  padding-left: 10rem;
  padding-top: 2rem;
  padding-bottom: 2rem;
  padding-right: 2rem;
  overflow-y: auto;
  min-height: 0;
}

.teacher-cv-section+.teacher-cv-section {
  margin-top: 1.8rem;
}

.teacher-cv-heading {
  font-size: 1.4rem;
  font-weight: 800;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  color: #fff;
  padding-bottom: 0.35rem;
  border-bottom: 2px solid rgba(255, 255, 255, 0.4);
}

.teacher-cv-paragraph {
  margin-top: 0.8rem;
  color: #fff;
  font-size: 1rem;
  line-height: 1.65;
}

.teacher-cv-list {
  margin-top: 0.85rem;
  display: grid;
  gap: 0.65rem;
  padding-left: 1.1rem;
  list-style: disc;
  color: #fff;
  font-size: 1rem;
  line-height: 1.6;
}

@media (max-width: 900px) {
  .teacher-cv-layout {
    grid-template-columns: 1fr;
    height: min(88vh, 860px);
    max-height: 88vh;
  }

  .teacher-cv-sidebar {
    border-right: none;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
  }
}
</style>
