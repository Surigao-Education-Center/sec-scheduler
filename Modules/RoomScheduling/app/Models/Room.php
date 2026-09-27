<?php

namespace Modules\RoomScheduling\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'building',
        'floor',
        'capacity',
        'type',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }
}
