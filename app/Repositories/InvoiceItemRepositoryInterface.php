<?php
namespace App\Repositories;

use App\Models\InvoiceItem;

interface InvoiceItemRepositoryInterface
{
    public function deleteByBilling(int $billingId): int;

    public function insertMany(array $items): bool;

    public function listByBilling(int $billingId);
}
