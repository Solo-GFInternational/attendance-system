<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class EmployeeReportWorkbook
{
    private const HEADINGS = ['Date', 'Day', 'Status', 'Sign In', 'Sign Out', 'Outings', 'Time Outside', 'Hours On Site', 'Note'];

    public function build(array $report): Spreadsheet
    {
        $book = new Spreadsheet;
        $book->getDefaultStyle()->getFont()->setName('Arial')->setSize(10);
        $sheet = $book->getActiveSheet()->setTitle('Attendance');

        // Typed text is always written as text so it can never run as a formula.
        $text = fn (array $cell, string $value) => $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);

        $employee = $report['employee'];
        $summary = $report['summary'];

        $text([1, 1], 'Employee Attendance Report');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $details = [
            ['Employee', $employee->name],
            ['Employee ID', $employee->employee_code],
            ['Department', $employee->department ?: '-'],
            ['Designation', $employee->designation ?: '-'],
            ['Site', $employee->site?->name ?? '-'],
            ['Period', $report['from']->format('d/m/Y') . ' to ' . $report['to']->format('d/m/Y')],
        ];

        $counts = [
            ['Days attended', $summary['daysAttended']],
            ['Late', $summary['late']],
            ['Leave', $summary['leave']],
            ['Absent', $summary['absent']],
            ['No record (Mon-Fri)', $summary['noRecordWeekdays']],
            ['No sign out', $summary['missingSignOut']],
        ];

        foreach ($details as $index => [$label, $value]) {
            $row = $index + 3;
            $text([1, $row], $label);
            $text([2, $row], (string) $value);
            $text([7, $row], $counts[$index][0]);
            $sheet->setCellValue([8, $row], $counts[$index][1]);
        }

        $sheet->getStyle('A3:A8')->getFont()->setBold(true);
        $sheet->getStyle('G3:G8')->getFont()->setBold(true);

        $head = 10;
        $sheet->fromArray(self::HEADINGS, null, "A{$head}");
        $sheet->getStyle("A{$head}:I{$head}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F172A']],
        ]);

        $row = $head;

        foreach ($report['rows'] as $day) {
            $row++;

            $sheet->setCellValue([1, $row], Date::PHPToExcel($day['date']->format('Y-m-d')));
            $text([2, $row], $day['date']->format('D'));
            $text([3, $row], (string) $day['status']);

            foreach ([4 => $day['checkIn'], 5 => $day['checkOut']] as $column => $time) {
                if ($time) {
                    $sheet->setCellValue([$column, $row], $time->secondsSinceMidnight() / 86400);
                }
            }

            $text([6, $row], collect($day['outings'])->map(fn ($outing) => self::outingText($outing))->implode("\n"));

            foreach ([7 => $day['outsideMinutes'], 8 => $day['onSiteMinutes']] as $column => $minutes) {
                if ($minutes !== null) {
                    $sheet->setCellValue([$column, $row], $minutes / 1440);
                }
            }

            $text([9, $row], trim(implode('. ', array_filter([$day['note'], $day['remarks']]))));
        }

        if ($row > $head) {
            $first = $head + 1;
            $total = $row + 1;

            $sheet->getStyle("A{$first}:A{$row}")->getNumberFormat()->setFormatCode('dd/mm/yyyy');
            $sheet->getStyle("D{$first}:E{$row}")->getNumberFormat()->setFormatCode('hh:mm AM/PM');
            $sheet->getStyle("F{$first}:F{$row}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A{$first}:I{$row}")->getAlignment()->setVertical('top');

            $text([1, $total], 'Total');
            $sheet->setCellValue("G{$total}", "=SUM(G{$first}:G{$row})");
            $sheet->setCellValue("H{$total}", "=SUM(H{$first}:H{$row})");
            $sheet->getStyle("G{$first}:H{$total}")->getNumberFormat()->setFormatCode('[h]:mm');
            $sheet->getStyle("A{$total}:I{$total}")->getFont()->setBold(true);
        }

        foreach (['A' => 14, 'B' => 26, 'C' => 12, 'D' => 12, 'E' => 12, 'F' => 48, 'G' => 20, 'H' => 15, 'I' => 30] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->freezePane('A' . ($head + 1));
        $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);

        return $book;
    }

    public static function outingText(array $outing): string
    {
        $text = ($outing['out']?->format('h:i A') ?? '?') . ' - ' . ($outing['return']?->format('h:i A') ?? 'not returned');

        return $outing['reason'] === '' ? $text : "{$text} ({$outing['reason']})";
    }
}
