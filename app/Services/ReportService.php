<?php
namespace App\Services;

use App\Models\Diagnosis;
use App\Models\MedicalImage;
use App\Models\Billing;
use Illuminate\Support\Collection;

class ReportService implements ReportServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDiagnoses(string $start, string $end): Collection
    {
        return Diagnosis::with(['patient', 'doctor'])
            ->whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary(string $start, string $end): array
    {
        $diagnoses = $this->getDiagnoses($start, $end);
        $imagesCount = MedicalImage::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->count();
        $billingSum = Billing::whereBetween('created_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->sum('amount');

        return [
            'diagnoses' => $diagnoses,
            'imagesCount' => $imagesCount,
            'billingSum' => $billingSum,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function exportRows($diagnoses): array
    {
        return collect($diagnoses)->map(function ($d) {
            return [
                'id' => $d->id,
                'patient' => $d->patient?->first_name . ' ' . $d->patient?->last_name,
                'doctor' => $d->doctor?->first_name . ' ' . $d->doctor?->last_name,
                'disease_type' => $d->disease_type,
                'created_at' => $d->created_at->toDateTimeString(),
            ];
        })->toArray();
    }
}
