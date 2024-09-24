<?php

namespace App\Filament\Resources\CallResource\Widgets;

use App\Enums\BikerStatus;
use App\Models\Biker;
use App\Models\Call;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Laravel\Octane\Facades\Octane;

class BikersAvaibleWidget extends BaseWidget
{

    protected static ?string $pollingInterval = '120s';

    protected function getStats(): array
    {
        [$avaibleBikers, $busyBikers, $todayCalls] = Octane::concurrently(
            [
                fn(): string => (string) Biker::where('status', BikerStatus::Avaible)->count(),
                fn(): string => (string) Biker::where('status', BikerStatus::Busy)->count(),
                fn(): string => (string) Call::whereDate('created_at', now())->count(),
            ]
        );

        return [
            Stat::make('Motoqueiros disponíveis', $avaibleBikers),
            Stat::make('Motoqueiros em atendimento', $busyBikers),
            Stat::make('Chamados de hoje', $todayCalls)
        ];
    }
}
