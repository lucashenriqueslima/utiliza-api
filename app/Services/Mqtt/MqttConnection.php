<?php

namespace App\Services\Mqtt;

// use PhpMqtt\Client\MqttClient;use PhpMqtt\Client\Facades\MQTT;

use PhpMqtt\Client\Contracts\MqttClient;
use PhpMqtt\Client\Facades\MQTT;

abstract class MqttConnection
{
    protected MqttClient $mqttClient;
    public function __construct()
    {
        $mqttClient = MQTT::connection();
    }
}
