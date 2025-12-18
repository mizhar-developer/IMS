<?php
namespace App\Repositories;

use App\Models\MedicalImage;

class ImageRepository implements ImageRepositoryInterface
{
    public function create(array $data): MedicalImage
    {
        return MedicalImage::create($data);
    }

    public function find(int $id): ?MedicalImage
    {
        return MedicalImage::find($id);
    }

    public function forPatient(int $patientId)
    {
        return MedicalImage::where('patient_id', $patientId)->latest()->get();
    }

    public function list($q = null, $perPage = 10)
    {
        return MedicalImage::with(['patient', 'uploader'])
            ->when($q, function ($query, $q) {
                $q = trim($q);
                $query->where('type', 'ILIKE', "%{$q}%")
                    ->orWhereRaw("metadata->>'original_name' ILIKE ?", ["%{$q}%"])
                    ->orWhereHas('patient', function ($q2) use ($q) {
                        $q2->where('first_name', 'ILIKE', "%{$q}%")->orWhere('last_name', 'ILIKE', "%{$q}%");
                    })
                    ->orWhereHas('uploader', function ($q2) use ($q) {
                        $q2->where('first_name', 'ILIKE', "%{$q}%")->orWhere('last_name', 'ILIKE', "%{$q}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
