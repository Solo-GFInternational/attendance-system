<?php

namespace App\Services;

use Carbon\Carbon;

class AttendanceAIQueryService
{
    protected AttendanceDataService $attendanceData;

    protected AttendanceAIService $ai;

    public function __construct(
        AttendanceDataService $attendanceData,
        AttendanceAIService $ai
    ) {
        $this->attendanceData = $attendanceData;
        $this->ai = $ai;
    }

    /**
     * Process a read-only attendance question.
     */
    public function ask(string $question): string
    {
        $question = trim($question);

        if ($question === '') {
            return 'Please enter a question.';
        }

        $today = Carbon::today()->toDateString();

        /*
         * Laravel retrieves and calculates the attendance facts.
         */
        $summary = $this->attendanceData->dailySummary($today);

        $lowerQuestion = strtolower($question);

        /*
         * ============================================================
         * HISTORICAL EMPLOYEE OUTING QUESTIONS
         * ============================================================
         */
        if (
    str_contains($lowerQuestion, 'outing') ||
    str_contains($lowerQuestion, 'outings') ||
    str_contains($lowerQuestion, 'outside') ||
    (
        str_contains($lowerQuestion, 'how long') &&
        str_contains($lowerQuestion, 'out')
    )
)
{
            $employeeSearch = $this->findEmployeeFromQuestion(
                $lowerQuestion,
                $summary['employees']
            );

            if ($employeeSearch) {
                [$from, $to] = $this->determineDateRange(
                    $lowerQuestion
                );

                return $this->employeeOutingAnswer(
                    $question,
                    $employeeSearch,
                    $from,
                    $to
                );
            }
        }

        /*
         * ============================================================
         * HISTORICAL EMPLOYEE ATTENDANCE
         * ============================================================
         */
        if (
            str_contains($lowerQuestion, 'attendance') &&
            !str_contains($lowerQuestion, 'today')
        ) {
            $employeeSearch = $this->findEmployeeFromQuestion(
                $lowerQuestion,
                $summary['employees']
            );

            if ($employeeSearch) {
                [$from, $to] = $this->determineDateRange(
                    $lowerQuestion
                );

                return $this->employeeAttendanceAnswer(
                    $question,
                    $employeeSearch,
                    $from,
                    $to
                );
            }
        }

        /*
         * ============================================================
         * TODAY - ABSENT
         * ============================================================
         */
        if (
            str_contains($lowerQuestion, 'how many') &&
            str_contains($lowerQuestion, 'absent')
        ) {
            return $this->ai->ask(
                "Answer the administrator's question using ONLY this "
                . "verified fact: There are {$summary['absent']} "
                . "employees absent today ({$summary['date']}). "
                . "Do not calculate or change the number. "
                . "Question: {$question}"
            );
        }

        /*
         * ============================================================
         * TODAY - PRESENT
         * ============================================================
         */
        if (
            str_contains($lowerQuestion, 'how many') &&
            str_contains($lowerQuestion, 'present')
        ) {
            return $this->ai->ask(
                "Answer the administrator's question using ONLY this "
                . "verified fact: There are {$summary['present']} "
                . "employees present today ({$summary['date']}). "
                . "Do not calculate or change the number. "
                . "Question: {$question}"
            );
        }

        /*
         * ============================================================
         * TODAY - LATE
         * ============================================================
         */
        if (
            str_contains($lowerQuestion, 'how many') &&
            str_contains($lowerQuestion, 'late')
        ) {
            return $this->ai->ask(
                "Answer the administrator's question using ONLY this "
                . "verified fact: There are {$summary['late']} "
                . "employees late today ({$summary['date']}). "
                . "Do not calculate or change the number. "
                . "Question: {$question}"
            );
        }

        /*
         * ============================================================
         * CURRENTLY OUT
         * ============================================================
         */
        if (
            str_contains($lowerQuestion, 'currently out') ||
            str_contains($lowerQuestion, 'who is out')
        ) {
            $out = $this->attendanceData->currentlyOut($today);

            if (count($out) === 0) {
                return 'There are currently no employees out.';
            }

            $names = collect($out)
                ->pluck('name')
                ->filter()
                ->values()
                ->all();

            $nameList = implode(', ', $names);

            return $this->ai->ask(
                "Answer the administrator using ONLY this verified "
                . "information. Employees currently out are: "
                . "{$nameList}. "
                . "Do not add or invent any names. "
                . "Question: {$question}"
            );
        }

        /*
         * ============================================================
         * GENERAL TODAY SUMMARY
         * ============================================================
         */
        $prompt = <<<PROMPT
You are an internal employee attendance assistant.

You are answering a question about an attendance management system.

IMPORTANT:
- Do not invent information.
- Do not calculate numbers from employee lists.
- Do not make up employee names.
- Do not claim to have performed an action.
- You are READ-ONLY.
- If the requested information is not available in the supplied data,
  say so clearly.

Verified attendance summary for {$today}:

Total active employees: {$summary['total_staff']}
Present: {$summary['present']}
Late: {$summary['late']}
Absent: {$summary['absent']}
Checked out: {$summary['checked_out']}
Currently out: {$summary['currently_out']}

Administrator question:

{$question}

Provide a short professional answer using only the verified
information above.
PROMPT;

        return $this->ai->ask($prompt);
    }

    /**
     * Find an employee mentioned in the question.
     *
     * Returns the employee code used by the data service.
     */
    protected function findEmployeeFromQuestion(
        string $question,
        array $employees
    ): ?string {
        foreach ($employees as $employee) {
            $name = strtolower(trim($employee['name'] ?? ''));
            $code = strtolower(trim($employee['employee_code'] ?? ''));

            /*
             * Match complete employee name.
             */
            if (
                $name !== '' &&
                str_contains($question, $name)
            ) {
                return $employee['employee_code'];
            }

            /*
             * Match employee code.
             */
            if (
                $code !== '' &&
                str_contains($question, $code)
            ) {
                return $employee['employee_code'];
            }

            /*
             * Match first name.
             *
             * Example:
             * "Show Shahil's outings in September."
             */
            if ($name !== '') {
                $nameParts = preg_split('/\s+/', $name);

                $firstName = $nameParts[0] ?? '';

                if (
                    $firstName !== '' &&
                    strlen($firstName) >= 3 &&
                    str_contains($question, $firstName)
                ) {
                    return $employee['employee_code'];
                }
            }
        }

        return null;
    }

    /**
     * Determine requested date range.
     */
    protected function determineDateRange(string $question): array
    {
        $today = Carbon::today();

        /*
         * Yesterday.
         */
        if (str_contains($question, 'yesterday')) {
            $yesterday = $today->copy()->subDay();

            return [
                $yesterday->toDateString(),
                $yesterday->toDateString(),
            ];
        }

        /*
         * Today.
         */
        if (
            str_contains($question, 'today') &&
            !str_contains($question, 'tomorrow')
        ) {
            return [
                $today->toDateString(),
                $today->toDateString(),
            ];
        }

        /*
         * Last week.
         */
        if (str_contains($question, 'last week')) {
            $start = $today->copy()
                ->startOfWeek()
                ->subWeek();

            $end = $start->copy()
                ->endOfWeek();

            return [
                $start->toDateString(),
                $end->toDateString(),
            ];
        }

        /*
         * This week.
         */
        if (str_contains($question, 'this week')) {
            return [
                $today->copy()
                    ->startOfWeek()
                    ->toDateString(),

                $today->copy()
                    ->endOfWeek()
                    ->toDateString(),
            ];
        }

        /*
         * Last month.
         */
        if (str_contains($question, 'last month')) {
            $month = $today->copy()->subMonth();

            return [
                $month->copy()
                    ->startOfMonth()
                    ->toDateString(),

                $month->copy()
                    ->endOfMonth()
                    ->toDateString(),
            ];
        }

        /*
         * This month.
         */
        if (str_contains($question, 'this month')) {
            return [
                $today->copy()
                    ->startOfMonth()
                    ->toDateString(),

                $today->copy()
                    ->endOfMonth()
                    ->toDateString(),
            ];
        }

        /*
         * Specific month.
         */
        $months = [
            'january' => 1,
            'february' => 2,
            'march' => 3,
            'april' => 4,
            'may' => 5,
            'june' => 6,
            'july' => 7,
            'august' => 8,
            'september' => 9,
            'october' => 10,
            'november' => 11,
            'december' => 12,
        ];

        foreach ($months as $monthName => $monthNumber) {
            if (str_contains($question, $monthName)) {
                preg_match(
                    '/\b(20\d{2})\b/',
                    $question,
                    $matches
                );

                $year = isset($matches[1])
                    ? (int) $matches[1]
                    : $today->year;

                $month = Carbon::create(
                    $year,
                    $monthNumber,
                    1
                );

                return [
                    $month->copy()
                        ->startOfMonth()
                        ->toDateString(),

                    $month->copy()
                        ->endOfMonth()
                        ->toDateString(),
                ];
            }
        }

        /*
         * Default: last 30 days.
         */
        return [
            $today->copy()
                ->subDays(30)
                ->toDateString(),

            $today->toDateString(),
        ];
    }

    /**
     * Build an exact historical outing response.
     *
     * Laravel retrieves and calculates the actual database values.
     * Ollama is NOT used to calculate or modify the records.
     */
    protected function employeeOutingAnswer(
        string $question,
        string $employeeSearch,
        string $from,
        string $to
    ): string {
        $employee = $this->attendanceData->findEmployee(
            $employeeSearch
        );

        if (!$employee) {
            return "I couldn't find an employee matching \"{$employeeSearch}\".";
        }

        /*
         * Get verified outing records from Laravel.
         */
        $data = $this->attendanceData->outingHistory(
            $from,
            $to,
            $employee['id']
        );

        $outings = $data['outings'] ?? [];

        /*
         * No outings.
         */
        if (count($outings) === 0) {
            return "{$employee['name']} had no recorded outings "
                . "between {$from} and {$to}.";
        }
/*
 * Duration question.
 *
 * Return only the verified duration instead of the
 * complete outing list.
 */
if (
    str_contains(strtolower($question), 'how long') ||
    str_contains(strtolower($question), 'total time') ||
    str_contains(strtolower($question), 'time outside')
) {
    $completedMinutes = 0;
    $currentlyOut = 0;

    foreach ($outings as $outing) {
        if ($outing['return_time'] !== null) {
            $completedMinutes +=
                (int) ($outing['duration_minutes'] ?? 0);
        } else {
            $currentlyOut++;
        }
    }

    $hours = intdiv($completedMinutes, 60);
    $minutes = $completedMinutes % 60;

    if ($hours > 0) {
        $durationText =
            "{$hours} hour" .
            ($hours === 1 ? '' : 's');

        if ($minutes > 0) {
            $durationText .=
                " {$minutes} minute" .
                ($minutes === 1 ? '' : 's');
        }
    } else {
        $durationText =
            "{$minutes} minute" .
            ($minutes === 1 ? '' : 's');
    }

    $answer = "{$employee['name']} had "
        . "{$durationText} of completed recorded time outside "
        . "between {$from} and {$to}.";

    if ($currentlyOut > 0) {
        $answer .=
            " There is {$currentlyOut} outing with no recorded "
            . "return time, so its duration is not included "
            . "in the completed total.";
    }

    return $answer;
}
        /*
         * Calculate verified totals.
         */
        $totalOutings = count($outings);

        $completedMinutes = 0;

        $currentlyOut = 0;

        foreach ($outings as $outing) {
            if ($outing['return_time'] !== null) {
                $completedMinutes +=
                    (int) ($outing['duration_minutes'] ?? 0);
            } else {
                $currentlyOut++;
            }
        }

        /*
         * Build exact response.
         */
        $lines = [];

        $lines[] = "{$employee['name']} "
            . "(Employee code: {$employee['employee_code']}) "
            . "has {$totalOutings} recorded outing(s) "
            . "between {$from} and {$to}.";

        $lines[] = '';

        $lines[] = 'Verified summary:';

        $lines[] = "- Total outings: {$totalOutings}";

        $lines[] = "- Completed time outside: {$completedMinutes} minutes";

        if ($currentlyOut > 0) {
            $lines[] = "- Currently out: {$currentlyOut}";
        }

        /*
         * Individual outing records.
         */
        $lines[] = '';

        $lines[] = 'Outing records:';

        foreach ($outings as $outing) {
            $date = $outing['date'] ?? 'Unknown date';

            $reason = $outing['reason'] ?? null;

            $outTime = $outing['out_time'] ?? null;

            $returnTime = $outing['return_time'] ?? null;

            $duration = $outing['duration_minutes'] ?? null;

            $line = "- {$date}";

            if ($reason !== null && $reason !== '') {
                $line .= " | Reason: {$reason}";
            } else {
                $line .= " | Reason: Not specified";
            }

            if ($outTime !== null) {
                $line .= " | Out: {$outTime}";
            }

            if ($returnTime !== null) {
                $line .= " | Returned: {$returnTime}";
            } else {
                $line .= " | Returned: Not recorded";
            }

            if ($duration !== null) {
                $line .= " | Duration: {$duration} minutes";
            }

            $lines[] = $line;
        }

        return implode("\n", $lines);
    }

    /**
     * Build an exact historical attendance response.
     *
     * Laravel formats the actual database values.
     * Ollama is NOT used to alter times or records.
     */
    protected function employeeAttendanceAnswer(
        string $question,
        string $employeeSearch,
        string $from,
        string $to
    ): string {
        $employee = $this->attendanceData->findEmployee(
            $employeeSearch
        );

        if (!$employee) {
            return "I couldn't find an employee matching \"{$employeeSearch}\".";
        }

        /*
         * Get verified attendance records.
         */
        $data = $this->attendanceData->employeeAttendance(
            $employee['id'],
            $from,
            $to
        );

        $attendance = $data['attendance'] ?? [];

        if (count($attendance) === 0) {
            return "{$employee['name']} has no attendance records "
                . "between {$from} and {$to}.";
        }

        /*
         * Build the answer directly from Laravel data.
         */
        $lines = [];

        $lines[] = "{$employee['name']} "
            . "(Employee code: {$employee['employee_code']}) "
            . "has {$from} to {$to} attendance records:";

        $lines[] = '';

        foreach ($attendance as $record) {
            $date = $record['date'] ?? 'Unknown date';

            $status = $record['status'] ?? 'Unknown';

            $checkIn = $record['check_in'] ?? null;

            $checkOut = $record['check_out'] ?? null;

            $attendanceLine = "- {$date}: {$status}";

            if ($checkIn !== null) {
                $attendanceLine .=
                    " | Check-in: {$checkIn}";
            } else {
                $attendanceLine .=
                    " | Check-in: Not recorded";
            }

            if ($checkOut !== null) {
                $attendanceLine .=
                    " | Check-out: {$checkOut}";
            } else {
                $attendanceLine .=
                    " | Check-out: Not recorded";
            }

            $lines[] = $attendanceLine;

            /*
             * Outings for this attendance record.
             */
            $outings = $record['outings'] ?? [];

            if (count($outings) > 0) {
                $lines[] = '  Outings:';

                foreach ($outings as $outing) {
                    $outTime = $outing['out_time'] ?? null;

                    $returnTime = $outing['return_time'] ?? null;

                    $duration = $outing['duration_minutes'] ?? null;

                    $reason = $outing['reason'] ?? null;

                    $outingLine = '  -';

                    if (
                        $reason !== null &&
                        $reason !== ''
                    ) {
                        $outingLine .=
                            " Reason: {$reason}";
                    } else {
                        $outingLine .=
                            " Reason: Not specified";
                    }

                    if ($outTime !== null) {
                        $outingLine .=
                            " | Out: {$outTime}";
                    }

                    if ($returnTime !== null) {
                        $outingLine .=
                            " | Returned: {$returnTime}";
                    } else {
                        $outingLine .=
                            " | Returned: Not recorded";
                    }

                    if ($duration !== null) {
                        $outingLine .=
                            " | Duration: {$duration} minutes";
                    }

                    $lines[] = $outingLine;
                }
            }
        }

        /*
         * Verified summary.
         */
        $attendanceCount = count($attendance);

        $outingCount = 0;

        foreach ($attendance as $record) {
            $outingCount += count(
                $record['outings'] ?? []
            );
        }

        $lines[] = '';

        $lines[] = 'Verified summary:';

        $lines[] = "- Attendance records: {$attendanceCount}";

        $lines[] = "- Total outings: {$outingCount}";

        return implode("\n", $lines);
    }
}