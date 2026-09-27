<?php

namespace Modules\RoomScheduling\Services;

use Illuminate\Support\Collection;
use Modules\RoomScheduling\Models\Room;
use Modules\RoomScheduling\Models\Schedule;

class ReportAnalyticsService
{
    public function build(array $filters = []): array
    {
        $schedules = Schedule::query()
            ->with(['room', 'instructor', 'section.subject'])
            ->when($filters['school_year'] ?? null, fn ($query) => $query->where('school_year', $filters['school_year']))
            ->when($filters['semester'] ?? null, fn ($query) => $query->where('semester', $filters['semester']))
            ->when($filters['day'] ?? null, fn ($query) => $query->where('day', $filters['day']))
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        $roomUtilization = $this->buildRoomUtilization($schedules);
        $instructorWorkload = $this->buildInstructorWorkload($schedules);
        $subjectCoverage = $this->buildSubjectCoverage($schedules);
        $conflictHistory = $this->buildConflictHistory($schedules);

        return [
            'schedules' => $schedules,
            'room_utilization' => $roomUtilization,
            'instructor_workload' => $instructorWorkload,
            'subject_coverage' => $subjectCoverage,
            'conflict_history' => $conflictHistory,
            'conflict_count' => count($conflictHistory),
            'room_count' => Room::count(),
        ];
    }

    protected function buildRoomUtilization(Collection $schedules): Collection
    {
        $allRooms = Room::query()->orderBy('name')->get();
        $days = config('roomscheduling.days', []);
        $dayStart = config('roomscheduling.day_start', '07:00');
        $dayEnd = config('roomscheduling.day_end', '21:00');
        $availableMinutesPerDay = max($this->minutesBetween($dayStart, $dayEnd), 0);
        $totalAvailableMinutes = count($days) * $availableMinutesPerDay;

        return $allRooms->map(function (Room $room) use ($schedules, $totalAvailableMinutes, $availableMinutesPerDay) {
            $scheduledMinutes = $schedules
                ->where('room_id', $room->id)
                ->sum(fn ($schedule) => $this->minutesBetween($schedule->start_time, $schedule->end_time));

            $utilizationRate = $totalAvailableMinutes > 0
                ? round(($scheduledMinutes / $totalAvailableMinutes) * 100, 2)
                : 0;

            return [
                'room_id' => $room->id,
                'room_name' => $room->name,
                'scheduled_minutes' => $scheduledMinutes,
                'available_minutes' => $totalAvailableMinutes,
                'utilization_rate' => $utilizationRate,
                'hours_used' => round($scheduledMinutes / 60, 2),
                'hours_available' => round($totalAvailableMinutes / 60, 2),
                'days_count' => count(config('roomscheduling.days', [])),
            ];
        })->sortByDesc('utilization_rate')->values();
    }

    protected function buildInstructorWorkload(Collection $schedules): Collection
    {
        return $schedules
            ->groupBy('instructor_id')
            ->map(function (Collection $slots, $instructorId) {
                $instructor = $slots->first()->instructor;
                $minutes = $slots->sum(fn ($schedule) => $this->minutesBetween($schedule->start_time, $schedule->end_time));

                return [
                    'instructor_id' => $instructorId,
                    'instructor_name' => $instructor?->full_name ?? 'Unassigned instructor',
                    'slot_count' => $slots->count(),
                    'scheduled_minutes' => $minutes,
                    'hours' => round($minutes / 60, 2),
                ];
            })
            ->sortByDesc('scheduled_minutes')
            ->values();
    }

    protected function buildSubjectCoverage(Collection $schedules): Collection
    {
        return $schedules
            ->groupBy(fn ($schedule) => $schedule->section?->subject?->id ?? 'unassigned')
            ->map(function (Collection $slots, $subjectId) {
                $subject = $slots->first()->section?->subject;

                return [
                    'subject_id' => $subjectId,
                    'subject_name' => $subject?->name ?? 'Unassigned subject',
                    'subject_code' => $subject?->code ?? 'N/A',
                    'section_count' => $slots->unique('section_id')->count(),
                    'slot_count' => $slots->count(),
                    'hours' => round($slots->sum(fn ($schedule) => $this->minutesBetween($schedule->start_time, $schedule->end_time)) / 60, 2),
                ];
            })
            ->sortByDesc('slot_count')
            ->values();
    }

    protected function buildConflictHistory(Collection $schedules): array
    {
        $conflicts = [];

        foreach ($schedules as $index => $schedule) {
            foreach ($schedules->slice($index + 1) as $candidate) {
                if ($schedule->day !== $candidate->day || $schedule->school_year !== $candidate->school_year || $schedule->semester !== $candidate->semester) {
                    continue;
                }

                $overlaps = $schedule->start_time < $candidate->end_time && $schedule->end_time > $candidate->start_time;

                if (! $overlaps) {
                    continue;
                }

                foreach (['room_id', 'instructor_id', 'section_id'] as $field) {
                    if ($schedule->{$field} && $candidate->{$field} && $schedule->{$field} === $candidate->{$field}) {
                        $label = match ($field) {
                            'room_id' => 'room',
                            'instructor_id' => 'instructor',
                            'section_id' => 'section',
                            default => 'schedule',
                        };

                        $conflicts[] = [
                            'type' => $label,
                            'schedule_a' => $schedule,
                            'schedule_b' => $candidate,
                            'message' => sprintf(
                                '%s conflict between %s and %s on %s from %s to %s.',
                                ucfirst($label),
                                $schedule->section?->section_code ?? 'schedule A',
                                $candidate->section?->section_code ?? 'schedule B',
                                $schedule->day,
                                $schedule->start_time,
                                $schedule->end_time,
                            ),
                        ];
                    }
                }
            }
        }

        return $conflicts;
    }

    protected function minutesBetween(string $start, string $end): int
    {
        $startMinutes = $this->toMinutes($start);
        $endMinutes = $this->toMinutes($end);

        return max($endMinutes - $startMinutes, 0);
    }

    protected function toMinutes(string $time): int
    {
        [$hours, $minutes] = array_map('intval', explode(':', $time));

        return ($hours * 60) + $minutes;
    }
}
