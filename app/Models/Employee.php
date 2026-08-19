<?php

namespace App\Models;

use App\Models\Concerns\HasDimensionFilters;
use Carbon\CarbonInterface;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Also the login identity for the employee-facing BLIP portal (guard: "employee"),
 * since this app has no separate account model for individual staff.
 */
class Employee extends Model implements AuthenticatableContract
{
    use Authenticatable, HasDimensionFilters;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'join_date' => 'date',
        'exit_date' => 'date',
        'base_salary' => 'decimal:2',
        'password' => 'hashed',
    ];

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function blipEnrollment(): HasOne
    {
        return $this->hasOne(BlipEnrollment::class);
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
