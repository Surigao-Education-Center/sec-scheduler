<?php

namespace Modules\RoomScheduling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\RoomScheduling\Services\ReportAnalyticsService;

class ReportsController extends Controller
{
    public function __construct(protected ReportAnalyticsService $analytics)
    {
    }

    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $report = $this->analytics->build($filters);

        return view('roomscheduling::reports.index', [
            'filters' => $filters,
            'report' => $report,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $filters = $this->filters($request);
        $report = $this->analytics->build($filters);

        $rows = [];
        $rows[] = ['school_year', 'semester', 'day', 'room_name', 'instructor_name', 'section_code', 'subject_name', 'start_time', 'end_time', 'duration_minutes', 'utilization_rate'];

        foreach ($report['schedules'] as $schedule) {
            $durationMinutes = max((int) round((strtotime($schedule->end_time) - strtotime($schedule->start_time)) / 60), 0);
            $utilizationRate = 0;

            if (isset($schedule->room)) {
                $roomUtilization = collect($report['room_utilization'])->firstWhere('room_id', $schedule->room_id);
                $utilizationRate = $roomUtilization['utilization_rate'] ?? 0;
            }

            $rows[] = [
                $schedule->school_year,
                $schedule->semester,
                $schedule->day,
                $schedule->room?->name ?? 'TBA',
                $schedule->instructor?->full_name ?? 'TBA',
                $schedule->section?->section_code ?? 'TBA',
                $schedule->section?->subject?->name ?? 'TBA',
                $schedule->start_time,
                $schedule->end_time,
                $durationMinutes,
                number_format((float) $utilizationRate, 2, '.', ''),
            ];
        }

        $csv = fopen('php://temp', 'w+');
        foreach ($rows as $row) {
            fputcsv($csv, $row);
        }
        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return response($content)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="room-scheduling-report.csv"')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function print(Request $request)
    {
        $filters = $this->filters($request);
        $report = $this->analytics->build($filters);

        return view('roomscheduling::reports.print', [
            'filters' => $filters,
            'report' => $report,
        ]);
    }

    protected function filters(Request $request): array
    {
        return [
            'school_year' => $request->get('school_year'),
            'semester' => $request->get('semester'),
            'day' => $request->get('day'),
        ];
    }
}
