<?php
namespace App\Repositories;

use App\Models\InvoiceItem;
use Illuminate\Support\Collection;

class InvoiceItemRepository implements InvoiceItemRepositoryInterface
{
    public function deleteByBilling(int $billingId): int
    {
        return InvoiceItem::where('billing_id', $billingId)->delete();
    }

    public function insertMany(array $items): bool
    {
        if (empty($items))
            return false;
        return (bool) InvoiceItem::insert($items);
    }

    public function listByBilling(int $billingId)
    {
        return InvoiceItem::where('billing_id', $billingId)->get();
    }
}
