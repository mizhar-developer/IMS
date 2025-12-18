<?php
namespace App\Repositories;

use App\Models\Diagnosis;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface DiagnosisRepositoryInterface
{
    public function create(array $data): Diagnosis;

    public function update(int $id, array $data): ?Diagnosis;

    public function find(int $id): ?Diagnosis;

    public function list(?string $q = null, int $perPage = 10): LengthAwarePaginator;

    public function delete(int $id): bool;
}
