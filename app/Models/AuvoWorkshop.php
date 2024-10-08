<?php

namespace App\Models;

use App\Enums\AssociationEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuvoWorkshop extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'days_of_week' => 'array',
        'association' => AssociationEnum::class,
    ];

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(AuvoCollaborator::class, 'auvo_collaborator_id');
    }
}
