@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-heading">
        <div>
            <div class="eyebrow">Live planning board</div>
            <h1>Room &amp; subject timetable</h1>
            <p>Keep sections, instructors, and rooms moving in sync.</p>
        </div>
        <a class="button" href="{{ route('roomscheduling.schedules.create') }}">+ Add schedule</a>
    </div>

    <form method="GET" class="filter-bar">
        <div class="field">
            <label for="school_year">School year</label>
            <input id="school_year" type="text" name="school_year" value="{{ $schoolYear }}" placeholder="2026-2027">
        </div>
        <div class="field">
            <label for="semester">Semester</label>
            <input id="semester" type="text" name="semester" value="{{ $semester }}" placeholder="First semester">
        </div>
        <button type="submit" class="button">Apply filters</button>
    </form>

    <div class="grid-toolbar">
        <div><span class="grid-status-dot"></span><strong>{{ $schedules->count() }} active slots</strong><span class="grid-help">Drag a class to any half-hour cell to move it.</span></div>
        <div class="grid-legend"><span class="legend-swatch"></span> Available time</div>
    </div>

    <div class="schedule-card">
        <div class="table-scroll">
        <table class="timetable">
            <thead class="table-light">
                <tr>
                    <th class="time-cell">Time</th>
                    @foreach ($days as $day)
                        <th>{{ $day }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $byDay = $schedules->groupBy('day');
                @endphp

                @if ($schedules->isEmpty())
                    <tr>
                        <td colspan="{{ count($days) + 1 }}" class="empty-state">
                            <strong>No schedules found</strong><br>
                            Try another school year or semester to see timetable activity.
                        </td>
                    </tr>
                @else
                    @foreach ($timeSlots as $timeSlot)
                        @php($time = $timeSlot->format('H:i'))
                        <tr>
                            <td class="time-cell">{{ $timeSlot->format('g:i A') }}</td>
                            @foreach ($days as $day)
                                <td class="drop-cell" data-day="{{ $day }}" data-time="{{ $time }}" aria-label="{{ $day }} at {{ $timeSlot->format('g:i A') }}">
                                    @foreach (($byDay[$day] ?? collect())->filter(fn ($slot) => \Illuminate\Support\Carbon::parse($slot->start_time)->format('H:i') === $time) as $slot)
                                        <div class="slot" draggable="true" tabindex="0" role="button" aria-label="Move {{ $slot->section->subject->code ?? 'schedule' }} on {{ $day }}" data-schedule-id="{{ $slot->id }}" data-start="{{ \Illuminate\Support\Carbon::parse($slot->start_time)->format('H:i') }}" data-end="{{ \Illuminate\Support\Carbon::parse($slot->end_time)->format('H:i') }}" data-update-url="{{ route('roomscheduling.timetable.position', $slot) }}">
                                            <span class="slot-grip" aria-hidden="true">::</span>
                                            <div class="slot-code">{{ $slot->section->subject->code ?? 'Open slot' }}</div>
                                            <div class="slot-section">{{ $slot->section->section_code ?? 'Unassigned section' }}</div>
                                            <div class="slot-meta">
                                                {{ $slot->room->name ?? 'Room TBA' }} · {{ $slot->instructor->full_name ?? 'Instructor TBA' }}<br>
                                                {{ \Illuminate\Support\Carbon::parse($slot->start_time)->format('g:i A') }}
                                                -
                                                {{ \Illuminate\Support\Carbon::parse($slot->end_time)->format('g:i A') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        </div>
    </div>
</div>
<div class="drag-toast" role="status" aria-live="polite"></div>
<script>
    (() => {
        const token = document.querySelector('meta[name="csrf-token"]').content;
        const toast = document.querySelector('.drag-toast');
        let dragged = null;

        const showToast = (message, error = false) => {
            toast.textContent = message;
            toast.classList.toggle('error', error);
            toast.classList.add('visible');
            window.setTimeout(() => toast.classList.remove('visible'), 3200);
        };

        document.querySelectorAll('.slot[draggable="true"]').forEach((slot) => {
            slot.addEventListener('dragstart', (event) => {
                dragged = slot;
                event.dataTransfer.effectAllowed = 'move';
                event.dataTransfer.setData('text/plain', slot.dataset.scheduleId);
                slot.classList.add('is-dragging');
                document.body.classList.add('dragging-schedule');
            });
            slot.addEventListener('dragend', () => {
                slot.classList.remove('is-dragging');
                document.body.classList.remove('dragging-schedule');
                document.querySelectorAll('.drop-cell').forEach((cell) => cell.classList.remove('is-over'));
                dragged = null;
            });
        });

        document.querySelectorAll('.drop-cell').forEach((cell) => {
            cell.addEventListener('dragover', (event) => {
                event.preventDefault();
                event.dataTransfer.dropEffect = 'move';
                cell.classList.add('is-over');
            });
            cell.addEventListener('dragleave', (event) => {
                if (!cell.contains(event.relatedTarget)) cell.classList.remove('is-over');
            });
            cell.addEventListener('drop', async (event) => {
                event.preventDefault();
                cell.classList.remove('is-over');
                if (!dragged) return;

                const start = cell.dataset.time;
                const startMinutes = Number(dragged.dataset.start.slice(0, 2)) * 60 + Number(dragged.dataset.start.slice(3));
                const endMinutes = Number(dragged.dataset.end.slice(0, 2)) * 60 + Number(dragged.dataset.end.slice(3));
                const duration = endMinutes - startMinutes;
                const endTotal = Number(start.slice(0, 2)) * 60 + Number(start.slice(3)) + duration;
                const end = `${String(Math.floor(endTotal / 60)).padStart(2, '0')}:${String(endTotal % 60).padStart(2, '0')}`;

                dragged.classList.add('is-saving');
                try {
                    const response = await fetch(dragged.dataset.updateUrl, {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                        body: JSON.stringify({ day: cell.dataset.day, start_time: start, end_time: end }),
                    });
                    const result = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(result.conflicts?.join(' ') || result.message || 'Unable to move schedule.');
                    showToast('Schedule moved successfully.');
                    window.setTimeout(() => window.location.reload(), 350);
                } catch (error) {
                    showToast(error.message, true);
                    dragged.classList.remove('is-saving');
                }
            });
        });
    })();
</script>
@endsection
