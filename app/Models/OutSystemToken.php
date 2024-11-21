<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class OutSystemToken extends Authenticatable
{
    use HasFactory, HasApiTokens;

    protected $guarded = [];
}
