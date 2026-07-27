<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-8">
    <PageHeroHeader
      :title="t('questionBank.title')"
      :subtitle="t('questionBank.subtitle')"
      theme="green"
      :icon="LibraryBig"
    >
      <template #actions>
        <div class="flex items-center gap-3 flex-wrap">
          <template v-if="activeTab === 'questions'">
            <button
              type="button"
              class="px-5 py-2.5 rounded-full bg-white/20 text-white hover:bg-white/30 backdrop-blur-md transition-all border border-white/30 text-sm font-medium flex items-center gap-2"
              @click="downloadTemplate"
            >
              <Download class="h-4 w-4" />
              Template Excel
            </button>
            <button
              type="button"
              class="px-5 py-2.5 rounded-full bg-emerald-600 text-white hover:bg-emerald-700 transition-colors shadow-lg shadow-black/10 text-sm font-medium flex items-center gap-2"
              @click="showImportModal = true"
            >
              <Upload class="h-4 w-4" />
              Upload Excel/CSV
            </button>
            <button
              v-if="total > 0"
              class="px-5 py-2.5 rounded-full bg-red-600 text-white hover:bg-red-700 transition-colors shadow-lg shadow-black/10 text-sm font-medium flex items-center gap-2"
              @click="removeAll"
            >
              <Trash2 class="h-4 w-4" />
              {{ t('questionBank.deleteAllQuestions') }}
            </button>
            <button class="px-5 py-2.5 rounded-full bg-[#1A1A1A] text-white hover:bg-gray-800 transition-colors shadow-lg shadow-black/10 text-sm font-medium flex items-center gap-2" @click="openAdd">
              <Plus class="h-4 w-4" />
              {{ t('questionBank.addQuestion') }}
            </button>
          </template>
          <template v-else>
            <button class="px-6 py-2.5 rounded-full bg-[#9DB359] text-white hover:bg-[#8ca34b] transition-colors shadow-lg shadow-[#9DB359]/20 flex items-center gap-2 font-medium" @click="openAddArticle">
              <Plus class="h-[18px] w-[18px]" />
              Tambah Article Quiz
            </button>
          </template>
        </div>
      </template>
    </PageHeroHeader>

    <!-- Tab Navigation -->
    <div class="mt-8 flex border-b border-gray-200">
      <button
        type="button"
        class="flex items-center gap-2 px-6 py-3 font-semibold text-sm border-b-2 transition-colors"
        :class="activeTab === 'questions' ? 'border-[#9DB359] text-[#9DB359]' : 'border-transparent text-gray-500 hover:text-gray-700'"
        @click="activeTab = 'questions'"
      >
        <LibraryBig class="w-4 h-4" />
        Bank Soal
        <span class="px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'questions' ? 'bg-[#9DB359]/15 text-[#6c7c3f]' : 'bg-gray-100 text-gray-600'">
          {{ total }}
        </span>
      </button>
      <button
        type="button"
        class="flex items-center gap-2 px-6 py-3 font-semibold text-sm border-b-2 transition-colors"
        :class="activeTab === 'articles' ? 'border-[#9DB359] text-[#9DB359]' : 'border-transparent text-gray-500 hover:text-gray-700'"
        @click="activeTab = 'articles'"
      >
        <BookOpen class="w-4 h-4" />
        Article Quiz (Bacaan)
        <span class="px-2 py-0.5 rounded-full text-xs" :class="activeTab === 'articles' ? 'bg-[#9DB359]/15 text-[#6c7c3f]' : 'bg-gray-100 text-gray-600'">
          {{ articleQuizzes.length }}
        </span>
      </button>
    </div>

    <!-- Skeleton Loader -->
    <div v-if="loading" class="mt-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="n in 4" :key="n" class="bg-white rounded-[2rem] p-6 border border-gray-100 shadow-sm animate-pulse">
          <div class="h-8 w-16 bg-gray-100 rounded mb-2 mx-auto"></div>
          <div class="h-4 w-24 bg-gray-100 rounded mx-auto"></div>
        </div>
      </div>
    </div>

    <!-- TAB 1: BANK SOAL -->
    <div v-else-if="activeTab === 'questions'">
      <div class="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-[#9DB359]/30 transition-colors">
          <div class="text-4xl font-bold text-[#1A1A1A] mb-1 group-hover:text-[#9DB359] transition-colors">{{ total }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ t('questionBank.totalQuestions') }}</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-green-500/30 transition-colors">
          <div class="text-4xl font-bold text-green-600 mb-1">{{ easy }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ t('questionBank.easyQuestions') }}</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-yellow-500/30 transition-colors">
          <div class="text-4xl font-bold text-yellow-500 mb-1">{{ medium }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ t('questionBank.mediumQuestions') }}</div>
        </div>
        <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 text-center group hover:border-red-500/30 transition-colors">
          <div class="text-4xl font-bold text-red-500 mb-1">{{ hard }}</div>
          <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ t('questionBank.hardQuestions') }}</div>
        </div>
      </div>

      <!-- Filters Bar -->
      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6 mt-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="relative md:col-span-1">
            <input v-model="search" type="text" :placeholder="t('questionBank.searchPlaceholder')" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 pl-10 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors" />
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </div>
          <select v-model="filterBatch" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors appearance-none">
            <option value="">-- Semua Batch / Paket --</option>
            <option v-for="b in availableBatches" :key="b" :value="b">{{ b }}</option>
          </select>
          <select v-model="filterCategory" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors appearance-none">
            <option value="">{{ t('questionBank.allCategories') }}</option>
            <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
          </select>
          <select v-model="filterDifficulty" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors appearance-none">
            <option value="">{{ t('questionBank.allDifficulties') }}</option>
            <option value="Easy">{{ t('modals.question.difficultyEasy') }}</option>
            <option value="Medium">{{ t('modals.question.difficultyMedium') }}</option>
            <option value="Hard">{{ t('modals.question.difficultyHard') }}</option>
          </select>
        </div>
      </div>

      <div class="mt-8 space-y-8">
        <template v-if="isMentor">
          <section v-if="ownFiltered.length" class="space-y-4">
            <div class="flex items-center justify-between gap-3">
              <h2 class="text-lg font-semibold text-[#1A1A1A]">{{ t('questionBank.myQuestionsSection') }}</h2>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-[#9DB359]/15 text-[#6c7c3f] border border-[#9DB359]/30">
                {{ ownFiltered.length }}
              </span>
            </div>
            <div class="space-y-6">
              <article
                v-for="q in ownFiltered"
                :key="q.id"
                class="rounded-[2rem] shadow-sm border-2 border-[#9DB359]/40 bg-gradient-to-br from-[#9DB359]/10 via-white to-white p-8 hover:shadow-md transition-shadow group"
              >
                <QuestionCardBody
                  :question="q"
                  :can-manage="true"
                  highlighted
                  @edit="edit(q)"
                  @remove="remove(q)"
                />
              </article>
            </div>
          </section>

          <section v-if="globalFiltered.length" class="space-y-4">
            <div class="flex items-center justify-between gap-3">
              <h2 class="text-lg font-semibold text-[#1A1A1A]">{{ t('questionBank.globalQuestionsSection') }}</h2>
              <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                {{ globalFiltered.length }}
              </span>
            </div>
            <div class="space-y-6">
              <article
                v-for="q in globalFiltered"
                :key="q.id"
                class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-md transition-shadow group"
              >
                <QuestionCardBody
                  :question="q"
                  :can-manage="false"
                  @edit="edit(q)"
                  @remove="remove(q)"
                />
              </article>
            </div>
          </section>

          <p v-if="!ownFiltered.length && !globalFiltered.length" class="text-center text-gray-500 py-8">
            {{ t('questionBank.noResultsFound') }}
          </p>
        </template>

        <template v-else>
          <div class="space-y-6">
            <article
              v-for="q in filtered"
              :key="q.id"
              class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-md transition-shadow group"
            >
              <QuestionCardBody
                :question="q"
                :can-manage="canManageQuestion(q)"
                @edit="edit(q)"
                @remove="remove(q)"
              />
            </article>
          </div>
        </template>
      </div>
    </div>

    <!-- TAB 2: ARTICLE QUIZ -->
    <div v-else class="mt-8 space-y-6">
      <!-- Article Quiz Filters Bar -->
      <div class="bg-white rounded-[2rem] shadow-xl shadow-black/5 border border-gray-100 p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="relative md:col-span-2">
            <input v-model="articleSearch" type="text" placeholder="Cari judul artikel atau isi bacaan..." class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 pl-10 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors" />
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
          </div>
          <select v-model="articleBatchFilter" class="w-full rounded-xl border border-gray-200 bg-gray-50/50 px-4 py-3 focus:border-[#9DB359] focus:ring-[#9DB359] transition-colors appearance-none">
            <option value="">-- Semua Batch --</option>
            <option v-for="b in availableBatches" :key="b" :value="b">{{ b }}</option>
          </select>
        </div>
      </div>

      <div v-if="filteredArticles.length === 0" class="bg-white rounded-[2rem] p-12 text-center border border-gray-100 shadow-sm text-gray-500">
        Belum ada Article Quiz (Artikel Bacaan) yang sesuai pencarian. Klik "Tambah Article Quiz" untuk membuat artikel baru.
      </div>
      <div v-else class="grid grid-cols-1 gap-6">
        <article
          v-for="art in filteredArticles"
          :key="art.id"
          class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 hover:shadow-md transition-all space-y-4"
        >
          <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold shrink-0">
                <BookOpen class="w-5 h-5" />
              </div>
              <div>
                <div class="flex items-center gap-2 flex-wrap mb-1">
                  <h3 class="text-xl font-bold text-[#1A1A1A]">{{ art.title }}</h3>
                  <!-- Batch Badges for Article Quiz -->
                  <span
                    v-for="b in getArticleBatches(art)"
                    :key="b"
                    class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200"
                  >
                    {{ b }}
                  </span>
                </div>
                <span class="text-xs text-gray-500">{{ art.questions ? art.questions.length : 0 }} soal di-assign</span>
              </div>
            </div>

            <div class="flex items-center gap-2">
              <button
                type="button"
                class="px-4 py-2 rounded-full border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors flex items-center gap-1.5"
                @click="openAssign(art)"
              >
                <Link2 class="w-3.5 h-3.5" />
                Assign Soal
              </button>
              <button
                type="button"
                class="w-9 h-9 rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50 transition-colors flex items-center justify-center"
                @click="editArticle(art)"
              >
                <Pencil class="w-4 h-4" />
              </button>
              <button
                type="button"
                class="w-9 h-9 rounded-full border border-red-100 text-red-600 hover:bg-red-50 transition-colors flex items-center justify-center"
                @click="removeArticle(art)"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>

          <div class="rounded-xl bg-amber-50/60 border border-amber-100 p-5 text-sm text-gray-800 whitespace-pre-line leading-relaxed">
            {{ art.content }}
          </div>

          <!-- Assigned Questions Badges -->
          <div v-if="art.questions && art.questions.length" class="pt-2">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Soal Terhubung:</h4>
            <div class="flex flex-wrap gap-2">
              <div
                v-for="q in art.questions"
                :key="q.id"
                class="inline-flex items-center gap-2 px-3 py-1 rounded-xl text-xs bg-gray-100 border border-gray-200 text-gray-700"
              >
                <span class="font-bold">#{{ q.id }}</span>
                <span v-if="q.batch" class="px-2 py-0.5 rounded-full text-[10px] bg-purple-100 text-purple-700 font-semibold">{{ q.batch }}</span>
                <span class="truncate max-w-xs">{{ q.question }}</span>
              </div>
            </div>
          </div>
        </article>
      </div>
    </div>

    <!-- Question Modal -->
    <QuestionModal
      v-if="showModal"
      :initial="editingItem"
      :article-quizzes="articleQuizzes"
      @close="closeModal"
      @submit="onSubmit"
    />

    <!-- Import Excel/CSV Modal -->
    <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="showImportModal = false"></div>
      <div class="relative w-full max-w-xl rounded-[2rem] bg-white p-8 shadow-2xl border border-gray-100 transform transition-all">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Upload Soal (Excel/CSV)</h2>
            <p class="text-gray-500 text-sm mt-0.5">Unggah file CSV/Excel berisi daftar soal & article quiz massal.</p>
          </div>
          <button class="p-2 rounded-full hover:bg-gray-100 text-gray-400" @click="showImportModal = false">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <form class="space-y-5" @submit.prevent="submitImport">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Batch / Kategori Default (Jika kosong pada file)</label>
            <input v-model="importDefaultBatch" type="text" placeholder="Tryout 1" class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih File Excel / CSV (.csv, .xlsx)</label>
            <input type="file" ref="importFileInput" accept=".csv, .txt, .xlsx, .xls" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#1A1A1A] file:text-white hover:file:bg-black transition-all" />
          </div>

          <div class="bg-blue-50/80 border border-blue-100 rounded-xl p-4 text-xs text-blue-900 leading-relaxed">
            💡 <strong>Tips Import:</strong> Gunakan tombol <strong>"Template Excel"</strong> untuk mengunduh format kolom yang benar (termasuk kolom <code>batch</code>, <code>article_title</code>, dan <code>article_content</code>).
          </div>

          <div class="flex items-center justify-end gap-3 pt-3">
            <button type="button" class="px-5 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 text-sm font-medium" @click="showImportModal = false">Batal</button>
            <button type="submit" :disabled="importing" class="px-6 py-2.5 rounded-full bg-emerald-600 text-white text-sm font-bold hover:bg-emerald-700 transition-all flex items-center gap-2">
              <Upload v-if="!importing" class="w-4 h-4" />
              <span v-if="importing">Mengimpor...</span>
              <span v-else>Mulai Upload</span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Article Quiz Add/Edit Modal -->
    <div v-if="showArticleModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="closeArticleModal"></div>
      <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all">
        <div class="flex items-center justify-between mb-6">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ editingArticle ? 'Edit Article Quiz' : 'Tambah Article Quiz' }}</h2>
            <p class="text-gray-500 mt-1">Masukkan judul dan teks bacaan artikel yang akan di-assign pada soal.</p>
          </div>
          <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="closeArticleModal">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <form class="space-y-6" @submit.prevent="submitArticle">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Judul Artikel / Keterangan Bacaan</label>
            <input v-model="articleForm.title" required type="text" placeholder="Contoh: Bacaan untuk nomor 31 – 33" class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all" />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Isi Teks Artikel Bacaan</label>
            <textarea v-model="articleForm.content" required rows="6" placeholder="Tuliskan isi teks bacaan di sini..." class="w-full rounded-xl border-gray-100 bg-gray-50 px-4 py-3 focus:bg-white focus:border-gray-200 focus:ring-0 transition-all resize-y"></textarea>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4">
            <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors" @click="closeArticleModal">Batal</button>
            <button type="submit" class="px-6 py-2.5 rounded-full bg-[#1A1A1A] text-white font-medium shadow-lg shadow-black/20 hover:bg-black hover:shadow-black/30 transform active:scale-95 transition-all">Simpan Article</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Assign Questions Modal -->
    <div v-if="showAssignModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/30 backdrop-blur-sm transition-opacity" @click="showAssignModal = false"></div>
      <div class="relative w-full max-w-3xl max-h-[90vh] overflow-y-auto rounded-[2rem] bg-white p-8 shadow-2xl shadow-black/10 border border-gray-100 transform transition-all flex flex-col">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Assign Soal ke Artikel</h2>
            <p class="text-gray-500 text-sm mt-0.5">Artikel: <strong class="text-amber-800">{{ assigningArticle?.title }}</strong></p>
          </div>
          <button class="p-2 rounded-full hover:bg-gray-100 transition-colors text-gray-400 hover:text-gray-600" @click="showAssignModal = false">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <p class="text-xs text-gray-500 mb-4">Pilih soal mana saja yang akan menggunakan artikel ini. Card artikel akan otomatis muncul di atas soal saat dikerjakan peserta.</p>

        <div class="space-y-3 max-h-96 overflow-y-auto pr-2 my-2">
          <div
            v-for="q in questions"
            :key="q.id"
            class="flex items-start gap-3 p-4 rounded-xl border transition-all"
            :class="assignSelectedIds.includes(q.id) ? 'border-[#9DB359] bg-[#9DB359]/5' : 'border-gray-100 bg-gray-50 hover:bg-white'"
          >
            <input
              :id="'assign-q-'+q.id"
              type="checkbox"
              :value="q.id"
              v-model="assignSelectedIds"
              class="mt-1 rounded border-gray-300 text-[#9DB359] focus:ring-[#9DB359] cursor-pointer"
            />
            <label :for="'assign-q-'+q.id" class="flex-1 cursor-pointer select-none text-sm">
              <div class="flex items-center gap-2 mb-1">
                <span class="font-bold text-gray-900">#{{ q.id }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-purple-100 text-purple-700 font-medium">{{ q.batch || 'Tryout 1' }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] bg-gray-200 text-gray-600">{{ q.category }}</span>
              </div>
              <p class="text-gray-800">{{ q.question }}</p>
            </label>
          </div>
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-gray-100 mt-4">
          <span class="text-xs text-gray-500">{{ assignSelectedIds.length }} soal terpilih</span>
          <div class="flex items-center gap-3">
            <button type="button" class="px-6 py-2.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium transition-colors text-sm" @click="showAssignModal = false">Batal</button>
            <button type="button" class="px-6 py-2.5 rounded-full bg-[#9DB359] text-white font-bold shadow-lg shadow-[#9DB359]/20 hover:bg-[#8ca34b] transition-all text-sm" @click="submitAssign">Simpan Penugasan</button>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { LibraryBig, Plus, Trash2, BookOpen, Link2, Pencil, Download, Upload } from 'lucide-vue-next'
import QuestionModal from '@/components/QuestionModal.vue'
import QuestionCardBody from '@/components/QuestionCardBody.vue'
import PageHeroHeader from '@/components/PageHeroHeader.vue'
import { useModal, useToast } from '@/composables/useNotification'
import { useI18n } from '@/composables/useI18n'
import { useAppStore } from '@/stores/app'

const { confirm } = useModal()
const toast = useToast()
const { t } = useI18n()
const store = useAppStore()

const activeTab = ref('questions')
const questions = ref([])
const articleQuizzes = ref([])
const availableBatches = ref([])
const loading = ref(false)
const search = ref('')
const filterBatch = ref('')
const filterCategory = ref('')
const filterDifficulty = ref('')
const showModal = ref(false)
const editingItem = ref(null)

const articleSearch = ref('')
const articleBatchFilter = ref('')

const showArticleModal = ref(false)
const editingArticle = ref(null)
const articleForm = ref({ title: '', content: '' })

const showAssignModal = ref(false)
const assigningArticle = ref(null)
const assignSelectedIds = ref([])

const showImportModal = ref(false)
const importDefaultBatch = ref('Tryout 1')
const importFileInput = ref(null)
const importing = ref(false)

const categories = computed(() => {
  if (!questions.value || !Array.isArray(questions.value)) return []
  return [...new Set(questions.value.map(q => q.category))]
})

const isMentor = computed(() => store.role === 'mentor')

const matchesFilters = (q) => {
  const s = search.value.toLowerCase()
  const matchSearch = q.question.toLowerCase().includes(s)
  const matchBatch = !filterBatch.value || q.batch === filterBatch.value
  const matchCat = !filterCategory.value || q.category === filterCategory.value
  const matchDiff = !filterDifficulty.value || q.difficulty === filterDifficulty.value
  return matchSearch && matchBatch && matchCat && matchDiff
}

const getArticleBatches = (art) => {
  if (!art || !art.questions || !Array.isArray(art.questions) || art.questions.length === 0) {
    return ['Tryout 1']
  }
  const batches = art.questions.map(q => q.batch || 'Tryout 1').filter(Boolean)
  return [...new Set(batches)]
}

const filteredArticles = computed(() => {
  if (!articleQuizzes.value || !Array.isArray(articleQuizzes.value)) return []
  const s = articleSearch.value.toLowerCase().trim()
  const b = articleBatchFilter.value

  return articleQuizzes.value.filter(art => {
    const matchSearch = !s || art.title.toLowerCase().includes(s) || art.content.toLowerCase().includes(s)
    const batches = getArticleBatches(art)
    const matchBatch = !b || batches.includes(b)
    return matchSearch && matchBatch
  })
})

const isOwnQuestion = (q) => Number(q?.created_by) === Number(store.user?.id)

const filtered = computed(() => {
  if (!questions.value || !Array.isArray(questions.value)) return []
  return questions.value.filter(matchesFilters)
})

const ownFiltered = computed(() => filtered.value.filter(isOwnQuestion))
const globalFiltered = computed(() => filtered.value.filter((q) => !isOwnQuestion(q)))

const total = computed(() => questions.value?.length || 0)
const easy = computed(() => questions.value?.filter(q => q.difficulty === 'Easy').length || 0)
const medium = computed(() => questions.value?.filter(q => q.difficulty === 'Medium').length || 0)
const hard = computed(() => questions.value?.filter(q => q.difficulty === 'Hard').length || 0)

const canManageQuestion = (question) => {
  if (store.role === 'admin') return true
  if (store.role !== 'mentor') return false
  return isOwnQuestion(question)
}

const sortQuestionsForDisplay = (items) => {
  if (!isMentor.value) return items
  return [...items].sort((a, b) => {
    const aOwn = isOwnQuestion(a) ? 0 : 1
    const bOwn = isOwnQuestion(b) ? 0 : 1
    if (aOwn !== bOwn) return aOwn - bOwn
    return Number(b.id) - Number(a.id)
  })
}

const loadQuestions = async () => {
  loading.value = true
  try {
    const [resQ, resA] = await Promise.all([
      window.axios.get('/api/questions'),
      window.axios.get('/api/article-quizzes')
    ])
    const items = (resQ.data.items || []).map(normalizeQuestion)
    questions.value = sortQuestionsForDisplay(items)
    articleQuizzes.value = resA.data || []
    
    // Extract unique batches
    const rawBatches = resQ.data.batches || items.map(q => q.batch).filter(Boolean)
    availableBatches.value = [...new Set(rawBatches)]
  } catch (e) {
    toast.error('Error', t('questionBank.toastLoadFailed'))
  } finally {
    loading.value = false
  }
}

const downloadTemplate = () => {
  window.open('/api/questions/template/download', '_blank')
}

const submitImport = async () => {
  const file = importFileInput.value?.files?.[0]
  if (!file) {
    toast.error('Error', 'Pilih file CSV / Excel terlebih dahulu.')
    return
  }

  const fd = new FormData()
  fd.append('file', file)
  fd.append('default_batch', importDefaultBatch.value || 'Tryout 1')

  importing.value = true
  try {
    const { data } = await window.axios.post('/api/questions/import', fd, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    toast.success('Sukses', data.message || 'Import berhasil!')
    showImportModal.value = false
    await loadQuestions()
  } catch (e) {
    const msg = e.response?.data?.message || 'Gagal mengimpor file.'
    toast.error('Error', msg)
  } finally {
    importing.value = false
  }
}

const openAdd = () => {
  editingItem.value = null
  showModal.value = true
}

const edit = (question) => {
  editingItem.value = { ...question }
  showModal.value = true
}

const remove = async (question) => {
  const confirmed = await confirm({
    title: t('questionBank.deleteConfirmTitle'),
    message: t('questionBank.deleteConfirmMessage'),
    confirmText: t('common.delete'),
    type: 'danger'
  })
  
  if (confirmed) {
    try {
      await window.axios.delete(`/api/questions/${question.id}`)
      questions.value = questions.value.filter(q => q.id !== question.id)
      await loadQuestions()
      toast.success('Success', t('questionBank.toastDeleted'))
    } catch (e) {
      toast.error('Error', t('questionBank.toastDeleteFailed'))
    }
  }
}

const removeAll = async () => {
  const confirmed = await confirm({
    title: t('questionBank.deleteAllConfirmTitle'),
    message: t('questionBank.deleteAllConfirmMessage'),
    confirmText: t('common.delete'),
    type: 'danger'
  })

  if (confirmed) {
    try {
      await window.axios.delete('/api/questions/all')
      await loadQuestions()
      toast.success('Success', t('questionBank.toastAllDeleted'))
    } catch (e) {
      toast.error('Error', t('questionBank.toastDeleteAllFailed'))
    }
  }
}

const closeModal = () => {
  showModal.value = false
  editingItem.value = null
}

const normalizeQuestion = (item) => ({
  ...item,
  batch: item?.batch || 'Tryout 1',
  image_url: item?.image_url || item?.image || null,
})

const toQuestionFormData = (payload) => {
  if (!(payload.image instanceof File)) return payload

  const fd = new FormData()
  Object.entries(payload).forEach(([key, value]) => {
    if (value == null || value === '') return
    if (key === 'options') {
      value.forEach((opt, idx) => {
        fd.append(`options[${idx}][key]`, opt.key)
        fd.append(`options[${idx}][label]`, opt.label)
      })
      return
    }
    fd.append(key, value)
  })
  return fd
}

const onSubmit = async (payload) => {
  try {
    const body = toQuestionFormData(payload)
    const multipart = body instanceof FormData
    const config = multipart ? { headers: { 'Content-Type': 'multipart/form-data' } } : undefined

    if (editingItem.value) {
      const { data } = await window.axios.put(`/api/questions/${editingItem.value.id}`, body, config)
      await loadQuestions()
      toast.success('Success', t('questionBank.toastUpdated'))
    } else {
      const { data } = await window.axios.post('/api/questions', body, config)
      await loadQuestions()
      toast.success('Success', t('questionBank.toastCreated'))
    }
    closeModal()
  } catch (e) {
    toast.error('Error', t('questionBank.toastSaveFailed'))
  }
}

// Article Quiz Handlers
const openAddArticle = () => {
  editingArticle.value = null
  articleForm.value = { title: '', content: '' }
  showArticleModal.value = true
}

const editArticle = (art) => {
  editingArticle.value = art
  articleForm.value = { title: art.title, content: art.content }
  showArticleModal.value = true
}

const closeArticleModal = () => {
  showArticleModal.value = false
  editingArticle.value = null
}

const submitArticle = async () => {
  try {
    if (editingArticle.value) {
      await window.axios.put(`/api/article-quizzes/${editingArticle.value.id}`, articleForm.value)
      toast.success('Success', 'Artikel berhasil diperbarui')
    } else {
      await window.axios.post('/api/article-quizzes', articleForm.value)
      toast.success('Success', 'Artikel berhasil dibuat')
    }
    closeArticleModal()
    await loadQuestions()
  } catch (e) {
    toast.error('Error', 'Gagal menyimpan artikel')
  }
}

const removeArticle = async (art) => {
  const confirmed = await confirm({
    title: 'Hapus Article Quiz',
    message: `Apakah Anda yakin ingin menghapus artikel "${art.title}"?`,
    confirmText: 'Hapus',
    type: 'danger'
  })

  if (confirmed) {
    try {
      await window.axios.delete(`/api/article-quizzes/${art.id}`)
      toast.success('Success', 'Artikel berhasil dihapus')
      await loadQuestions()
    } catch (e) {
      toast.error('Error', 'Gagal menghapus artikel')
    }
  }
}

const openAssign = (art) => {
  assigningArticle.value = art
  assignSelectedIds.value = (art.questions || []).map(q => q.id)
  showAssignModal.value = true
}

const submitAssign = async () => {
  try {
    await window.axios.post(`/api/article-quizzes/${assigningArticle.value.id}/assign-questions`, {
      question_ids: assignSelectedIds.value
    })
    toast.success('Success', 'Penugasan soal ke artikel berhasil disimpan')
    showAssignModal.value = false
    await loadQuestions()
  } catch (e) {
    toast.error('Error', 'Gagal menyimpan penugasan soal')
  }
}

onMounted(() => {
  loadQuestions()
})
</script>

<style scoped>
</style>
