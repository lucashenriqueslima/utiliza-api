<?php

namespace App\Jobs\CallRequest;

use App\Jobs\Call\HandleCallContinuityAfterCallRequest;
use App\Models\Biker;
use App\Models\CallRequest;
use App\Models\Call;
use App\Services\Firebase\FirebaseAuthService;
use App\Services\Firebase\FirebaseService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCallRequestPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected CallRequest $callRequest;
    protected Biker $biker;

    public function __construct(
        protected Call $call,
        protected Collection $bikers,
        protected string $firebaseAccessToken,

    ) {
        $this->biker = $this->bikers->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $firebaseService = new FirebaseService((new FirebaseAuthService())->getAccessToken());

        $this->callRequest = CallRequest::create([
            'call_id' => $this->call->id,
            'biker_id' => $this->biker->id,
        ]);

        try {
            $firebaseService->sendCallRequestPushNotification(
                $this->call,
                $this->callRequest,
                $this->biker,
            );

            $this->bikers->shift();

            HandleCallContinuityAfterCallRequest::dispatch(
                $this->call,
                $this->callRequest,
                $this->bikers,
                $this->firebaseAccessToken,
            )->delay(now()->addSeconds(12));
        } catch (\Exception $e) {
            $this->bikers->shift();

            HandleCallContinuityAfterCallRequest::dispatch(
                $this->call,
                $this->callRequest,
                $this->bikers,
                $this->firebaseAccessToken,
            )->delay(now()->addSeconds(12));
            Log::error($e->getMessage());
        }

        // $this->bikers = array_shift($this->bikers);

        // dispatch(new HandleCallContinuityAfterCallRequest(
        //     $this->callId,
        //     $this->callRequest->id,
        //     $this->bikers,
        //     $this->firebaseAccessToken
        // ))
        //     ->delay(now()->addSeconds(12));
    }
}
