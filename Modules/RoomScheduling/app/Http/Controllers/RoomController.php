<?php

namespace Modules\RoomScheduling\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\RoomScheduling\Http\Requests\RoomRequest;
use Modules\RoomScheduling\Models\Room;

class RoomController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(["data" => Room::orderBy("name")->get()]);
    }

    public function store(RoomRequest $request): JsonResponse
    {
        $room = Room::create($request->validated());

        return response()->json(["message" => "Room created.", "data" => $room], 201);
    }

    public function show(Room $room): JsonResponse
    {
        return response()->json(["data" => $room->load("schedules")]);
    }

    public function update(RoomRequest $request, Room $room): JsonResponse
    {
        $room->update($request->validated());

        return response()->json(["message" => "Room updated.", "data" => $room]);
    }

    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json(["message" => "Room removed."]);
    }
}
