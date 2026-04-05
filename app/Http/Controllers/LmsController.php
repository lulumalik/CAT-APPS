<?php

namespace App\Http\Controllers;

use App\Models\AssignmentSubmission;
use App\Models\CalendarEvent;
use App\Models\ChatMessage;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseCertificate;
use App\Models\CourseEnrollment;
use App\Models\CourseLesson;
use App\Models\CourseModule;
use App\Models\DiscussionComment;
use App\Models\DiscussionThread;
use App\Models\PaymentTransaction;
use App\Models\TestDefinition;
use App\Models\TestSubmission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LmsController extends Controller
{
    private function normalizedRole(Request $request): string
    {
        $role = (string) optional($request->user())->role;
        if ($role === 'mentor') {
            return 'instructor';
        }
        if ($role === 'user') {
            return 'student';
        }
        return $role;
    }

    private function ensureRole(Request $request, array $roles): void
    {
        if (!in_array($this->normalizedRole($request), $roles, true)) {
            abort(403);
        }
    }

    private function canManageCourse(Request $request, Course $course): bool
    {
        $role = $this->normalizedRole($request);
        if ($role === 'admin') {
            return true;
        }
        return $role === 'instructor' && $course->created_by === optional($request->user())->id;
    }

    public function courses(Request $request)
    {
        $role = $this->normalizedRole($request);
        $query = Course::query()->withCount(['modules', 'enrollments', 'assignments']);

        if ($search = trim((string) $request->input('search'))) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($role === 'student') {
            $query->where('is_published', true);
        } elseif ($role === 'instructor') {
            $query->where('created_by', optional($request->user())->id);
        }

        return response()->json($query->latest()->get());
    }

    public function storeCourse(Request $request)
    {
        $this->ensureRole($request, ['admin', 'instructor']);

        $data = $request->validate([
            'title' => 'required|string|max:180',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'is_published' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        $baseSlug = Str::slug($data['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $data['slug'] = $slug;
        $data['created_by'] = optional($request->user())->id;
        $course = Course::create($data);

        return response()->json($course, 201);
    }

    public function showCourse(Request $request, Course $course)
    {
        $role = $this->normalizedRole($request);
        if ($role === 'student' && !$course->is_published) {
            abort(403);
        }
        if ($role === 'instructor' && !$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $course->load([
            'modules.lessons.quizTest:id,name,category,start_time,end_time',
            'assignments',
        ]);

        $enrollment = null;
        if ($request->user()) {
            $enrollment = CourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $request->user()->id)
                ->first();
        }

        return response()->json([
            'course' => $course,
            'enrollment' => $enrollment,
        ]);
    }

    public function updateCourse(Request $request, Course $course)
    {
        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:180',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string|max:500',
            'price' => 'nullable|numeric|min:0',
            'is_published' => 'nullable|boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);

        if ($data['title'] !== $course->title) {
            $baseSlug = Str::slug($data['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Course::where('slug', $slug)->where('id', '<>', $course->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $data['slug'] = $slug;
        }

        $course->update($data);

        return response()->json($course);
    }

    public function destroyCourse(Request $request, Course $course)
    {
        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $course->delete();
        return response()->noContent();
    }

    public function addModule(Request $request, Course $course)
    {
        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:180',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? (($course->modules()->max('sort_order') ?? 0) + 1);
        $data['course_id'] = $course->id;

        $module = CourseModule::create($data);
        return response()->json($module, 201);
    }

    public function addLesson(Request $request, CourseModule $module)
    {
        $course = Course::findOrFail($module->course_id);
        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:180',
            'lesson_type' => 'required|in:video,article,file,live_class,quiz_test',
            'content' => 'nullable|string',
            'file_path' => 'nullable|string|max:500',
            'video_url' => 'nullable|string|max:500',
            'live_url' => 'nullable|string|max:500',
            'test_definition_id' => 'nullable|exists:test_definitions,id',
            'sort_order' => 'nullable|integer|min:0',
            'is_preview' => 'nullable|boolean',
        ]);

        if ($data['lesson_type'] === 'quiz_test' && empty($data['test_definition_id'])) {
            return response()->json([
                'message' => 'lesson_type quiz_test wajib memiliki test_definition_id',
                'errors' => ['test_definition_id' => ['test_definition_id wajib untuk quiz_test']],
            ], 422);
        }

        $data['module_id'] = $module->id;
        $data['sort_order'] = $data['sort_order'] ?? (($module->lessons()->max('sort_order') ?? 0) + 1);

        $lesson = CourseLesson::create($data);
        return response()->json($lesson->load('quizTest:id,name,category,start_time,end_time'), 201);
    }

    public function enroll(Request $request, Course $course)
    {
        $this->ensureRole($request, ['student', 'instructor', 'admin']);

        if (!$course->is_published && $this->normalizedRole($request) === 'student') {
            abort(403);
        }

        if ((float) $course->price > 0) {
            $paid = PaymentTransaction::where('course_id', $course->id)
                ->where('user_id', $request->user()->id)
                ->where('status', 'paid')
                ->exists();
            if (!$paid) {
                return response()->json([
                    'message' => 'Course berbayar membutuhkan pembayaran berhasil sebelum enrollment',
                ], 422);
            }
        }

        $enrollment = CourseEnrollment::firstOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $request->user()->id,
            ],
            [
                'status' => 'active',
                'progress' => 0,
                'enrolled_at' => now(),
            ]
        );

        return response()->json($enrollment, 201);
    }

    public function myEnrollments(Request $request)
    {
        $items = CourseEnrollment::with('course')
            ->where('user_id', $request->user()->id)
            ->latest('enrolled_at')
            ->get();
        return response()->json($items);
    }

    public function courseEnrollments(Request $request, Course $course)
    {
        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $items = CourseEnrollment::with('user:id,name,email,role')
            ->where('course_id', $course->id)
            ->latest('enrolled_at')
            ->get();

        return response()->json($items);
    }

    public function updateEnrollment(Request $request, CourseEnrollment $enrollment)
    {
        $course = Course::findOrFail($enrollment->course_id);
        $role = $this->normalizedRole($request);

        if ($role === 'student' && $enrollment->user_id !== $request->user()->id) {
            abort(403);
        }

        if (in_array($role, ['admin', 'instructor'], true) && !$this->canManageCourse($request, $course)) {
            abort(403);
        }

        if (!in_array($role, ['admin', 'instructor', 'student'], true)) {
            abort(403);
        }

        $data = $request->validate([
            'progress' => 'nullable|integer|min:0|max:100',
            'status' => 'nullable|in:active,completed,cancelled',
            'final_score' => 'nullable|numeric|min:0|max:100',
        ]);

        $updates = [];
        if (array_key_exists('progress', $data)) {
            $updates['progress'] = $data['progress'];
        }
        if (array_key_exists('status', $data)) {
            $updates['status'] = $data['status'];
        }
        if (array_key_exists('final_score', $data)) {
            $updates['final_score'] = $data['final_score'];
        }

        if (
            (array_key_exists('progress', $updates) && (int) $updates['progress'] >= 100) ||
            ((string) ($updates['status'] ?? '') === 'completed')
        ) {
            $updates['status'] = 'completed';
            $updates['completed_at'] = now();
        }

        $enrollment->update($updates);
        return response()->json($enrollment);
    }

    public function createAssignment(Request $request, Course $course)
    {
        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:180',
            'instructions' => 'nullable|string',
            'due_at' => 'nullable|date',
            'max_score' => 'nullable|integer|min:1|max:1000',
        ]);
        $data['course_id'] = $course->id;
        $data['created_by'] = $request->user()->id;
        $assignment = CourseAssignment::create($data);

        return response()->json($assignment, 201);
    }

    public function assignmentSubmissions(Request $request, CourseAssignment $assignment)
    {
        $course = Course::findOrFail($assignment->course_id);
        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $items = AssignmentSubmission::with('user:id,name,email,role')
            ->where('assignment_id', $assignment->id)
            ->latest('submitted_at')
            ->get();

        return response()->json($items);
    }

    public function submitAssignment(Request $request, CourseAssignment $assignment)
    {
        $data = $request->validate([
            'answer_text' => 'nullable|string',
            'attachment_path' => 'nullable|string|max:500',
        ]);

        $isEnrolled = CourseEnrollment::where('course_id', $assignment->course_id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['message' => 'Anda belum terdaftar di course ini'], 422);
        }

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'user_id' => $request->user()->id,
            ],
            [
                'answer_text' => $data['answer_text'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'submitted_at' => now(),
            ]
        );

        return response()->json($submission, 201);
    }

    public function gradeSubmission(Request $request, AssignmentSubmission $submission)
    {
        $assignment = CourseAssignment::findOrFail($submission->assignment_id);
        $course = Course::findOrFail($assignment->course_id);

        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $data = $request->validate([
            'score' => 'required|integer|min:0',
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $data['score'],
            'feedback' => $data['feedback'] ?? null,
            'graded_by' => $request->user()->id,
            'graded_at' => now(),
        ]);

        return response()->json($submission);
    }

    public function listThreads(Request $request)
    {
        $query = DiscussionThread::query()->with('creator:id,name,email');
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }
        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->integer('lesson_id'));
        }
        $threads = $query->latest()->get();
        return response()->json($threads);
    }

    public function createThread(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'lesson_id' => 'nullable|exists:course_lessons,id',
            'title' => 'required|string|max:180',
            'content' => 'nullable|string',
        ]);

        if (!empty($data['course_id'])) {
            $enrolled = CourseEnrollment::where('course_id', $data['course_id'])
                ->where('user_id', $request->user()->id)
                ->exists();
            if (!$enrolled && !in_array($this->normalizedRole($request), ['admin', 'instructor'], true)) {
                abort(403);
            }
        }

        $data['created_by'] = $request->user()->id;
        $thread = DiscussionThread::create($data);
        return response()->json($thread->load('creator:id,name,email'), 201);
    }

    public function addComment(Request $request, DiscussionThread $thread)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:discussion_comments,id',
        ]);

        $data['thread_id'] = $thread->id;
        $data['created_by'] = $request->user()->id;
        $comment = DiscussionComment::create($data);

        return response()->json($comment->load('creator:id,name,email'), 201);
    }

    public function listComments(Request $request, DiscussionThread $thread)
    {
        $comments = DiscussionComment::with('creator:id,name,email')
            ->where('thread_id', $thread->id)
            ->orderBy('created_at')
            ->get();
        return response()->json($comments);
    }

    public function sendMessage(Request $request, User $receiver)
    {
        $data = $request->validate([
            'body' => 'required|string|max:2000',
        ]);

        $msg = ChatMessage::create([
            'sender_id' => $request->user()->id,
            'receiver_id' => $receiver->id,
            'body' => $data['body'],
        ]);

        return response()->json($msg, 201);
    }

    public function listChatPeers(Request $request)
    {
        $role = $this->normalizedRole($request);
        $me = $request->user()->id;

        if (in_array($role, ['admin', 'instructor'], true)) {
            $users = User::select('id', 'name', 'email', 'role')
                ->where('id', '<>', $me)
                ->orderBy('name')
                ->limit(100)
                ->get();
            return response()->json($users);
        }

        $peerIds = ChatMessage::where('sender_id', $me)
            ->orWhere('receiver_id', $me)
            ->get()
            ->flatMap(function ($m) use ($me) {
                return [(int) ($m->sender_id === $me ? $m->receiver_id : $m->sender_id)];
            })
            ->unique()
            ->values();

        $users = User::select('id', 'name', 'email', 'role')
            ->whereIn('id', $peerIds)
            ->orderBy('name')
            ->get();

        return response()->json($users);
    }

    public function listMessages(Request $request, User $peer)
    {
        $me = $request->user()->id;
        $messages = ChatMessage::where(function ($q) use ($me, $peer) {
            $q->where('sender_id', $me)->where('receiver_id', $peer->id);
        })->orWhere(function ($q) use ($me, $peer) {
            $q->where('sender_id', $peer->id)->where('receiver_id', $me);
        })->orderBy('created_at')->get();

        ChatMessage::where('sender_id', $peer->id)
            ->where('receiver_id', $me)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    public function listCalendar(Request $request)
    {
        $query = CalendarEvent::query();
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->integer('course_id'));
        }
        if ($request->filled('from')) {
            $query->where('starts_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->where('starts_at', '<=', $request->date('to'));
        }
        return response()->json($query->orderBy('starts_at')->get());
    }

    public function createCalendar(Request $request)
    {
        $this->ensureRole($request, ['admin', 'instructor']);
        $data = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'title' => 'required|string|max:180',
            'description' => 'nullable|string',
            'event_type' => 'required|in:class,deadline,webinar,reminder',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ]);
        $data['created_by'] = $request->user()->id;
        $event = CalendarEvent::create($data);
        return response()->json($event, 201);
    }

    public function issueCertificate(Request $request, Course $course)
    {
        if (!$this->canManageCourse($request, $course)) {
            abort(403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'certificate_url' => 'nullable|string|max:500',
        ]);

        $enrollment = CourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $data['user_id'])
            ->first();

        if (!$enrollment || ($enrollment->status !== 'completed' && (int) $enrollment->progress < 100)) {
            return response()->json([
                'message' => 'Sertifikat hanya bisa diterbitkan untuk enrollment yang selesai',
            ], 422);
        }

        $certificate = CourseCertificate::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $data['user_id']],
            [
                'certificate_no' => 'CERT-' . $course->id . '-' . $data['user_id'] . '-' . now()->format('YmdHis'),
                'certificate_url' => $data['certificate_url'] ?? null,
                'issued_at' => now(),
            ]
        );

        return response()->json($certificate, 201);
    }

    public function myCertificates(Request $request)
    {
        $certificates = CourseCertificate::with('course:id,title,slug')
            ->where('user_id', $request->user()->id)
            ->latest('issued_at')
            ->get();
        return response()->json($certificates);
    }

    public function checkout(Request $request, Course $course)
    {
        $data = $request->validate([
            'provider' => 'required|string|max:50',
        ]);

        $payment = PaymentTransaction::create([
            'course_id' => $course->id,
            'user_id' => $request->user()->id,
            'provider' => $data['provider'],
            'reference' => 'PAY-' . strtoupper(Str::random(12)),
            'status' => 'pending',
            'amount' => $course->price,
            'meta' => ['course_slug' => $course->slug],
        ]);

        return response()->json($payment, 201);
    }

    public function markPaymentPaid(Request $request, PaymentTransaction $payment)
    {
        $this->ensureRole($request, ['admin', 'instructor']);

        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        CourseEnrollment::firstOrCreate(
            [
                'course_id' => $payment->course_id,
                'user_id' => $payment->user_id,
            ],
            [
                'status' => 'active',
                'progress' => 0,
                'enrolled_at' => now(),
            ]
        );

        return response()->json($payment);
    }

    public function reportsOverview(Request $request)
    {
        $this->ensureRole($request, ['admin', 'instructor']);

        $coursesCount = Course::count();
        $enrollmentsCount = CourseEnrollment::count();
        $activeStudents = CourseEnrollment::distinct('user_id')->count('user_id');
        $completedCount = CourseEnrollment::where('status', 'completed')
            ->orWhere('progress', '>=', 100)
            ->count();
        $completionRate = $enrollmentsCount > 0 ? round(($completedCount / $enrollmentsCount) * 100, 2) : 0;

        $assignmentSubmissions = AssignmentSubmission::count();
        $paidTransactions = PaymentTransaction::where('status', 'paid')->count();
        $grossRevenue = (float) PaymentTransaction::where('status', 'paid')->sum('amount');

        $catTests = TestDefinition::count();
        $catSubmissions = TestSubmission::count();

        return response()->json([
            'courses' => $coursesCount,
            'enrollments' => $enrollmentsCount,
            'active_students' => $activeStudents,
            'completion_rate' => $completionRate,
            'assignment_submissions' => $assignmentSubmissions,
            'paid_transactions' => $paidTransactions,
            'gross_revenue' => $grossRevenue,
            'cat_tests' => $catTests,
            'cat_submissions' => $catSubmissions,
            'quiz_mapping' => 'Quiz pada LMS dipetakan ke TestDefinition/TestSubmission CAT',
        ]);
    }
}
