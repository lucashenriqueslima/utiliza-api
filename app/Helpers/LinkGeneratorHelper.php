<?php

namespace App\Helpers;

class LinkGeneratorHelper
{
    public static function googleMaps(string $longitude, string $latitude): string
    {
        return "https://www.google.com/maps?q={$longitude},{$latitude}&z=17&hl=pt-BR";
    }

    public static function whatsapp(string $phone, string $message): string
    {
        $encodedMessage = rawurlencode($message);
        return "https://wa.me/55{$phone}?text={$encodedMessage}";
    }
}
