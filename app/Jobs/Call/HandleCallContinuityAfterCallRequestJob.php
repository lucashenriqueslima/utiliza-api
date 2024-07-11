<?php

namespace App\Jobs\Call;

use App\Jobs\CallRequest\SendCallRequestPushNotificationJob;
use App\Models\Call;
use App\Models\CallRequest;
use App\Services\CallRequestService;
use App\Services\CallService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Laravel\Octane\Facades\Octane;

class HandleCallContinuityAfterCallRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected Call $call,
        protected CallRequest $callRequest,
        protected Collection $bikers,
        protected string $firebaseAccessToken,
        protected array $distances,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(CallService $callService, CallRequestService $callRequestService): void
    {

        $this->call->refresh();
        $this->callRequest->refresh();

        if (
            $callRequestService->checkIfCallRequestWasAccepted($this->callRequest->status)
            || $callService->checkIfCallWasAccepted($this->call->status)
        ) {
            return;
        }

        $callRequestService->handleUpdateStatus($this->callRequest);

        if ($this->bikers->isEmpty()) {
            StartLookingForBikerToCallJob::dispatch($this->call);
            return;
        }

        SendCallRequestPushNotificationJob::dispatch(
            $this->call,
            $this->bikers,
            $this->distances,
            $this->firebaseAccessToken,
        );
    }
}
