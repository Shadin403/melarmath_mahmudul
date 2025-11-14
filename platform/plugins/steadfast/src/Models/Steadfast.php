<?php
namespace Khairulkabir\Steadfast\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Ecommerce\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class Steadfast extends BaseModel
{
    protected $table = 'steadfasts';

    protected $fillable = [
        'order_id',
        'steadfast_amount',
        'steadfast_consignment_id',
        'steadfast_is_sent',
        'stdf_delivery_status',
        'tracking_code',
        'error',
        'status',
      
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'name' => SafeContent::class,
        'steadfast_is_sent' => 'boolean',
        'steadfast_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Custom function to retrieve all orders along with matching steadfast records (if available).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function orderList($perPage = 10, $search = null)
    {
        $query = Order::leftJoin('steadfasts', 'ec_orders.id', '=', 'steadfasts.order_id')
        ->leftJoin('ec_order_product', 'ec_orders.id', '=', 'ec_order_product.order_id') // Join with the ec_order_product table
            ->select(
                'ec_orders.*',
                'steadfasts.steadfast_amount',
                'steadfasts.steadfast_consignment_id',
                'steadfasts.steadfast_is_sent',
                'steadfasts.stdf_delivery_status',
                'steadfasts.tracking_code as tracking_code',
                'steadfasts.status as steadfast_status',
                'ec_order_product.product_name' 
            )
            ->orderBy('ec_orders.created_at', 'desc')
            ->where('is_finished',1);

        
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('ec_orders.code', 'LIKE', "%{$search}%")
                    ->orWhere('steadfasts.steadfast_consignment_id', 'LIKE', "%{$search}%");
            });
        }
        return $query->paginate($perPage);

    }
}
