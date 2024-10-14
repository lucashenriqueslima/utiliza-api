<?php

namespace App\Models;

use App\Enums\BikerStatus;
use Database\Seeders\BikerSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Biker extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    protected $guarded = [];


    protected function casts(): array
    {
        return [
            'status' => BikerStatus::class,
        ];
    }

    public function bikerChangeCalls(): HasMany
    {
        return $this->hasMany(BikerChangeCall::class);
    }

    public function call(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    public function callRequests(): HasMany
    {
        return $this->hasMany(CallRequest::class);
    }

    public function geolocation(): HasOne
    {
        return $this->hasOne(BikerGeolocation::class);
    }

    public function motorcycle(): HasOne
    {
        return $this->hasOne(Motorcycle::class);
    }

    public function pixs(): HasMany
    {
        return $this->hasMany(PixKey::class);
    }
}
