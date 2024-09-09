<?php

namespace App\Services\Zip;

use ZipArchive;

class ZipService
{

    public function createTempDirIfDontExist($rawTempDir): string
    {
        $tempDir = storage_path($rawTempDir);

        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        return $tempDir;
    }
    public function createZipArchive(string $zipFileName, array $fileNames, string $tempDir)
    {
        $zip = new ZipArchive();
        $zipFile = storage_path("app/public/{$zipFileName}");

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($fileNames as $localFilename) {
                try {
                    $filePath = "{$tempDir}/{$localFilename}";
                    $zip->addFile($filePath, $localFilename);
                } catch (\Exception $e) {
                    throw new \Exception('Erro ao adicionar arquivo ao ZIP: ' . $e->getMessage());
                }
            }
            $zip->close();
        } else {
            throw new \Exception('Não foi possível criar o arquivo ZIP');
        }

        return $zipFile;
    }

    public function cleanupTempFiles($files, $tempDir)
    {
        foreach ($files as $localFilename) {
            unlink($tempDir . '/' . $localFilename);
        }
        rmdir($tempDir);
    }
}
