<?php
namespace App\Repositories;

use App\Models\Staff;

interface StaffRepositoryInterface
{
    // public function all();
    public function all($q = null);
    public function find(int $id): ?Staff;
    public function create(array $data): Staff;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
}
