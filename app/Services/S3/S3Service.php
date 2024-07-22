<?php

namespace App\Services\S3;

use App\Enums\S3Prefix;
use Illuminate\Support\Facades\Storage;
use Aws\S3\Exception\S3Exception;
use Illuminate\Support\Facades\Log;


class S3Service
{
    public function __construct(private S3Prefix $prefix)
    {
    }

    public static function getUrl(string $filePath): string
    {
        return Storage::url($filePath);
    }

    public function uploadFile(mixed $file, ?S3Prefix $prefix = null)
    {
        try {
            return Storage::putFile($prefix ?? $this->prefix->value, $file, 'public');
        } catch (S3Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }

    public static function filenameSanitizer(string $unsafeFilename)
    {
        $dangerousCharacters = array(" ", '"', "'", "&", "/", "\\", "?", "#");

        $safe_filename = str_replace($dangerousCharacters, '_', $unsafeFilename);

        return $safe_filename;
    }

    public function getFileUrl($file): string
    {
        return Storage::url($file);
    }
}
