<?php

namespace Modules\RoomScheduling\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\RoomScheduling\Http\Requests\InstructorRequest;
use Modules\RoomScheduling\Models\Instructor;

class InstructorController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(["data" => Instructor::orderBy("last_name")->get()]);
    }

    public function store(InstructorRequest $request): JsonResponse
    {
        $instructor = Instructor::create($request->validated());

        return response()->json(["message" => "Instructor created.", "data" => $instructor], 201);
    }

    public function show(Instructor $instructor): JsonResponse
    {
        return response()->json(["data" => $instructor->load("schedules")]);
    }

    public function update(InstructorRequest $request, Instructor $instructor): JsonResponse
    {
        $instructor->update($request->validated());

        return response()->json(["message" => "Instructor updated.", "data" => $instructor]);
    }

    public function destroy(Instructor $instructor): JsonResponse
    {
        $instructor->delete();

        return response()->json(["message" => "Instructor removed."]);
    }
}
