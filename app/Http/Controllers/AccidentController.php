<?php

namespace App\Http\Controllers;

use App\Models\Accident;
use App\Services\S3\S3Service;
use App\Services\Zip\ZipService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Octane\Facades\Octane;
use Illuminate\Support\Arr;

class AccidentController extends Controller
{
    public function download(string $accidentId, ZipService $zipService)
    {
        try {
            $accident = Accident::with(['images' => function ($query) {
                $query->where('is_current', true);
            }])
                ->findOrFail($accidentId);

            $files = $accident->images->mapWithKeys(function ($image, $index) {
                return [
                    $index => [
                        's3Path' => $image->path,
                        'localFilename' => S3Service::filenameSanitizer($image->type->getLabel() . '.' . pathinfo($image->path, PATHINFO_EXTENSION))
                    ]
                ];
            })->toArray();

            $tempDir = $zipService->createTempDirIfDontExist('app/temp');

            $tasks = [];
            foreach ($files as $file) {
                $tasks[] = function () use ($file, $tempDir) {
                    try {
                        $fileContents = Storage::get($file['s3Path']);
                        file_put_contents($tempDir . '/' . $file['localFilename'], $fileContents);
                    } catch (\Exception $e) {
                        Log::error('Erro ao baixar arquivo: ' . $e->getMessage());
                        return;
                    }
                };
            }

            Octane::concurrently($tasks, 20000);

            $localFilenames = Arr::pluck($files, 'localFilename');

            $zipFile = $zipService->createZipArchive('accident.zip', $localFilenames, $tempDir);

            $zipService->cleanupTempFiles($localFilenames, $tempDir);

            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (ModelNotFoundException $e) {
            abort(404);
        } catch (\Exception $e) {
            return dd($e);
        }
    }
}
