<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ExpertiseFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function expertise(): BelongsTo
    {
        return $this->belongsTo(Expertise::class);
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
