<?php
namespace App\Services;

use App\Repositories\StaffRepositoryInterface;
use App\Models\Staff;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Service for staff management.
 */
class StaffService implements StaffServiceInterface
{
    protected $repo;

    public function __construct(StaffRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    /**
     * List images uploaded by a staff member (paginated).
     */
    public function listUploadedImages(int $staffId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->uploadedImages($staffId, $perPage);
    }

    /**
     * Paginated staff list with optional search.
     */
    public function list(?string $q = null): LengthAwarePaginator
    {
        return $this->repo->all($q);
    }

    public function get(int $id): ?Staff
    {
        return $this->repo->find($id);
    }

    public function create(array $data): Staff
    {
        return $this->repo->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->repo->update($id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}
