<?php
namespace App\Services;

use App\Models\MedicalImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Interface ImageServiceInterface
 * Handles image upload and retrieval operations.
 */
interface ImageServiceInterface
{
    /**
     * Upload an image and return stored model.
     * @param array $data
     * @return MedicalImage
     */
    public function upload(array $data): MedicalImage;

    /**
     * List images for a patient (non-paginated collection).
     * @return Collection
     */
    public function listForPatient(int $patientId): Collection;

    /**
     * Paginated listing with optional search.
     */
    public function list(?string $q = null, int $perPage = 10): LengthAwarePaginator;
}
