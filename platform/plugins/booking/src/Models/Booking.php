<?php

namespace Botble\Booking\Models;

use Botble\Base\Models\BaseModel;
use Botble\Booking\Enums\ReservationStatusEnum;

class Booking extends BaseModel
{
    protected $table = 'bookings';

    protected $fillable = [
        'name', 'email', 'phone', 'date', 'start_time', 'end_time', 'status', 'note'
    ];

    protected $casts = [
        'status' => ReservationStatusEnum::class,
        'date'   => 'date:Y-m-d',
    ];
}
