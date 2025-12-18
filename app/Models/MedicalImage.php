<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalImage extends Model
{
    use HasFactory;

    protected $table = 'medical_images';

    protected $fillable = [
        'patient_id',
        'uploaded_by',
        'diagnosis_id',
        'type',
        's3_path',
        'mime',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
