<?php

namespace Modules\RoomScheduling\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'section_id',
        'room_id',
        'instructor_id',
        'day',
        'start_time',
        'end_time',
        'school_year',
        'semester',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Instructor::class);
    }

    /**
     * Restrict a query to rows on the same day/term whose time range
     * overlaps the given start/end time. Two ranges overlap when
     * existing.start < new.end AND existing.end > new.start.
     */
    public function scopeOverlapping(Builder $query, string $day, string $startTime, string $endTime, string $schoolYear, string $semester): Builder
    {
        return $query
            ->where('day', $day)
            ->where('school_year', $schoolYear)
            ->where('semester', $semester)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);
    }
}
