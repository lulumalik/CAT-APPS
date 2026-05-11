<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
        'email',
        'password',
        'role',
        'program_category',
        'in_quarantine',
    ];

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
        ];
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

    public function announcements()
    {
        return $this->hasMany(Announcement::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(UserNotification::class);
    }
}
