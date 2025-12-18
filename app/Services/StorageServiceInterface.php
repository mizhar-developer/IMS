<?php
namespace App\Services;

use Illuminate\Http\UploadedFile;

interface StorageServiceInterface
{
    /**
     * Store uploaded file and return storage path.
     *
     * @param UploadedFile $file Uploaded file instance from request
     * @param string $directory Destination directory on the storage disk
     * @return string The stored file path
     */
    public function storeFile(UploadedFile $file, string $directory): string;
}
