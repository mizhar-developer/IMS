<?php
namespace App\Services;

use App\Repositories\PatientRepositoryInterface;
use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PatientService implements PatientServiceInterface
{
    protected $repo;

    public function __construct(PatientRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * {@inheritdoc}
     */
    public function list(?string $q = null): LengthAwarePaginator
    {
        return $this->repo->all($q);
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $id): ?Patient
    {
        return $this->repo->find($id);
    }

    /**
     * List images for a patient (paginated)
     */
    public function listImages(int $patientId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->images($patientId, $perPage);
    }

    /**
     * List diagnoses for a patient (paginated)
     */
    public function listDiagnoses(int $patientId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->diagnoses($patientId, $perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): Patient
    {
        return $this->repo->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
