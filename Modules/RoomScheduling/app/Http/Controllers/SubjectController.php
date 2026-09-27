<?php

namespace Modules\RoomScheduling\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\RoomScheduling\Http\Requests\SubjectRequest;
use Modules\RoomScheduling\Models\Subject;

class SubjectController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(["data" => Subject::orderBy("code")->get()]);
    }

    public function store(SubjectRequest $request): JsonResponse
    {
        $subject = Subject::create($request->validated());

        return response()->json(["message" => "Subject created.", "data" => $subject], 201);
    }

    public function show(Subject $subject): JsonResponse
    {
        return response()->json(["data" => $subject->load("sections")]);
    }

    public function update(SubjectRequest $request, Subject $subject): JsonResponse
    {
        $subject->update($request->validated());

        return response()->json(["message" => "Subject updated.", "data" => $subject]);
    }

    public function destroy(Subject $subject): JsonResponse
    {
        $subject->delete();

        return response()->json(["message" => "Subject removed."]);
    }
}
