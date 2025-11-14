<?php

namespace SobkichuBazarPay\SobkichuBazarPay\Services\Gateways;

use Botble\Ecommerce\Models\Order;
use Botble\Payment\Enums\PaymentStatusEnum;
use Illuminate\Support\Str;

class SobkichuBazarPayPaymentService
{
    protected string $brandKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->brandKey = setting("payment_sobkichubazarpay_brand_key");
        $this->baseUrl = "https://secure.sobkichubazar.com.bd/api/payment/";
    }

    /**
     * Check if the necessary credentials are set and valid.
     */
    protected function checkCredentials(): ?array
    {
        if (empty($this->brandKey)) {
            return [
                "error" => true,
                "message" => "Brand Key not found. Please contact your admin.",
            ];
        }

        return null; // Credentials are valid
    }

    public function makePayment(array $data)
    {
        // Check credentials first
        $credentialCheck = $this->checkCredentials();
        if ($credentialCheck) {
            return $credentialCheck;
        }

        // Generate callback URLs
        $successUrl = route("payments.sobkichubazarpay.callback", [
            "status" => "success",
            "order_id" => $data["orders"][0]->id
        ]);
        
        $cancelUrl = route("payments.sobkichubazarpay.callback", [
            "status" => "cancel",
            "order_id" => $data["orders"][0]->id
        ]);

        $amount = $data["amount"]; // Payment amount

        // Prepare request data according to API documentation
        $requestData = [
            "success_url" => $successUrl,
            "cancel_url" => $cancelUrl,
            "metadata" => [
                "phone" => $data["customer_phone"] ?? "",
                "order_id" => $data["orders"][0]->id,
                "customer_name" => $data["customer_name"] ?? "",
                "customer_email" => $data["customer_email"] ?? ""
            ],
            "amount" => (string)round($amount, 2)
        ];

        // Make cURL request to SobkichuBazar API
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . "create",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "BRAND-KEY: " . $this->brandKey
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false || $httpCode !== 200) {
            return [
                "error" => true,
                "message" => "Payment gateway connection failed. Please try again later."
            ];
        }

        $responseData = json_decode($response, true);

        if (isset($responseData["checkout_url"]) || isset($responseData["payment_url"])) {
            return $responseData["checkout_url"] ?? $responseData["payment_url"];
        } else {
            return [
                "error" => true,
                "message" => $responseData["message"] ?? "Something went wrong. Please try again later."
            ];
        }
    }

    public function afterMakePayment($data, $response): string
    {
        $chargeId = $response["transaction_id"] ?? Str::random(20);
        $order = Order::query()->find($data["order_id"]);
        
        if ($order !== null) {
            $customer = $order->user;
            do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                "amount" => $response["amount"] ?? $data["amount"],
                "currency" => $response["currency"] ?? "BDT",
                "charge_id" => $chargeId,
                "order_id" => $order->id,
                "customer_id" => $customer->id,
                "customer_type" => get_class($customer),
                "payment_channel" => SOBKICHUBAZARPAY_PAYMENT_METHOD_NAME,
                "status" => PaymentStatusEnum::COMPLETED,
            ]);
        }

        return $chargeId;
    }

    public function getPaymentStatus($request)
    {
        $status = $request->get("status");
        $orderId = $request->get("order_id");
        $transactionId = $request->get("transaction_id");

        if ($status === "success" && $transactionId) {
            // Verify payment with SobkichuBazar API
            $verifyResult = $this->verifyPayment($transactionId);
            
            if ($verifyResult && $verifyResult["status"] === "COMPLETED") {
                return [
                    "status" => "success",
                    "order_id" => $orderId,
                    "transaction_id" => $transactionId,
                    "amount" => $verifyResult["amount"],
                    "payment_method" => "sobkichubazarpay"
                ];
            }
        }

        return [
            "status" => "failed",
            "order_id" => $orderId,
            "message" => "Payment was cancelled or failed."
        ];
    }

    protected function verifyPayment($transactionId)
    {
        $requestData = [
            "transaction_id" => $transactionId
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->baseUrl . "verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "BRAND-KEY: " . $this->brandKey
            ),
        ));

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response !== false && $httpCode === 200) {
            return json_decode($response, true);
        }

        return null;
    }

    public function getToken($data)
    {
        $order = Order::find($data["order_id"]);
        return $order->token;
    }

    public function supportedCurrencyCodes(): array
    {
        return ["BDT", "USD", "EUR", "GBP"];
    }
}

