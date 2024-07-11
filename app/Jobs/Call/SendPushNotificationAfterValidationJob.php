<?php

namespace App\Jobs\Call;

use App\Enums\CallStatus;
use App\Models\Biker;
use App\Models\Call;
use App\Services\Firebase\FirebaseAuthService;
use App\Services\Firebase\FirebaseService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class SendPushNotificationAfterValidationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Call $call,
        protected String $bikerFirebaseToken,
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $existsValidationErrors = $this->call->validationErrors()->exists();

        $firebaseService = new FirebaseService((new FirebaseAuthService())->getAccessToken());
        try {
            $firebaseService->sendPushNotificationAfterValidation($this->bikerFirebaseToken, $existsValidationErrors);

            if ($existsValidationErrors) {
                $this->call->validationErrors()->update(['status' => 'sent']);
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
