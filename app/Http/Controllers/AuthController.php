<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\ExamTrack;
use App\Models\RegistrationProgress;
use App\Models\User;
use App\Notifications\RegistrationProgressStatusNotification;
use Throwable;

class AuthController extends Controller
{
    private function serializeUser(User $user): array
    {
        $examTrack = null;
        if ($user->exam_track_id) {
            $user->loadMissing('examTrack.category');
            $examTrack = $user->examTrack ? [
                'id' => $user->examTrack->id,
                'name' => $user->examTrack->name,
                'slug' => $user->examTrack->slug,
                'category_id' => $user->examTrack->exam_category_id,
                'category_name' => $user->examTrack->category?->name,
            ] : null;
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
            'role' => $user->role,
            'program_category' => $user->program_category,
            'exam_category_id' => $user->exam_category_id,
            'exam_track_id' => $user->exam_track_id,
            'exam_track' => $examTrack,
            'avatar_url' => $user->avatar_url,
            'is_google_account' => $user->isGoogleAccount(),
            'in_quarantine' => (bool) $user->in_quarantine,
            'app_expires_at' => $user->app_expires_at?->toIso8601String(),
            'app_expired' => $user->isAppExpired(),
            'onboarding_completed' => $user->hasCompletedOnboarding(),
            'uses_simplified_onboarding' => User::usesSimplifiedOnboarding($user->program_category),
            'is_exam_only_program' => User::isExamOnlyProgram($user->program_category),
            'registration' => Schema::hasTable('registration_progress') ? $user->registrationProgress : null,
        ];
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|min:3|max:32|regex:/^[a-zA-Z0-9_]+$/|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'program_category' => 'nullable|in:'.implode(',', User::programCategories()),
        ], [
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);
        // Bisnis baru: pendaftar mandiri masuk program simulasi ujian (kelas ujian).
        $programCategory = User::normalizeProgramCategory($data['program_category'] ?? User::PROGRAM_TRY_OUT);

        $payload = [
            'name' => $data['name'],
            'username' => User::normalizeUsername($data['username']),
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role' => 'user',
            'program_category' => $programCategory,
            'in_quarantine' => User::supportsQuarantine($programCategory),
        ];

        if (! Schema::hasColumn('users', 'username')) {
            unset($payload['username']);
        }

        if (Schema::hasColumn('users', 'app_expires_at')) {
            $payload['app_expires_at'] = User::usesSimplifiedOnboarding($programCategory)
                ? null
                : User::defaultAppExpiresAt($programCategory);
        }

        $user = User::create($payload);

        if (Schema::hasTable('registration_progress')) {
            RegistrationProgress::create([
                'user_id' => $user->id,
                'current_step' => 'administration',
                'administration_status' => 'not_started',
                'administration_data' => [],
                'psychology_status' => 'not_started',
                'health_status' => 'not_started',
                'physical_status' => 'not_started',
                'fully_completed' => false,
            ]);
            $user->load('registrationProgress');
        }

        Auth::login($user);
        $request->session()->regenerate();

        if (! $user->hasVerifiedEmail()) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (Throwable $e) {
                Log::warning('Failed to send verification email after registration.', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        try {
            $user->notify(new RegistrationProgressStatusNotification(
                event: 'registered',
                step: 'administration',
                nextStep: 'administration',
            ));
        } catch (Throwable $e) {
            Log::warning('Failed to send registration welcome status email.', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
                'message' => 'Registrasi berhasil. Silakan verifikasi email Anda melalui tautan yang dikirimkan.',
                'user' => $this->serializeUser($user),
                'email_verification_required' => ! $user->hasVerifiedEmail(),
            ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        $credentials = [
            'username' => User::normalizeUsername((string) $request->input('username')),
            'password' => $request->input('password'),
        ];

        // Akun email biasa wajib verifikasi email dulu; akun Google dianggap terverifikasi.
        if (Auth::validate($credentials)) {
            $candidate = User::where('username', $credentials['username'])->first();
            if (
                $candidate
                && $candidate->role === 'user'
                && ! $candidate->isGoogleAccount()
                && ! $candidate->hasVerifiedEmail()
            ) {
                try {
                    $candidate->sendEmailVerificationNotification();
                } catch (Throwable $e) {
                    Log::warning('Failed to resend verification email on login.', [
                        'user_id' => $candidate->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'email_verification_required' => true,
                    'message' => 'Email Anda belum diverifikasi. Kami telah mengirim ulang tautan verifikasi ke email Anda.',
                ], 403);
            }
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if (Schema::hasTable('registration_progress')) {
                $user->loadMissing('registrationProgress');
            }
            return response()->json([
                'success' => true,
                'user' => $this->serializeUser($user),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function user(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (Schema::hasTable('registration_progress')) {
                $user->loadMissing('registrationProgress');
            }

            return response()->json([
                'success' => true,
                'user' => $this->serializeUser($user),
            ]);
        }

        return response()->json([
            'success' => false,
            'user' => null
        ], 401);
    }

    /**
     * Simpan minat ujian peserta (kategori + track spesifik).
     */
    public function setInterest(Request $request)
    {
        $data = $request->validate([
            'exam_track_id' => 'required|exists:exam_tracks,id',
        ]);

        $track = ExamTrack::with('category')->findOrFail($data['exam_track_id']);

        if (! $track->is_active || ! $track->category?->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori ujian ini sedang tidak tersedia.',
            ], 422);
        }

        $user = $request->user();
        $user->forceFill([
            'exam_category_id' => $track->exam_category_id,
            'exam_track_id' => $track->id,
        ])->save();

        return response()->json([
            'success' => true,
            'message' => "Minat ujian disimpan: {$track->category->name} — {$track->name}.",
            'user' => $this->serializeUser($user->fresh()),
        ]);
    }
}
