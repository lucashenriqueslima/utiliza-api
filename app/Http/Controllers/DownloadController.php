<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use ZipArchive;

class DownloadController extends Controller
{
    public function download()
    {
        $files = [
            'public/expertise/3xLTLV3PgP6MCcSm0YENOJGxZqqIwi3sDtdrbzUL.jpg' => 'image1.jpg',
            'public/expertise/0TUchqJTsgUGn7PuANDx5qFYcPpqWwApZga4oEOJ.jpg' => 'image2.jpg',
        ];

        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        try {
            foreach ($files as $s3Key => $localFilename) {
                $fileContents = Storage::get($s3Key);
                file_put_contents($tempDir . '/' . $localFilename, $fileContents);
            }

            $zipFile = $this->createZipArchive($files, $tempDir);

            $this->cleanupTempFiles($files, $tempDir);

            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar o download: ' . $e->getMessage()], 500);
        }
    }

    private function createZipArchive($files, $tempDir)
    {
        $zip = new ZipArchive();
        $zipFile = storage_path('app/public/file.zip');

        if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            foreach ($files as $localFilename) {
                $zip->addFile($tempDir . '/' . $localFilename, $localFilename);
            }
            $zip->close();
        } else {
            throw new \Exception('Não foi possível criar o arquivo ZIP');
        }

        return $zipFile;
    }

    private function cleanupTempFiles($files, $tempDir)
    {
        foreach ($files as $localFilename) {
            unlink($tempDir . '/' . $localFilename);
        }
        rmdir($tempDir);
    }
}
