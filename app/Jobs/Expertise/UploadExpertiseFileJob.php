<?php

namespace App\Jobs\Expertise;

use App\Enums\S3Prefix;
use App\Models\Expertise;
use App\Services\S3\S3Service;
use Aws\S3\Exception\S3Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadExpertiseFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected S3Service $s3Service;
    public function __construct(protected array $file, protected Expertise $expertise)
    {
        $this->s3Service = new S3Service(S3Prefix::Expertise);
    }

    public function handle(): void
    {
        try {
            $filePath = $this->s3Service->uploadFile($this->file['file']);

            $this->expertise->expertiseFile->create(
                [
                    'file_expertise_type' => $this->expertise->file['file_type'],
                    'path' => $filePath
                ],
            );
        } catch (S3Exception $e) {
            $this->expertise->expertiseFile->create(
                [
                    'file_expertise_type' => $this->file['file_type'],
                    'error_message' => $e->getMessage()
                ],
            );
        };
    }
}
