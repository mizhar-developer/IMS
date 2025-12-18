<?php
namespace App\Repositories;

use App\Models\Billing;
use Illuminate\Support\Collection;

class BillingRepository implements BillingRepositoryInterface
{
    public function findUnpaidByPatient(int $patientId): ?Billing
    {
        return Billing::where('patient_id', $patientId)->where('paid', false)->first();
    }

    public function findUnpaidByPatientForUpdate(int $patientId): ?Billing
    {
        return Billing::where('patient_id', $patientId)->where('paid', false)->lockForUpdate()->first();
    }

    public function create(array $data): Billing
    {
        return Billing::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $b = Billing::find($id);
        if (!$b)
            return false;
        return $b->update($data);
    }

    public function find(int $id): ?Billing
    {
        return Billing::with('patient')->find($id);
    }

    public function setPaid(int $id, bool $paid): Billing
    {
        $b = Billing::findOrFail($id);
        $b->paid = $paid;
        $b->save();
        return $b;
    }

    public function all(): Collection
    {
        return Billing::with('patient')->orderBy('created_at', 'desc')->get();
    }
}
