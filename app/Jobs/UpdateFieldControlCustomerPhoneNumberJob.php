<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateFieldControlCustomerPhoneNumberJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected $customer
    ) {
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

            $customerId = '';

            $response = $client->get('https://carchost.fieldcontrol.com.br/customers?q=code:"' . $this->customer->code . '"');


            if (!in_array($response->status(), [200, 201])) {
                Log::error("Error fetching customer {$this->customer->name}:  {$response->body()}");
            }

            $items = $response->json()['items'];

            foreach ($items as $item) {
                if ($item['code'] == $this->customer->code && $item['archived'] == false) {
                    $customerId = $item['id'];

                    continue;
                }
            }

            try {
                $response = $client->post(
                    "https://carchost.fieldcontrol.com.br/customers/{$customerId}/phones",
                    [
                        'number' => $this->customer->phone_number,
                        'type' => 'mobile',
                        'primary' => true,
                    ]
                );


                if (!in_array($response->status(), [200, 201])) {
                    Log::error("Error updating customer {$this->customer->name}:  {$response->body()}");
                }
                Log::info($response->json());
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
