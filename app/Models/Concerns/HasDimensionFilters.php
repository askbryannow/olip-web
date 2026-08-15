<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Shared "global filter" scoping used by every dashboard/report query:
 * business unit, state, outlet, and (where the table has it) employment type.
 */
trait HasDimensionFilters
{
    public function scopeMatchingDims(Builder $query, array $filters): Builder
    {
        if (($filters['bu'] ?? 'ALL') !== 'ALL') {
            $query->where('bu', $filters['bu']);
        }
        if (($filters['state'] ?? 'ALL') !== 'ALL') {
            $query->where('state', $filters['state']);
        }
        if (($filters['outletId'] ?? 'ALL') !== 'ALL') {
            $query->where('outlet_id', $filters['outletId']);
        }

        return $query;
    }

    /** For models that also carry an employment_type column (Employee, TrainingRecord). */
    public function scopeMatchingEmploymentType(Builder $query, array $filters): Builder
    {
        if (($filters['employmentType'] ?? 'ALL') !== 'ALL') {
            $query->where('employment_type', $filters['employmentType']);
        }

        return $query;
    }
}
