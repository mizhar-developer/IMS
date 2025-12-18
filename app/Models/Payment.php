<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'billing_id',
        'amount',
        'method',
        'reference',
        'paid_at',
    ];

    protected $dates = ['paid_at'];

    public function billing()
    {
        return $this->belongsTo(Billing::class);
    }
}
