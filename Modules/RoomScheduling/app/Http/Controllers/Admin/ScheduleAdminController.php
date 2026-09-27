<?php

namespace Modules\RoomScheduling\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\RoomScheduling\Http\Requests\ScheduleRequest;
use Modules\RoomScheduling\Models\Instructor;
use Modules\RoomScheduling\Models\Room;
use Modules\RoomScheduling\Models\Schedule;
use Modules\RoomScheduling\Models\Section;

class ScheduleAdminController extends Controller
{
    public function index(): View
    {
        return view('roomscheduling::schedule.index', [
            'schedules' => Schedule::with(['room', 'instructor', 'section.subject'])
                ->orderBy('day')
                ->orderBy('start_time')
                ->paginate(20),
        ]);
    }

    public function create(): View
    {
        return $this->formView(new Schedule());
    }

    /**
     * ScheduleRequest already runs the conflict check (room/instructor/section
     * overlap) via withValidator(). If it fails, Laravel redirects back with
     * the errors and old input automatically — the form below re-renders
     * showing exactly which field conflicted.
     */
    public function store(ScheduleRequest $request): RedirectResponse
    {
        Schedule::create($request->validated());

        return redirect()->route('roomscheduling.schedules.index')->with('success', 'Schedule created.');
    }

    public function edit(Schedule $schedule): View
    {
        return $this->formView($schedule);
    }

    public function update(ScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        return redirect()->route('roomscheduling.schedules.index')->with('success', 'Schedule updated.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()->route('roomscheduling.schedules.index')->with('success', 'Schedule removed.');
    }

    protected function formView(Schedule $schedule): View
    {
        return view('roomscheduling::schedule.form', [
            'schedule' => $schedule,
            'rooms' => Room::orderBy('name')->get(),
            'instructors' => Instructor::orderBy('last_name')->get(),
            'sections' => Section::with('subject')->orderBy('section_code')->get(),
            'days' => config('roomscheduling.days'),
        ]);
    }
}
