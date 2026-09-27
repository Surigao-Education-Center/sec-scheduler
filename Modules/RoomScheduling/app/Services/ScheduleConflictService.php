<?php

namespace Modules\RoomScheduling\Services;

use Modules\RoomScheduling\Models\Schedule;

class ScheduleConflictService
{
    /**
     * Check a proposed schedule slot for conflicts against room,
     * instructor, and section bookings.
     *
     * @param  array{
     *     room_id: int,
     *     instructor_id: int,
     *     section_id: int,
     *     day: string,
     *     start_time: string,
     *     end_time: string,
     *     school_year: string,
     *     semester: string
     * }  $data
     * @param  int|null  $ignoreScheduleId  Exclude this row (used when updating an existing schedule)
     * @return array<string, string>  Keyed by conflict type: room, instructor, section
     */
    public function findConflicts(array $data, ?int $ignoreScheduleId = null): array
    {
        $conflicts = [];

        $roomConflict = $this->overlapping($data, $ignoreScheduleId)
            ->where('room_id', $data['room_id'])
            ->with('section.subject')
            ->first();

        if ($roomConflict) {
            $conflicts['room_id'] = sprintf(
                'Room is already booked on %s %s-%s for %s.',
                $data['day'],
                $roomConflict->start_time,
                $roomConflict->end_time,
                optional($roomConflict->section->subject ?? null)->name ?? 'another subject'
            );
        }

        $instructorConflict = $this->overlapping($data, $ignoreScheduleId)
            ->where('instructor_id', $data['instructor_id'])
            ->with('section.subject')
            ->first();

        if ($instructorConflict) {
            $conflicts['instructor_id'] = sprintf(
                'Instructor already has a class on %s %s-%s (%s).',
                $data['day'],
                $instructorConflict->start_time,
                $instructorConflict->end_time,
                optional($instructorConflict->section->subject ?? null)->name ?? 'another subject'
            );
        }

        $sectionConflict = $this->overlapping($data, $ignoreScheduleId)
            ->where('section_id', $data['section_id'])
            ->first();

        if ($sectionConflict) {
            $conflicts['section_id'] = sprintf(
                'This section already has a class scheduled on %s %s-%s.',
                $data['day'],
                $sectionConflict->start_time,
                $sectionConflict->end_time
            );
        }

        return $conflicts;
    }

    public function hasConflicts(array $data, ?int $ignoreScheduleId = null): bool
    {
        return count($this->findConflicts($data, $ignoreScheduleId)) > 0;
    }

    protected function overlapping(array $data, ?int $ignoreScheduleId)
    {
        return Schedule::query()
            ->overlapping(
                $data['day'],
                $data['start_time'],
                $data['end_time'],
                $data['school_year'],
                $data['semester']
            )
            ->when($ignoreScheduleId, fn ($q) => $q->where('id', '!=', $ignoreScheduleId));
    }
}
