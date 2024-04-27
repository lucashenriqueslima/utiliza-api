<?php

namespace App\Jobs;

use App\Dtos\FirebasePushNotificationDTO;
use App\Enums\BikerStatus;
use App\Enums\CallRequestStatus;
use App\Enums\CallStatus;
use App\Models\Biker;
use App\Models\Call;
use App\Models\CallRequest;
use App\Services\Firebase\FirebaseService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class FindBikerForCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Call $call 
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        $bikers = DB::select(
            'SELECT bikers.id,
            firebase_token,
            ST_Distance_Sphere(POINT(?, ?), location) AS distance 
            FROM biker_geolocations 
            INNER JOIN bikers ON bikers.id = biker_geolocations.biker_id
            WHERE bikers.status = ? 
            -- AND biker_geolocations.updated_at > NOW() - INTERVAL 100 MINUTE
            ORDER BY distance', 
        [
            $this->call->location->longitude,
            $this->call->location->latitude,
            BikerStatus::Avaible->value
        ]

        );
        
        $firebaseService = new FirebaseService($this->call);

        foreach ($bikers as $biker) {

            $callRequest = CallRequest::create([
                'call_id' => $this->call->id,
                'biker_id' => $biker->id,
            ]);

            $firebaseService->sendPushNotification(
                $callRequest->id,
                $biker->firebase_token,
                number_format($biker->distance / 1000, 1),
                Carbon::createFromFormat('Y-m-d H:i:s', $callRequest->created_at)->addSeconds(15)
            );

            sleep(18);

            if($callRequest->refresh()->status == CallRequestStatus::Accepted->value) {
                break;
            }
            
            $callRequest->update([
                'status' => CallRequestStatus::NotAnsewered->value
            ]);
            
        }

    }   
}
