<?php

namespace App\Models;

use App\Models\Concerns\HasDimensionFilters;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasDimensionFilters;

    protected $guarded = [];
}
