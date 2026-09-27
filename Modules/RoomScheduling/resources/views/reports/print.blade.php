<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Scheduling Report</title>
    <style>
        body { font-family: Arial, sans-serif; color: #17211f; margin: 32px; }
        h1 { margin-bottom: 8px; }
        .meta { color: #5d6980; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        th, td { border: 1px solid #dfe7e3; padding: 8px 10px; text-align: left; }
        th { background: #f5f8f6; }
        .pill { display: inline-block; padding: 4px 8px; border-radius: 999px; background: #dff2e9; color: #146b63; }
        @media print { .no-print { display:none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding:10px 14px; border:0; background:#146b63;color:#fff;border-radius:7px;cursor:pointer;">Print / Save as PDF</button>
    </div>

    <h1>Room Scheduling Report</h1>
    <div class="meta">
        @if($filters['school_year'] || $filters['semester'] || $filters['day'])
            {{ $filters['school_year'] ?? 'All years' }} / {{ $filters['semester'] ?? 'All semesters' }} / {{ $filters['day'] ?? 'All days' }}
        @else
            All schedule data
        @endif
    </div>

    <h2>Room utilization</h2>
    <table>
        <thead>
            <tr><th>Room</th><th>Used hours</th><th>Utilization</th></tr>
        </thead>
        <tbody>
            @foreach($report['room_utilization'] as $room)
                <tr>
                    <td>{{ $room['room_name'] }}</td>
                    <td>{{ number_format($room['hours_used'], 2) }}h</td>
                    <td>{{ number_format($room['utilization_rate'], 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Instructor workload</h2>
    <table>
        <thead>
            <tr><th>Instructor</th><th>Slots</th><th>Hours</th></tr>
        </thead>
        <tbody>
            @foreach($report['instructor_workload'] as $instructor)
                <tr>
                    <td>{{ $instructor['instructor_name'] }}</td>
                    <td>{{ $instructor['slot_count'] }}</td>
                    <td>{{ number_format($instructor['hours'], 2) }}h</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Conflict history</h2>
    <table>
        <thead>
            <tr><th>Type</th><th>Details</th></tr>
        </thead>
        <tbody>
            @forelse($report['conflict_history'] as $conflict)
                <tr>
                    <td><span class="pill">{{ ucfirst($conflict['type']) }}</span></td>
                    <td>{{ $conflict['message'] }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No scheduling conflicts were detected.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
