<?php
namespace App\Repositories;

use App\Models\Staff;

class StaffRepository implements StaffRepositoryInterface
{
    public function all($q = null)
    {
        return Staff::when($q, function ($query, $q) {
            $q = trim($q);
            $query->where(function ($s) use ($q) {
                $s->where('first_name', 'ILIKE', "%{$q}%")
                    ->orWhere('last_name', 'ILIKE', "%{$q}%")
                    ->orWhere('role', 'ILIKE', "%{$q}%")
                    ->orWhere('email', 'ILIKE', "%{$q}%")
                    ->orWhere('phone', 'ILIKE', "%{$q}%");
            });
        })->latest()->paginate(10);
    }

    public function find(int $id): ?Staff
    {
        return Staff::find($id);
    }

    public function uploadedImages(int $staffId, int $perPage = 10)
    {
        return \App\Models\MedicalImage::where('uploaded_by', $staffId)->latest()->paginate($perPage);
    }

    public function create(array $data): Staff
    {
        return Staff::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $s = $this->find($id);
        if (!$s)
            return false;
        return $s->update($data);
    }

    public function delete(int $id): bool
    {
        $s = $this->find($id);
        if (!$s)
            return false;
        return (bool) $s->delete();
    }
}
