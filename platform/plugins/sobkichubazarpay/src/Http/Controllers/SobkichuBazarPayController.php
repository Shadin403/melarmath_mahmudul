<?php

namespace SobkichuBazarPay\SobkichuBazarPay\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Routing\Controller;
use SobkichuBazarPay\SobkichuBazarPay\Http\Requests\SobkichuBazarPayPaymentCallbackRequest;
use SobkichuBazarPay\SobkichuBazarPay\Services\Gateways\SobkichuBazarPayPaymentService;

class SobkichuBazarPayController extends Controller
{
    public function getCallback(
        SobkichuBazarPayPaymentCallbackRequest $request, 
        SobkichuBazarPayPaymentService $sobkichuBazarPayService, 
        BaseHttpResponse $response
    ) {
        $status = $sobkichuBazarPayService->getPaymentStatus($request);

        if ($status['status'] !== 'success') {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage(__('Payment failed or was cancelled!'));
        }

        $sobkichuBazarPayService->afterMakePayment($request->input(), $status);

        $token = $sobkichuBazarPayService->getToken($request->input());

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL($token))
            ->setMessage(__('Payment completed successfully!'));
    }
}

