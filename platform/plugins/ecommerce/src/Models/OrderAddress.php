<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Avatar;
use Botble\Ecommerce\Enums\OrderAddressTypeEnum;
use Botble\Ecommerce\Traits\LocationTrait;
use Botble\Media\Facades\RvMedia;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderAddress extends BaseModel
{
    use LocationTrait;

    protected $table = 'ec_order_addresses';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'state',
        'city',
        'address',
        'zip_code',
        'order_id',
        'type',
        'is_out_side_dhaka',
        'is_inside_of_dhaka',
        'road_name',
        'house_name',
        'map_location',
        'inside_dhaka',
        'qr_code',
        'pay_delevery_charge',
        'courier_option'
    ];

    public $timestamps = false;

    protected $casts = [
        'type' => OrderAddressTypeEnum::class,
    ];

    /**
     * Convert checkbox 'on' value to proper boolean/integer
     */
    public function setIsOutSideDhakaAttribute($value)
    {
        $this->attributes['is_out_side_dhaka'] = ($value === 'on' || $value === '1' || $value === 1) ? 1 : 0;
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            try {
                return (new Avatar())->create($this->name)->toBase64();
            } catch (Exception) {
                return RvMedia::getDefaultImage();
            }
        });
    }


    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withDefault();
    }
}
