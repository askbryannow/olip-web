<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'departments' => 'array',
        'opened_year' => 'integer',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /** Same bu/state/outlet filtering as HasDimensionFilters, but against the outlet's own id/columns. */
    public function scopeMatchingOutletDims(Builder $query, array $filters): Builder
    {
        if (($filters['bu'] ?? 'ALL') !== 'ALL') {
            $query->where('bu', $filters['bu']);
        }
        if (($filters['state'] ?? 'ALL') !== 'ALL') {
            $query->where('state', $filters['state']);
        }
        if (($filters['outletId'] ?? 'ALL') !== 'ALL') {
            $query->where('id', $filters['outletId']);
        }

        return $query;
    }
}
