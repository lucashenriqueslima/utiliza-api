<?php

namespace App\Models;

use App\Enums\ExpertiseFileType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;

class ExpertiseFile extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
            'file_expertise_type' => ExpertiseFileType::class,
        ];
    }

    public function extension(): string
    {
        return Arr::last(explode('.', $this->path));
    }


    public function expertise(): BelongsTo
    {
        return $this->belongsTo(Expertise::class);
    }

    public function validationErrors(): HasOne
    {
        return $this->hasOne(ExpertiseFileValidationError::class);
    }
}
