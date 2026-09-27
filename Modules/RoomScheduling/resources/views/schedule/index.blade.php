@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-heading">
        <div>
            <div class="eyebrow">Schedule slots</div>
            <h1>Manage schedules</h1>
            <p>Review, adjust, and maintain every room assignment in one place.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('roomscheduling.timetable') }}" class="button button-secondary">View timetable</a>
            <a href="{{ route('roomscheduling.schedules.create') }}" class="button">+ Add schedule</a>
        </div>
    </div>

    @if (session('success'))
        <div class="notice notice-success" role="status">{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <div class="table-scroll">
        <table class="data-table">
        <thead>
            <tr>
                <th>Subject / Section</th>
                <th>Room</th>
                <th>Instructor</th>
                <th>Day</th>
                <th>Time</th>
                <th>Term</th>
                <th class="actions-column">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schedules as $schedule)
                <tr>
                    <td><strong>{{ $schedule->section->subject->code ?? '—' }}</strong><span class="table-subtext">{{ $schedule->section->section_code ?? 'Unassigned section' }}</span></td>
                    <td>{{ $schedule->room->name ?? 'Room TBA' }}</td>
                    <td>{{ $schedule->instructor->full_name ?? 'Instructor TBA' }}</td>
                    <td><span class="day-badge">{{ $schedule->day }}</span></td>
                    <td class="time-range">{{ \Illuminate\Support\Carbon::parse($schedule->start_time)->format('g:i A') }}<span>to</span>{{ \Illuminate\Support\Carbon::parse($schedule->end_time)->format('g:i A') }}</td>
                    <td><span class="table-subtext">{{ $schedule->school_year }}</span>{{ $schedule->semester }}</td>
                    <td class="action-group">
                        <a href="{{ route('roomscheduling.schedules.edit', $schedule) }}" class="button button-small button-secondary">Edit</a>
                        <form action="{{ route('roomscheduling.schedules.destroy', $schedule) }}" method="POST" onsubmit="return confirm('Delete this schedule slot?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button button-small button-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="empty-state">No schedules yet. Add a slot to start building the timetable.</td></tr>
            @endforelse
        </tbody>
    </table>
        </div>
    </div>

    <div class="pagination-wrap">{{ $schedules->links() }}</div>
</div>
@endsection
