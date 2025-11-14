<?php

namespace Botble\Booking\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedTime extends Model
{
    protected $table = 'booking_blocked_times';
    protected $fillable = ['date', 'start_time', 'end_time', 'reason'];
}
