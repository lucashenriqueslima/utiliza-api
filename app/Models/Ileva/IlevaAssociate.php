<?php

namespace App\Models\Ileva;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

Class IlevaAssociate extends Model
{
    use HasFactory;

    protected $connection = 'ileva';
    protected $table = 'hbrd_asc_associado';
    protected $guarded = [];


    public function ilevaVehicle(): HasOne
    {
        return $this->hasOne(IlevaAssociateVehicle::class, 'id_associado');
    }
    
    public function ilevaPerson(): BelongsTo
    {
        return $this->belongsTo(IlevaAssociatePerson::class, 'id_pessoa', 'id');
    }
}
