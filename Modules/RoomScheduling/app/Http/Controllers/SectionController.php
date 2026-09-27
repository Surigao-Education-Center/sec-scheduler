<?php

namespace Modules\RoomScheduling\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RoomScheduling\Http\Requests\SectionRequest;
use Modules\RoomScheduling\Models\Section;

class SectionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $sections = Section::query()
            ->with("subject")
            ->when($request->subject_id, fn ($q) => $q->where("subject_id", $request->subject_id))
            ->when($request->school_year, fn ($q) => $q->where("school_year", $request->school_year))
            ->when($request->semester, fn ($q) => $q->where("semester", $request->semester))
            ->orderBy("section_code")
            ->get();

        return response()->json(["data" => $sections]);
    }

    public function store(SectionRequest $request): JsonResponse
    {
        $section = Section::create($request->validated());

        return response()->json(["message" => "Section created.", "data" => $section->load("subject")], 201);
    }

    public function show(Section $section): JsonResponse
    {
        return response()->json(["data" => $section->load(["subject", "schedules.room", "schedules.instructor"])]);
    }

    public function update(SectionRequest $request, Section $section): JsonResponse
    {
        $section->update($request->validated());

        return response()->json(["message" => "Section updated.", "data" => $section->load("subject")]);
    }

    public function destroy(Section $section): JsonResponse
    {
        $section->delete();

        return response()->json(["message" => "Section removed."]);
    }
}
