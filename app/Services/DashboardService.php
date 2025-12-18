<?php
namespace App\Services;

use App\Models\Patient;
use App\Models\MedicalImage;
use App\Models\Diagnosis;
use App\Models\User;
use App\Models\Billing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class DashboardService implements DashboardServiceInterface
{
    public function getCounts(): array
    {
        return [
            'patients' => Patient::count(),
            'staff' => User::count(),
            'images' => MedicalImage::count(),
            'diagnoses' => Diagnosis::count(),
            'billings' => Billing::count(),
        ];
    }

    /**
     * @param int $limit
     * @return Collection|\App\Models\MedicalImage[]
     */
    public function recentImages(int $limit = 6): Collection
    {
        return MedicalImage::latest()->limit($limit)->get();
    }

    /**
     * @param int $limit
     * @return Collection|\App\Models\Diagnosis[]
     */
    public function recentDiagnoses(int $limit = 6): Collection
    {
        return Diagnosis::latest()->limit($limit)->get();
    }

    /**
     * @return array<string,int>
     */
    public function staffByRole(): array
    {
        return User::select('role', DB::raw('count(*) as cnt'))->groupBy('role')->pluck('cnt', 'role')->toArray();
    }

    public function imagesTimeline(int $days = 14): array
    {
        $imagesPerDay = MedicalImage::select(DB::raw('DATE(created_at) as day'), DB::raw('count(*) as cnt'))
            ->where('created_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->pluck('cnt', 'day')
            ->toArray();

        $daysColl = collect();
        for ($i = $days - 1; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $daysColl->push($d);
        }
        return $daysColl->mapWithKeys(function ($d) use ($imagesPerDay) {
            return [$d => $imagesPerDay[$d] ?? 0];
        })->toArray();
    }
}
