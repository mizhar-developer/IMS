<?php
namespace App\Services;

use App\Models\Staff;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface StaffServiceInterface
 */
interface StaffServiceInterface
{
    /**
     * Paginated staff list
     */
    public function list(?string $q = null): LengthAwarePaginator;

    public function get(int $id): ?Staff;

    public function create(array $data): Staff;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
