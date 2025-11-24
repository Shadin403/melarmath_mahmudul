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

        // Prioritize stores by matching delivery_areas with user's selected area
        if ($userAreaId) {
            $query->selectRaw('mp_stores.*, (SELECT COUNT(*) FROM ec_products WHERE ec_products.store_id = mp_stores.id) as products_count');
        } else {
            $query->withCount('products');
        }

        $stores = $query->limit(10)->get();

        // Sort stores based on inside_dhaka matching user's area_id
        if ($userAreaId) {
            $stores = $stores->sortBy(function ($store) use ($userAreaId) {
                // Priority 1: Exact match with inside_dhaka field
                if ($store->inside_dhaka == $userAreaId) {
                    return 0;
                }
                return 1;
            })->values();
        }

        $data = $stores->map(function ($store) use ($userAreaId) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'logo' => $store->logo ? RvMedia::getImageUrl($store->logo, 'thumb') : null,
                'url' => $store->url,
                'rating' => number_format($store->reviews_avg_star ?? 0, 1),
                'products_count' => $store->products_count ?? 0,
                'address' => $store->address,
                'is_from_user_area' => $userAreaId && $store->inside_dhaka == $userAreaId,
            ];
        });

        return response()->json(['data' => $data]);
    }
}
