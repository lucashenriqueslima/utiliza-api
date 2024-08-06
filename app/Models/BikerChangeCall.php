<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BikerChangeCall extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_delivered' => 'boolean',
    ];

    public function biker(): BelongsTo
    {
        return $this->belongsTo(Biker::class);
    }


    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
