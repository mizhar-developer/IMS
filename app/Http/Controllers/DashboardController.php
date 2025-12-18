<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardServiceInterface;

class DashboardController extends Controller
{
    protected $service;

    public function __construct(DashboardServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $counts = $this->service->getCounts();
        $recentImages = $this->service->recentImages(6);
        $recentDiagnoses = $this->service->recentDiagnoses(6);
        $staffByRole = $this->service->staffByRole();
        $imagesTimeline = $this->service->imagesTimeline(14);

        $user = auth()->user();
        $role = $user->role ?? 'guest';

        $roleViewMap = [
            'admin' => 'admin',
            'doctor' => 'doctor',
            'radiologist' => 'radiologist',
            'accountant' => 'accountant',
            'receptionist' => 'receptionist',
            'manager' => 'manager',
            'patient' => 'patient',
        ];

        $viewName = 'dashboard.' . ($roleViewMap[$role] ?? 'default');
        if (!view()->exists($viewName)) {
            $viewName = 'dashboard.default';
        }

        return view($viewName, compact('counts', 'recentImages', 'recentDiagnoses', 'staffByRole', 'imagesTimeline', 'user'));
    }
}
