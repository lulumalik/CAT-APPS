<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TestDefinitionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\RegistrationProgressController;
use App\Http\Controllers\BimbleClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClassActivityController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\RegistrationFormPdfController;
use App\Http\Controllers\StudentDashboardPdfController;
use App\Http\Controllers\StudentReportController;
use App\Http\Controllers\ExamDefinitionController;
use App\Http\Controllers\BatchController;

Route::get('/csrf-token', fn () => response()->json(['token' => csrf_token()]));

// Public material routes
Route::get('/materials/public', [MaterialController::class, 'publicIndex']);
Route::get('/materials/public/{slug}', [MaterialController::class, 'publicShow']);

Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
    if ($request->user() === null) {
        return response()->json([
            'success' => false,
            'message' => 'Anda harus login terlebih dahulu.',
        ], 401);
    }

    if ($request->user()->hasVerifiedEmail()) {
        return response()->json([
            'success' => true,
            'message' => 'Email sudah terverifikasi.',
        ]);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json([
        'success' => true,
        'message' => 'Email verifikasi telah dikirim ulang.',
    ]);
})->middleware(['auth', 'throttle:6,1']);

Route::get('/free-tryout/tests', [TestDefinitionController::class, 'freeTryoutList']);
Route::get('/free-tryout/tests/{test}', [TestDefinitionController::class, 'freeTryoutShow']);
Route::post('/free-tryout/tests/{test}/submit', [TestDefinitionController::class, 'freeTryoutSubmit'])
    ->middleware('throttle:10,1');

// Public guardian invitation (accept link sent by the team via WhatsApp)
Route::get('/guardian-invite/{token}', [GuardianController::class, 'showInvite']);
Route::post('/guardian-invite/{token}/accept', [GuardianController::class, 'accept'])
    ->middleware('throttle:10,1');

// Test operations (requires authentication via session)
Route::middleware(['auth', 'app.not_expired'])->group(function () {
    Route::get('/incoming-tests', [TestDefinitionController::class, 'incoming']);
    Route::get('/available-tests', [TestDefinitionController::class, 'available']);

    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
    Route::get('/my-activity-history', [DashboardController::class, 'myActivityHistory']);

    Route::get('/bimble-classes/mine', [BimbleClassController::class, 'mine']);
    Route::get('/bimble-classes/{bimbleClass}/workspace', [BimbleClassController::class, 'workspace']);
    Route::get('/bimble-classes/{bimbleClass}/activities', [ClassActivityController::class, 'index']);

    Route::get('/my-registration', [RegistrationProgressController::class, 'mine']);
    // POST + PUT: multipart/form-data + files are not reliably parsed on PUT in PHP;
    // use POST from clients (Postman, axios FormData) when uploading files.
    Route::post('/my-registration/administration-file', [RegistrationProgressController::class, 'uploadAdministrationFile']);
    Route::get('/registration-files/{user}/{field}', [RegistrationProgressController::class, 'streamAdministrationFile']);
    Route::match(['post', 'put'], '/my-registration', [RegistrationProgressController::class, 'updateMine']);
    Route::get('/my-registration/forms', [RegistrationFormPdfController::class, 'catalog']);
    Route::get('/my-registration/berkas-pdf', [RegistrationFormPdfController::class, 'downloadTemplate']);
    Route::get('/my-registration/forms/pdf', [RegistrationFormPdfController::class, 'downloadAll']);
    Route::get('/my-registration/forms/{slug}/pdf', [RegistrationFormPdfController::class, 'downloadPage']);

    Route::get('/announcements', [AnnouncementController::class, 'index']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::patch('/notifications/read-all', [NotificationController::class, 'markAllRead']);

    Route::get('/tests/{test}', [TestDefinitionController::class, 'show']);
    Route::post('/tests/{test}/submit', [TestDefinitionController::class, 'submit']);
    Route::get('/available-exams', [ExamDefinitionController::class, 'available']);
    Route::get('/exams/{exam}', [ExamDefinitionController::class, 'show']);
    Route::post('/exams/{exam}/submit', [ExamDefinitionController::class, 'submit']);
    Route::get('/my-tests', [TestDefinitionController::class, 'myTests']);
    Route::get('/certificates/{certificateIssue}/download', [CertificateController::class, 'download']);

    // Private progress / results / reports — visible only to the student, their
    // linked parent, or staff (authorization handled inside the controller).
    Route::get('/parent/children', [ProgressController::class, 'children']);
    Route::get('/students/{student}/progress', [ProgressController::class, 'studentProgress']);
    Route::get('/students/{student}/results', [ProgressController::class, 'studentResults']);
    Route::get('/students/{student}/reports', [ProgressController::class, 'studentReports']);
    Route::get('/students/{student}/pdf', [StudentDashboardPdfController::class, 'downloadForStudent']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard/students/{student}/overview', [DashboardController::class, 'studentOverviewForStaff']);
    Route::post('/users/import', [UserController::class, 'import']);
    Route::apiResource('users', UserController::class);

    Route::get('/admin/registration-progress', [RegistrationProgressController::class, 'adminIndex']);
    Route::get('/admin/registration-progress/{user}', [RegistrationProgressController::class, 'adminShow']);
    Route::get('/admin/storage-diagnostic', [RegistrationProgressController::class, 'adminStorageDiagnostic']);
    Route::patch('/admin/registration-progress/{user}', [RegistrationProgressController::class, 'adminUpdate']);
    Route::patch('/admin/registration-progress/{user}/payment', [RegistrationProgressController::class, 'adminConfirmPayment']);
    Route::get('/admin/registration-progress/{user}/berkas-pdf', [RegistrationFormPdfController::class, 'adminDownloadTemplate']);
    Route::get('/admin/registration-progress/{user}/forms/pdf', [RegistrationFormPdfController::class, 'adminDownloadAll']);
    Route::get('/admin/registration-progress/{user}/forms/{slug}/pdf', [RegistrationFormPdfController::class, 'adminDownloadPage']);

    Route::post('/admin/announcements', [AnnouncementController::class, 'store']);
    Route::put('/admin/announcements/{announcement}', [AnnouncementController::class, 'update']);
    Route::delete('/admin/announcements/{announcement}', [AnnouncementController::class, 'destroy']);

    Route::get('/admin/certificate-templates', [CertificateController::class, 'templates']);
    Route::put('/admin/certificate-templates/{program}', [CertificateController::class, 'updateTemplate']);
    Route::post('/admin/certificates/issue', [CertificateController::class, 'issue']);
    Route::get('/admin/certificates/issues', [CertificateController::class, 'listIssues']);

    // Guardian (parent) invitations — admin only
    Route::get('/guardians/eligible-students', [GuardianController::class, 'eligibleStudents']);
    Route::get('/guardians', [GuardianController::class, 'index']);
    Route::post('/guardians', [GuardianController::class, 'store']);
    Route::patch('/guardians/{guardian}/sent', [GuardianController::class, 'markSent']);
    Route::delete('/guardians/{guardian}', [GuardianController::class, 'destroy']);

    Route::post('/batches', [BatchController::class, 'store']);
    Route::put('/batches/{batch}', [BatchController::class, 'update']);
    Route::delete('/batches/{batch}', [BatchController::class, 'destroy']);
    Route::delete('/batches/{batch}/students/{user}', [BatchController::class, 'detachStudent']);
});

Route::middleware(['auth', 'role:admin,mentor'])->group(function () {
    // Internal leaderboard (staff only — scores are private and not exposed publicly)
    Route::get('/rankings/categories', [RankingController::class, 'categories']);
    Route::get('/rankings/filters', [RankingController::class, 'filters']);
    Route::get('/rankings', [RankingController::class, 'index']);
    Route::get('/rankings/manual', [RankingController::class, 'manualList']);
    Route::post('/rankings/manual', [RankingController::class, 'manualStore']);
    Route::put('/rankings/manual/{entry}', [RankingController::class, 'manualUpdate']);
    Route::delete('/rankings/manual/{entry}', [RankingController::class, 'manualDestroy']);

    // Student reports (daily + weekly summary)
    Route::get('/student-reports', [StudentReportController::class, 'index']);
    Route::post('/student-reports', [StudentReportController::class, 'store']);
    Route::post('/student-reports/weekly', [StudentReportController::class, 'generateWeekly']);
    Route::delete('/student-reports/{report}', [StudentReportController::class, 'destroy']);

    Route::get('/batches', [BatchController::class, 'index']);
    Route::get('/batches/{batch}', [BatchController::class, 'show']);
    Route::get('/batches/{batch}/students', [BatchController::class, 'students']);
    Route::post('/batches/{batch}/students', [BatchController::class, 'attachStudent']);

    Route::get('/students/search', [UserController::class, 'searchableStudents']);

    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);

    Route::get('/tests', [TestDefinitionController::class, 'index']);
    Route::post('/tests', [TestDefinitionController::class, 'store']);
    Route::put('/tests/{test}', [TestDefinitionController::class, 'update']);
    Route::delete('/tests/{test}', [TestDefinitionController::class, 'destroy']);
    Route::post('/tests/{test}/duplicate', [TestDefinitionController::class, 'duplicate']);
    Route::get('/exams', [ExamDefinitionController::class, 'index']);
    Route::post('/exams', [ExamDefinitionController::class, 'store']);
    Route::put('/exams/{exam}', [ExamDefinitionController::class, 'update']);
    Route::delete('/exams/{exam}', [ExamDefinitionController::class, 'destroy']);
    Route::post('/exams/{exam}/duplicate', [ExamDefinitionController::class, 'duplicate']);

    Route::get('/tests/{test}/submissions', [TestDefinitionController::class, 'testSubmissions']);
    Route::get('/exams/{exam}/submissions', [ExamDefinitionController::class, 'examSubmissions']);
    Route::get('/tests/{test}/free-tryout-submissions', [TestDefinitionController::class, 'freeTryoutSubmissions']);
    Route::put('/submissions/{submission}', [TestDefinitionController::class, 'updateSubmission']);

    Route::apiResource('materials', MaterialController::class)->except(['show']);
    Route::get('/materials/{material}', [MaterialController::class, 'showAdmin']);

    Route::get('/bimble-classes', [BimbleClassController::class, 'index']);
    Route::get('/bimble-class-instructors', [BimbleClassController::class, 'instructors']);
    Route::post('/bimble-classes', [BimbleClassController::class, 'store']);
    Route::get('/bimble-classes/{bimbleClass}', [BimbleClassController::class, 'show']);
    Route::put('/bimble-classes/{bimbleClass}', [BimbleClassController::class, 'update']);
    Route::delete('/bimble-classes/{bimbleClass}', [BimbleClassController::class, 'destroy']);
    Route::post('/bimble-classes/{bimbleClass}/students', [BimbleClassController::class, 'attachStudent']);
    Route::delete('/bimble-classes/{bimbleClass}/students/{user}', [BimbleClassController::class, 'detachStudent']);
    Route::post('/bimble-classes/{bimbleClass}/batches', [BimbleClassController::class, 'syncBatches']);
    Route::post('/bimble-classes/{bimbleClass}/materials', [BimbleClassController::class, 'attachMaterial']);
    Route::delete('/bimble-classes/{bimbleClass}/materials/{material}', [BimbleClassController::class, 'detachMaterial']);
    Route::post('/bimble-classes/{bimbleClass}/tests', [BimbleClassController::class, 'attachTest']);
    Route::delete('/bimble-classes/{bimbleClass}/tests/{testDefinition}', [BimbleClassController::class, 'detachTest']);
    Route::post('/bimble-classes/{bimbleClass}/activities', [ClassActivityController::class, 'store']);
});
