<?php

namespace App\Models;

use App\Models\Concerns\HasDimensionFilters;
use Illuminate\Database\Eloquent\Model;

class EngagementRecord extends Model
{
    use HasDimensionFilters;

    protected $guarded = [];
}
