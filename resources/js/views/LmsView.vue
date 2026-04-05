<template>
  <main class="max-w-7xl mx-auto px-4 md:px-12 py-10 md:py-14 space-y-8">
    <section class="rounded-[2rem] border border-gray-100 bg-white shadow-xl shadow-black/5 p-8 md:p-12">
      <span class="inline-flex items-center rounded-full bg-[#9DB359]/10 text-[#6f8440] px-4 py-1.5 text-xs font-semibold tracking-wide uppercase">
        Learning Management System
      </span>
      <h1 class="mt-5 text-3xl md:text-5xl font-bold tracking-tight text-[#1A1A1A]">
        LMS Operasional CAT-APPS
      </h1>
      <p class="mt-5 text-gray-600 md:text-lg leading-relaxed max-w-4xl">
        Halaman ini sudah terhubung ke backend LMS. Fitur Quiz pada LMS memakai engine Test CAT yang sudah ada.
      </p>
    </section>

    <section v-if="!isAuthenticated" class="rounded-[2rem] border border-amber-200 bg-amber-50 p-7">
      <h2 class="text-xl font-bold text-amber-800">Silakan login dulu</h2>
      <p class="mt-2 text-amber-700">Fitur LMS aktif untuk user yang sudah login sesuai role.</p>
      <router-link to="/login" class="inline-flex mt-4 rounded-full bg-[#1A1A1A] text-white px-5 py-2.5 text-sm font-medium hover:bg-black transition-colors">
        Login
      </router-link>
    </section>

    <template v-else>
      <section class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border border-gray-100 p-5">
          <div class="text-xs text-gray-500 uppercase">Role</div>
          <div class="mt-1 text-xl font-bold capitalize">{{ normalizedRole }}</div>
        </div>
        <div class="rounded-2xl bg-white border border-gray-100 p-5">
          <div class="text-xs text-gray-500 uppercase">Courses</div>
          <div class="mt-1 text-xl font-bold">{{ courses.length }}</div>
        </div>
        <div class="rounded-2xl bg-white border border-gray-100 p-5">
          <div class="text-xs text-gray-500 uppercase">Enrollments</div>
          <div class="mt-1 text-xl font-bold">{{ myEnrollments.length }}</div>
        </div>
        <div class="rounded-2xl bg-white border border-gray-100 p-5">
          <div class="text-xs text-gray-500 uppercase">Threads</div>
          <div class="mt-1 text-xl font-bold">{{ threads.length }}</div>
        </div>
      </section>

      <section class="rounded-[2rem] border border-gray-100 bg-white p-4 md:p-5">
        <div class="flex flex-wrap gap-2">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            type="button"
            @click="activeTab = tab.key"
            class="rounded-full px-4 py-2 text-sm font-semibold transition-colors"
            :class="activeTab === tab.key ? 'bg-[#1A1A1A] text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
          >
            {{ tab.label }}
          </button>
        </div>
      </section>

      <section v-if="activeTab === 'courses'" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 space-y-4">
          <div class="rounded-[2rem] border border-gray-100 bg-white p-6">
            <div class="flex items-center justify-between gap-3">
              <h3 class="text-xl font-bold">Daftar Course</h3>
              <button type="button" class="text-sm font-medium text-[#9DB359]" @click="fetchCourses">Refresh</button>
            </div>
            <div class="mt-4">
              <input v-model="courseSearch" @keydown.enter="fetchCourses" type="text" placeholder="Cari course..." class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#9DB359]/30" />
            </div>
            <div class="mt-4 space-y-3 max-h-[460px] overflow-y-auto pr-1">
              <button
                v-for="course in courses"
                :key="course.id"
                type="button"
                @click="selectCourse(course.id)"
                class="w-full text-left rounded-2xl border px-4 py-3 transition-colors"
                :class="selectedCourseId === course.id ? 'border-[#9DB359] bg-[#9DB359]/10' : 'border-gray-100 bg-white hover:border-gray-200'"
              >
                <div class="font-semibold text-[#1A1A1A]">{{ course.title }}</div>
                <div class="mt-1 text-xs text-gray-500">Modules {{ course.modules_count || 0 }} · Enroll {{ course.enrollments_count || 0 }}</div>
                <div class="mt-2 text-xs">
                  <span class="rounded-full px-2 py-0.5" :class="course.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'">
                    {{ course.is_published ? 'Published' : 'Draft' }}
                  </span>
                </div>
              </button>
            </div>
          </div>

          <div v-if="canManageLms" class="rounded-[2rem] border border-gray-100 bg-white p-6">
            <h3 class="text-lg font-bold">Buat Course</h3>
            <div class="mt-4 space-y-3">
              <input v-model="courseForm.title" type="text" placeholder="Judul course" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none" />
              <textarea v-model="courseForm.description" rows="3" placeholder="Deskripsi" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none"></textarea>
              <input v-model.number="courseForm.price" type="number" min="0" placeholder="Harga" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm focus:outline-none" />
              <label class="inline-flex items-center gap-2 text-sm">
                <input v-model="courseForm.is_published" type="checkbox" />
                Publish sekarang
              </label>
              <button type="button" @click="createCourse" class="w-full rounded-xl bg-[#1A1A1A] text-white px-4 py-2.5 text-sm font-semibold hover:bg-black transition-colors">
                Simpan Course
              </button>
            </div>
          </div>
        </div>

        <div class="xl:col-span-2 space-y-4">
          <div v-if="!selectedCourse" class="rounded-[2rem] border border-dashed border-gray-200 bg-white p-10 text-center text-gray-500">
            Pilih course untuk melihat detail LMS.
          </div>

          <template v-else>
            <div class="rounded-[2rem] border border-gray-100 bg-white p-6">
              <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                  <h3 class="text-2xl font-bold">{{ selectedCourse.title }}</h3>
                  <p class="mt-2 text-sm text-gray-600">{{ selectedCourse.description || 'Tanpa deskripsi' }}</p>
                </div>
                <div class="text-right">
                  <div class="text-xs text-gray-500 uppercase">Harga</div>
                  <div class="text-xl font-bold">Rp {{ Number(selectedCourse.price || 0).toLocaleString('id-ID') }}</div>
                </div>
              </div>
              <div class="mt-4 flex flex-wrap gap-2">
                <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="selectedCourse.is_published ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600'">
                  {{ selectedCourse.is_published ? 'Published' : 'Draft' }}
                </span>
                <button v-if="isStudent && !enrolledInSelectedCourse" type="button" @click="enrollCourse(selectedCourse.id)" class="rounded-full bg-[#9DB359] text-white px-3 py-1 text-xs font-semibold hover:bg-[#8ca34b]">
                  Enroll
                </button>
                <button v-if="isStudent && selectedCourse.price > 0" type="button" @click="checkoutCourse(selectedCourse.id)" class="rounded-full bg-blue-600 text-white px-3 py-1 text-xs font-semibold hover:bg-blue-700">
                  Checkout
                </button>
                <button v-if="isStudent && myEnrollmentCurrent" type="button" @click="markMyProgress(myEnrollmentCurrent.id)" class="rounded-full bg-indigo-600 text-white px-3 py-1 text-xs font-semibold hover:bg-indigo-700">
                  Update Progress
                </button>
              </div>
            </div>

            <div class="rounded-[2rem] border border-gray-100 bg-white p-6">
              <h4 class="text-lg font-bold">Module & Lesson</h4>
              <div class="mt-4 space-y-4">
                <div v-for="module in selectedCourse.modules || []" :key="module.id" class="rounded-2xl border border-gray-100 p-4">
                  <div class="font-semibold">{{ module.title }}</div>
                  <div class="mt-2 space-y-2">
                    <div v-for="lesson in module.lessons || []" :key="lesson.id" class="rounded-xl bg-gray-50 px-3 py-2 text-sm">
                      <div class="font-medium">{{ lesson.title }}</div>
                      <div class="mt-1 text-xs text-gray-500">
                        {{ lesson.lesson_type }}
                        <span v-if="lesson.lesson_type === 'quiz_test' && lesson.quiz_test"> · Test CAT: {{ lesson.quiz_test.name }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div v-if="canManageSelectedCourse" class="mt-6 grid md:grid-cols-2 gap-4">
                <div class="rounded-2xl border border-gray-100 p-4">
                  <h5 class="text-sm font-bold">Tambah Module</h5>
                  <input v-model="moduleForm.title" type="text" placeholder="Judul module" class="mt-3 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                  <button type="button" @click="createModule" class="mt-3 w-full rounded-xl bg-[#1A1A1A] text-white px-4 py-2 text-sm font-semibold">Tambah Module</button>
                </div>
                <div class="rounded-2xl border border-gray-100 p-4">
                  <h5 class="text-sm font-bold">Tambah Lesson</h5>
                  <select v-model="lessonForm.module_id" class="mt-3 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                    <option :value="null">Pilih module</option>
                    <option v-for="module in selectedCourse.modules || []" :key="module.id" :value="module.id">{{ module.title }}</option>
                  </select>
                  <input v-model="lessonForm.title" type="text" placeholder="Judul lesson" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                  <select v-model="lessonForm.lesson_type" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                    <option value="article">Article</option>
                    <option value="video">Video</option>
                    <option value="file">File</option>
                    <option value="live_class">Live Class</option>
                    <option value="quiz_test">Quiz/Test CAT</option>
                  </select>
                  <input v-if="lessonForm.lesson_type === 'video'" v-model="lessonForm.video_url" type="text" placeholder="Video URL" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                  <input v-if="lessonForm.lesson_type === 'live_class'" v-model="lessonForm.live_url" type="text" placeholder="Link Zoom/Meet" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                  <input v-if="lessonForm.lesson_type === 'file'" v-model="lessonForm.file_path" type="text" placeholder="Path file" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                  <textarea v-if="['article','quiz_test'].includes(lessonForm.lesson_type)" v-model="lessonForm.content" rows="2" placeholder="Konten" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"></textarea>
                  <select v-if="lessonForm.lesson_type === 'quiz_test'" v-model="lessonForm.test_definition_id" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
                    <option :value="null">Pilih Test CAT</option>
                    <option v-for="test in catTests" :key="test.id" :value="test.id">{{ test.name }}</option>
                  </select>
                  <label class="mt-2 inline-flex items-center gap-2 text-xs text-gray-600">
                    <input v-model="lessonForm.is_preview" type="checkbox" />
                    Preview tanpa enroll
                  </label>
                  <button type="button" @click="createLesson" class="mt-3 w-full rounded-xl bg-[#9DB359] text-white px-4 py-2 text-sm font-semibold">Tambah Lesson</button>
                </div>
              </div>
            </div>

            <div class="rounded-[2rem] border border-gray-100 bg-white p-6">
              <h4 class="text-lg font-bold">Assignment</h4>
              <div class="mt-4 space-y-3">
                <div v-for="assignment in selectedCourse.assignments || []" :key="assignment.id" class="rounded-2xl border border-gray-100 p-4">
                  <div class="flex items-start justify-between gap-3">
                    <div>
                      <div class="font-semibold">{{ assignment.title }}</div>
                      <div class="text-xs text-gray-500">{{ assignment.instructions || 'Tanpa instruksi' }}</div>
                    </div>
                    <div class="text-xs text-gray-500">Due {{ formatDate(assignment.due_at) }}</div>
                  </div>
                  <div class="mt-3 flex flex-wrap gap-2">
                    <button v-if="isStudent" type="button" @click="submitAssignment(assignment.id)" class="rounded-full bg-[#1A1A1A] text-white px-3 py-1 text-xs font-semibold">
                      Submit
                    </button>
                    <button v-if="canManageSelectedCourse" type="button" @click="openAssignmentSubmissions(assignment.id)" class="rounded-full bg-amber-100 text-amber-700 px-3 py-1 text-xs font-semibold">
                      Lihat Submission
                    </button>
                  </div>
                </div>
              </div>
              <div v-if="canManageSelectedCourse" class="mt-5 rounded-2xl border border-gray-100 p-4">
                <h5 class="text-sm font-bold">Tambah Assignment</h5>
                <input v-model="assignmentForm.title" type="text" placeholder="Judul assignment" class="mt-3 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                <textarea v-model="assignmentForm.instructions" rows="2" placeholder="Instruksi" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"></textarea>
                <input v-model="assignmentForm.due_at" type="datetime-local" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                <input v-model.number="assignmentForm.max_score" type="number" min="1" max="1000" placeholder="Max score" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                <button type="button" @click="createAssignment" class="mt-3 w-full rounded-xl bg-[#1A1A1A] text-white px-4 py-2 text-sm font-semibold">Tambah Assignment</button>
              </div>
            </div>

            <div v-if="canManageSelectedCourse && selectedCourseEnrollments.length" class="rounded-[2rem] border border-gray-100 bg-white p-6">
              <h4 class="text-lg font-bold">Progress Tracking & Sertifikat</h4>
              <div class="mt-4 space-y-3">
                <div v-for="enroll in selectedCourseEnrollments" :key="enroll.id" class="rounded-2xl border border-gray-100 p-4">
                  <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                      <div class="font-semibold">{{ enroll.user?.name }}</div>
                      <div class="text-xs text-gray-500">{{ enroll.user?.email }} · {{ enroll.status }}</div>
                    </div>
                    <div class="text-xs text-gray-500">Progress {{ enroll.progress }}%</div>
                  </div>
                  <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" @click="updateEnrollmentStatus(enroll, 'active', Math.min(99, enroll.progress || 0))" class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">Set Active</button>
                    <button type="button" @click="updateEnrollmentStatus(enroll, 'completed', 100)" class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Set Completed</button>
                    <button type="button" @click="issueCertificate(enroll.user_id)" class="rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-700">Issue Certificate</button>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </section>

      <section v-if="activeTab === 'forum'" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 rounded-[2rem] border border-gray-100 bg-white p-6">
          <h3 class="text-xl font-bold">Buat Thread</h3>
          <select v-model="threadForm.course_id" class="mt-4 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
            <option :value="null">Umum</option>
            <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
          </select>
          <input v-model="threadForm.title" type="text" placeholder="Judul thread" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          <textarea v-model="threadForm.content" rows="4" placeholder="Isi diskusi" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm"></textarea>
          <button type="button" @click="createThread" class="mt-3 w-full rounded-xl bg-[#1A1A1A] text-white px-4 py-2 text-sm font-semibold">Publish Thread</button>
        </div>
        <div class="xl:col-span-2 rounded-[2rem] border border-gray-100 bg-white p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold">Forum Diskusi</h3>
            <button type="button" class="text-sm font-medium text-[#9DB359]" @click="fetchThreads">Refresh</button>
          </div>
          <div class="mt-4 space-y-4 max-h-[560px] overflow-y-auto pr-1">
            <div v-for="thread in threads" :key="thread.id" class="rounded-2xl border border-gray-100 p-4">
              <div class="font-semibold">{{ thread.title }}</div>
              <div class="text-xs text-gray-500 mt-1">{{ thread.creator?.name }} · {{ formatDate(thread.created_at) }}</div>
              <p class="text-sm text-gray-700 mt-2">{{ thread.content }}</p>
              <div class="mt-3 space-y-2">
                <div v-for="comment in commentsByThread[thread.id] || []" :key="comment.id" class="rounded-xl bg-gray-50 px-3 py-2">
                  <div class="text-xs text-gray-500">{{ comment.creator?.name }}</div>
                  <div class="text-sm">{{ comment.content }}</div>
                </div>
              </div>
              <div class="mt-3 flex gap-2">
                <input v-model="commentDraft[thread.id]" type="text" placeholder="Tulis komentar..." class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm" />
                <button type="button" @click="sendComment(thread.id)" class="rounded-xl bg-[#9DB359] text-white px-3 py-2 text-xs font-semibold">Kirim</button>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeTab === 'calendar'" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 rounded-[2rem] border border-gray-100 bg-white p-6">
          <h3 class="text-xl font-bold">Jadwal & Reminder</h3>
          <select v-model="calendarForm.course_id" class="mt-4 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
            <option :value="null">Tanpa course</option>
            <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }}</option>
          </select>
          <input v-model="calendarForm.title" type="text" placeholder="Judul event" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          <select v-model="calendarForm.event_type" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
            <option value="class">Class</option>
            <option value="deadline">Deadline</option>
            <option value="webinar">Webinar</option>
            <option value="reminder">Reminder</option>
          </select>
          <input v-model="calendarForm.starts_at" type="datetime-local" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          <input v-model="calendarForm.ends_at" type="datetime-local" class="mt-2 w-full rounded-xl border border-gray-200 px-3 py-2 text-sm" />
          <button v-if="canManageLms" type="button" @click="createCalendarEvent" class="mt-3 w-full rounded-xl bg-[#1A1A1A] text-white px-4 py-2 text-sm font-semibold">Simpan Event</button>
        </div>
        <div class="xl:col-span-2 rounded-[2rem] border border-gray-100 bg-white p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold">Kalender</h3>
            <button type="button" class="text-sm font-medium text-[#9DB359]" @click="fetchCalendar">Refresh</button>
          </div>
          <div class="mt-4 space-y-3 max-h-[560px] overflow-y-auto pr-1">
            <div v-for="event in calendarEvents" :key="event.id" class="rounded-2xl border border-gray-100 p-4">
              <div class="font-semibold">{{ event.title }}</div>
              <div class="mt-1 text-xs text-gray-500 uppercase">{{ event.event_type }}</div>
              <div class="mt-1 text-sm text-gray-600">{{ formatDate(event.starts_at) }} <span v-if="event.ends_at">- {{ formatDate(event.ends_at) }}</span></div>
              <div class="text-sm text-gray-700 mt-1">{{ event.description }}</div>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeTab === 'chat'" class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 rounded-[2rem] border border-gray-100 bg-white p-6">
          <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold">Chat Peers</h3>
            <button type="button" class="text-sm font-medium text-[#9DB359]" @click="fetchPeers">Refresh</button>
          </div>
          <div class="mt-4 space-y-2 max-h-[520px] overflow-y-auto pr-1">
            <button
              v-for="peer in peers"
              :key="peer.id"
              type="button"
              @click="selectPeer(peer)"
              class="w-full text-left rounded-xl border px-3 py-2 text-sm"
              :class="selectedPeer?.id === peer.id ? 'border-[#9DB359] bg-[#9DB359]/10' : 'border-gray-100 hover:border-gray-200'"
            >
              <div class="font-semibold">{{ peer.name }}</div>
              <div class="text-xs text-gray-500">{{ peer.email }}</div>
            </button>
          </div>
        </div>
        <div class="xl:col-span-2 rounded-[2rem] border border-gray-100 bg-white p-6">
          <h3 class="text-xl font-bold">Messaging</h3>
          <div v-if="!selectedPeer" class="mt-4 text-sm text-gray-500">Pilih peer untuk mulai chat.</div>
          <template v-else>
            <div class="mt-4 rounded-2xl bg-gray-50 p-4 h-[420px] overflow-y-auto space-y-2">
              <div
                v-for="msg in messages"
                :key="msg.id"
                class="max-w-[80%] rounded-xl px-3 py-2 text-sm"
                :class="msg.sender_id === user?.id ? 'ml-auto bg-[#1A1A1A] text-white' : 'bg-white border border-gray-200'"
              >
                {{ msg.body }}
              </div>
            </div>
            <div class="mt-3 flex gap-2">
              <input v-model="messageDraft" type="text" placeholder="Tulis pesan..." class="flex-1 rounded-xl border border-gray-200 px-3 py-2 text-sm" />
              <button type="button" @click="sendMessage" class="rounded-xl bg-[#9DB359] text-white px-4 py-2 text-sm font-semibold">Kirim</button>
            </div>
          </template>
        </div>
      </section>

      <section v-if="activeTab === 'certificates'" class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="rounded-[2rem] border border-gray-100 bg-white p-6">
          <h3 class="text-xl font-bold">Sertifikat Saya</h3>
          <button type="button" class="mt-2 text-sm font-medium text-[#9DB359]" @click="fetchMyCertificates">Refresh</button>
          <div class="mt-4 space-y-3 max-h-[480px] overflow-y-auto pr-1">
            <div v-for="cert in myCertificates" :key="cert.id" class="rounded-2xl border border-gray-100 p-4">
              <div class="font-semibold">{{ cert.course?.title }}</div>
              <div class="text-xs text-gray-500">{{ cert.certificate_no }}</div>
              <div class="text-xs text-gray-500 mt-1">Issued {{ formatDate(cert.issued_at) }}</div>
            </div>
            <div v-if="myCertificates.length === 0" class="text-sm text-gray-500">Belum ada sertifikat.</div>
          </div>
        </div>

        <div class="rounded-[2rem] border border-gray-100 bg-white p-6">
          <h3 class="text-xl font-bold">Payment & Enrollment</h3>
          <div class="mt-4 space-y-3">
            <select v-model="paymentForm.course_id" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
              <option :value="null">Pilih course berbayar</option>
              <option v-for="course in courses" :key="course.id" :value="course.id">{{ course.title }} - Rp {{ Number(course.price || 0).toLocaleString('id-ID') }}</option>
            </select>
            <select v-model="paymentForm.provider" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-sm">
              <option value="midtrans">Midtrans</option>
              <option value="stripe">Stripe</option>
              <option value="manual">Manual</option>
            </select>
            <button type="button" @click="checkoutByForm" class="w-full rounded-xl bg-[#1A1A1A] text-white px-4 py-2 text-sm font-semibold">Buat Transaksi</button>
            <div v-if="latestPayment" class="rounded-xl bg-gray-50 p-3 text-xs">
              Ref: {{ latestPayment.reference }} · {{ latestPayment.status }}
              <button v-if="canManageLms" type="button" @click="markPaid(latestPayment.id)" class="ml-2 rounded-full bg-emerald-100 text-emerald-700 px-2 py-1 font-semibold">Mark Paid</button>
            </div>
          </div>
        </div>
      </section>

      <section v-if="activeTab === 'reports'" class="rounded-[2rem] border border-gray-100 bg-white p-6">
        <div class="flex items-center justify-between">
          <h3 class="text-xl font-bold">Admin Dashboard & Reporting</h3>
          <button v-if="canManageLms" type="button" class="text-sm font-medium text-[#9DB359]" @click="fetchReports">Refresh</button>
        </div>
        <div v-if="!canManageLms" class="mt-4 text-sm text-gray-500">Hanya admin/instructor yang dapat melihat analytics.</div>
        <div v-else class="mt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
          <div class="rounded-2xl border border-gray-100 p-4">
            <div class="text-xs text-gray-500 uppercase">Courses</div>
            <div class="mt-1 text-2xl font-bold">{{ reports.courses || 0 }}</div>
          </div>
          <div class="rounded-2xl border border-gray-100 p-4">
            <div class="text-xs text-gray-500 uppercase">Enrollments</div>
            <div class="mt-1 text-2xl font-bold">{{ reports.enrollments || 0 }}</div>
          </div>
          <div class="rounded-2xl border border-gray-100 p-4">
            <div class="text-xs text-gray-500 uppercase">Completion Rate</div>
            <div class="mt-1 text-2xl font-bold">{{ reports.completion_rate || 0 }}%</div>
          </div>
          <div class="rounded-2xl border border-gray-100 p-4">
            <div class="text-xs text-gray-500 uppercase">Revenue</div>
            <div class="mt-1 text-2xl font-bold">Rp {{ Number(reports.gross_revenue || 0).toLocaleString('id-ID') }}</div>
          </div>
        </div>
      </section>
    </template>
  </main>
</template>

<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { storeToRefs } from 'pinia'
import { useAppStore } from '@/stores/app'
import { useToast } from '@/composables/useNotification'

const store = useAppStore()
const { user, isAuthenticated, role } = storeToRefs(store)
const toast = useToast()

const tabs = [
  { key: 'courses', label: 'Courses' },
  { key: 'forum', label: 'Forum' },
  { key: 'calendar', label: 'Kalender' },
  { key: 'chat', label: 'Chat' },
  { key: 'certificates', label: 'Sertifikat & Payment' },
  { key: 'reports', label: 'Reporting' },
]

const activeTab = ref('courses')
const courses = ref([])
const courseSearch = ref('')
const selectedCourseId = ref(null)
const selectedCourse = ref(null)
const selectedCourseEnrollments = ref([])
const catTests = ref([])
const myEnrollments = ref([])
const threads = ref([])
const commentsByThread = ref({})
const commentDraft = ref({})
const calendarEvents = ref([])
const peers = ref([])
const selectedPeer = ref(null)
const messages = ref([])
const messageDraft = ref('')
const myCertificates = ref([])
const reports = ref({})
const latestPayment = ref(null)
const selectedAssignmentSubmissions = ref([])

const courseForm = reactive({
  title: '',
  description: '',
  price: 0,
  is_published: false,
})

const moduleForm = reactive({
  title: '',
})

const lessonForm = reactive({
  module_id: null,
  title: '',
  lesson_type: 'article',
  content: '',
  file_path: '',
  video_url: '',
  live_url: '',
  test_definition_id: null,
  is_preview: false,
})

const assignmentForm = reactive({
  title: '',
  instructions: '',
  due_at: '',
  max_score: 100,
})

const threadForm = reactive({
  course_id: null,
  title: '',
  content: '',
})

const calendarForm = reactive({
  course_id: null,
  title: '',
  description: '',
  event_type: 'class',
  starts_at: '',
  ends_at: '',
})

const paymentForm = reactive({
  course_id: null,
  provider: 'midtrans',
})

const normalizedRole = computed(() => {
  if (role.value === 'mentor') return 'instructor'
  if (role.value === 'user') return 'student'
  return role.value || 'guest'
})

const canManageLms = computed(() => ['admin', 'instructor'].includes(normalizedRole.value))
const isStudent = computed(() => normalizedRole.value === 'student')
const canManageSelectedCourse = computed(() => {
  if (!selectedCourse.value) return false
  if (normalizedRole.value === 'admin') return true
  return normalizedRole.value === 'instructor' && selectedCourse.value.created_by === user.value?.id
})

const myEnrollmentCurrent = computed(() => {
  if (!selectedCourse.value) return null
  return myEnrollments.value.find((item) => item.course_id === selectedCourse.value.id) || null
})

const enrolledInSelectedCourse = computed(() => !!myEnrollmentCurrent.value)

const resetCourseForm = () => {
  courseForm.title = ''
  courseForm.description = ''
  courseForm.price = 0
  courseForm.is_published = false
}

const apiErrorMessage = (error) => error?.response?.data?.message || 'Terjadi kesalahan'

const formatDate = (value) => {
  if (!value) return '-'
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return value
  return date.toLocaleString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  })
}

const fetchCourses = async () => {
  try {
    const { data } = await window.axios.get('/api/lms/courses', {
      params: {
        search: courseSearch.value || undefined,
      },
    })
    courses.value = data || []
    if (selectedCourseId.value && !courses.value.find((item) => item.id === selectedCourseId.value)) {
      selectedCourseId.value = null
      selectedCourse.value = null
    }
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const selectCourse = async (id) => {
  selectedCourseId.value = id
  try {
    const { data } = await window.axios.get(`/api/lms/courses/${id}`)
    selectedCourse.value = data.course
    if (canManageSelectedCourse.value) {
      await fetchCourseEnrollments(id)
    } else {
      selectedCourseEnrollments.value = []
    }
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const createCourse = async () => {
  if (!courseForm.title) {
    toast.warning('Validasi', 'Judul course wajib diisi')
    return
  }
  try {
    await window.axios.post('/api/lms/courses', courseForm)
    toast.success('Berhasil', 'Course dibuat')
    resetCourseForm()
    await fetchCourses()
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const createModule = async () => {
  if (!selectedCourse.value?.id || !moduleForm.title) {
    toast.warning('Validasi', 'Pilih course dan isi judul module')
    return
  }
  try {
    await window.axios.post(`/api/lms/courses/${selectedCourse.value.id}/modules`, moduleForm)
    moduleForm.title = ''
    await selectCourse(selectedCourse.value.id)
    toast.success('Berhasil', 'Module ditambahkan')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const createLesson = async () => {
  if (!lessonForm.module_id || !lessonForm.title) {
    toast.warning('Validasi', 'Pilih module dan isi judul lesson')
    return
  }
  if (lessonForm.lesson_type === 'quiz_test' && !lessonForm.test_definition_id) {
    toast.warning('Validasi', 'Pilih Test CAT untuk lesson quiz')
    return
  }
  const payload = { ...lessonForm }
  try {
    await window.axios.post(`/api/lms/modules/${lessonForm.module_id}/lessons`, payload)
    lessonForm.title = ''
    lessonForm.content = ''
    lessonForm.file_path = ''
    lessonForm.video_url = ''
    lessonForm.live_url = ''
    lessonForm.test_definition_id = null
    await selectCourse(selectedCourse.value.id)
    toast.success('Berhasil', 'Lesson ditambahkan')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const fetchTests = async () => {
  if (!canManageLms.value) return
  try {
    const { data } = await window.axios.get('/api/tests')
    catTests.value = data || []
  } catch (error) {
    catTests.value = []
  }
}

const enrollCourse = async (courseId) => {
  try {
    await window.axios.post(`/api/lms/courses/${courseId}/enroll`)
    toast.success('Berhasil', 'Enroll sukses')
    await fetchMyEnrollments()
    await selectCourse(courseId)
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const fetchMyEnrollments = async () => {
  try {
    const { data } = await window.axios.get('/api/lms/me/enrollments')
    myEnrollments.value = data || []
  } catch (error) {
    myEnrollments.value = []
  }
}

const fetchCourseEnrollments = async (courseId) => {
  try {
    const { data } = await window.axios.get(`/api/lms/courses/${courseId}/enrollments`)
    selectedCourseEnrollments.value = data || []
  } catch (error) {
    selectedCourseEnrollments.value = []
  }
}

const updateEnrollmentStatus = async (enrollment, status, progress) => {
  try {
    await window.axios.put(`/api/lms/enrollments/${enrollment.id}`, {
      status,
      progress,
    })
    toast.success('Berhasil', 'Progress diperbarui')
    await fetchCourseEnrollments(selectedCourse.value.id)
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const markMyProgress = async (enrollmentId) => {
  const current = myEnrollmentCurrent.value?.progress || 0
  const nextProgress = Math.min(100, current + 25)
  try {
    await window.axios.put(`/api/lms/enrollments/${enrollmentId}`, {
      progress: nextProgress,
      status: nextProgress >= 100 ? 'completed' : 'active',
    })
    toast.success('Berhasil', `Progress jadi ${nextProgress}%`)
    await fetchMyEnrollments()
    if (selectedCourse.value) {
      await selectCourse(selectedCourse.value.id)
    }
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const createAssignment = async () => {
  if (!selectedCourse.value?.id || !assignmentForm.title) {
    toast.warning('Validasi', 'Pilih course dan isi judul assignment')
    return
  }
  try {
    await window.axios.post(`/api/lms/courses/${selectedCourse.value.id}/assignments`, assignmentForm)
    assignmentForm.title = ''
    assignmentForm.instructions = ''
    assignmentForm.due_at = ''
    assignmentForm.max_score = 100
    await selectCourse(selectedCourse.value.id)
    toast.success('Berhasil', 'Assignment dibuat')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const submitAssignment = async (assignmentId) => {
  const answer = window.prompt('Isi jawaban assignment')
  if (answer === null) return
  try {
    await window.axios.post(`/api/lms/assignments/${assignmentId}/submit`, {
      answer_text: answer,
    })
    toast.success('Berhasil', 'Assignment berhasil disubmit')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const openAssignmentSubmissions = async (assignmentId) => {
  try {
    const { data } = await window.axios.get(`/api/lms/assignments/${assignmentId}/submissions`)
    selectedAssignmentSubmissions.value = data || []
    if (selectedAssignmentSubmissions.value.length === 0) {
      toast.info('Info', 'Belum ada submission')
      return
    }
    const first = selectedAssignmentSubmissions.value[0]
    const scoreInput = window.prompt(`Nilai untuk ${first.user?.name} (0-100)`, `${first.score ?? 0}`)
    if (scoreInput === null) return
    const parsed = Number(scoreInput)
    if (Number.isNaN(parsed)) {
      toast.warning('Validasi', 'Nilai harus angka')
      return
    }
    await window.axios.put(`/api/lms/assignment-submissions/${first.id}/grade`, {
      score: parsed,
      feedback: 'Dinilai dari panel LMS',
    })
    toast.success('Berhasil', 'Submission pertama berhasil dinilai')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const fetchThreads = async () => {
  try {
    const { data } = await window.axios.get('/api/lms/forum/threads')
    threads.value = data || []
    await Promise.all((threads.value || []).map((thread) => fetchComments(thread.id)))
  } catch (error) {
    threads.value = []
  }
}

const createThread = async () => {
  if (!threadForm.title) {
    toast.warning('Validasi', 'Judul thread wajib diisi')
    return
  }
  try {
    await window.axios.post('/api/lms/forum/threads', threadForm)
    threadForm.title = ''
    threadForm.content = ''
    await fetchThreads()
    toast.success('Berhasil', 'Thread dibuat')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const fetchComments = async (threadId) => {
  try {
    const { data } = await window.axios.get(`/api/lms/forum/threads/${threadId}/comments`)
    commentsByThread.value = {
      ...commentsByThread.value,
      [threadId]: data || [],
    }
  } catch (error) {
    commentsByThread.value = {
      ...commentsByThread.value,
      [threadId]: [],
    }
  }
}

const sendComment = async (threadId) => {
  const content = (commentDraft.value[threadId] || '').trim()
  if (!content) return
  try {
    await window.axios.post(`/api/lms/forum/threads/${threadId}/comments`, { content })
    commentDraft.value = {
      ...commentDraft.value,
      [threadId]: '',
    }
    await fetchComments(threadId)
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const fetchCalendar = async () => {
  try {
    const { data } = await window.axios.get('/api/lms/calendar/events')
    calendarEvents.value = data || []
  } catch (error) {
    calendarEvents.value = []
  }
}

const createCalendarEvent = async () => {
  if (!calendarForm.title || !calendarForm.starts_at) {
    toast.warning('Validasi', 'Judul dan mulai event wajib diisi')
    return
  }
  try {
    await window.axios.post('/api/lms/calendar/events', calendarForm)
    calendarForm.title = ''
    calendarForm.description = ''
    calendarForm.starts_at = ''
    calendarForm.ends_at = ''
    await fetchCalendar()
    toast.success('Berhasil', 'Event kalender dibuat')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const fetchPeers = async () => {
  try {
    const { data } = await window.axios.get('/api/lms/chat/peers')
    peers.value = data || []
  } catch (error) {
    peers.value = []
  }
}

const selectPeer = async (peer) => {
  selectedPeer.value = peer
  await fetchMessages(peer.id)
}

const fetchMessages = async (peerId) => {
  try {
    const { data } = await window.axios.get(`/api/lms/chat/${peerId}`)
    messages.value = data || []
  } catch (error) {
    messages.value = []
  }
}

const sendMessage = async () => {
  if (!selectedPeer.value || !messageDraft.value.trim()) return
  try {
    await window.axios.post(`/api/lms/chat/${selectedPeer.value.id}`, {
      body: messageDraft.value.trim(),
    })
    messageDraft.value = ''
    await fetchMessages(selectedPeer.value.id)
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const fetchMyCertificates = async () => {
  try {
    const { data } = await window.axios.get('/api/lms/me/certificates')
    myCertificates.value = data || []
  } catch (error) {
    myCertificates.value = []
  }
}

const issueCertificate = async (userId) => {
  if (!selectedCourse.value?.id) return
  try {
    await window.axios.post(`/api/lms/courses/${selectedCourse.value.id}/certificates/issue`, {
      user_id: userId,
    })
    toast.success('Berhasil', 'Sertifikat diterbitkan')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const checkoutCourse = async (courseId) => {
  try {
    const { data } = await window.axios.post(`/api/lms/courses/${courseId}/payments/checkout`, {
      provider: paymentForm.provider || 'midtrans',
    })
    latestPayment.value = data
    toast.success('Berhasil', 'Transaksi dibuat')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const checkoutByForm = async () => {
  if (!paymentForm.course_id) {
    toast.warning('Validasi', 'Pilih course dulu')
    return
  }
  await checkoutCourse(paymentForm.course_id)
}

const markPaid = async (paymentId) => {
  try {
    const { data } = await window.axios.put(`/api/lms/payments/${paymentId}/mark-paid`)
    latestPayment.value = data
    toast.success('Berhasil', 'Pembayaran ditandai paid')
  } catch (error) {
    toast.error('Gagal', apiErrorMessage(error))
  }
}

const fetchReports = async () => {
  if (!canManageLms.value) {
    reports.value = {}
    return
  }
  try {
    const { data } = await window.axios.get('/api/lms/reports/overview')
    reports.value = data || {}
  } catch (error) {
    reports.value = {}
  }
}

const initialize = async () => {
  if (!isAuthenticated.value) return
  await Promise.all([
    fetchCourses(),
    fetchMyEnrollments(),
    fetchThreads(),
    fetchCalendar(),
    fetchMyCertificates(),
    fetchPeers(),
    fetchTests(),
    fetchReports(),
  ])
}

onMounted(async () => {
  await initialize()
})
</script>
