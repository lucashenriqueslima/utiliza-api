<?php

namespace App\Models\Ileva;

use App\Models\Call;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class IlevaAssociateVehicle extends Model
{
    use HasFactory;

    protected $connection = 'ileva';
    protected $table = 'hbrd_asc_veiculo';

    protected $guarded = [];

    public function ilevaAssociate()
    {
        return $this->belongsTo(IlevaAssociate::class, 'id_associado');
    }

    public function ilevaModel(): HasOne
    {
        return $this->hasOne(IlevaAssociateVehicleModel::class, 'id', 'id_modelo');
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class, 'ileva_associate_vehicle_id');
    } 

    public function ilevaColor(): HasOne
    {
        return $this->hasOne(IlevaAssociateVehicleColor::class, 'id', 'id_cor');
    }


}
