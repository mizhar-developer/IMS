<?php
namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DiagnosisReportExport implements FromCollection, WithHeadings
{
    protected $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function collection()
    {
        return new Collection($this->rows);
    }

    public function headings(): array
    {
        if (count($this->rows) === 0)
            return ['ID', 'Patient', 'Doctor', 'Disease', 'Created At'];
        return array_keys($this->rows[0]);
    }
}
