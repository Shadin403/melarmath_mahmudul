<?php

use App\Http\Controllers\HomeController;
use Botble\Ecommerce\Facades\InvoiceHelper;
use Botble\Ecommerce\Http\Controllers\OrderController;
use Botble\Ecommerce\Models\GlobalOption;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Ecommerce\Models\Invoice;
use Botble\Ecommerce\Models\Order;
use Illuminate\Http\Request;

Route::get('/change-variation-info/{id}', [HomeController::class, 'getTitleAndVariation'])->name('variationChange');

Route::get('/test', function () {
    $options = GlobalOption::whereIn('id', [7, 8])->with('values')->get();
    return $options;
});
// InvoiceController

Route::get('/get-dhaka-area/{id}', [HomeController::class, 'getDhakaArea']);
Route::get('/generate-invoice/{invoice}', function ($invoiceId, Request $request) {
    $invoice = Invoice::find($invoiceId);
    if ($invoice) {
        if ($request->query('type') == 'print') {
            return InvoiceHelper::streamInvoice($invoice);
        }

        // Use customer-invoice template for download
        return (new \Botble\Base\Supports\Pdf())
            ->templatePath(storage_path('app/templates/ecommerce/customer-invoice.tpl'))
            ->destinationPath(storage_path('app/templates/ecommerce/customer-invoice.tpl'))
            ->supportLanguage('bangladesh')
            ->paperSizeA4()
            ->data(InvoiceHelper::getDataForInvoiceTemplate($invoice))
            ->twigExtensions([
                new \Botble\Ecommerce\Supports\TwigExtension(),
            ])
            ->setProcessingLibrary(get_ecommerce_setting('invoice_processing_library', 'dompdf'))
            ->download(sprintf('invoice-%s.pdf', $invoice->code));
    }
})->name('generate-invoice');
Route::get('/get-order-using-token/{token}', [HomeController::class, 'getOrderUsingToken'])->name('getOrderUsingToken');

Route::get('/downloadInvoiceByOrderCode/{order_id}', function ($order_id) {
    $order = Order::where('code', 'LIKE', "%$order_id%")->first();
    $invoice = Invoice::where('reference_id', $order->id)->first();
    if ($invoice) {
        return InvoiceHelper::downloadInvoice($invoice);
    } else {
        return redirect('/');
    }
})->name('downloadInvoiceByOrderCode');

Route::get('/test-invoice/{id}', [HomeController::class, 'generateOrderPDF'])->name('testInvoice');
