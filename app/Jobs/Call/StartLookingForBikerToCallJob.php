<?php

namespace App\Jobs\Call;

use App\Dtos\FirebasePushNotificationDTO;
use App\Enums\BikerStatus;
use App\Enums\CallRequestStatus;
use App\Enums\CallStatus;
use App\Jobs\CallRequest\SendCallRequestPushNotification;
use App\Jobs\CallRequest\SendCallRequestPushNotificationJob;
use App\Models\Biker;
use App\Models\Call;
use App\Models\CallRequest;
use App\Services\Firebase\FirebaseAuthService;
use App\Services\Firebase\FirebaseService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StartLookingForBikerToCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Call $call;
    protected Collection $bikers;
    public function __construct(
        Call $call
    ) {
        $this->call = $call;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {



        $this->bikers = Biker::selectRaw(
            '
        bikers.id,
        ST_Distance_Sphere(POINT(?, ?), biker_geolocations.location) AS distance',
            [
                $this->call->location->longitude,
                $this->call->location->latitude
            ]
        )
            ->join('biker_geolocations', 'bikers.id', '=', 'biker_geolocations.biker_id')
            ->where('bikers.status', BikerStatus::Avaible->value)
            ->orderBy('distance')
            ->get();

        if ($this->bikers->isEmpty()) {
            StartLookingForBikerToCallJob::dispatch($this->call)->delay(now()->addMinutes(2));
        }

        SendCallRequestPushNotificationJob::dispatch(
            $this->call,
            $this->bikers,
            $this->bikers->pluck('distance')->toArray(),
            (new FirebaseAuthService())->getAccessToken()

        );
    }
}
