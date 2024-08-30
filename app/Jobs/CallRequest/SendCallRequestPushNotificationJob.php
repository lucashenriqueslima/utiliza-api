<?php

namespace App\Jobs\CallRequest;

use App\Jobs\Call\HandleCallContinuityAfterCallRequestJob;
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

class SendCallRequestPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected CallRequest $callRequest;

    public function __construct(
        protected Call $call,
        protected Collection $bikers,
        protected array $distances,
        protected string $firebaseAccessToken,

    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $firebaseService = new FirebaseService((new FirebaseAuthService())->getAccessToken());

        $biker = $this->bikers->shift();
        $this->distances;

        $this->callRequest = CallRequest::create([
            'call_id' => $this->call->id,
            'biker_id' => $biker->id,
        ]);

        try {
            $firebaseService->sendCallRequestPushNotification(
                $this->call,
                $this->callRequest,
                $biker,
                array_shift($this->distances) / 1000,
            );

            HandleCallContinuityAfterCallRequestJob::dispatch(
                $this->call,
                $this->callRequest,
                $this->bikers,
                $this->firebaseAccessToken,
                $this->distances,
            )->delay(now()->addSeconds(27));
        } catch (\Exception $e) {

            Log::error($e->getMessage());

            HandleCallContinuityAfterCallRequestJob::dispatch(
                $this->call,
                $this->callRequest,
                $this->bikers,
                $this->firebaseAccessToken,
                $this->distances,
            )->delay(now()->addSeconds(27));
            Log::error($e->getMessage());
        }
    }
}
