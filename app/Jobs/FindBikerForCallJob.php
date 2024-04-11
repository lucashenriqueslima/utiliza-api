<?php

namespace App\Jobs;

use App\Dtos\FirebasePushNotificationDTO;
use App\Enums\BikerStatus;
use App\Enums\CallStatus;
use App\Models\Biker;
use App\Models\Call;
use App\Models\CallRequest;
use App\Services\Firebase\FirebaseService;
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
            AND biker_geolocations.updated_at > NOW() - INTERVAL 1 MINUTE
            ORDER BY distance', 
        [
            $this->call->location->latitude,
            $this->call->location->longitude,
            BikerStatus::Avaible->value
        ]



        );

        foreach ($bikers as $biker) {
            (new FirebaseService())->sendPushNotification(
                $biker->firebase_token
            );

            sleep(10);

            if($this->call->refresh()->status != CallStatus::InService->value) {
                CallRequest::create([
                    'call_id' => $this->call->id,
                    'biker_id' => $biker->id
                ]);
                continue;
            }

            break;
        }

    }   
}
