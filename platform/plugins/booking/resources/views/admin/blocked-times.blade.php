@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="max-w-3xl">
        <h3>Blocked Times</h3>

        <form method="POST" action="{{ route('booking.blocked-times.save') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Start Time</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">End Time</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Reason (optional)</label>
                <input type="text" name="reason" class="form-control">
            </div>
            <button class="btn btn-primary">Block Time</button>
        </form>

        <hr>

        <h5>Blocked Times List</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Reason</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($times as $t)
                    <tr>
                        <td>{{ $t->date }}</td>
                        <td>{{ $t->start_time }}</td>
                        <td>{{ $t->end_time }}</td>
                        <td>{{ $t->reason }}</td>
                        <td>
                            <form method="POST" action="{{ route('booking.blocked-times.delete', $t) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Unblock</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No blocked times.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
