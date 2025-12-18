<?php
namespace App\Services;

use App\Models\Billing;
use App\Repositories\PatientRepositoryInterface;
use App\Repositories\BillingRepositoryInterface;
use App\Repositories\InvoiceItemRepositoryInterface;
use App\Repositories\PaymentRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Service implementing billing operations. Uses repository for persistence.
 */
class BillingService implements BillingServiceInterface
{
    protected BillingRepositoryInterface $repo;
    protected PatientRepositoryInterface $patientRepo;
    protected InvoiceItemRepositoryInterface $itemRepo;
    protected PaymentRepositoryInterface $paymentRepo;

    public function __construct(BillingRepositoryInterface $repo, InvoiceItemRepositoryInterface $itemRepo, PaymentRepositoryInterface $paymentRepo, PatientRepositoryInterface $patientRepo)
    {
        $this->repo = $repo;
        $this->itemRepo = $itemRepo;
        $this->paymentRepo = $paymentRepo;
        $this->patientRepo = $patientRepo;
    }

    /**
     * Calculate or update billing for a patient based on images and diagnoses.
     */
    public function calculateForPatient(int $patientId): Billing
    {
        // Use a DB transaction and lock the unpaid invoice row to avoid race conditions
        return DB::transaction(function () use ($patientId) {
            $patient = $this->patientRepo->find($patientId);
            if (!$patient) {
                throw new \RuntimeException("Patient not found: {$patientId}");
            }

            // Pricing logic (could be moved to config or a pricing service)
            $base = 50.00;
            $perImage = 30.00;
            $perDiagnosis = 75.00;

            $imageCount = $patient->images->count();
            $diagnosisCount = $patient->diagnoses->count();

            $amount = $base + ($imageCount * $perImage) + ($diagnosisCount * $perDiagnosis);

            // breakdown removed — we persist line items via invoice items only

            // Find unpaid invoice and lock it for update
            $billing = $this->repo->findUnpaidByPatientForUpdate($patientId);
            if ($billing) {
                $this->repo->update($billing->id, ['amount' => $amount]);
                $billing = $this->repo->find($billing->id);
                $this->syncInvoiceItems($billing, $base, $perImage, $perDiagnosis, $imageCount, $diagnosisCount);
                return $billing;
            }

            $billing = $this->repo->create([
                'patient_id' => $patientId,
                'amount' => $amount,
                'paid' => false,
            ]);

            $this->syncInvoiceItems($billing, $base, $perImage, $perDiagnosis, $imageCount, $diagnosisCount);
            return $billing;
        });
    }

    /**
     * Synchronize invoice items for a billing based on computed pricing pieces.
     */
    protected function syncInvoiceItems(Billing $billing, float $base, float $perImage, float $perDiagnosis, int $imageCount, int $diagnosisCount): void
    {
        // Replace existing items with a fresh set corresponding to the latest breakdown
        $this->itemRepo->deleteByBilling($billing->id);

        $items = [];
        $items[] = [
            'billing_id' => $billing->id,
            'description' => 'Base fee',
            'quantity' => 1,
            'unit_price' => $base,
            'total' => $base,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        if ($imageCount > 0) {
            $items[] = [
                'billing_id' => $billing->id,
                'description' => 'Images',
                'quantity' => $imageCount,
                'unit_price' => $perImage,
                'total' => $imageCount * $perImage,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($diagnosisCount > 0) {
            $items[] = [
                'billing_id' => $billing->id,
                'description' => 'Diagnoses',
                'quantity' => $diagnosisCount,
                'unit_price' => $perDiagnosis,
                'total' => $diagnosisCount * $perDiagnosis,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($items)) {
            $this->itemRepo->insertMany($items);
        }
    }

    /**
     * Adjust an invoice by amount. Notes parameter is currently ignored.
     */
    public function adjustInvoice(int $invoiceId, float $adjustment, ?string $notes = null): ?Billing
    {
        $b = $this->repo->find($invoiceId);
        if (!$b)
            return null;
        if ($b->paid)
            return $b; // do not adjust paid invoices

        $newAmount = $b->amount + $adjustment;
        $newAdjustment = ($b->adjustment ?? 0) + $adjustment;
        $this->repo->update($invoiceId, ['amount' => $newAmount, 'adjustment' => $newAdjustment]);
        return $this->repo->find($invoiceId);
    }

    public function markPaid(int $invoiceId): Billing
    {
        $billing = $this->repo->setPaid($invoiceId, true);
        // create a payment record for audit
        try {
            $this->paymentRepo->create([
                'billing_id' => $billing->id,
                'amount' => $billing->amount,
                'method' => 'generated_pdf',
                'reference' => 'auto',
                'paid_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // do not fail marking paid if payment record can't be created
        }

        return $billing;
    }

    public function markUnpaid(int $invoiceId): Billing
    {
        return $this->repo->setPaid($invoiceId, false);
    }

    public function getInvoice(int $invoiceId): ?Billing
    {
        return $this->repo->find($invoiceId);
    }
}
