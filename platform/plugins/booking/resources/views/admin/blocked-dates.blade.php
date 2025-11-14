@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="max-w-3xl">
        <h3>Blocked Dates</h3>

        <form method="POST" action="{{ route('booking.blocked-dates.save') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Reason (optional)</label>
                <input type="text" name="reason" class="form-control">
            </div>
            <button class="btn btn-primary">Block Date</button>
        </form>

        <hr>

        <h5>Blocked Dates List</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reason</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($dates as $d)
                    <tr>
                        <td>{{ $d->date }}</td>
                        <td>{{ $d->reason }}</td>
                        <td>
                            <form method="POST" action="{{ route('booking.blocked-dates.delete', $d) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Unblock</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3">No blocked dates.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
