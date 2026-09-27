@extends('layouts.app')

@section('content')
<div class="page-wrap form-shell">
    <div class="page-heading" style="margin-bottom: 18px;">
        <div>
            <div class="eyebrow">Room setup</div>
            <h1>{{ $room->exists ? 'Edit Room' : 'Add Room' }}</h1>
            <p>{{ $room->exists ? 'Update the room details below.' : 'Create a new instructional room for scheduling.' }}</p>
        </div>
    </div>

    <div class="form-panel">
        @if ($errors->any())
            <div class="notice notice-error" style="border-color: #efc3b5; background: #fff5f1; color: #924c3b; margin-bottom: 20px;">
                <ul class="mb-0" style="padding-left: 18px; margin: 0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ $room->exists ? route('roomscheduling.rooms.update', $room) : route('roomscheduling.rooms.store') }}" class="entity-form">
            @csrf
            @if ($room->exists) @method('PUT') @endif

            <div class="form-grid">
                <div class="field-group">
                    <label for="code">Code</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $room->code) }}" placeholder="e.g. R101" required>
                </div>
                <div class="field-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $room->name) }}" placeholder="e.g. Room 101" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label for="building">Building</label>
                    <input type="text" id="building" name="building" value="{{ old('building', $room->building) }}" placeholder="Main building">
                </div>
                <div class="field-group">
                    <label for="floor">Floor</label>
                    <input type="text" id="floor" name="floor" value="{{ old('floor', $room->floor) }}" placeholder="2nd floor">
                </div>
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label for="capacity">Capacity</label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" required>
                </div>
                <div class="field-group">
                    <label for="type">Type</label>
                    <select id="type" name="type" required>
                        @foreach (['lecture', 'laboratory', 'hybrid'] as $type)
                            <option value="{{ $type }}" @selected(old('type', $room->type) === $type)>{{ ucfirst($type) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label class="switch-field" for="is_active">
                <input type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $room->is_active ?? true))>
                Active room
            </label>

            <div class="form-actions">
                <button type="submit" class="button">Save room</button>
                <a href="{{ route('roomscheduling.rooms.index') }}" class="button button-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
