<?php

return [
    'name' => 'RoomScheduling',

    // Days recognized by the scheduler
    'days' => [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ],

    // Default semester labels — adjust to match your enrollment system
    'semesters' => [
        '1st Semester',
        '2nd Semester',
        'Summer',
    ],

    // Earliest/latest bookable time, used for grid rendering and validation
    'day_start' => '07:00',
    'day_end'   => '21:00',
];
