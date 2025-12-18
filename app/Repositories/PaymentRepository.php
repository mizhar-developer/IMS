<?php
namespace App\Repositories;

use App\Models\Payment;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function findByBilling(int $billingId)
    {
        return Payment::where('billing_id', $billingId)->get();
    }
}
