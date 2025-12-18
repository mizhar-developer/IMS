<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'notes',
        'profile_picture'
    ];

    public function images()
    {
        return $this->hasMany(MedicalImage::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }

    public function billings()
    {
        return $this->hasMany(Billing::class);
    }
}
