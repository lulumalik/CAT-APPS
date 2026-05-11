<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\RegistrationProgress;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'program_category' => 'required|in:'.implode(',', User::programCategories()),
        ]);
        $programCategory = User::normalizeProgramCategory($data['program_category']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($data['password']),
            'role' => 'user',
            'program_category' => $programCategory,
            'in_quarantine' => User::supportsQuarantine($programCategory),
        ]);

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

        return response()->json([
            'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'program_category' => $user->program_category,
                    'in_quarantine' => (bool) $user->in_quarantine,
                    'registration' => Schema::hasTable('registration_progress') ? $user->registrationProgress : null,
                ]
            ], 201);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
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
                    'email' => $user->email,
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
                    'email' => $user->email,
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
