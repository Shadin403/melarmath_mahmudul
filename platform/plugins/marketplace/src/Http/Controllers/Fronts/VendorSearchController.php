<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Marketplace\Models\Store;
use Illuminate\Http\Request;
use Botble\Media\Facades\RvMedia;

class VendorSearchController extends BaseController
{
    public function ajaxSearch(Request $request)
    {
        $keyword = $request->input('q');

        if (!$keyword) {
            return response()->json(['data' => []]);
        }

        $stores = Store::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('phone', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('address', 'LIKE', '%' . $keyword . '%');
            })
            ->withCount('products')
            ->limit(10)
            ->get();

        $data = $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'logo' => $store->logo ? RvMedia::getImageUrl($store->logo, 'thumb') : null,
                'url' => $store->url,
                'rating' => number_format($store->reviews_avg_star ?? 0, 1),
                'products_count' => $store->products_count,
                'address' => $store->address,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
