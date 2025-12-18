<?php
namespace App\Http\Controllers;

use App\Services\BillingServiceInterface;
use App\Models\Billing;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BillingController extends Controller
{
    protected $service;

    public function __construct(BillingServiceInterface $service)
    {
        $this->service = $service;
    }

    public function showForPatient($patientId)
    {
        $billing = $this->service->calculateForPatient($patientId);
        return view('reports.patient_history', compact('billing'));
    }

    public function index()
    {
        $invoices = Billing::with('patient')->orderBy('created_at', 'desc')->get();
        $patients = Patient::orderBy('first_name')->get();

        $totals = [
            'outstanding' => Billing::where('paid', false)->sum('amount'),
            'collected' => Billing::where('paid', true)->sum('amount'),
            'grand' => Billing::sum('amount'),
        ];

        return view('billing.index', ['invoices' => $invoices, 'patients' => $patients, 'totals' => $totals]);
    }

    public function create()
    {
        $patients = Patient::orderBy('first_name')->get();
        return view('billing.create', compact('patients'));
    }

    /**
     * Generate or update an invoice for a patient, applying an optional adjustment.
     */
    public function generateForPatient(Request $request)
    {
        $data = $request->validate([
            'patient_id' => 'required|integer|exists:patients,id',
            'adjustment' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // If there is already an unpaid invoice for this patient, use it
        // Otherwise generate one. This preserves any previous manual adjustments
        // so subsequent adjustments add to the current invoice amount.
        $billing = Billing::where('patient_id', $data['patient_id'])->where('paid', false)->first();
        if (!$billing) {
            $billing = $this->service->calculateForPatient($data['patient_id']);
        }

        $adjust = (float) ($data['adjustment'] ?? 0);
        if ($adjust !== 0.0 && $billing) {
            $billing->amount = $billing->amount + $adjust;
            $billing->adjustment = ($billing->adjustment ?? 0) + $adjust;
            $billing->save();
        }

        return redirect()->route('billing.index')->with('success', 'Invoice generated/updated.');
    }

    /**
     * Generate/download PDF for a billing invoice.
     */
    public function generate($id)
    {
        $billing = $this->service->getInvoice($id);
        abort_unless($billing, 404);

        // compute patient outstanding amount (before marking this invoice paid)
        $patientOutstanding = Billing::where('patient_id', $billing->patient_id)->where('paid', false)->sum('amount');

        // Mark this invoice as paid via service
        $billing = $this->service->markPaid($billing->id);

        if (class_exists(\Barryvdh\DomPDF\Facade::class) || class_exists('PDF')) {
            try {
                $pdf = \PDF::loadView('billing.invoice', compact('billing', 'patientOutstanding'));
                return $pdf->download("invoice_{$billing->id}.pdf");
            } catch (\Throwable $e) {
                Log::error('PDF generation failed: ' . $e->getMessage(), ['exception' => $e]);
            }
        }

        // Fallback: render HTML view
        return view('billing.invoice', compact('billing', 'patientOutstanding'));
    }

    public function show(\Illuminate\Http\Request $request, $id)
    {
        $billing = Billing::with('patient')->findOrFail($id);
        $patientOutstanding = Billing::where('patient_id', $billing->patient_id)->where('paid', false)->sum('amount');
        $autoprint = (bool) $request->query('print', false);
        return view('billing.invoice', compact('billing', 'patientOutstanding', 'autoprint'));
    }

    public function edit($id)
    {
        $billing = $this->service->getInvoice($id);
        abort_unless($billing, 404);
        if ($billing->paid) {
            return redirect()->route('billing.index')->with('error', 'Paid invoices cannot be edited.');
        }
        return view('billing.edit', compact('billing'));
    }

    public function adjust(Request $request, $id)
    {
        $data = $request->validate([
            'adjustment' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        $adjust = (float) $data['adjustment'];
        $this->service->adjustInvoice($id, $adjust, $data['notes'] ?? null);

        return redirect()->route('billing.index')->with('success', 'Invoice adjusted.');
    }

    public function export()
    {
        $invoices = Billing::with('patient')->orderBy('created_at', 'desc')->get();

        $filename = 'invoices_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice ID', 'Patient', 'Amount', 'Paid', 'Created At']);
            foreach ($invoices as $inv) {
                fputcsv($out, [
                    $inv->id,
                    $inv->patient?->first_name . ' ' . $inv->patient?->last_name,
                    number_format($inv->amount, 2),
                    $inv->paid ? 'Paid' : 'Unpaid',
                    $inv->created_at->toDateTimeString(),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Mark a paid invoice as unpaid (reverse payment).
     */
    public function markUnpaid($id)
    {
        $billing = $this->service->getInvoice($id);
        abort_unless($billing, 404);
        if (!$billing->paid) {
            return redirect()->route('billing.index')->with('info', 'Invoice is already unpaid.');
        }

        $this->service->markUnpaid($id);

        return redirect()->route('billing.index')->with('success', 'Invoice marked as unpaid.');
    }

    public function destroy($id)
    {
        $billing = $this->service->getInvoice($id);
        abort_unless($billing, 404);

        if ($billing->paid) {
            return redirect()->route('billing.index')->with('error', 'Cannot delete a paid invoice.');
        }

        try {
            $billing->delete();
        } catch (\Throwable $e) {
            return redirect()->route('billing.index')->with('error', 'Unable to delete invoice.');
        }

        return redirect()->route('billing.index')->with('success', 'Invoice deleted.');
    }
}
