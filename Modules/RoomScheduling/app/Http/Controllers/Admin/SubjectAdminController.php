<?php

namespace Modules\RoomScheduling\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\RoomScheduling\Http\Requests\SubjectRequest;
use Modules\RoomScheduling\Models\Subject;

class SubjectAdminController extends Controller
{
    public function index(): View
    {
        return view("roomscheduling::subjects.index", [
            "subjects" => Subject::orderBy("code")->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view("roomscheduling::subjects.form", ["subject" => new Subject()]);
    }

    public function store(SubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()->route("roomscheduling.subjects.index")->with("success", "Subject added.");
    }

    public function edit(Subject $subject): View
    {
        return view("roomscheduling::subjects.form", compact("subject"));
    }

    public function update(SubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()->route("roomscheduling.subjects.index")->with("success", "Subject updated.");
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()->route("roomscheduling.subjects.index")->with("success", "Subject removed.");
    }
}
