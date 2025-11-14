<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Base\Rules\OnOffRule;
use Botble\Support\Http\Requests\Request;

class DhakaAreaRequest extends Request
{
    public function rules(): array
    {
        // Get ref_lang from request data (form input) or query parameters
        $currentLang = $this->input('ref_lang', request()->get('ref_lang', 'bn'));
        $isEnglishMode = str_starts_with($currentLang, 'en');

        if ($isEnglishMode) {
            return [
                'name_en' => ['required', 'string', 'max:255'],
                // Other fields are preserved as hidden inputs in English mode
            ];
        } else {
            return [
                'thana_id' => ['required', 'integer', 'exists:ec_global_option_value,id'],
                'areas' => ['required', 'array', 'min:1'],
                'areas.*.name' => ['required', 'string', 'max:255'],
                'areas.*.price' => ['required', 'numeric', 'min:0'],
            ];
        }
    }

    public function attributes(): array
    {
        return [
            'name' => __('Area Name'),
            'name_en' => __('Area Name'),
            'thana_id' => __('Thana'),
            'price' => __('Amount'),
            'areas' => __('Areas'),
            'areas.*.name' => __('Area Name'),
            'areas.*.price' => __('Amount'),
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('Area name is required'),
            'name.max' => __('Area name cannot exceed 255 characters'),
            'name_en.required' => __('Area name is required'),
            'name_en.max' => __('Area name cannot exceed 255 characters'),
            'thana_id.required' => __('Please select a thana'),
            'thana_id.exists' => __('Selected thana is invalid'),
            'price.required' => __('Amount is required'),
            'price.numeric' => __('Amount must be a valid number'),
            'price.min' => __('Amount must be greater than or equal to 0'),
            'areas.required' => __('At least one area is required'),
            'areas.array' => __('Areas must be an array'),
            'areas.min' => __('At least one area is required'),
            'areas.*.name.required' => __('Area name is required'),
            'areas.*.name.max' => __('Area name cannot exceed 255 characters'),
            'areas.*.price.required' => __('Amount is required'),
            'areas.*.price.numeric' => __('Amount must be a valid number'),
            'areas.*.price.min' => __('Amount must be greater than or equal to 0'),
        ];
    }
}
