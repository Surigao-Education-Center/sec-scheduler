<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\RoomScheduling\Models\Instructor;
use Modules\RoomScheduling\Models\Room;
use Modules\RoomScheduling\Models\Schedule;
use Modules\RoomScheduling\Models\Section;
use Modules\RoomScheduling\Models\Subject;
use Tests\TestCase;

class RoomSchedulingReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_reporting_dashboard_loads_with_summary_metrics(): void
    {
        $this->seedScheduleData();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->get(route('roomscheduling.reports.index', [
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
        ]));

        $response->assertOk();
        $response->assertSee('Room utilization');
        $response->assertSee('Instructor workload');
        $response->assertSee('Section coverage');
    }

    public function test_csv_export_returns_schedule_report(): void
    {
        $this->seedScheduleData();
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $response = $this->get(route('roomscheduling.reports.export.csv', [
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertSee('room_name');
        $response->assertSee('utilization_rate');
    }

    public function test_custom_schedule_allows_section_overlap_when_requested(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));

        $subject = Subject::create([
            'code' => 'CS220',
            'name' => 'Algorithms',
            'units' => 3,
            'lecture_hours' => 3,
            'lab_hours' => 0,
            'is_active' => true,
        ]);

        $roomA = Room::create([
            'code' => 'R101',
            'name' => 'Room 101',
            'building' => 'Main',
            'floor' => '1',
            'capacity' => 40,
            'type' => 'lecture',
            'is_active' => true,
        ]);

        $roomB = Room::create([
            'code' => 'R102',
            'name' => 'Room 102',
            'building' => 'Main',
            'floor' => '1',
            'capacity' => 45,
            'type' => 'lecture',
            'is_active' => true,
        ]);

        $instructorA = Instructor::create([
            'employee_no' => 'EMP-101',
            'first_name' => 'Alice',
            'last_name' => 'Brown',
            'email' => 'alice@example.com',
            'phone' => '09100000000',
            'is_active' => true,
        ]);

        $instructorB = Instructor::create([
            'employee_no' => 'EMP-102',
            'first_name' => 'Bob',
            'last_name' => 'Green',
            'email' => 'bob@example.com',
            'phone' => '09111111111',
            'is_active' => true,
        ]);

        $section = Section::create([
            'subject_id' => $subject->id,
            'section_code' => 'CS220-1A',
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'max_students' => 30,
        ]);

        Schedule::create([
            'section_id' => $section->id,
            'room_id' => $roomA->id,
            'instructor_id' => $instructorA->id,
            'day' => 'Monday',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
        ]);

        $response = $this->post(route('roomscheduling.schedules.store'), [
            'section_id' => $section->id,
            'room_id' => $roomB->id,
            'instructor_id' => $instructorB->id,
            'day' => 'Monday',
            'start_time' => '09:30',
            'end_time' => '10:30',
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'allow_non_block_sectioning' => true,
        ]);

        $response->assertRedirect(route('roomscheduling.schedules.index'));
        $this->assertDatabaseCount('schedules', 2);
    }

    protected function seedScheduleData(): void
    {
        $subjectA = Subject::create([
            'code' => 'CS101',
            'name' => 'Intro to Programming',
            'units' => 3,
            'lecture_hours' => 2,
            'lab_hours' => 1,
            'is_active' => true,
        ]);

        $subjectB = Subject::create([
            'code' => 'MATH201',
            'name' => 'Discrete Math',
            'units' => 3,
            'lecture_hours' => 3,
            'lab_hours' => 0,
            'is_active' => true,
        ]);

        $roomA = Room::create([
            'code' => 'R101',
            'name' => 'Room 101',
            'building' => 'Main',
            'floor' => '1',
            'capacity' => 40,
            'type' => 'lecture',
            'is_active' => true,
        ]);

        $roomB = Room::create([
            'code' => 'R202',
            'name' => 'Room 202',
            'building' => 'Main',
            'floor' => '2',
            'capacity' => 35,
            'type' => 'lecture',
            'is_active' => true,
        ]);

        $instructorA = Instructor::create([
            'employee_no' => 'EMP-001',
            'first_name' => 'Anna',
            'last_name' => 'Smith',
            'email' => 'anna@example.com',
            'phone' => '09123456789',
            'is_active' => true,
        ]);

        $instructorB = Instructor::create([
            'employee_no' => 'EMP-002',
            'first_name' => 'Ben',
            'last_name' => 'Ng',
            'email' => 'ben@example.com',
            'phone' => '09987654321',
            'is_active' => true,
        ]);

        $sectionA = Section::create([
            'subject_id' => $subjectA->id,
            'section_code' => 'CS101-A',
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'max_students' => 30,
        ]);

        $sectionB = Section::create([
            'subject_id' => $subjectB->id,
            'section_code' => 'MATH201-B',
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
            'max_students' => 25,
        ]);

        Schedule::create([
            'section_id' => $sectionA->id,
            'room_id' => $roomA->id,
            'instructor_id' => $instructorA->id,
            'day' => 'Monday',
            'start_time' => '08:00:00',
            'end_time' => '10:00:00',
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
        ]);

        Schedule::create([
            'section_id' => $sectionB->id,
            'room_id' => $roomA->id,
            'instructor_id' => $instructorB->id,
            'day' => 'Wednesday',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
        ]);

        Schedule::create([
            'section_id' => $sectionA->id,
            'room_id' => $roomB->id,
            'instructor_id' => $instructorA->id,
            'day' => 'Thursday',
            'start_time' => '13:00:00',
            'end_time' => '15:00:00',
            'school_year' => '2026-2027',
            'semester' => '1st Semester',
        ]);
    }
}
