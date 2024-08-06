<?php

namespace App\Services\Mqtt;

use PhpMqtt\Client\Contracts\MqttClient;
use PhpMqtt\Client\Facades\MQTT;

class MqttService
{

    private MqttClient $mqttClient;

    public function __construct()
    {
        $this->mqttClient = MQTT::connection();
    }

    public function publish(string $topic, string $message, int $qos = 0, bool $retain = false): void
    {
        $this->mqttClient->publish($topic, $message, $qos, $retain);
    }

    public function registerLoopEvent(): void
    {
        $this->mqttClient->registerLoopEventHandler(function (MqttClient $mqttClient, float $elapsedTime) {
            if ($elapsedTime > 20) {
                $mqttClient->interrupt();
            }
        });
    }

    public function subscribe(string $topic, callable $callback): void
    {
        $this->mqttClient->subscribe($topic, $callback, 2);
    }

    public function unsubscribe(string $topic): void
    {
        $this->mqttClient->unsubscribe($topic);
    }

    public function interrupt(): void
    {
        $this->mqttClient->interrupt();
    }

    public function loop(): void
    {
        $this->mqttClient->loop();
    }

    public function disconnect(): void
    {
        $this->mqttClient->disconnect();
    }
}
