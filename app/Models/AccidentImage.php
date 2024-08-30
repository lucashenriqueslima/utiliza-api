<?php

namespace App\Models;

use App\Enums\AccidentImageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccidentImage extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'type' => AccidentImageType::class,
            'is_current' => 'boolean',
        ];
    }

    public function accident(): BelongsTo
    {
        return $this->belongsTo(Accident::class);
    }
}
