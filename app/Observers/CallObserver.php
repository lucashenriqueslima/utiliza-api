<?php

namespace App\Observers;

use App\Enums\CallStatus;
use App\Enums\ExpertiseStatus;
use App\Filament\Resources\CallResource;
use App\Filament\Resources\CallResource\Pages\ValidateExpertise;
use App\Jobs\Call\StartLookingForBikerToCallJob;
use App\Models\Call;
use App\Models\Expertise;
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
        StartLookingForBikerToCallJob::dispatch($call);
    }

    /**
     * Handle the Call "updated" event.
     */
    public function updated(Call $call): void
    {

        if (!$call->wasChanged('status')) {
            return;
        }

        match ($call->status) {
            CallStatus::SearchingBiker => $this->handleSearchingBiker($call),
            CallStatus::WaitingValidation => $this->handleWaitingValidation($call),
            CallStatus::Approved => $this->handleApproved($call),
            default => null,
        };
    }

    private function handleWaitingValidation(Call $call): void
    {
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

    private function handleSearchingBiker(Call $call): void
    {
        if (!$call->biker_id) {
            StartLookingForBikerToCallJob::dispatch($call);

            Expertise::where('call_id', $call->id)
                ->update(['status' => ExpertiseStatus::Canceled]);
        }
    }

    private function handleApproved(Call $call): void
    {
        $call->bill()->create();
    }
}
