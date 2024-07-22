<?php

namespace App\Services;

use App\Enums\CallStatus;
use ZipArchive;

class CallService
{


    public static function extractCoordinatesFromGoogleMapsUrl(string $url): ?array
    {

        $pattern2 = '/maps\?q=(-?\d+\.\d+),(-?\d+\.\d+)/';
        $pattern1 = '/3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/';

        if (preg_match($pattern1, $url, $matches)) {
            $latitude = $matches[1];
            $longitude = $matches[2];
        } elseif (preg_match($pattern2, $url, $matches)) {
            $latitude = $matches[1];
            $longitude = $matches[2];
        } else {
            return null;
        }

        return [
            'lat' => floatval($latitude),
            'lng' => floatval($longitude)
        ];
    }

    public static function cleanupTempFiles($files, $tempDir)
    {
        foreach ($files as $localFilename) {
            unlink($tempDir . '/' . $localFilename);
        }
        rmdir($tempDir);
    }


    public static function createZipArchive($files, $tempDir)
    {
        $zip = new ZipArchive();
        $zipFile = storage_path('app/public/chamado.zip');

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {

            foreach ($files as $localFilename) {
                try {
                    $zip->addFile($tempDir . '/' . $localFilename, $localFilename);
                } catch (\Exception $e) {
                    dd($e);
                    throw new \Exception('Erro ao adicionar arquivo ao ZIP: ' . $e->getMessage());
                }
            }
            $zip->close();
        } else {
            throw new \Exception('Não foi possível criar o arquivo ZIP');
        }

        return $zipFile;
    }


    public static function checkIfCallWasAccepted(CallStatus $status)
    {
        if ($status != CallStatus::SearchingBiker) {
            return true;
        }

        return false;
    }
}
