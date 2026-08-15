<?php

namespace App\Models;

use App\Models\Concerns\HasDimensionFilters;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasDimensionFilters;

    protected $guarded = [];

    protected $casts = [
        'join_date' => 'date',
        'exit_date' => 'date',
        'base_salary' => 'decimal:2',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    /** Active as of a given date: joined on/before it, and not yet exited (or exited after it). */
    public function scopeActiveAt(Builder $query, CarbonInterface $asOf): Builder
    {
        return $query->whereDate('join_date', '<=', $asOf)
            ->where(function (Builder $q) use ($asOf) {
                $q->whereNull('exit_date')->orWhereDate('exit_date', '>', $asOf);
            });
    }
}
