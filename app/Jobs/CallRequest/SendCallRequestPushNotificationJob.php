<?php

namespace App\Jobs\CallRequest;

use App\Enums\CallStatus;
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

        $this->call->refresh();

        if ($this->call->status !== CallStatus::SearchingBiker) {
            return;
        }

        try {
            $firebaseService = new FirebaseService((new FirebaseAuthService())->getAccessToken());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        $biker = $this->bikers->shift();
        $this->distances;

        $this->callRequest = CallRequest::create([
            'call_id' => $this->call->id,
            'biker_id' => $biker->id,
        ]);

        try {
            $timeLimitToAcceptCallRequest = Carbon::now()->addSeconds(26);

            $firebaseService->sendCallRequestPushNotification(
                $this->call,
                $this->callRequest,
                $biker,
                array_shift($this->distances) / 1000,
                $timeLimitToAcceptCallRequest,
            );


            HandleCallContinuityAfterCallRequestJob::dispatch(
                $this->call,
                $this->callRequest,
                $this->bikers,
                $this->firebaseAccessToken,
                $this->distances,
            )->delay(now()->addSeconds(30));
        } catch (\Exception $e) {

            Log::error($e->getMessage());

            HandleCallContinuityAfterCallRequestJob::dispatch(
                $this->call,
                $this->callRequest,
                $this->bikers,
                $this->firebaseAccessToken,
                $this->distances,
            )->delay(now()->addSeconds(30));
            Log::error($e->getMessage());
        }
    }
}
