<?php

namespace Khairulkabir\Steadfast\Http\Controllers;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Khairulkabir\Steadfast\Http\Requests\SteadfastRequest;
use Khairulkabir\Steadfast\Models\Steadfast;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Khairulkabir\Steadfast\Tables\SteadfastTable;
use Botble\Ecommerce\Models\Order;
use Illuminate\Http\Request;
use Khairulkabir\Steadfast\Supports\PrintPDFHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use Khairulkabir\Steadfast\Henddler\SteadfastHenddler;
use Khairulkabir\Steadfast\Services\SteadfastApiServices;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Exception;
use Botble\Ecommerce\Facades\OrderHelper;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Enums\OrderHistoryActionEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Khairulkabir\Steadfast\Models\SteadfastsConfig;
use Botble\Ecommerce\Events\OrderCancelledEvent;
use Botble\Ecommerce\Events\OrderCompletedEvent;
use Botble\Ecommerce\Events\OrderConfirmedEvent;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Facades\EcommerceHelper as EcommerceHelperFacade;
use Botble\Payment\Models\Payment;
use Botble\Ecommerce\Events\ProductQuantityUpdatedEvent;
use Botble\Ecommerce\Facades\Discount;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Composer;
use Symfony\Component\Process\Process;


use Throwable;

class SteadfastController extends BaseController
{
    protected $steadfastHandler;
    protected $composer;

    public function __construct(SteadfastHenddler $steadfastHandler,Composer $composer)
    {
        $this->composer = $composer;
        $this->steadfastHandler = $steadfastHandler;
        $this
            ->breadcrumb()
            ->add(trans(trans('plugins/steadfast::steadfast.name')), route('steadfast.index'));
    }

    public function index(Request $request, SteadfastTable $table)
    {
        $perPage = $request->get('perPage', 10); // Default perPage to 10 if not provided
        $search = $request->get('search');
        $steadfastConfig = SteadfastsConfig::first();

        // If user selects 'All', set perPage to total number of orders
        if ($perPage == -1) {
            $perPage = Order::count(); // Get total number of orders
        }

        // Call the orderList() method and pass the desired perPage value
        $data = Steadfast::orderList($perPage, $search);

        // Pass the paginated data to the view
        return view('plugins/steadfast::dataTable.index', [
            'dataTable' => $table->html(),
            'data' => $data,
            'config' => $steadfastConfig,
        ]);
    }

    public function printInvoice($id,$type=null)
    {
        // Fetch the order by ID
        $order = Order::findOrFail($id);
        $type = request()->query('type');
        $printPDFHelper = new PrintPDFHelper();
        $pdfData = $printPDFHelper->getDataForInvoiceTemplate($order);

       
        if (!is_null($type) && $type == "label") {
            return view('plugins/steadfast::invoice.label', $pdfData);
        }
       
        return view('plugins/steadfast::invoice.index', $pdfData);
    }

    public function bulkPrintInvoice(Request $request)
    {
        try {
            
            // Fetch order IDs from the request
            $orderIds = $request->input('ids', []);
            $type = $request->input('type', null); // Fetch the 'type' input
    
            // Validate that order IDs are provided and are in an array format
            if (empty($orderIds) || !is_array($orderIds)) {
                return response()->json(['status' => false, 'message' => 'No order IDs provided or invalid format.'], 400);
            }
    
            // Fetch all the orders based on the IDs
            $orders = Order::whereIn('id', $orderIds)->get();
    
            // If no orders found, return an error response
            if ($orders->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No orders found for the provided IDs.'], 404);
            }
    
            $printPDFHelper = new PrintPDFHelper();
            $pdfData = [];
    
            // Collect data for each order and convert to array
            foreach ($orders as $order) {
                $pdfData[] = (array) $printPDFHelper->getDataForInvoiceTemplate($order);
            }
    
            $view = ($type == "label") 
                    ? 'plugins/steadfast::invoice.bulk-label' 
                    : 'plugins/steadfast::invoice.bulk';

        // Render the appropriate view as HTML
            $renderedHtml = view($view, compact('pdfData'))->render();
    
            // Return the rendered HTML as JSON
            return response()->json(['status' => true, 'html' => $renderedHtml], 200);
    
        } catch (\Exception $e) {
            // Catch any errors and return a JSON response with the error message
            return response()->json(['status' => false, 'message' => 'An error occurred while fetching invoices.', 'error' => $e->getMessage()], 500);
        }
    }
    
    public function createOrder(Request $request)
    {
        $ids = json_decode($request->input('ids'), true); // Decode as an array
        $amounts = json_decode($request->input('amounts'), true); // Decode as an array
    
        if (empty($ids) || empty($amounts)) {
            return response()->json(['status' => false, 'message' => 'No orders or amounts provided.']);
        }
    
        // Call the placeOrder method and pass the ids and amounts
        $response = $this->steadfastHandler->placeOrder($ids, $amounts);
       
       
    
        // Check if the response indicates success or failure and return accordingly
        if ($response['status']) {
           
            return response()->json([
                'status' => false,
                'message' => $response['message'],
                'url'=> $this->httpResponse()->setMessage(trans($response['message']))
            ]);
        } else {
          
            return response()->json([
                'status' => false,
                'message' => $response['message'],
                'url'=> redirect()->back()->withErrors($response['message'])
            ]);
        }
    }

        public function deliveryStatus(Request $request)
    {
        $orderId= $request->input('id');
        try {
            // Find the record by the order_id which is passed as $orderId
            $steadfast = Steadfast::where('order_id', $orderId)->first();

            // If record not found, return error
            if (!$steadfast || !$steadfast->steadfast_consignment_id) {
                return response()->json(['status' => false, 'message' => 'No consignment ID found for this order.']);
            }

            // Get the consignment ID from the found record
            $consignmentId = $steadfast->steadfast_consignment_id;

            // Call the API to check the delivery status
            $apiService = new SteadfastApiServices();
            $response = $apiService->checkDeliveryStatusByConsignmentId($consignmentId);
            // Check if the response is successful
            if ($response['status'] == 200 && isset($response['delivery_status'])) {
                // Update the delivery status in the database
                $steadfast->stdf_delivery_status = $response['delivery_status'];
                $steadfast->save();
                return response()->json($response);

            }
            return response()->json($response);
        } catch (\Exception $e) {
            return $this
            ->httpResponse()
            ->setMessage(trans('Error: ' . $e->getMessage()));
        }
    }

    public function showError(Request $request)
    {
        try {
            $id = $request->input('id');
            
            // Find the Steadfast record based on the provided id
            $steadfast = Steadfast::where('order_id', $id)->first();

            // Check if the record is found and if it has an error
            if ($steadfast && !empty($steadfast->error)) {
                return response()->json(['status' => true, 'message' => $steadfast->error]);
            }

            return response()->json(['status' => false, 'message' => 'No error found for this order.']);
            
        } catch (\Exception $e) {
            // Catch and handle any exceptions
            return response()->json(['status' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function saveBulkChangeItem(Order $item, string $inputValue)
    {
        if ($inputValue == OrderStatusEnum::CANCELED) {
            if (! $item->canBeCanceledByAdmin()) {
                throw new Exception(trans('plugins/ecommerce::order.order_cannot_be_canceled'));
            }

            OrderHelper::cancelOrder($item);

            OrderHistory::query()->create([
                'action' => OrderHistoryActionEnum::CANCEL_ORDER,
                'description' => trans('plugins/ecommerce::order.order_was_canceled_by'),
                'order_id' => $item->getKey(),
                'user_id' => Auth::id(),
            ]);

          
        }

        

    }

    public function webhookHandler(Request $request)
    {
        $steadfastConfig = SteadfastsConfig::first();

        if (!$steadfastConfig || empty($steadfastConfig->auth_token)) {
            return response()->json(['status' => false, 'message' => 'Please insert the token in settings'], 400);
        }

        $expectedToken = $steadfastConfig->auth_token;
        $providedToken = $request->bearerToken();

        if ($expectedToken !== $providedToken) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $data = $request->all();
        if (!isset($data['consignment_id']) || !isset($data['status'])) {
            return response()->json(['status' => false, 'message' => 'Invalid payload'], 400);
        }

        $steadfast = Steadfast::where('steadfast_consignment_id', $data['consignment_id'])->first();

        if (!$steadfast) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        try {
            $order = Order::find($steadfast->order_id);

            if (!$order) {
                return response()->json(['status' => false, 'message' => 'Order not found'], 404);
            }

            $statusMap = [
                'pending' => 'processing',
                'delivered' => 'completed',
                'cancelled' => 'cancelled',
            ];

            $order->status = $statusMap[$data['status']] ?? 'pending';
            $order->save();

            $steadfast->stdf_delivery_status = $data['status'];
            $steadfast->save();

            
                $this->orderHistory($order,  $data);
            

            return response()->json(['status' => true, 'message' => 'Order status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed to update order status: ' . $e->getMessage()], 500);
        }
    }


    public function orderHistory(Order $order, $data)
    {

        $payment = $order->payment;




        if($data['status']=="delivered"){
            $order->is_confirmed = true;
            $order->completed_at = $data['updated_at'];
            $order->save();

            if($payment->status !="completed" ){
                OrderHistory::query()->create([
                    'action' => OrderHistoryActionEnum::CONFIRM_PAYMENT,
                    'description' => trans('payment_was_confirmed_by_Steadfast', [
                        'money' => format_price($order->amount),
                    ]),
                    'order_id' => $order->getKey(),
                    'user_id' => 0,
                ]);

                $payment->status = "completed";
                $payment->save();
            }

            $shipment = $order->shipment;

            $shipment->cod_status = "completed";
            $shipment->cross_checking_status = "completed";
            $shipment->status = "delivered";
            $shipment->save();

          



           

            OrderHistory::query()->create([
                'action' => OrderHistoryActionEnum::CONFIRM_ORDER,
                'description' => trans('order_was_verified_by_was_confirmed_by_Steadfast'),
                'order_id' => $order->getKey(),
                'user_id' => 0,
            ]);

            $payment->save();
    
           
        }


        if($data['status']=="partial_delivered"){
            
            $order->is_confirmed = true;
            $order->save();

            if($payment->status !="completed" ){
                OrderHistory::query()->create([
                    'action' => OrderHistoryActionEnum::CONFIRM_PAYMENT,
                    'description' => trans('payment_was_confirmed_by_Steadfast', [
                        'money' => format_price($order->amount),
                    ]),
                    'order_id' => $order->getKey(),
                    'user_id' => 0,
                ]);

                $payment->status = "completed";
                $payment->save();
            }

            $shipment = $order->shipment;

            $shipment->cod_status = "completed";
            $shipment->cross_checking_status = "completed";
            $shipment->status = "completed";
            $shipment->save();

            
            OrderHistory::query()->create([
                'action' => OrderHistoryActionEnum::CONFIRM_ORDER,
                'description' => trans('order_was_verified_by_was_confirmed_by_Steadfast'),
                'order_id' => $order->getKey(),
                'user_id' => 0,
            ]);
    
            
        }

        if($data['status']=="pending"){
            OrderHistory::query()->create([
                'action' => "picked",
                'description' => trans('Changed_status_of_shipping_to:_Picked_by_Steadfast'),
                'order_id' => $order->getKey(),
                'user_id' => 0,
            ]);

            $shipment = $order->shipment;
            $shipment->status = "picked";
            $shipment->save();

        }

        if($data['status']=="cancelled"){


            OrderHistory::query()->create([
                'action' => OrderHistoryActionEnum::CANCEL_ORDER,
                'description' => trans('order_was_verified_by_was_confirmed_by_Steadfast'),
                'order_id' => $order->getKey(),
                'user_id' => 0,
            ]);
            


            $shipment = $order->shipment;
            $shipment->status = OrderHistoryActionEnum::CANCEL_ORDER;
            $shipment->save();


            $order->status = OrderStatusEnum::CANCELED;
            $order->is_confirmed = true;

            $reason = "cancelled Steadfast";
            $reasonDescription = "cancelled by Steadfast";
            
            $order->cancellation_reason = "cancelled Steadfast";
            $order->cancellation_reason_description = "cancelled by Steadfast";
           
            $order->save();

          
    
            event(new OrderCancelledEvent($order, $reason, $reasonDescription));
    
            foreach ($order->products as $orderProduct) {
                $product = $orderProduct->product;
                $product->quantity += $orderProduct->qty;
                $product->save();
    
                if ($product->is_variation) {
                    $originalProduct = $product->original_product;
    
                    if ($originalProduct->id != $product->id) {
                        $originalProduct->quantity += $orderProduct->qty;
                        $originalProduct->save();
                    }
                }
    
                event(new ProductQuantityUpdatedEvent($product));
            }
    
            if ($order->coupon_code && $order->user_id) {
                Discount::getFacadeRoot()->afterOrderCancelled($order->coupon_code, $order->user_id);
            }

        }


        
        
    }

    public function fakeOrderCheck(Request $request)
    {
        // Get phone numbers input and split into an array if provided as a string
        $phoneNumbers = $request->input('number');
        $cms = $request->input('cms');
        $api = $request->input('api');
    
        // Convert phone numbers into an array if a single string is provided
        if (is_string($phoneNumbers)) {
            $phoneNumbers = preg_split('/[,\s]+/', $phoneNumbers, -1, PREG_SPLIT_NO_EMPTY);
        }
    
        // Ensure CMS or API is selected
        if (!$cms && !$api) {
            return response()->json(['status' => false, 'message' => 'Please select at least one option (CMS or API).']);
        }
    
        // Ensure phone numbers are provided
        if (empty($phoneNumbers)) {
            return response()->json(['status' => false, 'message' => 'Phone numbers are required and should be an array or string.']);
        }
    
        $response = ['status' => true, 'data' => []];
    
        foreach ($phoneNumbers as $phoneNumber) {
            $fphoneNumber = $this->normalizePhoneNumber($phoneNumber);
            $phoneResponse = ['phone' => $fphoneNumber, 'data' => []];
    
            // Handle CMS Data
            if ($cms) {
                $orders = Order::whereHas('shippingAddress', function ($query) use ($fphoneNumber) {
                    $query->where('phone', 'LIKE', '%' . $fphoneNumber);
                })->with('shipment')->where('is_finished', 1)->get();
    
                $groupedOrders = $orders->groupBy(function ($order) {
                    return $order->shipment->shipping_company_name ?: 'Unknown';
                });
    
                $cmsResult = [];
                $cmsTotalOrders = 0;
                $cmsCompletedOrders = 0;
                $cmsCanceledOrders = 0;
    
                foreach ($groupedOrders as $shippingCompanyName => $orders) {
                    $totalOrders = $orders->count();
                    $completedOrders = $orders->where('status', OrderStatusEnum::COMPLETED)->count();
                    $canceledOrders = $orders->where('status', OrderStatusEnum::CANCELED)->count();
    
                    $cmsTotalOrders += $completedOrders + $canceledOrders;
                    $cmsCompletedOrders += $completedOrders;
                    $cmsCanceledOrders += $canceledOrders;
    
                    $cmsResult[] = [
                        'shipping_company_name' => $shippingCompanyName,
                        'total_orders' => $completedOrders + $canceledOrders,
                        'completed_orders' => $completedOrders,
                        'canceled_orders' => $canceledOrders,
                    ];
                }
    
                $cmsSuccessRatio = ($cmsCompletedOrders + $cmsCanceledOrders) > 0
                    ? round(($cmsCompletedOrders / ($cmsCompletedOrders + $cmsCanceledOrders)) * 100, 2)
                    : 0;
    
                $phoneResponse['data']['cms'] = [
                    'orders' => $cmsResult,
                    'overall_stats' => [
                        'overall_total_orders' => $cmsTotalOrders,
                        'overall_completed_orders' => $cmsCompletedOrders,
                        'overall_canceled_orders' => $cmsCanceledOrders,
                        'success_ratio' => $cmsSuccessRatio,
                    ],
                ];
            }
    
            // Handle API Data
            if ($api) {
                // Check if phone number is exactly 11 digits
                if (strlen($fphoneNumber) !== 11) {
                    $phoneResponse['data']['api'] = ['error' => 'Phone number must be exactly 11 digits'];
                } else {
                    try {
                        $steadfastConfig = SteadfastsConfig::first();
                        $authKey = $steadfastConfig->bdcourier_auth;
    
                        // Check if API is enabled and if authKey exists
                        if (!$steadfastConfig->bdcourier_API || $steadfastConfig->bdcourier_API == 0) {
                            throw new \Exception('bdcourier_API is not enabled. Please enable it in settings.');
                        }
                        if (empty($authKey)) {
                            throw new \Exception('bdcourier Auth key is missing. Please provide it in settings.');
                        }
    
                        // Proceed with API call
                        $apiResponse = $this->getApiData($fphoneNumber, $authKey);
    
                        if (isset($apiResponse['courierData'])) {
                            $apiData = $apiResponse['courierData'];
    
                            $apiResult = [];
                            $apiTotalOrders = 0;
                            $apiCompletedOrders = 0;
                            $apiCanceledOrders = 0;
    
                            foreach ($apiData as $courier => $courierData) {
                                if ($courier !== 'summary') {
                                    $totalOrders = $courierData['total_parcel'];
                                    $completedOrders = $courierData['success_parcel'];
                                    $canceledOrders = $courierData['cancelled_parcel'];
    
                                    $apiTotalOrders += $totalOrders;
                                    $apiCompletedOrders += $completedOrders;
                                    $apiCanceledOrders += $canceledOrders;
    
                                    $apiResult[] = [
                                        'shipping_company_name' => ucfirst($courier),
                                        'total_orders' => $totalOrders,
                                        'completed_orders' => $completedOrders,
                                        'canceled_orders' => $canceledOrders,
                                    ];
                                }
                            }
    
                            $apiSuccessRatio = $apiTotalOrders > 0
                                ? round(($apiCompletedOrders / $apiTotalOrders) * 100, 2)
                                : 0;
    
                            $phoneResponse['data']['api'] = [
                                'orders' => $apiResult,
                                'overall_stats' => [
                                    'overall_total_orders' => $apiTotalOrders,
                                    'overall_completed_orders' => $apiCompletedOrders,
                                    'overall_canceled_orders' => $apiCanceledOrders,
                                    'success_ratio' => $apiSuccessRatio,
                                ],
                            ];
                        } else {
                            $phoneResponse['data']['api'] = ['error' => 'Failed to fetch API data: ' . json_encode($apiResponse)];
                        }
                    } catch (\Exception $e) {
                        // If an error occurs in the API section, capture it in the 'api' key for that phone number
                        $phoneResponse['data']['api'] = ['error' => $e->getMessage()];
                    }
                }
            }
    
            // Add each phone's result independently
            $response['data'][] = $phoneResponse;
        }
    
        return response()->json($response);
    }
    
    private function normalizePhoneNumber($phone) {
        // Remove any spaces, dashes, or parentheses
        $phone = preg_replace('/[\s\-\(\)]+/', '', $phone);

        // Match and normalize the phone number to retain the leading 0
        if (preg_match('/^(?:\+?880|880|0)?(1[3-9]\d{8})$/', $phone, $matches)) {
            return '0' . $matches[1]; // Prefix with '0' if it was stripped by the regex
        }

        // Return null if the format is invalid
        return $phone;
    }


    public function freeTrial(){
        try {
            $apiService = new SteadfastApiServices();
            $response = $apiService->startFreeTrial();
            return $response;
        } catch (\Exception $e) {
            return response()->json( [
                'status' => false,
                'message' => 'Error retrieving balance: ' . $e->getMessage()
            ]);
        }
    }


    private function getApiData($phoneNumber, $authKey)
    {
        try {
            // Make the POST request using Laravel's Http client
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $authKey, // Use the dynamic auth key
            ])
            // ->withoutVerifying()
            ->post('https://bdcourier.com/api/courier-check', [
                'phone' => $phoneNumber,  // Pass each phone number dynamically
            ]);

            // Check if the request was successful
            if ($response->successful()) {
                // Return the JSON-decoded response
                return $response->json();
            } else {
                // Handle the error response
                return ['error' => 'Failed to fetch API data.'. $response->json()];
            }

        } catch (\Exception $e) {
            // Handle exceptions such as network errors or connection failures
            return ['error' => $e->getMessage()];
        }
    }



    public function check_balance()
    {
        try {
            // Initialize API service
            $apiService = new SteadfastApiServices();
    
            // Attempt to get the current balance
            $response = $apiService->getCurrentBalance();
    
            // Return a successful response
            return $response;
    
        } catch (\Exception $e) {
            // Catch any exception and return the error message
            return [
                'status' => false,
                'message' => 'Error retrieving balance: ' . $e->getMessage()
            ];
        }
    }

    public function license_check(Request $request){
        $apiService = new SteadfastApiServices();
        $response = $apiService->verify_license();

        dd( $response);

    

    }
  
    
}
