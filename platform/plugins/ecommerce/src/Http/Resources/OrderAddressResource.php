<?php

namespace Botble\Ecommerce\Http\Resources;

use App\Models\DhakaArea;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Ecommerce\Models\OrderAddress;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin OrderAddress
 */
class OrderAddressResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'state' => $this->state,
            'city' => $this->city,
            'country_name' => $this->country_name,
            'state_name' => $this->state_name,
            'city_name' => $this->city_name,
            'address' => $this->address,
            'zip_code' => $this->zip_code,
            'order_id' => $this->order_id,
            'is_inside_of_dhaka' => $this->is_inside_of_dhaka,
            'inside_dhaka' => $this->inside_dhaka,
            'is_out_side_dhaka' => $this->is_out_side_dhaka,
            'courier_option' => $this->courier_option,
            'map_location' => $this->map_location,
            'is_inside_of_dhaka_name' => GlobalOptionValue::where('id', $this->is_inside_of_dhaka)->first()?->option_value,
            'inside_dhaka_name' => DhakaArea::where('id', $this->inside_dhaka)->first()?->name,
        ];
    }
}
