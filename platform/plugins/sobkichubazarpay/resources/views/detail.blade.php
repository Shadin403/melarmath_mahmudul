<div class="payment-detail">
    <p><strong>{{ __('Payment Method') }}:</strong> SobkichuBazarPay</p>
    <p><strong>{{ __('Payment ID') }}:</strong> {{ $payment->charge_id }}</p>
    <p><strong>{{ __('Amount') }}:</strong> {{ format_price($payment->amount) }}</p>
    <p><strong>{{ __('Status') }}:</strong> {{ $payment->status->label() }}</p>
    <p><strong>{{ __('Date') }}:</strong> {{ $payment->created_at->format('d/m/Y H:i:s') }}</p>
</div>

