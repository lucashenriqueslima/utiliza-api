<?php

namespace App\Observers;

use App\Enums\CallStatus;
use App\Filament\Resources\CallResource;
use App\Filament\Resources\CallResource\Pages\ValidateExpertise;
use App\Models\Call;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Facades\Log;


class CallObserver
{
    /**
     * Handle the Call "created" event.
     */
    public function created(Call $call): void
    {
        //
    }

    /**
     * Handle the Call "updated" event.
     */
    public function updated(Call $call): void
    {
        Log::info($call->id);
        if ($call->status === CallStatus::WaitingValidation) {
            Notification::make()
                ->warning()
                ->title('Existe um chamado aguardando validação')
                ->body("O chamado #{$call->id} está aguardando validação.")
                ->actions([
                    Action::make('validate_expertise')
                        ->label('Validar')
                        ->button()
                        ->icon('heroicon-o-eye')
                        ->color('danger')
                        ->markAsRead()
                        ->url(route('filament.admin.resources.calls.validate', ['callId' => $call->id]))
                        ->openUrlInNewTab()
                ])
                ->sendToDatabase(User::select('id')->get());
        }
    }

    /**
     * Handle the Call "deleted" event.
     */
    public function deleted(Call $call): void
    {
        //
    }

    /**
     * Handle the Call "restored" event.
     */
    public function restored(Call $call): void
    {
        //
    }

    /**
     * Handle the Call "force deleted" event.
     */
    public function forceDeleted(Call $call): void
    {
        //
    }
}
