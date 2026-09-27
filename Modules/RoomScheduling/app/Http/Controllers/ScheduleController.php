<?php

namespace Modules\RoomScheduling\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RoomScheduling\Http\Requests\ScheduleRequest;
use Modules\RoomScheduling\Models\Instructor;
use Modules\RoomScheduling\Models\Room;
use Modules\RoomScheduling\Models\Schedule;
use Modules\RoomScheduling\Models\Section;
use Modules\RoomScheduling\Services\ScheduleConflictService;

class ScheduleController extends Controller
{
    public function __construct(protected ScheduleConflictService $conflicts)
    {
    }

    /**
     * List schedules, optionally filtered by room, instructor, section, or term.
     */
    public function index(Request $request): JsonResponse
    {
        $schedules = Schedule::query()
            ->with(['room', 'instructor', 'section.subject'])
            ->when($request->room_id, fn ($q) => $q->where('room_id', $request->room_id))
            ->when($request->instructor_id, fn ($q) => $q->where('instructor_id', $request->instructor_id))
            ->when($request->section_id, fn ($q) => $q->where('section_id', $request->section_id))
            ->when($request->school_year, fn ($q) => $q->where('school_year', $request->school_year))
            ->when($request->semester, fn ($q) => $q->where('semester', $request->semester))
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        return response()->json(['data' => $schedules]);
    }

    /**
     * Create a schedule slot. Conflict checks run inside ScheduleRequest;
     * if we reach here the slot is confirmed conflict-free.
     */
    public function store(ScheduleRequest $request): JsonResponse
    {
        $schedule = Schedule::create($request->validated());

        return response()->json([
            'message' => 'Schedule created.',
            'data' => $schedule->load(['room', 'instructor', 'section.subject']),
        ], 201);
    }

    public function show(Schedule $schedule): JsonResponse
    {
        return response()->json(['data' => $schedule->load(['room', 'instructor', 'section.subject'])]);
    }

    public function update(ScheduleRequest $request, Schedule $schedule): JsonResponse
    {
        $schedule->update($request->validated());

        return response()->json([
            'message' => 'Schedule updated.',
            'data' => $schedule->load(['room', 'instructor', 'section.subject']),
        ]);
    }

    public function destroy(Schedule $schedule): JsonResponse
    {
        $schedule->delete();

        return response()->json(['message' => 'Schedule removed.']);
    }

    /**
     * Dry-run a proposed slot without saving — useful for a scheduling UI
     * that wants to warn the user before they hit "Save".
     */
    public function checkConflicts(Request $request): JsonResponse
    {
        $data = $request->validate([
            'room_id' => ['required', 'integer'],
            'instructor_id' => ['required', 'integer'],
            'section_id' => ['required', 'integer'],
            'day' => ['required', 'string'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'school_year' => ['required', 'string'],
            'semester' => ['required', 'string'],
            'ignore_id' => ['nullable', 'integer'],
            'allow_non_block_sectioning' => ['nullable', 'boolean'],
        ]);

        $conflicts = $this->conflicts->findConflicts(
            $data,
            $data['ignore_id'] ?? null,
            (bool) ($data['allow_non_block_sectioning'] ?? false)
        );

        return response()->json([
            'has_conflicts' => count($conflicts) > 0,
            'conflicts' => $conflicts,
        ]);
    }

    /**
     * Weekly grid data: rooms as rows/columns of a timetable for a given term.
     */
    public function weeklyGrid(Request $request): JsonResponse
    {
        $request->validate([
            'school_year' => ['required', 'string'],
            'semester' => ['required', 'string'],
        ]);

        $schedules = Schedule::query()
            ->with(['room', 'instructor', 'section.subject'])
            ->where('school_year', $request->school_year)
            ->where('semester', $request->semester)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        return response()->json(['data' => $schedules]);
    }
}
