<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;

// Ensure role middleware alias is available when routes are registered
app('router')->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);

Route::get('/', function () {
    return view('home');
});

// Authentication
use App\Http\Controllers\AuthController;
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Patients: accessible by doctors, receptionists, managers (admin allowed by middleware)
    Route::resource('patients', PatientController::class)->middleware('role:doctor,receptionist,manager');

    // Staff management - admin and manager
    Route::resource('staff', StaffController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'])->middleware('role:admin,manager');

    // Image listing (upload merged into diagnoses)
    Route::get('images', [ImageController::class, 'index'])->name('images.index')->middleware('role:radiologist,doctor,admin');

    // Diagnoses - doctor, radiologist, admin
    Route::get('diagnoses/create', [DiagnosisController::class, 'create'])->name('diagnoses.create')->middleware('role:doctor,radiologist');
    Route::post('diagnoses', [DiagnosisController::class, 'store'])->name('diagnoses.store')->middleware('role:doctor,radiologist');
    Route::get('diagnoses', [DiagnosisController::class, 'index'])->name('diagnoses.index')->middleware('role:doctor,radiologist,admin');
    Route::get('diagnoses/{id}', [DiagnosisController::class, 'show'])->name('diagnoses.show')->middleware('role:doctor,radiologist,admin,patient');
    Route::get('diagnoses/{id}/edit', [DiagnosisController::class, 'edit'])->name('diagnoses.edit')->middleware('role:doctor,radiologist');
    Route::put('diagnoses/{id}', [DiagnosisController::class, 'update'])->name('diagnoses.update')->middleware('role:doctor,radiologist');
    Route::post('diagnoses/{id}/comments', [DiagnosisController::class, 'storeComment'])->name('diagnoses.comments.store')->middleware('auth');
    Route::delete('diagnoses/{id}', [DiagnosisController::class, 'destroy'])->name('diagnoses.destroy')->middleware('role:doctor,radiologist,admin');

    // Billing - accountant and manager (admin allowed)
    Route::get('billing/patient/{id}', [BillingController::class, 'showForPatient'])->name('billing.patient')->middleware('role:accountant,manager');
    // Billing index for accountants, managers, admins
    Route::get('billing', [BillingController::class, 'index'])->name('billing.index')->middleware('role:accountant,manager');
    Route::get('billing/create', [BillingController::class, 'create'])->name('billing.create')->middleware('role:accountant,manager');
    Route::post('billing/generate', [BillingController::class, 'generateForPatient'])->name('billing.generate_for_patient')->middleware('role:accountant,manager');
    Route::post('billing/{id}/generate', [BillingController::class, 'generate'])->name('billing.generate')->middleware('role:accountant,manager');
    Route::get('billing/export', [BillingController::class, 'export'])->name('billing.export')->middleware('role:accountant,manager');
    Route::get('billing/{id}', [BillingController::class, 'show'])->name('billing.show')->middleware('role:accountant,manager');
    Route::get('billing/{id}/edit', [BillingController::class, 'edit'])->name('billing.edit')->middleware('role:accountant,manager');
    Route::post('billing/{id}/adjust', [BillingController::class, 'adjust'])->name('billing.adjust')->middleware('role:accountant,manager');
    // Mark invoice unpaid (allows reversing paid status)
    Route::post('billing/{id}/mark-unpaid', [BillingController::class, 'markUnpaid'])->name('billing.mark_unpaid')->middleware('role:accountant,manager');
    // Delete invoice (only unpaid invoices allowed)
    Route::delete('billing/{id}', [BillingController::class, 'destroy'])->name('billing.destroy')->middleware('role:accountant,manager,admin');

    // Reports - accountant and manager (admin allowed)
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index')->middleware('role:accountant,manager');
    Route::get('reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel')->middleware('role:accountant,manager');
    Route::get('reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.export.pdf')->middleware('role:accountant,manager');
    Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv')->middleware('role:accountant,manager');
});

