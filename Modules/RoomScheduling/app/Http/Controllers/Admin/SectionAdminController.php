<?php

namespace Modules\RoomScheduling\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\RoomScheduling\Http\Requests\SectionRequest;
use Modules\RoomScheduling\Models\Section;
use Modules\RoomScheduling\Models\Subject;

class SectionAdminController extends Controller
{
    public function index(): View
    {
        return view("roomscheduling::sections.index", [
            "sections" => Section::with("subject")->orderBy("section_code")->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view("roomscheduling::sections.form", [
            "section" => new Section(),
            "subjects" => Subject::orderBy("code")->get(),
        ]);
    }

    public function store(SectionRequest $request): RedirectResponse
    {
        Section::create($request->validated());

        return redirect()->route("roomscheduling.sections.index")->with("success", "Section added.");
    }

    public function edit(Section $section): View
    {
        return view("roomscheduling::sections.form", [
            "section" => $section,
            "subjects" => Subject::orderBy("code")->get(),
        ]);
    }

    public function update(SectionRequest $request, Section $section): RedirectResponse
    {
        $section->update($request->validated());

        return redirect()->route("roomscheduling.sections.index")->with("success", "Section updated.");
    }

    public function destroy(Section $section): RedirectResponse
    {
        $section->delete();

        return redirect()->route("roomscheduling.sections.index")->with("success", "Section removed.");
    }
}
