<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BimbleClass extends Model
{
    public const PROGRAM_VIP = 'vip';

    public const PROGRAM_VIP_OFFLINE = 'vip_offline'; // legacy

    public const PROGRAM_VIP_ONLINE = 'vip_online'; // legacy

    public const PROGRAM_REGULAR = 'regular';

    public const PROGRAM_REGULAR_OFFLINE = 'regular_offline'; // legacy

    public const PROGRAM_REGULAR_ONLINE = 'regular_online'; // legacy

    public const PROGRAM_BIMBINGAN_ONLINE = 'bimbingan_online';

    public const PROGRAM_TRY_OUT = 'try_out';

    protected $fillable = [
        'name',
        'class_code',
        'instructor_name',
        'instructor_id',
        'academic_period',
        'academic_period_start',
        'academic_period_end',
        'participant_count',
        'program_type',
        'cover_image_path',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'academic_period_start' => 'date:Y-m-d',
            'academic_period_end' => 'date:Y-m-d',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bimble_class_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class, 'bimble_class_material')
            ->withPivot(['session_number', 'sort_order'])
            ->withTimestamps();
    }

    public function testDefinitions(): BelongsToMany
    {
        return $this->belongsToMany(TestDefinition::class, 'bimble_class_test')
            ->withPivot(['kind', 'sort_order'])
            ->withTimestamps();
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ClassActivity::class)->latest('happened_at');
    }

    public function isTryOut(): bool
    {
        return $this->program_type === self::PROGRAM_TRY_OUT;
    }

    public static function programTypes(): array
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

    public static function normalizeProgramType(?string $programType): string
    {
        if (! $programType) {
            return self::PROGRAM_REGULAR;
        }

        if (in_array($programType, [self::PROGRAM_VIP_OFFLINE, self::PROGRAM_VIP_ONLINE], true)) {
            return self::PROGRAM_VIP;
        }

        if (in_array($programType, [self::PROGRAM_REGULAR_OFFLINE, self::PROGRAM_REGULAR_ONLINE], true)) {
            return self::PROGRAM_REGULAR;
        }

        if (! in_array($programType, self::programTypes(), true)) {
            return self::PROGRAM_REGULAR;
        }

        return $programType;
    }

    public static function isVipProgram(?string $programType): bool
    {
        $normalized = self::normalizeProgramType($programType);

        return $normalized === self::PROGRAM_VIP;
    }
}
