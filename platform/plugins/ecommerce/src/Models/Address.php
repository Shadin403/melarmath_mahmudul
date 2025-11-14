<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Traits\LocationTrait;

class Address extends BaseModel
{
    use LocationTrait;

    protected $table = 'ec_customer_addresses';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'country',
        'state',
        'city',
        'address',
        'zip_code',
        'customer_id',
        'is_default',
        'is_inside_dhaka',
        'is_inside_of_dhaka',
        'inside_dhaka',
        'is_out_side_dhaka',
        'qr_code',
        'courier_option',
        'map_location',
    ];

    /**
     * Convert checkbox 'on' value to proper boolean/integer
     */
    public function setIsOutSideDhakaAttribute($value)
    {
        $this->attributes['is_out_side_dhaka'] = ($value === 'on' || $value === '1' || $value === 1) ? 1 : 0;
    }
}
