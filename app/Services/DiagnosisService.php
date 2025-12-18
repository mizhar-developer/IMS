<?php
namespace App\Services;

use App\Models\Diagnosis;
use App\Repositories\DiagnosisRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Services\BillingServiceInterface;

class DiagnosisService implements DiagnosisServiceInterface
{
    protected DiagnosisRepositoryInterface $repo;
    protected BillingServiceInterface $billingService;

    public function __construct(DiagnosisRepositoryInterface $repo, BillingServiceInterface $billingService)
    {
        $this->repo = $repo;
        $this->billingService = $billingService;
    }
    /**
     * Create a diagnosis record.
     * @param array $data
     * @return Diagnosis
     */
    public function create(array $data): Diagnosis
    {
        // data: patient_id, image_id, doctor_id, disease_type, report, confidence
        // Basic validation and simple logic; in a real system this could call ML services
        $payload = [
            'patient_id' => $data['patient_id'],
            // 'image_id' => $data['image_id'] ?? null,
            'doctor_id' => $data['doctor_id'] ?? null,
            'disease_type' => $data['disease_type'] ?? 'unspecified',
            'report' => $data['report'] ?? null,
            'confidence' => $data['confidence'] ?? null,
        ];

        $diagnosis = $this->repo->create($payload);

        // Try to (re)calculate billing for patient when a diagnosis is created.
        try {
            if (!empty($payload['patient_id'])) {
                $this->billingService->calculateForPatient($payload['patient_id']);
            }
        } catch (\Throwable $e) {
            // do not fail diagnosis creation if billing update fails; log in production
            \Log::error('Billing update failed during diagnosis creation: ' . $e->getMessage());
        }

        return $diagnosis;
    }

    public function update(int $id, array $data): ?Diagnosis
    {
        $payload = [
            // 'image_id' => $data['image_id'] ?? null,
            'doctor_id' => $data['doctor_id'] ?? null,
            'disease_type' => $data['disease_type'] ?? null,
            'report' => $data['report'] ?? null,
            'confidence' => $data['confidence'] ?? null,
        ];

        return $this->repo->update($id, array_filter($payload, function ($v) {
            return $v !== null;
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $id): ?Diagnosis
    {
        return $this->repo->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    /**
     * {@inheritdoc}
     */
    public function list(?string $q = null, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->list($q, $perPage);
    }
}
