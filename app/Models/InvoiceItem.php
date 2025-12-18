<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_id',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }
}
