<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\RegistrationProgress;
use App\Models\User;
use App\Notifications\RegistrationProgressStatusNotification;
use Throwable;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'username' => 'required|string|min:3|max:32|regex:/^[a-zA-Z0-9_]+$/|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'program_category' => 'required|in:'.implode(',', User::programCategories()),
        ]);
        $programCategory = User::normalizeProgramCategory($data['program_category']);

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

        $user = User::create($payload);

        if (Schema::hasTable('registration_progress')) {
            RegistrationProgress::create([
                'user_id' => $user->id,
                'current_step' => 'administration',
                'administration_status' => 'not_started',
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
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'role' => $user->role,
                    'program_category' => $user->program_category,
                    'in_quarantine' => (bool) $user->in_quarantine,
                    'registration' => Schema::hasTable('registration_progress') ? $user->registrationProgress : null,
                ],
                'email_verification_required' => ! $user->hasVerifiedEmail(),
            ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'username' => User::normalizeUsername((string) $request->input('username')),
            'password' => $request->input('password'),
        ])) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            if (Schema::hasTable('registration_progress')) {
                $user->loadMissing('registrationProgress');
            }
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'role' => $user->role,
                    'program_category' => $user->program_category,
                    'in_quarantine' => (bool) $user->in_quarantine,
                    'registration' => Schema::hasTable('registration_progress') ? $user->registrationProgress : null,
                ]
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
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'role' => $user->role,
                    'program_category' => $user->program_category,
                    'in_quarantine' => (bool) $user->in_quarantine,
                    'registration' => Schema::hasTable('registration_progress') ? $user->registrationProgress : null,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'user' => null
        ], 401);
    }
}
