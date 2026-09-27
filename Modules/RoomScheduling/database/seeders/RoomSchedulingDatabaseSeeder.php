<?php

namespace Modules\RoomScheduling\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\RoomScheduling\Models\Instructor;
use Modules\RoomScheduling\Models\Room;
use Modules\RoomScheduling\Models\Schedule;
use Modules\RoomScheduling\Models\Section;
use Modules\RoomScheduling\Models\Subject;

class RoomSchedulingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = collect([
            ['code' => 'R101', 'name' => 'Room 101', 'building' => 'Main Building', 'floor' => '1', 'capacity' => 40, 'type' => 'lecture'],
            ['code' => 'R202', 'name' => 'Room 202', 'building' => 'Main Building', 'floor' => '2', 'capacity' => 35, 'type' => 'lecture'],
            ['code' => 'LAB1', 'name' => 'Computer Lab 1', 'building' => 'IT Building', 'floor' => '2', 'capacity' => 30, 'type' => 'laboratory'],
            ['code' => 'LAB2', 'name' => 'Computer Lab 2', 'building' => 'IT Building', 'floor' => '2', 'capacity' => 30, 'type' => 'laboratory'],
        ])->mapWithKeys(fn (array $room) => [$room['code'] => Room::updateOrCreate(['code' => $room['code']], $room)]);

        $instructors = collect([
            ['first_name' => 'Juan', 'last_name' => 'Dela Cruz', 'email' => 'juan.delacruz@example.com'],
            ['first_name' => 'Maria', 'last_name' => 'Santos', 'email' => 'maria.santos@example.com'],
            ['first_name' => 'Andrei', 'last_name' => 'Reyes', 'email' => 'andrei.reyes@example.com'],
            ['first_name' => 'Liza', 'last_name' => 'Garcia', 'email' => 'liza.garcia@example.com'],
        ])->mapWithKeys(fn (array $instructor) => [
            $instructor['email'] => Instructor::updateOrCreate(['email' => $instructor['email']], $instructor + ['is_active' => true]),
        ]);

        $subjects = collect([
            ['code' => 'CS101', 'name' => 'Introduction to Programming', 'units' => 3, 'lecture_hours' => 2, 'lab_hours' => 3],
            ['code' => 'CS102', 'name' => 'Data Structures', 'units' => 3, 'lecture_hours' => 3, 'lab_hours' => 0],
            ['code' => 'IT201', 'name' => 'Database Systems', 'units' => 3, 'lecture_hours' => 2, 'lab_hours' => 3],
            ['code' => 'GE101', 'name' => 'Purposive Communication', 'units' => 3, 'lecture_hours' => 3, 'lab_hours' => 0],
            ['code' => 'MATH101', 'name' => 'College Algebra', 'units' => 3, 'lecture_hours' => 3, 'lab_hours' => 0],
            ['code' => 'PE101', 'name' => 'Fitness and Wellness', 'units' => 2, 'lecture_hours' => 2, 'lab_hours' => 0],
        ])->mapWithKeys(fn (array $subject) => [$subject['code'] => Subject::updateOrCreate(['code' => $subject['code']], $subject + ['is_active' => true])]);

        $schoolYear = '2026-2027';
        $semester = 'First semester';
        $sections = collect([
            ['subject' => 'CS101', 'code' => 'BSIT-1A', 'max_students' => 35],
            ['subject' => 'CS102', 'code' => 'BSIT-2A', 'max_students' => 35],
            ['subject' => 'IT201', 'code' => 'BSIT-2B', 'max_students' => 30],
            ['subject' => 'GE101', 'code' => 'BSIT-1B', 'max_students' => 40],
            ['subject' => 'MATH101', 'code' => 'BSIT-1A', 'max_students' => 35],
            ['subject' => 'PE101', 'code' => 'BSIT-1B', 'max_students' => 40],
        ])->mapWithKeys(function (array $section) use ($subjects, $schoolYear, $semester) {
            $model = Section::updateOrCreate(
                ['subject_id' => $subjects[$section['subject']]->id, 'section_code' => $section['code'], 'school_year' => $schoolYear, 'semester' => $semester],
                ['max_students' => $section['max_students']],
            );

            return [$section['code'].'-'.$section['subject'] => $model];
        });

        $scheduleRows = [
            ['section' => 'CS101', 'code' => 'BSIT-1A', 'room' => 'LAB1', 'instructor' => 'juan.delacruz@example.com', 'day' => 'Monday', 'start' => '07:30', 'end' => '09:00'],
            ['section' => 'GE101', 'code' => 'BSIT-1B', 'room' => 'R101', 'instructor' => 'maria.santos@example.com', 'day' => 'Monday', 'start' => '09:30', 'end' => '11:00'],
            ['section' => 'CS102', 'code' => 'BSIT-2A', 'room' => 'R202', 'instructor' => 'andrei.reyes@example.com', 'day' => 'Monday', 'start' => '13:00', 'end' => '14:30'],
            ['section' => 'MATH101', 'code' => 'BSIT-1A', 'room' => 'R101', 'instructor' => 'liza.garcia@example.com', 'day' => 'Tuesday', 'start' => '07:30', 'end' => '09:00'],
            ['section' => 'IT201', 'code' => 'BSIT-2B', 'room' => 'LAB2', 'instructor' => 'juan.delacruz@example.com', 'day' => 'Tuesday', 'start' => '10:00', 'end' => '11:30'],
            ['section' => 'PE101', 'code' => 'BSIT-1B', 'room' => 'R202', 'instructor' => 'maria.santos@example.com', 'day' => 'Wednesday', 'start' => '08:00', 'end' => '10:00'],
            ['section' => 'CS101', 'code' => 'BSIT-1A', 'room' => 'LAB1', 'instructor' => 'juan.delacruz@example.com', 'day' => 'Wednesday', 'start' => '10:30', 'end' => '12:00'],
            ['section' => 'GE101', 'code' => 'BSIT-1B', 'room' => 'R101', 'instructor' => 'maria.santos@example.com', 'day' => 'Thursday', 'start' => '13:00', 'end' => '14:30'],
            ['section' => 'CS102', 'code' => 'BSIT-2A', 'room' => 'R202', 'instructor' => 'andrei.reyes@example.com', 'day' => 'Friday', 'start' => '08:30', 'end' => '10:00'],
            ['section' => 'IT201', 'code' => 'BSIT-2B', 'room' => 'LAB2', 'instructor' => 'juan.delacruz@example.com', 'day' => 'Friday', 'start' => '10:30', 'end' => '12:00'],
        ];

        foreach ($scheduleRows as $row) {
            $section = $sections[$row['code'].'-'.$row['section']];
            Schedule::updateOrCreate(
                ['section_id' => $section->id, 'day' => $row['day'], 'school_year' => $schoolYear, 'semester' => $semester],
                ['room_id' => $rooms[$row['room']]->id, 'instructor_id' => $instructors[$row['instructor']]->id, 'start_time' => $row['start'], 'end_time' => $row['end']],
            );
        }
    }
}
