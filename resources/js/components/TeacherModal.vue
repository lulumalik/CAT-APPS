<template>
  <Teleport to="body">
    <Transition name="teacher-modal-fade">
      <div
        v-if="isOpen"
        class="fixed inset-0 z-[130] flex items-center justify-center bg-black/55 p-4 backdrop-blur-[2px]"
        role="dialog"
        aria-modal="true"
        aria-labelledby="teacher-modal-title"
        @click.self="closeModal"
      >
        <div class="w-full max-w-5xl overflow-hidden rounded-3xl border border-border bg-white shadow-2xl shadow-black/20">
          <div
            v-if="!selectedTeacher"
            class="border-b border-border bg-gradient-to-r from-sky to-white px-5 py-4 md:px-6 md:py-5"
          >
            <div class="flex items-start justify-between gap-4">
              <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">Detail Keunggulan</p>
                <h3 id="teacher-modal-title" class="text-lg md:text-xl font-bold text-text">Daftar Pengajar</h3>
                <p class="mt-1 text-sm text-gray-600">Klik pengajar untuk melihat profil lengkap.</p>
              </div>
              <button
                type="button"
                class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-border text-gray-500 transition-colors hover:bg-background hover:text-text"
                aria-label="Tutup"
                @click="closeModal"
              >
                <XIcon class="h-4 w-4" />
              </button>
            </div>
          </div>

          <div v-if="!selectedTeacher" class="max-h-[80vh] overflow-y-auto p-5 md:p-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
              <button
                v-for="teacher in teachers"
                :key="teacher.id"
                type="button"
                class="group flex h-full flex-col rounded-2xl border border-border bg-gradient-to-br from-white to-sky/40 p-3 text-left shadow transition-all duration-300 hover:-translate-y-1 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/35"
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

          <div v-else class="relative overflow-hidden">
            <button
              type="button"
              class="absolute right-3 top-3 z-40 inline-flex h-10 w-10 items-center justify-center rounded-full bg-black/65 text-white shadow-lg transition hover:bg-black/80 focus:outline-none focus:ring-2 focus:ring-white/90"
              aria-label="Tutup detail pengajar"
              @click="closeModal"
            >
              <XIcon class="h-5 w-5" />
            </button>
            <div class="absolute z-30 h-[700px] overflow-y-auto bg-cover bg-center px-5 py-5 md:px-6 md:py-6">
              <div class="rounded-2xl p-4 md:p-6">
                <div class="grid gap-4 md:grid-cols-[220px_1fr] md:items-center">
                  <div class="relative mx-auto w-40 md:w-52">
                    <div class="h-40 w-40 overflow-hidden rounded-full border-4 border-white shadow-lg md:h-52 md:w-52">
                      <img
                        :src="selectedTeacher.image"
                        :alt="selectedTeacher.name"
                        class="h-full w-full object-cover object-top"
                      />
                    </div>
                  </div>

                  <div class="space-y-2 text-left">
                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-primary">Data Diri</p>
                    <h4 class="text-xl font-extrabold leading-tight text-text md:text-2xl">{{ selectedTeacher.name }}</h4>
                    <p class="text-sm font-semibold uppercase tracking-wide text-primary">{{ selectedTeacher.role }}</p>
                    <div class="mt-3 space-y-1.5 text-sm text-gray-700">
                      <p>
                        <span class="font-semibold text-text">Tempat, Tanggal Lahir:</span>
                        {{ selectedTeacher.birthPlaceDate }}
                      </p>
                    </div>
                  </div>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 md:grid-cols-2">
                  <div class="rounded-xl">
                    <p
                      class="mb-2 rounded-full bg-gradient-to-r from-primary to-white px-4 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-white"
                    >
                      Riwayat Pendidikan
                    </p>
                    <ul class="teacher-history-list mt-3 text-sm text-gray-700">
                      <li
                        v-for="item in selectedTeacher.education"
                        :key="item"
                        class="teacher-history-item flex items-start gap-2"
                      >
                        <span class="teacher-history-dot mt-1.5 h-2 w-2 shrink-0 rounded-full bg-secondary" />
                        <span>{{ item }}</span>
                      </li>
                    </ul>
                  </div>

                  <div class="rounded-xl">
                    <p
                      class="mb-2 rounded-full bg-gradient-to-r from-primary to-white px-4 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-white"
                    >
                      Pengalaman Mengajar
                    </p>
                    <ul class="teacher-history-list mt-3 text-sm text-gray-700">
                      <li
                        v-for="item in selectedTeacher.teaching"
                        :key="item"
                        class="teacher-history-item flex items-start gap-2"
                      >
                        <span class="teacher-history-dot mt-1.5 h-2 w-2 shrink-0 rounded-full bg-secondary" />
                        <span>{{ item }}</span>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                  <button
                    type="button"
                    class="rounded-full border border-primary px-5 py-2 text-sm font-semibold text-primary transition-colors hover:bg-primary/5"
                    @click="selectedTeacher = null"
                  >
                    Kembali
                  </button>
                </div>
              </div>
            </div>
            <div>
              <img :src="cvTemplateUrl" alt="CV Template" class="w-full h-screen" />
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

.teacher-history-item + .teacher-history-item {
  margin-top: 0.55rem;
}
</style>
