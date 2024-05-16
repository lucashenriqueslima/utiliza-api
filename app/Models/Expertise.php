<?php

namespace App\Models;

use App\Enums\ExpertiseStatus;
use App\Enums\ExpertiseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Expertise extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => ExpertiseStatus::class,
            'type' => ExpertiseType::class,
        ];
    }

    public function call(): HasOne
    {
        return $this->hasOne(Call::class);
    }

    public function associate(): HasOne
    {
        return $this->hasOne(Associate::class);
    }
    public function files(): HasMany
    {
        return $this->hasMany(ExpertiseFile::class);
    }

    public function formInputs(): HasMany
    {
        return $this->hasMany(ExpertiseFormInput::class);
    }

    public function thirdParty(): HasMany
    {
        return $this->hasMany(ThirdParty::class);
    }
}
