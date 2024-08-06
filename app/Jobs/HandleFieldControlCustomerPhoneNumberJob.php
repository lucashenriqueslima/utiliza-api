<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class HandleFieldControlCustomerPhoneNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected string $id,
        protected string $phoneNumber
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(PendingRequest $client): void
    {
        $client = $client->withHeaders([
            "Content-Type" => "application/json",
            "X-Api-Key" => env('FIELD_CONTROL_API_KEY')
        ]);

        try {

            $response = $client->get("https://carchost.fieldcontrol.com.br/customers/{$this->id}/phones");

            Log::info("Phone number " . print_r($response->json()['items']) . " already exists in Field Control");

            if (!empty($response->json()['items'])) {
                return;
            }

            $response = $client->post(
                "https://carchost.fieldcontrol.com.br/customers/{$this->id}/phones",
                [
                    'number' => $this->phoneNumber,
                    'type' => 'mobile',
                    'primary' => true,
                ]
            );

            if (!in_array($response->status(), [200, 201])) {
                Log::error("Error updating customer: {$response->body()}");
            }
        } catch (\Exception $e) {
            Log::error("Error updating customer :  {$e->getMessage()}");
        }
    }
}
