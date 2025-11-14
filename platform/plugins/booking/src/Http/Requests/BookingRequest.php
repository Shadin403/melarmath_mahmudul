<?php

namespace Botble\Booking\Http\Requests;

use Botble\Support\Http\Requests\Request;

class BookingRequest extends Request
{
    public function rules(): array
    {
        return [
            'name'       => ['required','string','max:150'],
            'email'      => ['nullable','email','max:150'],
            'phone'      => ['nullable','string','max:50'],
            'date'       => ['required','date'],
            'start_time' => ['required','date_format:H:i'],
            'end_time'   => ['required','date_format:H:i','after:start_time'],
            'status'     => ['required','in:pending,confirmed,canceled'],
        ];
    }
}
