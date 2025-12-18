<?php
namespace App\Repositories;

use App\Models\Patient;

class PatientRepository implements PatientRepositoryInterface
{
    public function all($q = null)
    {
        return Patient::when($q, function ($query, $q) {
            $q = trim($q);
            $query->where(function ($q2) use ($q) {
                $q2->where('first_name', 'ILIKE', "%{$q}%")
                    ->orWhere('last_name', 'ILIKE', "%{$q}%")
                    ->orWhere('email', 'ILIKE', "%{$q}%")
                    ->orWhere('phone', 'ILIKE', "%{$q}%");
            });
        })->latest()->paginate(10);
    }

    public function find(int $id): ?Patient
    {
        return Patient::find($id);
    }

    public function images(int $patientId, int $perPage = 10)
    {
        return \App\Models\MedicalImage::where('patient_id', $patientId)->latest()->paginate($perPage);
    }

    public function diagnoses(int $patientId, int $perPage = 10)
    {
        return \App\Models\Diagnosis::where('patient_id', $patientId)->latest()->paginate($perPage);
    }

    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $p = $this->find($id);
        if (!$p)
            return false;
        return $p->update($data);
    }

    public function delete(int $id): bool
    {
        $p = $this->find($id);
        if (!$p)
            return false;
        return (bool) $p->delete();
    }
}
