<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiagnosisComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'diagnosis_id',
        'user_type',
        'user_id',
        'content',
        'image_id',
    ];

    public function diagnosis()
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function image()
    {
        return $this->belongsTo(MedicalImage::class, 'image_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'user_id');
    }
}
