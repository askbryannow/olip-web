<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlipEnrollment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'endorsed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'promotion_ready_at' => 'datetime',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(BlipCohort::class, 'cohort_id');
    }

    public function stamps(): HasMany
    {
        return $this->hasMany(BlipStamp::class, 'enrollment_id');
    }

    public function stampsEarnedCount(): int
    {
        return $this->stamps->where('status', 'earned')->count();
    }

    /**
     * Where this Traveller stands, per the Passport status ladder:
     * Registering -> Applicant -> Traveller -> Voyager -> Endorsed -> Promotion-Ready.
     */
    public function passportStage(): string
    {
        if ($this->promotion_ready_at) {
            return 'Promotion-Ready';
        }

        if (! $this->confirmed_at) {
            return match ($this->status) {
                'on_hold' => 'On Hold',
                'declined' => 'Declined',
                'endorsed' => 'Awaiting Cohort Placement',
                default => 'Registering',
            };
        }

        $earned = $this->stampsEarnedCount();

        return match (true) {
            $earned >= 4 => 'Endorsed',
            $earned === 3 => 'Voyager',
            $earned >= 1 => 'Traveller',
            default => 'Applicant',
        };
    }
}
