<?php
namespace App\Services;

use App\Models\Diagnosis;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface DiagnosisServiceInterface
 * Encapsulates diagnosis business logic.
 */
interface DiagnosisServiceInterface
{
    public function create(array $data): Diagnosis;

    public function update(int $id, array $data): ?Diagnosis;

    /**
     * Get diagnosis with relations.
     */
    public function get(int $id): ?Diagnosis;

    /**
     * Paginated list of diagnoses; optional search.
     */
    public function list(?string $q = null, int $perPage = 10): LengthAwarePaginator;

    public function delete(int $id): bool;
}
