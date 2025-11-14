<?php

return [
    [
        'name' => 'Bookings',
        'flag' => 'booking.index',
    ],
    [
        'name' => 'Create',
        'flag' => 'booking.create',
        'parent_flag' => 'booking.index',
    ],
    [
        'name' => 'Edit',
        'flag' => 'booking.edit',
        'parent_flag' => 'booking.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'booking.destroy',
        'parent_flag' => 'booking.index',
    ],
];
