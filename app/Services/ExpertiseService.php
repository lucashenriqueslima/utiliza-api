<?php

namespace App\Services;

use App\Enums\S3Prefix;
use App\Models\Expertise;
use App\Services\S3\S3Service;
use Illuminate\Http\Request;
use Laravel\Octane\Facades\Octane;

class ExpertiseService
{
    private S3Service $s3Service;
    public function __construct()
    {
        $this->s3Service = new S3Service(S3Prefix::Expertise);
    }
    public function verifyExpertiseMainFormFiles()
    {
    }
    public function handleExpertiseMainFormFiles($files, Expertise $expertise): void
    {

        $audioPath = $this->s3Service->uploadFile($files['audio']['file']);
        $videoPath = $this->s3Service->uploadFile($files['video']['file']);
        $this->uploadImages($files['images'], $expertise);


        // [$audioPath, $videoPath, $imagesPath] = Octane::concurrently(
        //     [
        //         fn () => $this->s3Service->uploadFile($files['audio']['file']),
        //         fn () => $this->s3Service->uploadFile($files['video']['file']),
        //         fn () => $this->uploadImages($files['images']),

        //     ]
        // );

        $expertise->files()->createMany([
            ['file_expertise_type' => $files['audio']['expertise_file_type'], 'path' => $audioPath],
            ['file_expertise_type' => $files['video']['expertise_file_type'], 'path' => $videoPath],
        ]);
    }

    public function uploadImages(array $images, Expertise $expertise): void
    {
        $imagePath = '';
        foreach ($images as $image) {
            $imagePath = $this->s3Service->uploadFile($image['file']);

            $expertise->files()->create(
                [
                    'file_expertise_type' => $image['expertise_file_type'],
                    'path' => $imagePath
                ]
            );
        }
    }

    public function storeExpertiseFile(string $path)
    {
    }
}
