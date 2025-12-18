<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diagnosis extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        // 'image_id',
        'doctor_id',
        'disease_type',
        'report',
        'confidence'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // public function image()
    // {
    //     return $this->belongsTo(MedicalImage::class, 'image_id');
    // }

    public function images()
    {
        return $this->hasMany(MedicalImage::class, 'diagnosis_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
