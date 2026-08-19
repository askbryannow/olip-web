<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlipEmbassy extends Model
{
    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    public function stamps(): HasMany
    {
        return $this->hasMany(BlipStamp::class, 'embassy_id');
    }
}
