<?php

namespace App\Http\Controllers;

use App\Models\RegistrationProgress;
use App\Models\StudentGuardian;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class GuardianController extends Controller
{
    private const INVITE_TTL_DAYS = 14;

    /**
     * Students eligible to have a guardian invited (registration fully completed).
     */
    public function eligibleStudents(Request $request)
    {
        $query = User::query()->where('role', 'user');

        if (Schema::hasTable('registration_progress')) {
            $query->whereHas('registrationProgress', fn ($q) => $q->where(function ($inner) {
                $inner->where('fully_completed', true)
                    ->orWhere('current_step', 'completed');
            }));
        }

        if ($search = trim((string) $request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");

                if (Schema::hasColumn('users', 'username')) {
                    $q->orWhere('username', 'like', "%{$search}%");
                }
            });
        }

        if (Schema::hasTable('student_guardians')) {
            $query->withCount(['guardianLinks as guardians_count']);
        }

        $columns = ['id', 'name', 'email'];
        if (Schema::hasColumn('users', 'username')) {
            $columns[] = 'username';
        }
        if (Schema::hasColumn('users', 'program_category')) {
            $columns[] = 'program_category';
        }

        $students = $query->orderBy('name')->limit(50)->get($columns);

        return response()->json(['items' => $students]);
    }

    public function index(Request $request)
    {
        $query = StudentGuardian::query()
            ->with(['student:id,name,username,email', 'guardian:id,name,email', 'inviter:id,name']);

        if ($studentId = $request->input('student_id')) {
            $query->where('student_user_id', $studentId);
        }

        if ($status = $request->input('status')) {
            $query->where('invite_status', $status);
        }

        $items = $query->orderByDesc('id')->limit(200)->get()
            ->map(fn ($link) => $this->serialize($request, $link));

        return response()->json(['items' => $items]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_user_id' => 'required|exists:users,id',
            'guardian_name' => 'required|string|max:120',
            'relationship' => ['required', Rule::in(['ayah', 'ibu', 'wali'])],
            'phone' => 'nullable|string|max:32',
            'email' => 'nullable|email|max:120',
        ]);

        $student = User::findOrFail($data['student_user_id']);
        if ($student->role !== 'user') {
            return response()->json(['message' => 'Akun ini bukan peserta.'], 422);
        }

        if (Schema::hasTable('registration_progress')) {
            $completed = RegistrationProgress::where('user_id', $student->id)
                ->where(function ($q) {
                    $q->where('fully_completed', true)
                        ->orWhere('current_step', 'completed');
                })
                ->exists();
            if (! $completed) {
                return response()->json([
                    'message' => 'Peserta belum menyelesaikan registrasi, belum bisa mengundang orang tua.',
                ], 422);
            }
        }

        $link = StudentGuardian::create([
            'student_user_id' => $student->id,
            'guardian_name' => $data['guardian_name'],
            'relationship' => $data['relationship'],
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'invite_token' => StudentGuardian::generateToken(),
            'invite_status' => StudentGuardian::STATUS_PENDING,
            'invited_by' => $request->user()->id,
            'invited_at' => now(),
            'expires_at' => now()->addDays(self::INVITE_TTL_DAYS),
        ]);

        $link->load(['student:id,name,username,email', 'guardian:id,name,email', 'inviter:id,name']);

        return response()->json($this->serialize($request, $link), 201);
    }

    /**
     * Mark the invite as sent (after the team forwarded the link via WhatsApp) and
     * refresh the token validity window.
     */
    public function markSent(Request $request, StudentGuardian $guardian)
    {
        if ($guardian->invite_status === StudentGuardian::STATUS_ACCEPTED) {
            return response()->json(['message' => 'Undangan sudah diterima.'], 422);
        }

        $guardian->update([
            'invite_status' => StudentGuardian::STATUS_SENT,
            'invite_token' => $guardian->invite_token ?: StudentGuardian::generateToken(),
            'invited_at' => now(),
            'expires_at' => now()->addDays(self::INVITE_TTL_DAYS),
        ]);

        $guardian->load(['student:id,name,username,email', 'guardian:id,name,email', 'inviter:id,name']);

        return response()->json($this->serialize($request, $guardian));
    }

    public function destroy(StudentGuardian $guardian)
    {
        $guardian->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Public: read invite info for the join page.
     */
    public function showInvite(string $token)
    {
        $link = StudentGuardian::where('invite_token', $token)
            ->with('student:id,name')
            ->first();

        if (! $link) {
            return response()->json(['message' => 'Undangan tidak ditemukan.'], 404);
        }

        if ($link->invite_status === StudentGuardian::STATUS_ACCEPTED) {
            return response()->json(['message' => 'Undangan ini sudah digunakan.', 'status' => 'accepted'], 410);
        }

        if ($link->isExpired()) {
            $link->update(['invite_status' => StudentGuardian::STATUS_EXPIRED]);

            return response()->json(['message' => 'Undangan sudah kedaluwarsa. Hubungi tim kami.', 'status' => 'expired'], 410);
        }

        return response()->json([
            'guardian_name' => $link->guardian_name,
            'relationship' => $link->relationshipLabel(),
            'email' => $link->email,
            'student_name' => $link->student?->name,
            'expires_at' => $link->expires_at?->toIso8601String(),
        ]);
    }

    /**
     * Public: accept the invite, creating/linking a parent account.
     */
    public function accept(Request $request, string $token)
    {
        $link = StudentGuardian::where('invite_token', $token)->first();

        if (! $link) {
            return response()->json(['message' => 'Undangan tidak ditemukan.'], 404);
        }

        if ($link->invite_status === StudentGuardian::STATUS_ACCEPTED) {
            return response()->json(['message' => 'Undangan ini sudah digunakan.'], 410);
        }

        if ($link->isExpired()) {
            $link->update(['invite_status' => StudentGuardian::STATUS_EXPIRED]);

            return response()->json(['message' => 'Undangan sudah kedaluwarsa.'], 410);
        }

        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:120',
            'password' => 'required|string|min:6',
        ]);

        if ($link->email && strcasecmp($link->email, $data['email']) !== 0) {
            return response()->json([
                'message' => 'Email harus sama dengan yang terdaftar pada undangan.',
            ], 422);
        }

        $existing = User::where('email', $data['email'])->first();

        if ($existing) {
            if ($existing->role !== 'parent') {
                return response()->json([
                    'message' => 'Email sudah dipakai akun lain. Gunakan email berbeda.',
                ], 422);
            }
            if (! Hash::check($data['password'], $existing->password)) {
                return response()->json([
                    'message' => 'Email sudah terdaftar sebagai orang tua, tetapi kata sandi salah.',
                ], 422);
            }
            $guardian = $existing;
        } else {
            $guardian = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'parent',
                'in_quarantine' => false,
            ]);
        }

        $link->update([
            'guardian_user_id' => $guardian->id,
            'guardian_name' => $data['name'],
            'invite_status' => StudentGuardian::STATUS_ACCEPTED,
            'accepted_at' => now(),
        ]);

        if (Schema::hasTable('user_notifications')) {
            UserNotification::create([
                'user_id' => $link->student_user_id,
                'type' => 'guardian_linked',
                'title' => 'Orang tua terhubung',
                'message' => sprintf('%s telah terhubung sebagai orang tua/wali Anda.', $data['name']),
                'payload' => ['guardian_id' => $guardian->id],
            ]);
        }

        Auth::login($guardian);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'message' => 'Akun orang tua berhasil dibuat dan terhubung.',
            'user' => [
                'id' => $guardian->id,
                'name' => $guardian->name,
                'email' => $guardian->email,
                'role' => $guardian->role,
            ],
        ]);
    }

    private function serialize(Request $request, StudentGuardian $link): array
    {
        $base = rtrim((string) config('app.frontend_url'), '/');

        return [
            'id' => $link->id,
            'student' => $link->student ? [
                'id' => $link->student->id,
                'name' => $link->student->name,
                'username' => $link->student->username,
                'email' => $link->student->email,
            ] : null,
            'guardian_name' => $link->guardian_name,
            'relationship' => $link->relationship,
            'relationship_label' => $link->relationshipLabel(),
            'phone' => $link->phone,
            'email' => $link->email,
            'invite_status' => $link->invite_status,
            'invite_token' => $link->invite_token,
            'invite_url' => $link->invite_token ? "{$base}/parent/join/{$link->invite_token}" : null,
            'whatsapp_message' => $this->whatsappMessage($link, $base),
            'invited_by' => $link->inviter?->name,
            'invited_at' => $link->invited_at?->toIso8601String(),
            'accepted_at' => $link->accepted_at?->toIso8601String(),
            'expires_at' => $link->expires_at?->toIso8601String(),
            'guardian_account' => $link->guardian ? [
                'id' => $link->guardian->id,
                'name' => $link->guardian->name,
                'email' => $link->guardian->email,
            ] : null,
        ];
    }

    private function whatsappMessage(StudentGuardian $link, string $base): ?string
    {
        if (! $link->invite_token) {
            return null;
        }

        $url = "{$base}/parent/join/{$link->invite_token}";
        $studentName = $link->student?->name ?? 'ananda';

        return "Assalamu'alaikum Bapak/Ibu {$link->guardian_name},\n\n"
            ."Kami dari tim Pratistha Cendekia Prestasi. Ananda {$studentName} sudah terdaftar dan "
            ."dashboard orang tua sudah tersedia.\n\n"
            ."Silakan buka tautan berikut untuk membuat akun & memantau perkembangan ananda:\n{$url}\n\n"
            ."Tautan berlaku terbatas. Jika ada kendala, balas pesan ini.\n\nTerima kasih.";
    }
}
