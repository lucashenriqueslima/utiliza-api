<?php

namespace App\Models;

use App\Enums\CallStatus;
use App\Models\Ileva\IlevaAssociateVehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Call extends Model
{
    use HasFactory, HasSpatial;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'status' => CallStatus::class,
            'location' => Point::class,
        ];
    }

    public function associateCar(): BelongsTo
    {
        return $this->belongsTo(AssociateCar::class);
    }

    public function biker(): BelongsTo
    {
        return $this->belongsTo(Biker::class);
    }

    public function ilevaAssociateVehicle(): BelongsTo
    {
        return $this->belongsTo(IlevaAssociateVehicle::class, 'ileva_associate_vehicle_id' );
    }

    public function requests(): HasMany
    {
        return $this->hasMany(CallRequest::class);
    }
    
}
