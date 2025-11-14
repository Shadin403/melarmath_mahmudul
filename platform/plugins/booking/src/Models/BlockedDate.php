<?php

namespace Botble\Booking\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedDate extends Model
{
    protected $table = 'booking_blocked_dates';
    protected $fillable = ['date', 'reason'];
}
