<?php
namespace App\Services;

use Illuminate\Support\Collection;

/**
 * Interface ReportServiceInterface
 * Encapsulates logic for generating reports and export rows.
 */
interface ReportServiceInterface
{
    /**
     * Return diagnoses collection for a date range.
     */
    public function getDiagnoses(string $start, string $end): Collection;

    /**
     * Return summary data array: diagnoses collection, imagesCount, billingSum
     */
    public function getSummary(string $start, string $end): array;

    /**
     * Convert diagnoses collection into array rows for export.
     */
    public function exportRows($diagnoses): array;
}
