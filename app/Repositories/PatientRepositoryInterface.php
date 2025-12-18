<?php
namespace App\Repositories;

use App\Models\Patient;

interface PatientRepositoryInterface
{
    public function all($q = null);
    public function find(int $id): ?Patient;
    public function create(array $data): Patient;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
