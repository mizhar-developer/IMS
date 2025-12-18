<?php
namespace App\Repositories;

use App\Models\Diagnosis;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DiagnosisRepository implements DiagnosisRepositoryInterface
{
    public function create(array $data): Diagnosis
    {
        return Diagnosis::create($data);
    }

    public function update(int $id, array $data): ?Diagnosis
    {
        $d = Diagnosis::find($id);
        if (!$d)
            return null;
        $d->fill($data);
        $d->save();
        return $d;
    }

    public function find(int $id): ?Diagnosis
    {
        return Diagnosis::with(['patient', 'doctor', 'images'])->find($id);
    }

    public function list(?string $q = null, int $perPage = 10): LengthAwarePaginator
    {
        return Diagnosis::with(['patient', 'doctor', 'images'])
            ->when($q, function ($query, $q) {
                $q = trim($q);
                $query->where('disease_type', 'ILIKE', "%{$q}%")
                    ->orWhere('report', 'ILIKE', "%{$q}%")
                    ->orWhereHas('patient', function ($p) use ($q) {
                        $p->where('first_name', 'ILIKE', "%{$q}%")->orWhere('last_name', 'ILIKE', "%{$q}%");
                    })
                    ->orWhereHas('doctor', function ($d) use ($q) {
                        $d->where('first_name', 'ILIKE', "%{$q}%")->orWhere('last_name', 'ILIKE', "%{$q}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function delete(int $id): bool
    {
        $d = Diagnosis::find($id);
        if (!$d)
            return false;
        // deleting will cascade if model relationships configured; otherwise remove images separately
        return (bool) $d->delete();
    }
}
