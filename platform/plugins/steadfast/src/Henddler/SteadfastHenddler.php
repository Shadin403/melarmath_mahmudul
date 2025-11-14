<?php

namespace Khairulkabir\Steadfast\Henddler;

use Khairulkabir\Steadfast\Models\SteadfastsConfig;
use Khairulkabir\Steadfast\Models\Steadfast;
use Botble\Ecommerce\Models\Order;
use Khairulkabir\Steadfast\Services\SteadfastApiServices;

class SteadfastHenddler
{
    protected $steadfastApiServices;
    public function __construct(SteadfastApiServices $steadfastApiServices)
    {
        $this->steadfastApiServices = $steadfastApiServices;
    }

    public function placeOrder($ids, $amounts)
    {
        $filteredData = [];
        $response = null;
        $allSuccessful = true; // Track if all operations were successful
        $errorMessages = []; // Store detailed error messages for failed consignments
        try {
            if (is_array($ids)) {

                // Loop through each order ID and its corresponding amount
                foreach ($ids as $key => $id) {
                    $order = Order::find($id);
                    if ($order) {
                        $data = $this->filterData($order, $amounts[$key]); // Pass amount to filterData
                      
                        $response = $this->steadfastApiServices->placeOrder($data);
                        
                        $allSuccessful = $this->handleSingleResponse($order, $response, $errorMessages); // Handle single order response and track success
                       
                       

                    } else {
                        throw new \Exception("Order with ID: $id not found.");
                    }
                }

            } else {
            
                // Single order processing if $ids is not an array
                $order = Order::find($ids);
                if ($order) {
                    $data = $this->filterData($order, $amounts);
                    $response = $this->steadfastApiServices->placeOrder($data);
                    $allSuccessful = $this->handleSingleResponse($order, $response, $errorMessages); // Handle single order response and track success

                } else {
                    throw new \Exception("Order with ID: $ids not found.");
                }
            }

            // Return appropriate message based on the result
            if ($allSuccessful) {
                return ['status' => true, 'message' => 'All orders were successfully sent to Steadfast'];
            } else {
                
                return ['status' => false, 'message' => 'please see the error. Some orders failed to send   : ' . implode(', ', $errorMessages)];
            }
        } catch (\Exception $e) {
            // Catch any errors and pass the exception message for easier debugging
            return ['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()];
        }
    }

    /**
     * Filters the data for the order to the desired format, including the custom amount.
     *
     * @param Order $order
     * @param float $amount
     * @return array
     */
    public function filterData($order, $amount): array
    {
        $orderCode = str_replace('#', '', $order->code);
        $codAmount = is_numeric($amount) ? (float)$amount : floatval(preg_replace('/[^0-9.]/', '', $amount));
         // Check the `notes` setting in `SteadfastApiServices` and decide on the note value
         $note = ($this->steadfastApiServices->notes == 1 && !empty($order->description))
         ? $order->description
         : 'No special instructions';
        return [
            'invoice' => $orderCode,
            'recipient_name' => $order->shippingAddress->name,
            'recipient_phone' => $this->normalizePhoneNumber($order->shippingAddress->phone),
            'recipient_address' => $order->shippingAddress->address . ', ' .
                                   $order->shippingAddress->city_name . ', ' .
                                   $order->shippingAddress->state_name . ', ' .
                                   $order->shippingAddress->country_name . ', ' .
                                   $order->shippingAddress->zip_code,
            'cod_amount' => $codAmount, // Use the provided amount instead of $order->amount
            'note' =>  $note,
        ];
    }

    function normalizePhoneNumber($phone) {
               // Remove any spaces, dashes, or parentheses
               $phone = preg_replace('/[\s\-\(\)]+/', '', $phone);

               // Match and normalize the phone number to retain the leading 0
               if (preg_match('/^(?:\+?880|880|0)?(1[3-9]\d{8})$/', $phone, $matches)) {
                   return '0' . $matches[1]; // Prefix with '0' if it was stripped by the regex
               }
       
               // Return null if the format is invalid
               return $phone;
    }

    /**
     * Handles the response for a single order.
     *
     * @param array $response
     * @param array &$errorMessages
     * @return bool
     */
    public function handleSingleResponse($order, $response, &$errorMessages): bool
    {
        try {
            if ($response['status'] == 200 && isset($response['consignment'])) {
                $consignment = $response['consignment'];

                $stdf_delivery_status = $this->steadfastApiServices->checkDeliveryStatusByInvoiceId($consignment['invoice']);
                $delivery_status = $stdf_delivery_status['delivery_status'];
               

                if ($order) {
                    // Create or update the Steadfast record
                    Steadfast::updateOrCreate(
                        ['order_id' => $order->id],
                        [
                            'stdf_delivery_status' => $delivery_status,
                            'tracking_code' => $consignment['tracking_code'],
                            'steadfast_consignment_id' => $consignment['consignment_id'],
                            'steadfast_is_sent' => 1, // Mark as sent
                            'steadfast_amount' => $consignment['cod_amount'],
                            'error'=> '',
                        ]
                    );

                    $shipment = $order->shipment;

                    $shipment->tracking_id = $consignment['tracking_code'];
                    $shipment->shipping_company_name = 'Stead Fast';
                    $shipment->tracking_link = 'https://steadfast.com.bd/tracking';
                    


                    $shipment->save();



                    return true; // Success
                } else {
                    throw new \Exception("Order with code: " . $consignment['invoice'] . " not found.");
                }
            } else {
                Steadfast::updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'stdf_delivery_status' => "error",
                        'error'=> json_encode($response),  // Convert array to JSON string
                    ]  
                );

                $errorMessages[] = $response['message'];
              

                return false; // Add this line to return false for unsuccessful responses
            }
        } catch (\Exception $e) {
            // Log the error and pass it to the error message array
            $errorMessages[] = $e->getMessage();
            return false;
        }
    }

    /**
     * Handles the response for bulk orders.
     *
     * @param array $response
     * @param array &$errorMessages
     * @return bool
     */
}
