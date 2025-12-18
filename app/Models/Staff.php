<?php

namespace App\Models;

class Staff extends User
{
    // Keep a lightweight compatibility model so existing code that
    // referenced App\Models\Staff continues to work while users
    // are stored in the `users` table.

    protected $table = 'users';

    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'email',
        'phone',
        'notes',
        'profile_picture',
        'password'
    ];

    public function uploadedImages()
    {
        return $this->hasMany(MedicalImage::class, 'uploaded_by');
    }
}
