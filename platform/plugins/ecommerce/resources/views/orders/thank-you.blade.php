@extends('plugins/ecommerce::orders.master')

@section('title', __('Order successfully. Order number :id', ['id' => $order->code]))

@section('content')
<script src="https://cdn.jsdelivr.net/npm/@tsparticles/confetti@3.0.3/tsparticles.confetti.bundle.min.js"></script>
<script>
    const end = Date.now() + 2 * 1000;

    // go Buckeyes!
    const colors = ["#bb0000", "#ffffff"];

    (function frame() {
        confetti({
            particleCount: 2,
            angle: 60,
            spread: 55,
            origin: { x: 0 },
            colors: colors,
        });

        confetti({
            particleCount: 2,
            angle: 120,
            spread: 55,
            origin: { x: 1 },
            colors: colors,
        });

        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    })();
</script>
<div class="row">
    <div class="col-lg-7 col-md-6 col-12">
        @include('plugins/ecommerce::orders.partials.logo')

        <div class="thank-you">
            <x-core::icon name="ti ti-circle-check-filled" />

            <div class="d-inline-block">
                <h3 class="thank-you-sentence">
                    {{ __('Your order is successfully placed') }}
                </h3>
                <p>{{ __('Thank you for purchasing our products!') }}</p>
            </div>
        </div>

        @include('plugins/ecommerce::orders.thank-you.customer-info', compact('order'))

        <a class="btn payment-checkout-btn" href="{{ BaseHelper::getHomepageUrl() }}">
            {{ __('Continue shopping') }}
        </a>
    </div>
    <div class="col-lg-5 col-md-6 d-none d-md-block mt-5 mt-md-0 mb-5">
        <div class="my-3 bg-light p-3">
            @include('plugins/ecommerce::orders.thank-you.order-info')

            @include('plugins/ecommerce::orders.thank-you.total-info', ['order' => $order])
        </div>
    </div>
</div>
@stop