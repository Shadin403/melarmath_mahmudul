<?php

namespace Botble\Booking\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Booking\Models\BlockedDate;
use Botble\Booking\Models\BlockedTime;
use Illuminate\Http\Request;

class BlockedController extends BaseController
{
    public function dates()
    {
        $this->pageTitle('Blocked Dates');
        $dates = BlockedDate::orderBy('date', 'asc')->get();

        return view('plugins/booking::admin.blocked-dates', compact('dates'));
    }

    public function saveDate(Request $request)
    {
        $request->validate([
            'date'   => 'required|date',
            'reason' => 'nullable|string|max:255',
        ]);

        BlockedDate::firstOrCreate(
            ['date' => $request->date],
            ['reason' => $request->reason]
        );

        return back()->with('status', 'Date blocked successfully!');
    }

    public function deleteDate(BlockedDate $date)
    {
        $date->delete();
        return back()->with('status', 'Date unblocked!');
    }

    public function times()
    {
        $this->pageTitle('Blocked Times');
        $times = BlockedTime::orderBy('date', 'asc')->get();

        return view('plugins/booking::admin.blocked-times', compact('times'));
    }

    public function saveTime(Request $request)
    {
        $request->validate([
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'reason'     => 'nullable|string|max:255',
        ]);

        BlockedTime::create($request->only('date', 'start_time', 'end_time', 'reason'));

        return back()->with('status', 'Time blocked successfully!');
    }

    public function deleteTime(BlockedTime $time)
    {
        $time->delete();
        return back()->with('status', 'Time unblocked!');
    }
}
