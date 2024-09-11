<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuvoWorkshop extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'days_of_week' => 'array',
    ];

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(AuvoCollaborator::class);
    }
}
