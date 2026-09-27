@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 640px;">
    <h3>{{ $room->exists ? 'Edit Room' : 'Add Room' }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $room->exists ? route('roomscheduling.rooms.update', $room) : route('roomscheduling.rooms.store') }}">
        @csrf
        @if ($room->exists) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Code</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $room->code) }}" placeholder="e.g. R101" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $room->name) }}" placeholder="e.g. Room 101" required>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Building</label>
                <input type="text" name="building" class="form-control" value="{{ old('building', $room->building) }}">
            </div>
            <div class="col mb-3">
                <label class="form-label">Floor</label>
                <input type="text" name="floor" class="form-control" value="{{ old('floor', $room->floor) }}">
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Capacity</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $room->capacity) }}" min="1" required>
            </div>
            <div class="col mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select" required>
                    @foreach (['lecture', 'laboratory', 'hybrid'] as $type)
                        <option value="{{ $type }}" @selected(old('type', $room->type) === $type)>{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" @checked(old('is_active', $room->is_active ?? true))>
            <label class="form-check-label" for="is_active">Active</label>
        </div>

        <button type="submit" class="btn btn-primary">Save Room</button>
        <a href="{{ route('roomscheduling.rooms.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
