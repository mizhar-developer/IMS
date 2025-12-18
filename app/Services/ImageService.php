<?php
namespace App\Services;

use App\Repositories\ImageRepositoryInterface;
use App\Services\StorageServiceInterface;
use App\Services\BillingServiceInterface;
use App\Models\MedicalImage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ImageService implements ImageServiceInterface
{
    protected $repo;
    protected $storage;
    protected $billing;

    public function __construct(ImageRepositoryInterface $repo, StorageServiceInterface $storage, BillingServiceInterface $billing)
    {
        $this->repo = $repo;
        $this->storage = $storage;
        $this->billing = $billing;
    }

    /**
     * Upload a medical image and persist metadata.
     * @param array $data expected keys: file, patient_id, uploaded_by, type, diagnosis_id
     */
    public function upload(array $data): MedicalImage
    {
        // expects keys: file, patient_id, uploaded_by, type
        $file = $data['file'];
        // Choose directory based on context: diagnosis-specific images go under diagnoses/,
        // otherwise use patients/{id}/images
        if (!empty($data['diagnosis_id'])) {
            $dir = 'diagnoses/' . $data['diagnosis_id'] . '/images';
        } elseif (!empty($data['type']) && strtolower($data['type']) === 'diagnosis') {
            $dir = 'diagnoses/' . ($data['patient_id'] ?? 'unknown') . '/images';
        } else {
            $dir = 'patients/' . $data['patient_id'] . '/images';
        }

        $path = $this->storage->storeFile($file, $dir);

        $entry = [
            'patient_id' => $data['patient_id'],
            'uploaded_by' => $data['uploaded_by'] ?? (auth()->check() ? auth()->id() : null),
            'diagnosis_id' => $data['diagnosis_id'] ?? null,
            'type' => $data['type'] ?? 'unknown',
            's3_path' => $path,
            'mime' => $file->getClientMimeType(),
            'metadata' => [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
            ],
        ];

        $image = $this->repo->create($entry);

        // If this image is associated with a diagnosis, update billing for the patient.
        try {
            if (!empty($entry['diagnosis_id']) && !empty($entry['patient_id'])) {
                $this->billing->calculateForPatient((int) $entry['patient_id']);
            }
        } catch (\Throwable $e) {
            // Log billing errors but don't fail the upload
            Log::error('Billing update failed after image upload: ' . $e->getMessage(), ['exception' => $e]);
        }

        return $image;
    }
    /**
     * Paginated list of images.
     */
    public function list(?string $q = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->list($q, $perPage);
    }

    /**
     * Collection of images for a patient.
     */
    public function listForPatient(int $patientId): Collection
    {
        return $this->repo->forPatient($patientId);
    }
}
