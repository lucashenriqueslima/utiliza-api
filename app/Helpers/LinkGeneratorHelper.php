<?php

namespace App\Helpers;

class LinkGeneratorHelper
{
    public static function googleMaps(?string $longitude, ?string $latitude): string
    {
        if (!$longitude || !$latitude) {
            return '';
        }

        return "https://www.google.com/maps?q={$longitude},{$latitude}&z=17&hl=pt-BR";
    }

    public static function googleMapsRoute(?array $origin, ?array $destination): string
    {
        if (!$origin || !$destination) {
            return '';
        }
        return "https://www.google.com/maps/dir/?api=1&origin={$origin['lat']},{$origin['lng']}&destination={$destination['lat']},{$destination['lng']}";
    }

    public static function whatsapp(?string $phone = null, string $message): string
    {
        if (!$phone) {
            return '';
        }

        $encodedMessage = rawurlencode($message);
        return "https://wa.me/55{$phone}?text={$encodedMessage}";
    }
}
