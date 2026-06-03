<template>
  <Teleport to="body">
    <Transition name="teacher-modal-fade">
      <div
        v-if="isOpen"
        class="teacher-modal-overlay fixed inset-0 z-[130] flex items-end sm:items-center justify-center bg-black/55 p-0 sm:p-4 backdrop-blur-[2px]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="teacher-modal-title"
        @click.self="closeModal"
      >
        <div
          class="teacher-modal-panel w-full sm:max-w-5xl flex flex-col overflow-hidden rounded-t-3xl sm:rounded-3xl border border-border programs-themed-bg shadow-2xl shadow-black/20 max-h-[92dvh] sm:max-h-[min(90dvh,860px)]"
          @click.stop
        >
          <div v-if="!selectedTeacher" class="shrink-0 border-b border-border px-5 py-4 md:px-6 md:py-5">
            <div class="flex items-start justify-between gap-4">
              <div class="text-white">
                <h3 id="teacher-modal-title" class="text-lg md:text-xl font-bold">Daftar Pengajar</h3>
                <p class="mt-1 text-sm">Klik pengajar untuk melihat profil lengkap.</p>
              </div>
              <button
                type="button"
                class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full border border-border text-white transition-colors hover:bg-background hover:text-text"
                aria-label="Tutup"
                @click="closeModal"
              >
                <XIcon class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div v-if="!selectedTeacher" class="teacher-modal-scroll flex-1 min-h-0 overflow-y-auto overscroll-contain p-5 md:p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <button
                v-for="teacher in teachers"
                :key="teacher.id"
                type="button"
                class="group flex h-full flex-col rounded-2xl border border-border bg-white p-3 text-left shadow transition-all duration-300 hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/35"
                @click="selectedTeacher = teacher"
              >
                <div class="aspect-[9/12] w-full overflow-hidden rounded-xl bg-gray-100">
                  <img
                    :src="teacher.image"
                    :alt="teacher.name"
                    class="h-full w-full object-cover object-center transition-transform duration-500 group-hover:scale-105"
                  />
                </div>
                <h4 class="mt-3 text-sm font-bold leading-snug text-text">{{ teacher.name }}</h4>
                <p class="mt-1 text-xs font-semibold uppercase tracking-wide text-primary">{{ teacher.role }}</p>
              </button>
            </div>
          </div>

          <div v-else class="relative flex flex-col flex-1 min-h-0">
            <button
              type="button"
              class="absolute right-3 top-3 z-40 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/65 text-white shadow-lg transition hover:bg-black/80 focus:outline-none focus:ring-2 focus:ring-white/90"
              aria-label="Tutup detail pengajar"
              @click="closeModal"
            >
              <XIcon class="h-5 w-5" />
            </button>

            <div class="teacher-modal-scroll flex-1 min-h-0 overflow-y-auto overscroll-contain">
              <div class="teacher-cv-layout">
                <aside class="teacher-cv-sidebar">
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

                  <div class="mt-6 pb-2 sm:pb-0">
                    <button
                      type="button"
                      class="w-full sm:w-auto rounded-full border border-primary px-5 py-2.5 text-sm font-semibold text-primary transition-colors hover:bg-primary/5"
                      @click="selectedTeacher = null"
                    >
                      Kembali
                    </button>
                  </div>
                </aside>

                <section class="teacher-cv-content ui-programs-bg">
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
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue'
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

const setBodyScrollLock = (locked) => {
  if (typeof document === 'undefined') return
  document.body.style.overflow = locked ? 'hidden' : ''
}

watch(
  () => props.isOpen,
  (isOpen) => {
    setBodyScrollLock(isOpen)
    if (!isOpen) selectedTeacher.value = null
  },
)

onBeforeUnmount(() => {
  setBodyScrollLock(false)
})
</script>

<style scoped>
.teacher-modal-fade-enter-active,
.teacher-modal-fade-leave-active {
  transition: all 0.28s ease;
}

.teacher-modal-fade-enter-from,
.teacher-modal-fade-leave-to {
  opacity: 0;
  transform: translateY(12px);
}

.teacher-modal-scroll {
  -webkit-overflow-scrolling: touch;
}

.teacher-cv-layout {
  display: grid;
  grid-template-columns: minmax(260px, 320px) 1fr;
  min-height: min-content;
}

.teacher-cv-sidebar {
  background: #fff;
  padding: 2.75rem 1.5rem 1.5rem;
  border-right: 1px solid rgba(0, 0, 0, 0.08);
}

.teacher-cv-photo-frame {
  background: #ffffff;
  padding: 0;
}

.teacher-cv-photo {
  width: 100%;
  aspect-ratio: 4 / 5;
  object-fit: cover;
  object-position: top center;
  display: block;
}

.teacher-cv-identity {
  margin-top: 1.25rem;
  padding-bottom: 1rem;
  border-bottom: 1px solid rgba(0, 0, 0, 0.28);
}

.teacher-cv-name {
  font-size: clamp(1.1rem, 4vw, 1.9rem);
  font-weight: 800;
  line-height: 1.2;
  letter-spacing: 0.02em;
  color: #1e2430;
  text-transform: uppercase;
}

.teacher-cv-role {
  margin-top: 0.45rem;
  font-size: 0.85rem;
  font-weight: 600;
  letter-spacing: 0.12em;
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
  padding: 2rem 2rem 2.5rem 3rem;
}

.teacher-cv-section + .teacher-cv-section {
  margin-top: 1.8rem;
}

.teacher-cv-heading {
  font-size: clamp(1.1rem, 3.5vw, 1.4rem);
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
  .teacher-modal-overlay {
    align-items: flex-end;
  }

  .teacher-cv-layout {
    grid-template-columns: 1fr;
  }

  .teacher-cv-sidebar {
    border-right: none;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    padding: 3rem 1.25rem 1.25rem;
  }

  .teacher-cv-photo {
    aspect-ratio: 3 / 4;
    max-height: 320px;
  }

  .teacher-cv-content {
    padding: 1.5rem 1.25rem 2rem;
  }

  .teacher-cv-section:last-child {
    padding-bottom: 0.5rem;
  }
}
</style>
