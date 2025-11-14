<?php

namespace Botble\Ecommerce\Http\Controllers;

use App\Models\DhakaArea;
use Illuminate\Http\Request;
use Botble\Location\Models\City;
use Botble\Location\Models\State;
use Botble\Ecommerce\Models\Address;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Base\Http\Controllers\BaseController;

class AjaxController extends BaseController
{
    public function getThanas()
    {
        $thanas = GlobalOptionValue::where('option_id', 7)->pluck('option_value', 'id');

        return response()->json(['data' => $thanas]);
    }

    public function getAreas($thana_id)
    {
        $areas = DhakaArea::where('thana_id', $thana_id)->get();

        return response()->json(['data' => $areas]);
    }

    public function getCustomerAddressByPhone(Request $request)
    {
        $customer = Customer::where('phone', $request->input('phone'))->first();

        if ($customer) {
            $addresses = Address::where('customer_id', $customer->id)->get();

            foreach ($addresses as $address) {
                if ($address->is_inside_dhaka) {
                    $thana = GlobalOptionValue::find($address->is_inside_dhaka);
                    $address->thana_name = $thana ? $thana->option_value : null;
                }
                if ($address->inside_dhaka) {
                    $area = DhakaArea::find($address->inside_dhaka);
                    $address->area_name = $area ? $area->name : null;
                }
                if ($address->state) {
                    $state = State::find($address->state);
                    $address->state_name = $state ? $state->name : null;
                }
                if ($address->city) {
                    $city = City::find($address->city);
                    $address->city_name = $city ? $city->name : null;
                }
            }

            return response()->json(['data' => $addresses]);
        }

        return response()->json(['data' => []]);
    }
}
