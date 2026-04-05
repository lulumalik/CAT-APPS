<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TestDefinitionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\LmsController;

// Public material routes
Route::get('/materials/public', [MaterialController::class, 'publicIndex']);
Route::get('/materials/public/{slug}', [MaterialController::class, 'publicShow']);

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/user', [AuthController::class, 'user']);
Route::post('/register', [AuthController::class, 'register']);

// Public test routes (accessible to authenticated users)
Route::get('/incoming-tests', [TestDefinitionController::class, 'incoming']);
Route::get('/available-tests', [TestDefinitionController::class, 'available']);

// Test operations (requires authentication via session)
Route::middleware('auth')->group(function () {
    Route::get('/tests/{test}', [TestDefinitionController::class, 'show']);
    Route::post('/tests/{test}/submit', [TestDefinitionController::class, 'submit']);
    Route::get('/my-tests', [TestDefinitionController::class, 'myTests']);

    Route::get('/lms/courses', [LmsController::class, 'courses']);
    Route::post('/lms/courses', [LmsController::class, 'storeCourse']);
    Route::get('/lms/courses/{course}', [LmsController::class, 'showCourse']);
    Route::put('/lms/courses/{course}', [LmsController::class, 'updateCourse']);
    Route::delete('/lms/courses/{course}', [LmsController::class, 'destroyCourse']);

    Route::post('/lms/courses/{course}/modules', [LmsController::class, 'addModule']);
    Route::post('/lms/modules/{module}/lessons', [LmsController::class, 'addLesson']);
    Route::post('/lms/courses/{course}/enroll', [LmsController::class, 'enroll']);
    Route::get('/lms/me/enrollments', [LmsController::class, 'myEnrollments']);
    Route::get('/lms/courses/{course}/enrollments', [LmsController::class, 'courseEnrollments']);
    Route::put('/lms/enrollments/{enrollment}', [LmsController::class, 'updateEnrollment']);

    Route::post('/lms/courses/{course}/assignments', [LmsController::class, 'createAssignment']);
    Route::get('/lms/assignments/{assignment}/submissions', [LmsController::class, 'assignmentSubmissions']);
    Route::post('/lms/assignments/{assignment}/submit', [LmsController::class, 'submitAssignment']);
    Route::put('/lms/assignment-submissions/{submission}/grade', [LmsController::class, 'gradeSubmission']);

    Route::get('/lms/forum/threads', [LmsController::class, 'listThreads']);
    Route::post('/lms/forum/threads', [LmsController::class, 'createThread']);
    Route::get('/lms/forum/threads/{thread}/comments', [LmsController::class, 'listComments']);
    Route::post('/lms/forum/threads/{thread}/comments', [LmsController::class, 'addComment']);

    Route::get('/lms/chat/peers', [LmsController::class, 'listChatPeers']);
    Route::get('/lms/chat/{peer}', [LmsController::class, 'listMessages']);
    Route::post('/lms/chat/{receiver}', [LmsController::class, 'sendMessage']);

    Route::get('/lms/calendar/events', [LmsController::class, 'listCalendar']);
    Route::post('/lms/calendar/events', [LmsController::class, 'createCalendar']);

    Route::post('/lms/courses/{course}/certificates/issue', [LmsController::class, 'issueCertificate']);
    Route::get('/lms/me/certificates', [LmsController::class, 'myCertificates']);

    Route::post('/lms/courses/{course}/payments/checkout', [LmsController::class, 'checkout']);
    Route::put('/lms/payments/{payment}/mark-paid', [LmsController::class, 'markPaymentPaid']);

    Route::get('/lms/reports/overview', [LmsController::class, 'reportsOverview']);
});

Route::middleware('role:admin')->group(function () {
    Route::apiResource('users', UserController::class);
});

Route::middleware('role:admin,mentor,instructor')->group(function () {
    Route::get('/questions', [QuestionController::class, 'index']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions/{question}', [QuestionController::class, 'update']);
    Route::delete('/questions/{question}', [QuestionController::class, 'destroy']);

    Route::get('/tests', [TestDefinitionController::class, 'index']);
    Route::post('/tests', [TestDefinitionController::class, 'store']);
    Route::put('/tests/{test}', [TestDefinitionController::class, 'update']);
    Route::delete('/tests/{test}', [TestDefinitionController::class, 'destroy']);

    Route::get('/tests/{test}/submissions', [TestDefinitionController::class, 'testSubmissions']);
    Route::put('/submissions/{submission}', [TestDefinitionController::class, 'updateSubmission']);

    Route::apiResource('materials', MaterialController::class)->except(['show']);
    Route::get('/materials/{material}', [MaterialController::class, 'showAdmin']);
});
