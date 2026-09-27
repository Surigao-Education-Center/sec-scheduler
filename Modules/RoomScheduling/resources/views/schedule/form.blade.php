@extends('layouts.app')

@section('content')
<div class="container py-4" style="max-width: 720px;">
    <h3>{{ $schedule->exists ? 'Edit Schedule' : 'Add Schedule' }}</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Live conflict warning banner, filled in by JS below before the form is even submitted --}}
    <div id="conflict-banner" class="alert alert-warning d-none"></div>

    <form method="POST" id="schedule-form" action="{{ $schedule->exists ? route('roomscheduling.schedules.update', $schedule) : route('roomscheduling.schedules.store') }}">
        @csrf
        @if ($schedule->exists) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Section</label>
            <select name="section_id" id="section_id" class="form-select" required>
                <option value="">— Select section —</option>
                @foreach ($sections as $section)
                    <option value="{{ $section->id }}" @selected(old('section_id', $schedule->section_id) == $section->id)>
                        {{ $section->subject->code ?? '' }} · {{ $section->section_code }} ({{ $section->school_year }}, {{ $section->semester }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Room</label>
                <select name="room_id" id="room_id" class="form-select" required>
                    <option value="">— Select room —</option>
                    @foreach ($rooms as $room)
                        <option value="{{ $room->id }}" @selected(old('room_id', $schedule->room_id) == $room->id)>
                            {{ $room->code }} - {{ $room->name }} (cap. {{ $room->capacity }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col mb-3">
                <label class="form-label">Instructor</label>
                <select name="instructor_id" id="instructor_id" class="form-select" required>
                    <option value="">— Select instructor —</option>
                    @foreach ($instructors as $instructor)
                        <option value="{{ $instructor->id }}" @selected(old('instructor_id', $schedule->instructor_id) == $instructor->id)>
                            {{ $instructor->last_name }}, {{ $instructor->first_name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">Day</label>
                <select name="day" id="day" class="form-select" required>
                    <option value="">— Select day —</option>
                    @foreach ($days as $day)
                        <option value="{{ $day }}" @selected(old('day', $schedule->day) === $day)>{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col mb-3">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" id="start_time" class="form-control" value="{{ old('start_time', $schedule->start_time) }}" required>
            </div>
            <div class="col mb-3">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time" id="end_time" class="form-control" value="{{ old('end_time', $schedule->end_time) }}" required>
            </div>
        </div>

        <div class="row">
            <div class="col mb-3">
                <label class="form-label">School Year</label>
                <input type="text" name="school_year" id="school_year" class="form-control" value="{{ old('school_year', $schedule->school_year) }}" placeholder="e.g. 2026-2027" required>
            </div>
            <div class="col mb-3">
                <label class="form-label">Semester</label>
                <select name="semester" id="semester" class="form-select" required>
                    <option value="">— Select semester —</option>
                    @foreach (config('roomscheduling.semesters') as $semester)
                        <option value="{{ $semester }}" @selected(old('semester', $schedule->semester) === $semester)>{{ $semester }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" id="submit-btn" class="btn btn-primary">Save Schedule</button>
        <a href="{{ route('roomscheduling.schedules.index') }}" class="btn btn-link">Cancel</a>
        <span id="checking-indicator" class="text-muted small ms-2 d-none">Checking for conflicts…</span>
    </form>
</div>

<script>
(function () {
    const checkUrl = @json(route('roomscheduling.api.schedules.check-conflicts'));
    const ignoreId = @json($schedule->id);

    const fields = ['section_id', 'room_id', 'instructor_id', 'day', 'start_time', 'end_time', 'school_year', 'semester'];
    const banner = document.getElementById('conflict-banner');
    const indicator = document.getElementById('checking-indicator');
    const submitBtn = document.getElementById('submit-btn');

    let debounceTimer = null;

    function currentValues() {
        const values = {};
        fields.forEach((f) => {
            values[f] = document.getElementById(f).value;
        });
        return values;
    }

    function allFieldsFilled(values) {
        return fields.every((f) => values[f] && values[f].length > 0);
    }

    function checkConflicts() {
        const values = currentValues();

        if (!allFieldsFilled(values)) {
            banner.classList.add('d-none');
            return;
        }

        if (ignoreId) {
            values.ignore_id = ignoreId;
        }

        indicator.classList.remove('d-none');

        fetch(checkUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(values),
        })
            .then((res) => res.json())
            .then((data) => {
                indicator.classList.add('d-none');

                if (data.has_conflicts) {
                    const messages = Object.values(data.conflicts).join('<br>');
                    banner.innerHTML = '<strong>Scheduling conflict:</strong><br>' + messages;
                    banner.classList.remove('d-none');
                    submitBtn.disabled = true;
                } else {
                    banner.classList.add('d-none');
                    submitBtn.disabled = false;
                }
            })
            .catch(() => {
                // If the check itself fails (network, auth, etc.), don't block
                // submission — the server-side validation in ScheduleRequest
                // will still catch real conflicts when the form is submitted.
                indicator.classList.add('d-none');
            });
    }

    fields.forEach((f) => {
        document.getElementById(f).addEventListener('change', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(checkConflicts, 300);
        });
    });
})();
</script>
@endsection
