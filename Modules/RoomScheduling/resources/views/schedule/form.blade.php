@extends('layouts.app')

@section('content')
<div class="page-wrap form-shell">
    <div class="page-heading" style="margin-bottom: 18px;">
        <div>
            <div class="eyebrow">Schedule setup</div>
            <h1>{{ $schedule->exists ? 'Edit Schedule' : 'Add Schedule' }}</h1>
            <p>{{ $schedule->exists ? 'Adjust the schedule details below.' : 'Create a new class slot and validate room or instructor conflicts.' }}</p>
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

        {{-- Live conflict warning banner, filled in by JS below before the form is even submitted --}}
        <div id="conflict-banner" class="notice notice-warning d-none" style="display:none;"></div>

        <form method="POST" id="schedule-form" action="{{ $schedule->exists ? route('roomscheduling.schedules.update', $schedule) : route('roomscheduling.schedules.store') }}" class="entity-form">
            @csrf
            @if ($schedule->exists) @method('PUT') @endif

            <div class="field-group">
                <label for="section_id">Section</label>
                <select name="section_id" id="section_id" required>
                    <option value="">— Select section —</option>
                    @foreach ($sections as $section)
                        <option value="{{ $section->id }}" @selected(old('section_id', $schedule->section_id) == $section->id)>
                            {{ $section->subject->code ?? '' }} · {{ $section->section_code }} ({{ $section->school_year }}, {{ $section->semester }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label for="room_id">Room</label>
                    <select name="room_id" id="room_id" required>
                        <option value="">— Select room —</option>
                        @foreach ($rooms as $room)
                            <option value="{{ $room->id }}" @selected(old('room_id', $schedule->room_id) == $room->id)>
                                {{ $room->code }} - {{ $room->name }} (cap. {{ $room->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label for="instructor_id">Instructor</label>
                    <select name="instructor_id" id="instructor_id" required>
                        <option value="">— Select instructor —</option>
                        @foreach ($instructors as $instructor)
                            <option value="{{ $instructor->id }}" @selected(old('instructor_id', $schedule->instructor_id) == $instructor->id)>
                                {{ $instructor->last_name }}, {{ $instructor->first_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label for="day">Day</label>
                    <select name="day" id="day" required>
                        <option value="">— Select day —</option>
                        @foreach ($days as $day)
                            <option value="{{ $day }}" @selected(old('day', $schedule->day) === $day)>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="field-group">
                    <label for="start_time">Start time</label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $schedule->start_time) }}" required>
                </div>
                <div class="field-group">
                    <label for="end_time">End time</label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $schedule->end_time) }}" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="field-group">
                    <label for="school_year">School year</label>
                    <input type="text" name="school_year" id="school_year" value="{{ old('school_year', $schedule->school_year) }}" placeholder="e.g. 2026-2027" required>
                </div>
                <div class="field-group">
                    <label for="semester">Semester</label>
                    <select name="semester" id="semester" required>
                        <option value="">— Select semester —</option>
                        @foreach (config('roomscheduling.semesters') as $semester)
                            <option value="{{ $semester }}" @selected(old('semester', $schedule->semester) === $semester)>{{ $semester }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label class="switch-field" for="allow_non_block_sectioning">
                <input type="checkbox" name="allow_non_block_sectioning" value="1" id="allow_non_block_sectioning" @checked(old('allow_non_block_sectioning', false))>
                Allow custom non-block sectioning
            </label>

            <div class="form-actions">
                <button type="submit" id="submit-btn" class="button">Save schedule</button>
                <a href="{{ route('roomscheduling.schedules.index') }}" class="button button-secondary">Cancel</a>
                <span id="checking-indicator" class="text-muted small ms-2 d-none" style="color: var(--muted);">Checking for conflicts…</span>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const checkUrl = @json(route('roomscheduling.api.schedules.check-conflicts'));
    const ignoreId = @json($schedule->id);

    const fields = ['section_id', 'room_id', 'instructor_id', 'day', 'start_time', 'end_time', 'school_year', 'semester'];
    const banner = document.getElementById('conflict-banner');
    const indicator = document.getElementById('checking-indicator');
    const submitBtn = document.getElementById('submit-btn');
    const allowNonBlockSectioning = document.getElementById('allow_non_block_sectioning');

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
            banner.style.display = 'none';
            return;
        }

        values.allow_non_block_sectioning = allowNonBlockSectioning && allowNonBlockSectioning.checked ? 1 : 0;

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
                    banner.style.display = 'block';
                    submitBtn.disabled = true;
                } else {
                    banner.classList.add('d-none');
                    banner.style.display = 'none';
                    submitBtn.disabled = false;
                }
            })
            .catch(() => {
                indicator.classList.add('d-none');
            });
    }

    fields.forEach((f) => {
        const el = document.getElementById(f);
        if (!el) return;
        el.addEventListener('change', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(checkConflicts, 300);
        });
    });

    if (allowNonBlockSectioning) {
        allowNonBlockSectioning.addEventListener('change', () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(checkConflicts, 200);
        });
    }
})();
</script>
@endsection
