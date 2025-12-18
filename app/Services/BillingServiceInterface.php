<?php
namespace App\Services;

use App\Models\Billing;

/**
 * Interface BillingServiceInterface
 * Encapsulates billing business logic and invoice operations.
 */
interface BillingServiceInterface
{
    /**
     * Calculate or update an invoice for a patient based on current images/diagnoses.
     */
    public function calculateForPatient(int $patientId): Billing;

    /**
     * Adjust an existing invoice by an amount and optional notes.
     */
    public function adjustInvoice(int $invoiceId, float $adjustment, ?string $notes = null): ?Billing;

    /**
     * Mark an invoice paid and return the updated Billing.
     */
    public function markPaid(int $invoiceId): Billing;

    /**
     * Mark an invoice unpaid (reverse payment).
     */
    public function markUnpaid(int $invoiceId): Billing;

    /**
     * Return invoice details with relations.
     */
    public function getInvoice(int $invoiceId): ?Billing;
}
