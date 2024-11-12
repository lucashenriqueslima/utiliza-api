<?php

namespace App\Models;

use App\Enums\AssociationEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dependent extends Model
{
    use HasFactory;

    //guarded
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'association' => AssociationEnum::class,
            'contract_date' => 'date',
        ];
    }

    public function associate(): BelongsTo
    {
        return $this->belongsTo(Associate::class);
    }
}
