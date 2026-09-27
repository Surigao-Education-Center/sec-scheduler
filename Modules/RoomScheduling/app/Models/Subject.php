<?php

namespace Modules\RoomScheduling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'units',
        'lecture_hours',
        'lab_hours',
        'description',
        'is_active',
    ];

    protected $casts = [
        'units' => 'integer',
        'lecture_hours' => 'integer',
        'lab_hours' => 'integer',
        'is_active' => 'boolean',
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
