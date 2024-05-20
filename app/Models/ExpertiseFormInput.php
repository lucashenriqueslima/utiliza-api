<?php

namespace App\Models;

use App\Enums\ExpertiseInputFieldType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertiseFormInput extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'field_type' => ExpertiseInputFieldType::class,
        ];
    }
}
