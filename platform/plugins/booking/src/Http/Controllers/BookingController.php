<?php

namespace Botble\Booking\Http\Controllers;
use Theme;


use Botble\Base\Http\Actions\DeleteResourceAction;

use Botble\Booking\Models\Booking;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Booking\Tables\BookingTable;
use Botble\Booking\Forms\BookingForm;
use Illuminate\Http\Request;
use Botble\Setting\Facades\Setting;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Botble\Booking\Http\Requests\BookingRequest;


class BookingController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/booking::booking.name')), route('booking.index'));
    }

    public function index(BookingTable $table)
    {
        $this->pageTitle(trans('plugins/booking::booking.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/booking::booking.create'));

        return BookingForm::create()->renderForm();
    }

    public function store(BookingRequest $request)
    {
        $form = BookingForm::create()->setRequest($request);

        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('booking.index'))
            ->setNextUrl(route('booking.edit', $form->getModel()->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function edit(Booking $booking)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $booking->name]));

        return BookingForm::createFromModel($booking)->renderForm();
    }

    public function update(Booking $booking, BookingRequest $request)
    {
        BookingForm::createFromModel($booking)
            ->setRequest($request)
            ->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('booking.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(Booking $booking)
    {
        return DeleteResourceAction::make($booking);
    }
    public function widget()
    {
       return Theme::scope('booking-page')->render();

    }

    public function slots(Request $request)
{
    try {
        $start = \Carbon\Carbon::parse($request->query('start', now()->startOfWeek()));

        $slotMinutes = (int) \Setting::get('booking_slot_minutes', 30);
        $open  = \Setting::get('booking_open_time', '09:00');
        $close = \Setting::get('booking_close_time', '18:00');

        $todayStart = now()->startOfDay();
        $todayDate  = $todayStart->toDateString();
        $nowTime    = now()->format('H:i');

        $days = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $start->copy()->addDays($i)->toDateString();
            $dayCarbon = \Carbon\Carbon::parse($date);

            if ($dayCarbon->lt($todayStart)) {
                $days[] = [
                    'date'  => $date,
                    'slots' => [
                        ['start' => '-', 'end' => '-', 'available' => false, 'closed' => true],
                    ],
                ];
                continue;
            }

            $isBlockedDay = \Botble\Booking\Models\BlockedDate::whereDate('date', $date)->exists();
            if ($isBlockedDay) {
                $days[] = [
                    'date'  => $date,
                    'slots' => [
                        ['start' => '-', 'end' => '-', 'available' => false, 'closed' => true],
                    ],
                ];
                continue;
            }

            $daySlots = [];
            $from = \Carbon\Carbon::parse($date . ' ' . $open);
            $to   = \Carbon\Carbon::parse($date . ' ' . $close);

            for ($t = $from->copy(); $t->lt($to); $t->addMinutes($slotMinutes)) {
                $st = $t->format('H:i');
                $et = $t->copy()->addMinutes($slotMinutes)->format('H:i');

                $isBlockedTime = \Botble\Booking\Models\BlockedTime::query()
                    ->whereDate('date', $date)
                    ->where(function ($q) use ($st, $et) {
                        $q->whereBetween('start_time', [$st, $et])
                          ->orWhereBetween('end_time', [$st, $et])
                          ->orWhere(function ($q2) use ($st, $et) {
                              $q2->where('start_time', '<=', $st)
                                 ->where('end_time', '>=', $et);
                          });
                    })
                    ->exists();

                $isBooked = \Botble\Booking\Models\Booking::query()
                    ->whereDate('date', $date)
                    ->where('start_time', $st)
                    ->where('end_time', $et)
                    ->whereNotIn('status', ['canceled'])
                    ->exists();

                $isPastSlotToday = ($date === $todayDate) && ($et <= $nowTime);

                $available = ! $isBooked && ! $isBlockedTime && ! $isPastSlotToday;

                $daySlots[] = [
                    'start'     => $st,
                    'end'       => $et,
                    'available' => $available,
                    'closed'    => $isBlockedTime, // فقط برای بازه‌های بلاک شده true می‌گذاریم
                ];
            }

            $days[] = [
                'date'  => $date,
                'slots' => $daySlots,
            ];
        }

        return response()->json([
            'start' => $start->toDateString(),
            'days'  => $days,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
}




public function reserve(Request $request)
{
    try {
        \Log::info('📥 Reserve request data', $request->all());

        if ($request->filled('date')) {
            $request->merge([
                'date' => date('Y-m-d', strtotime($request->input('date')))
            ]);
        }

        $data = $request->validate([
            'name'       => ['required','string','max:150'],
            'email'      => ['nullable','email','max:150'],
            'phone'      => ['nullable','string','max:50'],
            'date'       => ['required','date'],
            'start_time' => ['required','date_format:H:i'],
            'end_time'   => ['required','date_format:H:i','after:start_time'],
        ]);

        \Log::info('✅ Validated booking data', $data);

        $conflict = \Botble\Booking\Models\Booking::query()
            ->whereDate('date', $data['date'])
            ->where('start_time', $data['start_time'])
            ->where('end_time', $data['end_time'])
            ->whereNotIn('status', ['canceled'])
            ->exists();

        if ($conflict) {
            \Log::warning('⛔ Conflict detected', $data);
            return response()->json(['ok'=>false, 'message'=>'این اسلات قبلاً رزرو شده است.'], 422);
        }

        try {
            $data['status'] = \Botble\Booking\Enums\ReservationStatusEnum::PENDING;
        } catch (\Throwable $e) {
            $data['status'] = 'pending';
        }

        $booking = \Botble\Booking\Models\Booking::create($data);

        \Log::info('🎉 Booking created', ['id'=>$booking->id]);

        return response()->json(['ok'=>true, 'id'=>$booking->id, 'message'=>'رزرو با موفقیت ثبت شد.']);
    } catch (\Throwable $e) {
        \Log::error('💥 Booking reserve error: '.$e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);
        return response()->json(['ok'=>false, 'error'=>$e->getMessage()], 500);
    }
}


}
