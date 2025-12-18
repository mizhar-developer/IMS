<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\PatientRepositoryInterface;
use App\Repositories\PatientRepository;
use App\Repositories\StaffRepositoryInterface;
use App\Repositories\StaffRepository;
use App\Repositories\ImageRepositoryInterface;
use App\Repositories\ImageRepository;

use App\Services\PatientServiceInterface;
use App\Services\PatientService;
use App\Services\StaffServiceInterface;
use App\Services\StaffService;
use App\Services\ImageServiceInterface;
use App\Services\ImageService;
use App\Services\StorageServiceInterface;
use App\Services\S3StorageService;
use App\Services\DiagnosisServiceInterface;
use App\Services\DiagnosisService;
use App\Repositories\DiagnosisRepositoryInterface;
use App\Repositories\DiagnosisRepository;
use App\Repositories\InvoiceItemRepositoryInterface;
use App\Repositories\InvoiceItemRepository;
use App\Repositories\PaymentRepositoryInterface;
use App\Repositories\PaymentRepository;
use App\Services\BillingServiceInterface;
use App\Services\DashboardServiceInterface;
use App\Services\DashboardService;
use App\Services\ReportServiceInterface;
use App\Services\ReportService;
use App\Services\BillingService;
use Illuminate\Routing\Router;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repository bindings
        $this->app->bind(PatientRepositoryInterface::class, PatientRepository::class);
        $this->app->bind(StaffRepositoryInterface::class, StaffRepository::class);
        $this->app->bind(ImageRepositoryInterface::class, ImageRepository::class);

        // Service bindings
        $this->app->bind(PatientServiceInterface::class, PatientService::class);
        $this->app->bind(StaffServiceInterface::class, StaffService::class);
        $this->app->bind(ImageServiceInterface::class, ImageService::class);
        $this->app->bind(StorageServiceInterface::class, S3StorageService::class);
        $this->app->bind(DiagnosisServiceInterface::class, DiagnosisService::class);
        // Diagnosis repository binding
        $this->app->bind(DiagnosisRepositoryInterface::class, DiagnosisRepository::class);
        $this->app->bind(InvoiceItemRepositoryInterface::class, InvoiceItemRepository::class);
        $this->app->bind(PaymentRepositoryInterface::class, PaymentRepository::class);
        $this->app->bind(BillingServiceInterface::class, BillingService::class);
        // Billing repository binding
        $this->app->bind(\App\Repositories\BillingRepositoryInterface::class, \App\Repositories\BillingRepository::class);
        $this->app->bind(ReportServiceInterface::class, ReportService::class);
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure role middleware alias is registered (for newer Laravel kernel behavior)
        $this->app->make(Router::class)->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
    }
}
