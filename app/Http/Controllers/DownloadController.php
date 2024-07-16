<?php

namespace App\Http\Controllers;

use Aws\S3\S3Client;
use ZipArchive;

class DownloadController extends Controller
{
    public function download()
    {
        $s3Client = new S3Client([
            'region'  => env('AWS_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' => [
                'key'    => env('AWS_ACCESS_KEY_ID'),
                'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ],
        ]);

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
                $s3Client->getObject([
                    'Bucket' => env('AWS_BUCKET'),
                    'Key'    => $s3Key,
                    'SaveAs' => $tempDir . '/' . $localFilename,
                ]);
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
