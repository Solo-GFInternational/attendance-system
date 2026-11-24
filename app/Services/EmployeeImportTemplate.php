<?php

namespace App\Services;

use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class EmployeeImportTemplate
{
    private const HEADINGS = ['Employee ID', 'Employee Name', 'Department', 'Designation', 'Site'];

    private const HEADING_STYLE = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1F4E79']],
    ];

    /**
     * @param  Collection<int, \App\Models\Site>  $sites
     */
    public function build(Collection $sites): Spreadsheet
    {
        $book = new Spreadsheet;
        $book->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);

        $sheet = $book->getActiveSheet()->setTitle('Employees');
        $sheet->fromArray(self::HEADINGS);
        $sheet->getStyle('A1:E1')->applyFromArray(self::HEADING_STYLE);
        $sheet->freezePane('A2');

        foreach (['A' => 18, 'B' => 32, 'C' => 30, 'D' => 30, 'E' => 24] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        // Text keeps leading zeros. Set on the whole column, because formatting
        // individual blank cells would export them as blank CSV rows.
        $sheet->getStyle('A:A')->getNumberFormat()->setFormatCode('@');

        $this->addInstructions($book, $sites);

        $siteSheet = $book->createSheet()->setTitle('Sites');
        $siteSheet->setCellValue('A1', 'Site');
        $siteSheet->getStyle('A1')->applyFromArray(self::HEADING_STYLE);
        $siteSheet->getColumnDimension('A')->setWidth(32);

        foreach ($sites->values() as $index => $site) {
            $siteSheet->setCellValueExplicit([1, $index + 2], $site->name, DataType::TYPE_STRING);
        }

        if ($sites->isNotEmpty()) {
            $sheet->setDataValidation('E2:E5000', (new DataValidation)
                ->setType(DataValidation::TYPE_LIST)
                ->setAllowBlank(true)
                ->setShowDropDown(true)
                ->setFormula1('Sites!$A$2:$A$' . ($sites->count() + 1)));
        }

        $book->setActiveSheetIndex(0);

        return $book;
    }

    private function addInstructions(Spreadsheet $book, Collection $sites): void
    {
        $sheet = $book->createSheet()->setTitle('How to use');

        $lines = [
            'How to use this file',
            '1. On the Employees sheet, enter one employee per row, starting at row 2. Keep the headings as they are.',
            '2. Employee ID must be unique. An ID already in the system updates that employee.',
            '3. Site: pick from the list, or type the site code. New employees must have a site.',
            '4. A blank Department or Designation clears the current value. A blank Site keeps the current site.',
            '5. With the Employees sheet open, choose File > Save As > CSV UTF-8 (Comma delimited).',
            '6. In the system, go to Employees > Import and choose the CSV file.',
            'If sites were added or renamed, download a new template.',
        ];

        foreach ($lines as $index => $line) {
            $sheet->setCellValue([1, $index + 1], $line);
        }

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(13);

        $sheet->setCellValue('A10', 'Example');
        $sheet->getStyle('A10')->getFont()->setBold(true);
        $sheet->fromArray(self::HEADINGS, null, 'A11');
        $sheet->getStyle('A11:E11')->applyFromArray(self::HEADING_STYLE);

        $example = ['EMP001', 'Example Person', 'Production', 'Machine Operator', $sites->first()?->name ?? 'Site name'];

        foreach ($example as $index => $value) {
            $sheet->setCellValueExplicit([$index + 1, 12], $value, DataType::TYPE_STRING);
        }

        foreach (['A', 'B', 'C', 'D', 'E'] as $column) {
            $sheet->getColumnDimension($column)->setWidth(24);
        }
    }
}
