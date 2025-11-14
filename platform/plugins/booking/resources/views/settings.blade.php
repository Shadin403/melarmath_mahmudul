@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="max-w-3xl">
        <form method="POST" action="{{ route('booking.settings.save') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Slot duration (minutes)</label>
                <select name="booking_slot_minutes" class="form-control">
                    @foreach([15,30,60] as $m)
                        <option value="{{ $m }}" {{ $m == $slot ? 'selected':'' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Workday start time</label>
                <input type="time" name="booking_open_time" class="form-control" value="{{ $open }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Workday end time</label>
                <input type="time" name="booking_close_time" class="form-control" value="{{ $close }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Week starts on</label>
                <select name="booking_week_start" class="form-control">
                    @foreach(['Mon'=>'Monday','Sun'=>'Sunday','Sat'=>'Saturday'] as $k=>$v)
                        <option value="{{ $k }}" {{ $k == $week ? 'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-primary">Save</button>
        </form>
    </div>
@endsection
