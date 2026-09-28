<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AreaActivity extends Model
{
    protected $fillable = [
        'area_id',
        'body',
    ];

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
