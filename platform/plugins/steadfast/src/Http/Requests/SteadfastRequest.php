<?php

namespace Khairulkabir\Steadfast\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SteadfastRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:220'],
            'status' => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
