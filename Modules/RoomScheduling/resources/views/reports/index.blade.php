@extends('layouts.app')

@section('content')
<div class="page-wrap">
    <div class="page-heading">
        <div>
            <div class="eyebrow">Reporting & analytics</div>
            <h1>Scheduling insights</h1>
            <p>Monitor room use, instructor workload, subject coverage, and conflict history.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('roomscheduling.reports.export.csv', request()->query()) }}" class="button button-secondary">Export CSV</a>
            <a href="{{ route('roomscheduling.reports.print', request()->query()) }}" class="button">Print / Save PDF</a>
        </div>
    </div>

    <form method="GET" class="filter-bar">
        <div class="field">
            <label for="school_year">School year</label>
            <input id="school_year" name="school_year" value="{{ $filters['school_year'] ?? '' }}" placeholder="2026-2027">
        </div>
        <div class="field">
            <label for="semester">Semester</label>
            <input id="semester" name="semester" value="{{ $filters['semester'] ?? '' }}" placeholder="1st Semester">
        </div>
        <div class="field">
            <label for="day">Day</label>
            <input id="day" name="day" value="{{ $filters['day'] ?? '' }}" placeholder="Monday">
        </div>
        <button type="submit" class="button">Apply filters</button>
    </form>

    <div class="report-grid" style="display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:16px; margin-top:22px;">
        <div class="schedule-card" style="padding:20px;">
            <div class="eyebrow">Room utilization</div>
            <h3 style="margin:12px 0 4px; font-size:28px;">{{ number_format($report['room_utilization']->avg('utilization_rate') ?? 0, 2) }}%</h3>
            <p style="margin:0; color:#71807b;">Average room use across all rooms</p>
        </div>

        <div class="schedule-card" style="padding:20px;">
            <div class="eyebrow">Instructor workload</div>
            <h3 style="margin:12px 0 4px; font-size:28px;">{{ $report['instructor_workload']->count() }}</h3>
            <p style="margin:0; color:#71807b;">Active instructors with assigned slots</p>
        </div>

        <div class="schedule-card" style="padding:20px;">
            <div class="eyebrow">Section coverage</div>
            <h3 style="margin:12px 0 4px; font-size:28px;">{{ $report['subject_coverage']->sum('section_count') }}</h3>
            <p style="margin:0; color:#71807b;">Sections currently covered</p>
        </div>

        <div class="schedule-card" style="padding:20px;">
            <div class="eyebrow">Conflict history</div>
            <h3 style="margin:12px 0 4px; font-size:28px;">{{ $report['conflict_count'] }}</h3>
            <p style="margin:0; color:#71807b;">Overlaps detected in the selected view</p>
        </div>
    </div>

    <div class="schedule-card" style="margin-top:22px;">
        <div style="padding:18px 20px; border-bottom:1px solid var(--line);">
            <h3 style="margin:0;">Room utilization</h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Room</th>
                        <th>Used hours</th>
                        <th>Available hours</th>
                        <th>Utilization rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['room_utilization'] as $room)
                        <tr>
                            <td>{{ $room['room_name'] }}</td>
                            <td>{{ number_format($room['hours_used'], 2) }}h</td>
                            <td>{{ number_format($room['hours_available'], 2) }}h</td>
                            <td><strong>{{ number_format($room['utilization_rate'], 2) }}%</strong></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No rooms available for this schedule range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="schedule-card" style="margin-top:22px;">
        <div style="padding:18px 20px; border-bottom:1px solid var(--line);">
            <h3 style="margin:0;">Instructor workload</h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Instructor</th>
                        <th>Slots</th>
                        <th>Scheduled hours</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['instructor_workload'] as $instructor)
                        <tr>
                            <td>{{ $instructor['instructor_name'] }}</td>
                            <td>{{ $instructor['slot_count'] }}</td>
                            <td>{{ number_format($instructor['hours'], 2) }}h</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="empty-state">No instructor workload to display.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="schedule-card" style="margin-top:22px;">
        <div style="padding:18px 20px; border-bottom:1px solid var(--line);">
            <h3 style="margin:0;">Subject coverage</h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Subject</th>
                        <th>Sections</th>
                        <th>Slots</th>
                        <th>Hours</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['subject_coverage'] as $subject)
                        <tr>
                            <td>{{ $subject['subject_name'] }} ({{ $subject['subject_code'] }})</td>
                            <td>{{ $subject['section_count'] }}</td>
                            <td>{{ $subject['slot_count'] }}</td>
                            <td>{{ number_format($subject['hours'], 2) }}h</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty-state">No subject coverage available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="schedule-card" style="margin-top:22px;">
        <div style="padding:18px 20px; border-bottom:1px solid var(--line);">
            <h3 style="margin:0;">Conflict history</h3>
        </div>
        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($report['conflict_history'] as $conflict)
                        <tr>
                            <td><span class="day-badge" style="background:#fff0ec;color:#a34d3a;">{{ ucfirst($conflict['type']) }}</span></td>
                            <td>{{ $conflict['message'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2" class="empty-state">No scheduling conflicts detected in the selected range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
