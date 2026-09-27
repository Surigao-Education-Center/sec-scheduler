@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Rooms</h3>
        <a href="{{ route('roomscheduling.rooms.create') }}" class="btn btn-primary btn-sm">+ Add Room</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-sm bg-white">
        <thead class="table-light">
            <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Building / Floor</th>
                <th>Capacity</th>
                <th>Type</th>
                <th>Active</th>
                <th style="width:140px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rooms as $room)
                <tr>
                    <td>{{ $room->code }}</td>
                    <td>{{ $room->name }}</td>
                    <td>{{ $room->building }} {{ $room->floor ? '/ Fl. '.$room->floor : '' }}</td>
                    <td>{{ $room->capacity }}</td>
                    <td>{{ ucfirst($room->type) }}</td>
                    <td>{{ $room->is_active ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('roomscheduling.rooms.edit', $room) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                        <form action="{{ route('roomscheduling.rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this room?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-3">No rooms yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $rooms->links() }}
</div>
@endsection
