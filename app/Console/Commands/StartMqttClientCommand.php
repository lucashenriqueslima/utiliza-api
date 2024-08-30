<?php

namespace App\Console\Commands;

use App\Enums\CallStatus;
use App\Models\BikerChangeCall;
use App\Models\Call;
use App\Services\BikerChangeCallService;
use App\Services\BikerGeolocationService;
use App\Services\ExpertiseFileValidationErrorService;
use App\Services\Mqtt\MqttService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\Exceptions\MqttClientException;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\DB;
use Laravel\Octane\Facades\Octane;

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
    public function handle(
        MqttService $mqttService,
        BikerGeolocationService $bikerGeolocationService,
        ExpertiseFileValidationErrorService $expertiseFileValidationErrorService,
        BikerChangeCallService $bikerChangeCallService
    ): void {

        try {

            // $base_url = 'ady4g3wrobmle-ats.iot.sa-east-1.amazonaws.com';
            // $mqtt = new MqttClient(
            //     $base_url,
            //     8883,
            //     NULL,
            //     MqttClient::MQTT_3_1_1
            // );
            // $connectionSettings = (new ConnectionSettings())
            //     ->setConnectTimeout(3)
            //     ->setUseTls(TRUE)
            //     // Download root cert from https://docs.aws.amazon.com/iot/latest/developerguide/server-authentication.html
            //     ->setTlsCertificateAuthorityFile('/var/www/html/docker/8.3/aws-mqtt-ca1.pem')
            //     // @see https://docs.aws.amazon.com/iot/latest/developerguide/fleet-provision-api.html#create-keys-cert
            //     ->setTlsClientCertificateKeyFile('/var/www/html/docker/8.3/aws-mqtt-private.pem.key')
            //     ->setTlsClientCertificateFile('/var/www/html/docker/8.3/aws-mqtt-certificate.pem.crt');
            // $mqtt->connect($connectionSettings);

            // $mqtt->subscribe(
            //     'biker/+/geolocation',
            //     function ($topic, $message, $retained, $matchedWildcards) use ($mqtt) {
            //         $payload = json_decode($message);
            //         echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);
            //         $mqtt->interrupt();
            //         $mqtt->disconnect();
            //     }
            // );

            // $mqtt->loop(TRUE);

            $this->oldPublishes = json_decode($this->argument('oldPublishes'), true);

            $this->newPublishes = $this->getNewPublishes(
                $expertiseFileValidationErrorService,
                $bikerChangeCallService
            );

            $this->handleSubscriptions($mqttService, $bikerGeolocationService);

            $mqttService->registerLoopEvent();

            Log::info(sprintf("New publishes: %s", json_encode($this->newPublishes)));

            $this->handlePublishes($mqttService, $this->newPublishes, $this->oldPublishes);

            $mqttService->loop();

            Artisan::call('mqtt:start', [
                'oldPublishes' => json_encode($this->newPublishes)
            ]);
        } catch (MqttClientException $e) {
            $this->fail($e->getMessage());
        }
    }


    private function handleSubscriptions(MqttService $mqttService, BikerGeolocationService $bikerGeolocationService): void
    {
        $mqttService->subscribe('biker/+/geolocation', function (string $topic, string $message) use ($mqttService, $bikerGeolocationService): void {

            echo sprintf("Received message on topic [%s]: %s\n", $topic, $message);

            $decodedJson = json_decode($message, true);
            $bikerId = explode('/', $topic)[1];


            $bikerGeolocationService->update(
                $bikerId,
                (float) $decodedJson['latitude'],
                (float) $decodedJson['longitude']
            );

            $mqttService->interrupt();
        });
    }

    private function handlePublishes(
        MqttService $mqttService,
        array $newPublishes,
        array $oldPublishes
    ): void {

        $newPublishesToPublish = array_udiff($newPublishes, $oldPublishes, [$this, 'udiffCompare']);


        foreach ($newPublishesToPublish as $publish) {
            $mqttService->publish($publish['topic'], $publish['message']);
            Log::info(sprintf("Published message on topic [%s]: %s", $publish['topic'], $publish['message']));
        }
    }

    private function getNewPublishes(
        ExpertiseFileValidationErrorService $expertiseFileValidationErrorService,
        BikerChangeCallService $bikerChangeCallService
    ): array {
        $validionErrors = $expertiseFileValidationErrorService->getValidationErrors()
            ->map(fn($validationError): array => [
                'topic' => "call/{$validationError->id}/biker/$validationError->biker_id/main-expertise-validation-errors",
                'message' => json_encode($validationError)
            ])
            ->toArray();


        $bikerChangesToPublish = $bikerChangeCallService->getBikerChangeCalls()
            ->map(fn(BikerChangeCall $bikerChangeCall): array => [
                'topic' => "call/{$bikerChangeCall->call_id}/biker/{$bikerChangeCall->biker_id}/call-cancelation",
                'message' => json_encode(['reason' => $bikerChangeCall->reason])
            ])
            ->toArray();

        return array_merge($validionErrors, $bikerChangesToPublish);
    }

    private function udiffCompare($a, $b): int
    {
        return $a['topic'] <=> $b['topic'];
    }
}
