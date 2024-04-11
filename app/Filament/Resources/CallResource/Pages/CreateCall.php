<?php

namespace App\Filament\Resources\CallResource\Pages;

use App\Filament\Resources\CallResource;
use App\Jobs\FindBikerForCallJob;
use App\Models\Associate;
use App\Models\Ileva\IlevaAssociate;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;

class CreateCall extends CreateRecord
{
    protected static string $resource = CallResource::class;

    protected function handleRecordCreation(array $data): Model
    {   


        $ilevaAssociate = IlevaAssociate::with(['ilevaPerson', 'ilevaVehicle', 'ilevaVehicle.ilevaModel', 'ilevaVehicle.ilevaModel.ilevaBrand', 'ilevaVehicle.ilevaColor'])
                                            ->find($data['associate_id']);
        $associate = Associate::updateOrCreate(
            ['ileva_associate_id' => $ilevaAssociate->id],
            [
                'ileva_associate_id' => $ilevaAssociate->id,
                'name' => $ilevaAssociate->ilevaPerson->nome,
                'email' => $ilevaAssociate->ilevaPerson->email,
                'phone' => $ilevaAssociate->ilevaPerson->tel_celular,
                'cpf' => $ilevaAssociate->ilevaPerson->cpf,

            ]
        );

        $car = $associate->car()->updateOrCreate(
            ['ileva_associate_vehicle_id' => $data['associate_vehicle_id']],
            [
                'ileva_associate_vehicle_id' => $data['associate_vehicle_id'],
                'brand' => $ilevaAssociate->ilevaVehicle->ilevaModel->ilevaBrand->nome,
                'plate' => $ilevaAssociate->ilevaVehicle->placa,
                'model' => $ilevaAssociate->ilevaVehicle->ilevaModel->nome,
                'color' => $ilevaAssociate->ilevaVehicle->ilevaColor->nome,
            ],
        );

        return static::getModel()::create(
            [
                'ileva_associate_vehicle_id' => $data['associate_vehicle_id'],
                'associate_car_id' => $car->id,
                'location' => new Point(floatval($data['latitude']), floatval($data['longitude']))
            ]
        );
    }

    protected function afterCreate(): void
    {
        FindBikerForCallJob::dispatch($this->record);
    }




    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
