<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductCollection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Route;

use Botble\Ecommerce\Http\Controllers\AjaxController;

AdminHelper::registerRoutes(function (): void {
    Route::prefix('ajax')->name('admin.ajax.')->group(function (): void {
        Route::get('search-products', function () {
            $products = Product::query()
                ->wherePublished()
                ->where('is_variation', false)
                ->when(request()->input('search'), function (Builder $query, string $search): void {
                    $query->where('name', 'like', "%$search%");
                })
                ->select('name', 'id')
                ->paginate();

            return BaseHttpResponse::make()
                ->setData($products);
        })->name('search-products');

        Route::get('search-categories', function () {
            $categories = ProductCategory::query()
                ->when(request()->input('search'), function (Builder $query, string $search): void {
                    $query->where('name', 'like', "%$search%");
                })
                ->select('name', 'id')
                ->paginate();

            return BaseHttpResponse::make()
                ->setData($categories);
        })->name('search-categories');

        Route::get('search-collections', function () {
            $collections = ProductCollection::query()
                ->when(request()->input('search'), function (Builder $query, string $search): void {
                    $query->where('name', 'like', "%$search%");
                })
                ->select('name', 'id')
                ->paginate();

            return BaseHttpResponse::make()
                ->setData($collections);
        })->name('search-collections');

        Route::get('thanas', [AjaxController::class, 'getThanas'])->name('thanas');

        Route::get('areas/{thana_id}', [AjaxController::class, 'getAreas'])->name('areas');
    });
});

Route::group(['prefix' => 'ajax', 'as' => 'public.ajax.'], function () {
    Route::post('get-customer-address-by-phone', [AjaxController::class, 'getCustomerAddressByPhone'])->name('get-customer-address-by-phone');
});
