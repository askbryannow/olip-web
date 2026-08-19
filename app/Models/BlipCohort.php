<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlipCohort extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function enrollments(): HasMany
    {
        return $this->hasMany(BlipEnrollment::class, 'cohort_id');
    }
}
