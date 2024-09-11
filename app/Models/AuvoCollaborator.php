<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuvoCollaborator extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function workshops(): HasMany
    {
        return $this->hasMany(AuvoWorkshop::class);
    }
}
