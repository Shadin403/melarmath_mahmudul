<?php

namespace Botble\Marketplace\Http\Controllers\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Marketplace\Models\Store;
use Illuminate\Http\Request;
use Botble\Media\Facades\RvMedia;
use Illuminate\Support\Facades\Session;

class VendorSearchController extends BaseController
{
    public function ajaxSearch(Request $request)
    {
        $keyword = $request->input('q');

        if (!$keyword) {
            return response()->json(['data' => []]);
        }

        // Get user's selected area from session
        $userLocation = Session::get('user_selected_location');
        $userAreaId = $userLocation['area_id'] ?? null;

        $query = Store::query()
            ->where('mp_stores.status', BaseStatusEnum::PUBLISHED)
            ->where(function ($query) use ($keyword) {
                $query->where('mp_stores.name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('mp_stores.phone', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('mp_stores.address', 'LIKE', '%' . $keyword . '%');
            });

        // Prioritize stores by matching customer's address area with user's selected area
        if ($userAreaId) {
            $query->leftJoin('ec_customers', 'mp_stores.customer_id', '=', 'ec_customers.id')
                ->leftJoin('ec_customer_addresses', function ($join) {
                    $join->on('ec_customers.id', '=', 'ec_customer_addresses.customer_id')
                        ->where('ec_customer_addresses.is_default', '=', 1);
                })
                ->selectRaw('mp_stores.*, (SELECT COUNT(*) FROM ec_products WHERE ec_products.store_id = mp_stores.id) as products_count')
                ->orderByRaw(
                    "CASE
                        WHEN ec_customer_addresses.inside_dhaka = ? THEN 0
                        ELSE 1
                    END",
                    [$userAreaId]
                );
        } else {
            $query->withCount('products');
        }

        $stores = $query->limit(10)->get();

        $data = $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'logo' => $store->logo ? RvMedia::getImageUrl($store->logo, 'thumb') : null,
                'url' => $store->url,
                'rating' => number_format($store->reviews_avg_star ?? 0, 1),
                'products_count' => $store->products_count ?? 0,
                'address' => $store->address,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
