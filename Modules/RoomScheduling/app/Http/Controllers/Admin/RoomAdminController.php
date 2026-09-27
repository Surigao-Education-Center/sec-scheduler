<?php

namespace Modules\RoomScheduling\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Modules\RoomScheduling\Http\Requests\RoomRequest;
use Modules\RoomScheduling\Models\Room;

class RoomAdminController extends Controller
{
    public function index(): View
    {
        return view("roomscheduling::rooms.index", [
            "rooms" => Room::orderBy("name")->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view("roomscheduling::rooms.form", ["room" => new Room()]);
    }

    public function store(RoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());

        return redirect()->route("roomscheduling.rooms.index")->with("success", "Room added.");
    }

    public function edit(Room $room): View
    {
        return view("roomscheduling::rooms.form", compact("room"));
    }

    public function update(RoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        return redirect()->route("roomscheduling.rooms.index")->with("success", "Room updated.");
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()->route("roomscheduling.rooms.index")->with("success", "Room removed.");
    }
}
