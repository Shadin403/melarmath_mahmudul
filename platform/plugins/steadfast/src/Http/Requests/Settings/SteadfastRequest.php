<?php 
namespace Khairulkabir\Steadfast\Http\Requests\Settings;

use Botble\Support\Http\Requests\Request;

class SteadfastRequest extends Request
{
    public function rules(): array
    {
        return [
            'client_name' =>'required|string|max:255|sometimes|required_if:enable_disable,true',
            'license_code' =>'required|string|max:255|sometimes|required_if:enable_disable,true',
            // Validate Enable/Disable as a boolean value
            'enable_disable' => 'nullable|boolean',

            // Validate Notes as a boolean value
            'notes' => 'nullable|boolean',

            // API Key: Can be nullable, must be a string, and max length is 255 characters
            'api_key' => 'nullable|string|max:255',

            // Secret Key: Can be nullable, must be a string, and max length is 255 characters
            'secret_key' => 'nullable|string|max:255',

            'auth_token'=> 'required||string|max:255',

            'use_as_invoice_info' => 'nullable|boolean',

            // Business Name: Required if 'use_as_invoice_info' is enabled
            'business_name' => 'nullable|string|max:255|sometimes|required_if:use_as_invoice_info,true',

            // Business Address: Required if 'use_as_invoice_info' is enabled
            'business_address' => 'nullable|string|max:255|sometimes|required_if:use_as_invoice_info,true',

            // Business Email: Must be a valid email and required if 'use_as_invoice_info' is enabled
            'business_email' => 'nullable|email|max:255|sometimes|required_if:use_as_invoice_info,true',

            // Business Number: Required if 'use_as_invoice_info' is enabled
            'business_number' => 'nullable|string|max:255|sometimes|required_if:use_as_invoice_info,true',

            // Business Logo: Required if 'use_as_invoice_info' is enabled
            'business_logo' => 'nullable|string|max:255|sometimes|required_if:use_as_invoice_info,true',

            // bdcourier_API: Can be nullable, must be a boolean
            'bdcourier_API' => 'nullable|boolean',

            // bdcourier_auth: Required if bdcourier_API is enabled (true)
            'bdcourier_auth' => 'nullable|string|sometimes|required_if:bdcourier_API,true',
            
        ];
    }

    public function messages(): array
    {
        return [
            'license_code.required' => 'The license_code field is required to enable the plugin',
            'client_name.required' => 'The license_code field is required to enable the plugin',
            'auth_token.required'=> 'The auth_token field is required.',
            'enable_disable.required' => 'The Enable/Disable field is required.',
            'notes.required' => 'The Notes field is required.',
            'business_email.email' => 'The Business Email must be a valid email address.',
            'business_logo.max' => 'The Business Logo field must not exceed 255 characters.',
            'bdcourier_auth.required_if' => 'The bdcourier Auth field is required when the bdcourier API is enabled.',
            'business_name.required_if' => 'The Business Name is required when "Use as Invoice Info" is enabled.',
            'business_address.required_if' => 'The Business Address is required when "Use as Invoice Info" is enabled.',
            'business_email.required_if' => 'The Business Email is required when "Use as Invoice Info" is enabled.',
            'business_number.required_if' => 'The Business Number is required when "Use as Invoice Info" is enabled.',
            'business_logo.required_if' => 'The Business Logo is required when "Use as Invoice Info" is enabled.',
        ];
    }
}
