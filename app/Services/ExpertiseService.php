<?php

namespace App\Services;

use App\Enums\S3Prefix;
use App\Jobs\Expertise\UploadExpertiseFileJob;
use App\Models\Expertise;
use App\Services\S3\S3Service;
use Aws\S3\Exception\S3Exception;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class ExpertiseService
{

    public static function handleExpertiseThirdPartyFormTexts(Request $request, Expertise $expertise, string $callId)
    {
        $thirdParty = $expertise->thirdParty()->create([
            'call_id' => $callId,
            'name' => $request->name,
            'cpf' => $request->cpf,
            'phone' => $request->phone,
        ]);


        $thirdParty->car()->create([
            'plate' => $request->plate,
        ]);
    }

    public static function handleExpertiseMainFormFiles(Request $request, Expertise $expertise): void
    {
        $s3service = new S3Service(S3Prefix::Expertise);

        if ($request->video) {
            self::uploadFile($request->video, $expertise, $s3service);
        }

        foreach ($request->images as $image) {
            self::uploadFile($image, $expertise, $s3service);
        }
    }

    public static function handleExpertiseSecondaryFormFiles(Request $request, Expertise $expertise): void
    {
        $s3service = new S3Service(S3Prefix::Expertise);


        self::uploadFile($request->video_360, $expertise, $s3service);

        self::uploadFile($request->biker_observation, $expertise, $s3service);

        foreach ($request->witness_reports as $witness_report) {
            if ($witness_report['file']) {
                self::uploadFile($witness_report, $expertise, $s3service);
            }
        }

        foreach ($request->commercial_facades as $commercial_facade) {
            if ($commercial_facade['file']) {
                self::uploadFile($commercial_facade, $expertise, $s3service);
            }
        }
    }

    private static function uploadFile(array $file, Expertise $expertise, S3Service $s3Service): void
    {
        try {
            $filePath = $s3Service->uploadFile($file['file']);

            $expertise->files()->create(
                [
                    'file_expertise_type' => $file['file_type'],
                    'path' => $filePath
                ],
            );
        } catch (S3Exception $e) {
            $expertise->files()->create(
                [
                    'file_expertise_type' => $file['file_type'],
                    'error_message' => $e->getMessage()
                ],
            );
        };
    }
}
