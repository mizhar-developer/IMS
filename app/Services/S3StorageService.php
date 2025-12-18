<?php
namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class S3StorageService implements StorageServiceInterface
{
    /**
     * Store the uploaded file to S3 and return its path.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string
     */
    public function storeFile(UploadedFile $file, string $directory): string
    {
        $path = Storage::disk('s3')->putFile($directory, $file, 'private');
        return (string) $path;
    }
}
