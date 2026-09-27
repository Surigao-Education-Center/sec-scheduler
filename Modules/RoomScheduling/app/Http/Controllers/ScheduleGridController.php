<?php

namespace Modules\RoomScheduling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Modules\RoomScheduling\Models\Room;
use Modules\RoomScheduling\Models\Schedule;
use Modules\RoomScheduling\Services\ScheduleConflictService;

class ScheduleGridController extends Controller
{
    public function __construct(protected ScheduleConflictService $conflicts)
    {
    }

    /**
     * Renders the Blade weekly timetable view for the host app to embed
     * (e.g. under an admin "Scheduling" menu item).
     */
    public function index(Request $request)
    {
        $schoolYear = $request->get("school_year");
        $semester = $request->get("semester");

        $schedules = Schedule::query()
            ->with(["room", "instructor", "section.subject"])
            ->when($schoolYear, fn ($q) => $q->where("school_year", $schoolYear))
            ->when($semester, fn ($q) => $q->where("semester", $semester))
            ->orderBy("start_time")
            ->get();

        $rooms = Room::orderBy("name")->get();
        $days = config("roomscheduling.days");
        $timeSlots = collect(range(7 * 60, 19 * 60, 30))->map(
            fn (int $minutes) => Carbon::createFromTime(intdiv($minutes, 60), $minutes % 60)
        );

        return view("roomscheduling::schedule.grid", compact("schedules", "rooms", "days", "timeSlots", "schoolYear", "semester"));
    }

    public function updatePosition(Request $request, Schedule $schedule): JsonResponse
    {
        $data = $request->validate([
            'day' => ['required', Rule::in(config('roomscheduling.days'))],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $proposed = array_merge($schedule->only([
            'room_id', 'instructor_id', 'section_id', 'school_year', 'semester',
        ]), $data);
        $conflicts = $this->conflicts->findConflicts($proposed, $schedule->id);

        if ($conflicts !== []) {
            return response()->json([
                'message' => 'This move creates a scheduling conflict.',
                'conflicts' => array_values($conflicts),
            ], 422);
        }

        $schedule->update($data);

        return response()->json([
            'message' => 'Schedule moved.',
            'data' => [
                'day' => $schedule->day,
                'start_time' => Carbon::parse($schedule->start_time)->format('H:i'),
                'end_time' => Carbon::parse($schedule->end_time)->format('H:i'),
            ],
        ]);
    }
}
