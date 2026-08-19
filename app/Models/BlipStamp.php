<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlipStamp extends Model
{
    protected $guarded = [];

    protected $casts = [
        'visit_date' => 'date',
        'earned_at' => 'datetime',
    ];

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(BlipEnrollment::class, 'enrollment_id');
    }

    public function embassy(): BelongsTo
    {
        return $this->belongsTo(BlipEmbassy::class, 'embassy_id');
    }
}
