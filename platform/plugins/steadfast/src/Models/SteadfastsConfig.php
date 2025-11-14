<?php

namespace Khairulkabir\Steadfast\Models;

use Botble\Base\Models\BaseModel;

class SteadfastsConfig extends BaseModel
{
    protected $table = 'steadfasts_configs';

    protected $fillable = [
        'enable_disable',
        'notes',
        'api_key',
        'secret_key',
        'business_name',
        'business_address',
        'business_email',
        'business_number',
        'terms_conditions',
        'business_logo',
        'auth_token',
        'use_as_invoice_info',
        'bdcourier_API',
        'bdcourier_auth',
        'license_code',
        'client_name',
        'license_file',
          
    ];
}
