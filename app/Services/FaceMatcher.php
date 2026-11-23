<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Collection;

/**
 * Face matching for the attendance kiosk.
 *
 * Descriptors are the 128-number face-api.js embeddings. Two faces are
 * compared by Euclidean distance: smaller = more alike. face-api's usual
 * cut-off is 0.6; the limits below are deliberately stricter, and a match
 * also has to be clearly better than the next-closest employee, so the
 * kiosk prefers "not recognised, try again" over picking the wrong person.
 */
class FaceMatcher
{
    /** Average distance across all samples must be at or below this. */
    public const MATCH_MEAN_MAX = 0.47;

    /** No single sample may be further than this from the matched face. */
    public const MATCH_SAMPLE_MAX = 0.52;

    /** Best match must beat the second-best employee by at least this much. */
    public const MATCH_MIN_MARGIN = 0.06;

    /** A new registration closer than this to another employee is refused. */
    public const DUPLICATE_MAX = 0.50;

    /** Registration samples must all be within this of their average. */
    public const REGISTRATION_SPREAD_MAX = 0.40;


    /**
     * Read an employee's stored descriptor. Handles both storage styles
     * used in this app (array, or a JSON string inside the array cast).
     */
    public function descriptorOf(Employee $employee): ?array
    {
        $value = $employee->face_descriptor;

        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value) || count($value) !== 128) {
            return null;
        }

        return array_map('floatval', array_values($value));
    }


    public function distance(array $a, array $b): float
    {
        $sum = 0.0;

        for ($i = 0; $i < 128; $i++) {
            $diff = $a[$i] - $b[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }


    /**
     * Active employees with a usable registered face.
     *
     * @return Collection<int, array{employee: Employee, descriptor: array}>
     */
    public function knownFaces(?int $exceptEmployeeId = null): Collection
    {
        return Employee::query()
            ->whereNotNull('face_descriptor')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->when($exceptEmployeeId, fn ($q) => $q->where('id', '!=', $exceptEmployeeId))
            ->get(['id', 'employee_code', 'name', 'face_descriptor'])
            ->map(fn (Employee $e) => ['employee' => $e, 'descriptor' => $this->descriptorOf($e)])
            ->filter(fn ($known) => $known['descriptor'] !== null)
            ->values();
    }


    /**
     * Identify one person from several camera samples of the same face.
     *
     * Returns ['employee' => Employee, 'distance' => float] or null when the
     * result is not certain enough.
     */
    public function identify(array $samples, Collection $known): ?array
    {
        if ($known->isEmpty() || $samples === []) {
            return null;
        }

        // distances[employeeIndex][sampleIndex]
        $distances = [];

        foreach ($known as $k => $face) {
            foreach ($samples as $s => $sample) {
                $distances[$k][$s] = $this->distance($sample, $face['descriptor']);
            }
        }

        $means = array_map(fn ($d) => array_sum($d) / count($d), $distances);
        asort($means);
        $ranked = array_keys($means);

        $best = $ranked[0];
        $second = $ranked[1] ?? null;

        // 1. Close enough on average, and on every single sample.
        if ($means[$best] > self::MATCH_MEAN_MAX) {
            return null;
        }

        if (max($distances[$best]) > self::MATCH_SAMPLE_MAX) {
            return null;
        }

        // 2. Clearly better than the next most similar employee.
        if ($second !== null && ($means[$second] - $means[$best]) < self::MATCH_MIN_MARGIN) {
            return null;
        }

        // 3. Every sample on its own points to the same employee.
        foreach (array_keys($samples) as $s) {
            $column = array_column($distances, $s);
            if (array_keys($column, min($column))[0] !== $best) {
                return null;
            }
        }

        return [
            'employee' => $known[$best]['employee'],
            'distance' => $means[$best],
        ];
    }


    /**
     * Average several registration samples into one descriptor.
     * Returns null if the samples disagree (person moved / changed).
     */
    public function averageSamples(array $samples): ?array
    {
        $count = count($samples);

        if ($count === 0) {
            return null;
        }

        $average = array_fill(0, 128, 0.0);

        foreach ($samples as $sample) {
            for ($i = 0; $i < 128; $i++) {
                $average[$i] += $sample[$i] / $count;
            }
        }

        foreach ($samples as $sample) {
            if ($this->distance($sample, $average) > self::REGISTRATION_SPREAD_MAX) {
                return null;
            }
        }

        return $average;
    }


    /**
     * Another active employee whose registered face looks like this one.
     */
    public function findDuplicate(array $descriptor, ?int $exceptEmployeeId = null): ?Employee
    {
        $closest = null;
        $closestDistance = INF;

        foreach ($this->knownFaces($exceptEmployeeId) as $face) {
            $d = $this->distance($descriptor, $face['descriptor']);

            if ($d < $closestDistance) {
                $closestDistance = $d;
                $closest = $face['employee'];
            }
        }

        return $closestDistance <= self::DUPLICATE_MAX ? $closest : null;
    }


    /**
     * Normalise request input: accepts either face_descriptors (list of
     * samples) or the older single face_descriptor.
     *
     * @return array<int, array<int, float>>
     */
    public static function samplesFromRequest(array $validated): array
    {
        $samples = $validated['face_descriptors'] ?? [$validated['face_descriptor']];

        return array_map(
            fn ($sample) => array_map('floatval', array_values($sample)),
            array_values($samples)
        );
    }


    /**
     * Validation rules for the two input shapes above.
     */
    public static function rules(int $maxSamples = 5): array
    {
        return [
            'face_descriptors' => ['required_without:face_descriptor', 'array', 'min:1', 'max:' . $maxSamples],
            'face_descriptors.*' => ['array', 'size:128'],
            'face_descriptors.*.*' => ['numeric'],
            'face_descriptor' => ['required_without:face_descriptors', 'array', 'size:128'],
            'face_descriptor.*' => ['numeric'],
        ];
    }
}
