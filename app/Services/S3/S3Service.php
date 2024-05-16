<?php

namespace App\Services\S3;

use App\Enums\S3Prefix;
use Illuminate\Support\Facades\Storage;
use Aws\S3\Exception\S3Exception;

class S3Service
{
    public function __construct(private readonly S3Prefix $prefix)
    {
    }

    public function uploadFile($file)
    {
        try {
            return Storage::putFile($this->prefix->value, $file, 'public');
        } catch (S3Exception $e) {
            return $e->getMessage();
        }
    }
}
