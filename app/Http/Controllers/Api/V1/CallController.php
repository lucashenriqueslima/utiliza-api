<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CallStatus;
use App\Enums\ExpertiseFileValidationErrorStatus;
use App\Enums\ExpertisePersonType;
use App\Enums\ExpertiseStatus;
use App\Enums\ExpertiseType;
use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Expertise;
use App\Models\ExpertiseFileValidationError;
use App\Services\CallService;
use App\Services\S3\S3Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Laravel\Octane\Facades\Octane;
use ZipArchive;

class CallController extends Controller
{
    public function update(Request $request, Call $call)
    {

        if ($request->status === CallStatus::WaitingValidation->value) {

            $expertisesIdToUpdate = [];

            Log::info($request->main_expertise_group);

            $call->expertises->each(function ($expertise) use (&$expertisesIdToUpdate, $request) {
                if (
                    !$expertise->status &&
                    $expertise->type == ExpertiseType::Main &&
                    $expertise->main_expertise_group == $request->main_expertise_group
                ) {
                    $expertisesIdToUpdate[] = $expertise->id;
                }
            });

            Expertise::whereIn('id', $expertisesIdToUpdate)
                ->update(['status' => ExpertiseStatus::Waiting]);
        }



        $call->update(['status' => $request->status]);
    }

    public function showStatus(string $id)
    {
        return
            response()->json([
                'status' => Call::select('status')
                    ->find($id)
                    ->status,
            ]);
    }

    public function download(Call $call)
    {

        $files = $call->expertises->mapWithKeys(function ($expertise) {
            return $expertise->files->mapWithKeys(function ($file) use ($expertise) {
                $involvedName = $expertise->person_type == ExpertisePersonType::Associate ? '' : $expertise->thirdParty->name ?? '';
                $involvedPerson = $expertise->person_type ? $expertise->person_type->getLabel() : '';

                return [$file->path => S3Service::filenameSanitizer($involvedPerson . '_' . $involvedName . '_' . $file->file_expertise_type->getLabel() . '_' . $file->id . '.' . pathinfo($file->path, PATHINFO_EXTENSION))];
            });
        })->toArray();


        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        try {

            $tasks = [];

            foreach ($files as $file => $localFilename) {
                $tasks[] = function () use ($file, $localFilename, $tempDir) {
                    try {
                        $fileContents = Storage::get($file);
                        file_put_contents($tempDir . '/' . $localFilename, $fileContents);
                    } catch (\Exception $e) {
                        Log::error('Erro ao baixar arquivo: ' . $e->getMessage());
                        return;
                    }
                };
            }

            Octane::concurrently($tasks, 20000);

            $zipFile = CallService::createZipArchive($files, $tempDir, $call->id);

            CallService::cleanupTempFiles($files, $tempDir);

            return response()->download($zipFile)->deleteFileAfterSend(true);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao processar o download: ' . $e->getMessage()], 500);
        }
    }
}
