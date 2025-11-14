<ul>
    @foreach($payments->payments as $payment)
        <li>
            @include('plugins/sobkichubazarpay::detail', compact('payment'))
        </li>
    @endforeach
</ul>

