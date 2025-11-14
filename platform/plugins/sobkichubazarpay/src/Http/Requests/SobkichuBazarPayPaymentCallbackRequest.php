<?php

namespace SobkichuBazarPay\SobkichuBazarPay\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SobkichuBazarPayPaymentCallbackRequest extends Request
{
    public function rules(): array
    {
        return [
            'status' => 'required|string',
            'order_id' => 'required|integer',
            'transaction_id' => 'nullable|string',
            'amount' => 'nullable|numeric'
        ];
    }
}

