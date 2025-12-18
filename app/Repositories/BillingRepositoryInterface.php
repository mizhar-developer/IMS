<?php
namespace App\Repositories;

use App\Models\Billing;

interface BillingRepositoryInterface
{
    /**
     * Find unpaid invoice for patient or null.
     */
    public function findUnpaidByPatient(int $patientId): ?Billing;

    /**
     * Find unpaid invoice for patient and lock the row for update in a transaction.
     */
    public function findUnpaidByPatientForUpdate(int $patientId): ?Billing;

    /**
     * Create a new billing record.
     */
    public function create(array $data): Billing;

    /**
     * Update an existing billing record.
     */
    public function update(int $id, array $data): bool;

    /**
     * Find a billing by id with relations.
     */
    public function find(int $id): ?Billing;

    /**
     * Mark billing as paid/unpaid.
     */
    public function setPaid(int $id, bool $paid): Billing;

    /**
     * List all invoices (simple retrieval)
     */
    public function all(): \Illuminate\Support\Collection;
}
