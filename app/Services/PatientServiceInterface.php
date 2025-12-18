<?php
namespace App\Services;

use App\Models\Patient;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface PatientServiceInterface
 * Service abstraction for patient operations.
 */
interface PatientServiceInterface
{
    /**
     * List patients with optional search query.
     * @param string|null $q
     * @return LengthAwarePaginator
     */
    public function list(?string $q = null): LengthAwarePaginator;

    /**
     * Get a single patient by id.
     */
    public function get(int $id): ?Patient;

    /**
     * Create a patient from data array.
     */
    public function create(array $data): Patient;

    /**
     * Update patient record.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a patient.
     */
    public function delete(int $id): bool;
}
