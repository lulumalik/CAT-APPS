<?php

namespace App\Models;

use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'program_category',
        'in_quarantine',
        'app_expires_at',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $user): void {
            if (blank($user->username)) {
                $user->username = self::generateUniqueUsername(
                    (string) ($user->email ?? ''),
                    (string) ($user->name ?? '')
                );
            }
        });
    }

    public const PROGRAM_VIP = 'vip';

    public const PROGRAM_VIP_OFFLINE = 'vip_offline'; // legacy

    public const PROGRAM_VIP_ONLINE = 'vip_online'; // legacy

    public const PROGRAM_REGULAR = 'regular';

    public const PROGRAM_REGULAR_OFFLINE = 'regular_offline'; // legacy

    public const PROGRAM_REGULAR_ONLINE = 'regular_online'; // legacy

    public const PROGRAM_BIMBINGAN_ONLINE = 'bimbingan_online';

    public const PROGRAM_TRY_OUT = 'try_out';

    public static function programCategories(): array
    {
        return [
            self::PROGRAM_VIP,
            self::PROGRAM_VIP_OFFLINE,
            self::PROGRAM_VIP_ONLINE,
            self::PROGRAM_REGULAR,
            self::PROGRAM_REGULAR_OFFLINE,
            self::PROGRAM_REGULAR_ONLINE,
            self::PROGRAM_BIMBINGAN_ONLINE,
            self::PROGRAM_TRY_OUT,
        ];
    }

    public static function canonicalProgramCategories(): array
    {
        return [
            self::PROGRAM_VIP,
            self::PROGRAM_REGULAR,
            self::PROGRAM_BIMBINGAN_ONLINE,
            self::PROGRAM_TRY_OUT,
        ];
    }

    public static function normalizeProgramCategory(?string $programCategory): string
    {
        if (! $programCategory) {
            return self::PROGRAM_REGULAR;
        }

        if (in_array($programCategory, [self::PROGRAM_VIP_OFFLINE, self::PROGRAM_VIP_ONLINE], true)) {
            return self::PROGRAM_VIP;
        }

        if (in_array($programCategory, [self::PROGRAM_REGULAR_OFFLINE, self::PROGRAM_REGULAR_ONLINE], true)) {
            return self::PROGRAM_REGULAR;
        }

        if (! in_array($programCategory, self::programCategories(), true)) {
            return self::PROGRAM_REGULAR;
        }

        return $programCategory;
    }

    public static function supportsQuarantine(?string $programCategory): bool
    {
        $normalized = self::normalizeProgramCategory($programCategory);

        return $normalized === self::PROGRAM_VIP;
    }

    public static function usesSimplifiedOnboarding(?string $programCategory): bool
    {
        $normalized = self::normalizeProgramCategory($programCategory);

        return in_array($normalized, [self::PROGRAM_BIMBINGAN_ONLINE, self::PROGRAM_TRY_OUT], true);
    }

    public static function isExamOnlyProgram(?string $programCategory): bool
    {
        return self::normalizeProgramCategory($programCategory) === self::PROGRAM_TRY_OUT;
    }

    /**
     * Eligible for batch/class roster:
     * - try_out (kelas ujian) → never
     * - bimbingan_online → payment confirmed (lunas)
     * - regular / vip → administration fully completed
     * - no registration_progress (admin-created) → allowed
     */
    public function canJoinBatch(): bool
    {
        if ($this->role !== 'user') {
            return false;
        }

        if (self::isExamOnlyProgram($this->program_category)) {
            return false;
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('registration_progress')) {
            return true;
        }

        $progress = $this->relationLoaded('registrationProgress')
            ? $this->registrationProgress
            : $this->registrationProgress()->first();

        // Akun dibuat admin tanpa alur pendaftaran.
        if (! $progress) {
            return true;
        }

        if (self::usesSimplifiedOnboarding($this->program_category)) {
            return (bool) $progress->payment_confirmed;
        }

        return (bool) $progress->fully_completed;
    }

    public function batchIneligibleMessage(): string
    {
        if (self::isExamOnlyProgram($this->program_category)) {
            return 'Peserta Kelas Ujian tidak dimasukkan ke batch kursus.';
        }

        if (self::usesSimplifiedOnboarding($this->program_category)) {
            return 'Peserta bimbingan online belum lunas, belum bisa dimasukkan ke batch.';
        }

        return 'Administrasi peserta (reguler/VIP) belum selesai, belum bisa dimasukkan ke batch.';
    }

    public function hasCompletedOnboarding(): bool
    {
        if ($this->role !== 'user') {
            return true;
        }

        if (self::usesSimplifiedOnboarding($this->program_category)) {
            if (! $this->hasVerifiedEmail()) {
                return false;
            }

            if (! \Illuminate\Support\Facades\Schema::hasTable('registration_progress')) {
                return false;
            }

            $progress = $this->relationLoaded('registrationProgress')
                ? $this->registrationProgress
                : $this->registrationProgress()->first();

            return (bool) ($progress?->payment_confirmed);
        }

        if (! \Illuminate\Support\Facades\Schema::hasTable('registration_progress')) {
            return false;
        }

        $progress = $this->relationLoaded('registrationProgress')
            ? $this->registrationProgress
            : $this->registrationProgress()->first();

        return (bool) ($progress?->fully_completed);
    }

    public static function normalizeUsername(?string $username): string
    {
        $normalized = Str::of((string) $username)
            ->trim()
            ->lower()
            ->replaceMatches('/[^a-z0-9_]/', '_')
            ->replaceMatches('/_+/', '_')
            ->trim('_')
            ->toString();

        return $normalized !== '' ? $normalized : 'user';
    }

    public static function generateUniqueUsername(string $email = '', string $name = ''): string
    {
        $base = '';
        if ($email !== '' && str_contains($email, '@')) {
            $base = (string) Str::before($email, '@');
        } elseif ($email !== '') {
            $base = $email;
        } elseif ($name !== '') {
            $base = (string) Str::of($name)->replace(' ', '_');
        }

        $candidate = self::normalizeUsername($base);
        $final = $candidate;
        $counter = 1;

        while (self::query()->where('username', $final)->exists()) {
            $counter++;
            $final = "{$candidate}_{$counter}";
        }

        return $final;
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'in_quarantine' => 'boolean',
            'app_expires_at' => 'datetime',
        ];
    }

    public function isAppExpired(): bool
    {
        return $this->app_expires_at !== null && $this->app_expires_at->isPast();
    }

    public static function defaultAppExpiresAt(?string $programCategory = null, ?\Illuminate\Support\Carbon $from = null): \Illuminate\Support\Carbon
    {
        $from = $from ?? now();
        $normalized = self::normalizeProgramCategory($programCategory);

        return match ($normalized) {
            self::PROGRAM_BIMBINGAN_ONLINE => $from->copy()->addMonths(6),
            self::PROGRAM_TRY_OUT => $from->copy()->addMonths(3),
            default => $from->copy()->addYear(),
        };
    }

    public static function defaultAppExpiresMonths(?string $programCategory): ?int
    {
        $normalized = self::normalizeProgramCategory($programCategory);

        return match ($normalized) {
            self::PROGRAM_BIMBINGAN_ONLINE => 6,
            self::PROGRAM_TRY_OUT => 3,
            default => null,
        };
    }

    public function registrationProgress()
    {
        return $this->hasOne(RegistrationProgress::class);
    }

    public function bimbleClasses()
    {
        return $this->belongsToMany(BimbleClass::class, 'bimble_class_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function batches()
    {
        return $this->belongsToMany(Batch::class, 'batch_user')
            ->withTimestamps();
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    /** Guardian links where this user is the student. */
    public function guardianLinks()
    {
        return $this->hasMany(StudentGuardian::class, 'student_user_id');
    }

    /** Guardian links where this user is the parent/guardian account. */
    public function childLinks()
    {
        return $this->hasMany(StudentGuardian::class, 'guardian_user_id');
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }
}
