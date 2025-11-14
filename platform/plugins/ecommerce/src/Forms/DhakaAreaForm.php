<?php

namespace Botble\Ecommerce\Forms;

use App\Models\DhakaArea;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Http\Requests\DhakaAreaRequest;
use Botble\Ecommerce\Models\GlobalOptionValue;

class DhakaAreaForm extends FormAbstract
{
    public function setup(): void
    {
        $thanas = GlobalOptionValue::where('option_id', 7)->pluck('option_value', 'id')->toArray();

        $this
            ->model(DhakaArea::class)
            ->setValidatorClass(DhakaAreaRequest::class)
            ->add('thana_id', SelectField::class, SelectFieldOption::make()
                ->label(__('Thana'))
                ->required()
                ->choices(['' => __('Select a Thana...')] + $thanas)
                ->searchable()
                ->addAttribute('class', 'select-search-full')
                ->toArray())
            ->add('name', TextField::class, TextFieldOption::make()
                ->label(__('Area Name') . ' (' . __('Bengali') . ')')
                ->required()
                ->maxLength(255)
                ->addAttribute('data-counter', 255)
                ->addAttribute('placeholder', 'এলাকার নাম বাংলায় লিখুন')
                ->toArray())
            ->add('price', NumberField::class, NumberFieldOption::make()
                ->label(__('Amount'))
                ->required()
                ->step(0.01)
                ->min(0)
                ->addAttribute('placeholder', __('Amount'))
                ->toArray())
            ->setBreakFieldPoint('price');
    }
}
