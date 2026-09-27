<?php

namespace Modules\RoomScheduling\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\RoomScheduling\Http\Requests\InstructorRequest;
use Modules\RoomScheduling\Models\Instructor;

class InstructorAdminController extends Controller
{
    public function index(): View
    {
        return view("roomscheduling::instructors.index", [
            "instructors" => Instructor::orderBy("last_name")->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view("roomscheduling::instructors.form", ["instructor" => new Instructor()]);
    }

    public function store(InstructorRequest $request): RedirectResponse
    {
        Instructor::create($request->validated());

        return redirect()->route("roomscheduling.instructors.index")->with("success", "Instructor added.");
    }

    public function edit(Instructor $instructor): View
    {
        return view("roomscheduling::instructors.form", compact("instructor"));
    }

    public function update(InstructorRequest $request, Instructor $instructor): RedirectResponse
    {
        $instructor->update($request->validated());

        return redirect()->route("roomscheduling.instructors.index")->with("success", "Instructor updated.");
    }

    public function destroy(Instructor $instructor): RedirectResponse
    {
        $instructor->delete();

        return redirect()->route("roomscheduling.instructors.index")->with("success", "Instructor removed.");
    }
}
