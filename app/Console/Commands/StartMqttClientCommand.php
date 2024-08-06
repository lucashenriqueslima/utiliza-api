<?php

namespace App\Console\Commands;

use App\Enums\CallStatus;
use App\Models\BikerChangeCall;
use App\Models\Call;
use App\Services\BikerGeolocationService;
use App\Services\Mqtt\MqttService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Exceptions\MqttClientException;
use Illuminate\Contracts\Console\Isolatable;

class StartMqttClientCommand extends Command implements Isolatable
{
    private array $newPublishes;
    private array $oldPublishes;


    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:start {oldPublishes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start MQTT client';

    /**
     * Execute the console command.
     */
    public function handle(MqttService $mqttService): void
    {

        $base_url = 'ady4g3wrobmle-ats.iot.sa-east-1.amazonaws.com';
        $mqtt = new MqttClient(
            $base_url,
            8883,
            NULL,
            MqttClient::MQTT_3_1_1
        );
        $connectionSettings = (new ConnectionSettings())
            ->setConnectTimeout(3)
            ->setUseTls(TRUE)
            // Download root cert from https://docs.aws.amazon.com/iot/latest/developerguide/server-authentication.html
            ->setTlsCertificateAuthorityFile('/etc/ssl/certs/AmazonRootCA12.pem')
            // @see https://docs.aws.amazon.com/iot/latest/developerguide/fleet-provision-api.html#create-keys-cert
            ->setTlsClientCertificateKeyFile('/etc/ssl/certs/mqtt-broker-2-private.pem.key')
            ->setTlsClientCertificateFile('/etc/ssl/certs/mqtt-broker-2.pem.crt');
        $mqtt->connect($connectionSettings);

        $mqtt->subscribe(
            'biker/+/geolocation',
            function ($topic, $message, $retained, $matchedWildcards) use ($mqtt) {
                $payload = json_decode($message);
                echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
                $mqtt->interrupt();
                $mqtt->disconnect();
            }
        );

        $mqtt->loop(TRUE);
        // try {

        //     $this->oldPublishes = $this->argument('oldPublishes');

        //     $this->newPublishes = $this->getNewPublishes();

        //     $this->handleSubscriptions($mqttService);

        //     // $mqttService->registerLoopEvent();

        //     // $this->handlePublishes($mqttService, $this->newPublishes, $this->oldPublishes);

        //     $mqttService->loop();

        //     $this->oldPublishes = $this->newPublishes;

        //     Artisan::call('mqtt:start', [
        //         'oldPublishes' => $this->oldPublishes
        //     ]);
        // } catch (MqttClientException $e) {
        //     $this->fail($e->getMessage());
        // }
    }


    private function handleSubscriptions(MqttService $mqttService): void
    {
        $mqttService->subscribe('biker/+/geolocation', function (string $topic, string $message) use ($mqttService): void {
            $this->log("Received message on topic {$topic} with message {$message}");
            // match ($topic) {
            //     'biker//geolocation' => $this->updateBikerGeolocation($message),
            //     default => Log::info("Received message on topic {$topic} with message {$message}"),
            // };
        });
    }

    private function handlePublishes(MqttService $mqttService, array $newPublishes, array $oldPublishes): void
    {
        $newPublishesToPublish = array_diff($newPublishes, $oldPublishes);

        foreach ($newPublishesToPublish as $publish) {
            $mqttService->publish($publish['topic'], $publish['message']);
        }
    }

    private function getNewPublishes(): array
    {
        $validationsToPublish = Call::where('status', CallStatus::WaitingBikerSeeValidation)
            ->pluck('biker_id')
            ->map(fn ($bikerId): array => [
                'topic' => "biker/{$bikerId}/validation",
                'message' => 'true'
            ])
            ->toArray();

        $bikerChangesToPublish = BikerChangeCall::select('biker_id', 'reason')
            ->where('is_delivered', false)
            ->get()
            ->map(fn (BikerChangeCall $bikerChangeCall): array => [
                'topic' => "biker/{$bikerChangeCall->biker_id}/canceled-call",
                'message' => $bikerChangeCall->reason
            ])
            ->toArray();

        return array_merge($validationsToPublish, $bikerChangesToPublish);
    }

    private function updateBikerGeolocation(string $json): void
    {
        $decodedJson = json_decode($json, true);

        BikerGeolocationService::update($decodedJson['biker_id'], $decodedJson['latitude'], $decodedJson['longitude']);
    }
}
