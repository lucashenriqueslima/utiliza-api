<?php

namespace App\Models;

use App\Enums\AccidentStatus;
use App\Enums\AssociationEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Accident extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => AccidentStatus::class,
            'association' => AssociationEnum::class,
        ];
    }

    public function images(): HasMany
    {
        return $this->hasMany(AccidentImage::class);
    }
}
