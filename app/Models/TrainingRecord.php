<?php

namespace App\Models;

use App\Models\Concerns\HasDimensionFilters;
use Illuminate\Database\Eloquent\Model;

class TrainingRecord extends Model
{
    use HasDimensionFilters;

    protected $guarded = [];
}
