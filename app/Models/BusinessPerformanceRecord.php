<?php

namespace App\Models;

use App\Models\Concerns\HasDimensionFilters;
use Illuminate\Database\Eloquent\Model;

class BusinessPerformanceRecord extends Model
{
    use HasDimensionFilters;

    protected $guarded = [];
}
