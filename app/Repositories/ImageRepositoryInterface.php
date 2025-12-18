<?php
namespace App\Repositories;

use App\Models\MedicalImage;

interface ImageRepositoryInterface
{
    public function create(array $data): MedicalImage;
    public function find(int $id): ?MedicalImage;
    public function forPatient(int $patientId);
}
