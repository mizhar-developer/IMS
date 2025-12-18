<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReportServiceInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DiagnosisReportExport;
use PDF;

class ReportController extends Controller
{
    protected $service;

    public function __construct(ReportServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $start = $request->query('start_date') ?: now()->subDays(30)->toDateString();
        $end = $request->query('end_date') ?: now()->toDateString();

        $data = $this->service->getSummary($start, $end);
        $rows = $data['diagnoses']->map(function ($d) {
            return [
                'id' => $d->id,
                'patient' => $d->patient?->first_name . ' ' . $d->patient?->last_name,
                'doctor' => $d->doctor?->first_name . ' ' . $d->doctor?->last_name,
                'disease_type' => $d->disease_type,
                'created_at' => $d->created_at->toDateTimeString(),
            ];
        });

        return view('reports.index', ['rows' => $rows, 'imagesCount' => $data['imagesCount'], 'billingSum' => $data['billingSum'], 'start' => $start, 'end' => $end]);
    }

    public function exportExcel(Request $request)
    {
        $start = $request->query('start_date') ?: now()->subDays(30)->toDateString();
        $end = $request->query('end_date') ?: now()->toDateString();

        $diagnoses = $this->service->getDiagnoses($start, $end);
        $rows = $this->service->exportRows($diagnoses);

        // If Maatwebsite Excel is available, use it; otherwise fall back to CSV download
        if (class_exists(\Maatwebsite\Excel\Excel::class)) {
            return \Maatwebsite\Excel\Facades\Excel::download(new DiagnosisReportExport($rows), "diagnoses_{$start}_{$end}.xlsx");
        }

        // CSV fallback
        $filename = "diagnoses_{$start}_{$end}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            if (empty($rows)) {
                fputcsv($out, ['ID', 'Patient', 'Doctor', 'Disease', 'Created At']);
            } else {
                // headings
                fputcsv($out, array_keys($rows[0]));
                foreach ($rows as $row) {
                    fputcsv($out, $row);
                }
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        if (!class_exists(\Dompdf\Dompdf::class) && !class_exists(\Barryvdh\DomPDF\Facade::class)) {
            return redirect()->route('reports.index')->with('error', 'PDF export unavailable. Run: composer require barryvdh/laravel-dompdf');
        }

        $start = $request->query('start_date') ?: now()->subDays(30)->toDateString();
        $end = $request->query('end_date') ?: now()->toDateString();

        $diagnoses = $this->service->getDiagnoses($start, $end);
        $rows = $this->service->exportRows($diagnoses);

        // prefer facade if available
        if (class_exists(\Barryvdh\DomPDF\Facade::class) || class_exists('PDF')) {
            $pdf = \PDF::loadView('reports.pdf', ['rows' => $rows, 'start' => $start, 'end' => $end]);
            return $pdf->download("diagnoses_{$start}_{$end}.pdf");
        }

        // fallback to generating HTML response if dompdf missing (shouldn't reach here)
        return response()->view('reports.pdf', ['rows' => $rows, 'start' => $start, 'end' => $end]);
    }

    public function exportCsv(Request $request)
    {
        $start = $request->query('start_date') ?: now()->subDays(30)->toDateString();
        $end = $request->query('end_date') ?: now()->toDateString();

        $diagnoses = $this->service->getDiagnoses($start, $end);
        $rows = $this->service->exportRows($diagnoses);

        $filename = "diagnoses_{$start}_{$end}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            if (empty($rows)) {
                fputcsv($out, ['ID', 'Patient', 'Doctor', 'Disease', 'Created At']);
            } else {
                fputcsv($out, array_keys($rows[0]));
                foreach ($rows as $row) {
                    fputcsv($out, $row);
                }
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }
}
