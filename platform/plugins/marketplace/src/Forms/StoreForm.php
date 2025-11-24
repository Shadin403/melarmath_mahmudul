<?php

namespace Botble\Marketplace\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Facades\Assets;
use Botble\Base\Forms\FieldOptions\ContentFieldOption;
use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\EditorField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Forms\Concerns\HasLocationFields;
use Botble\Ecommerce\Models\Customer;
use Botble\Marketplace\Facades\MarketplaceHelper;
use Botble\Marketplace\Forms\Concerns\HasSubmitButton;
use Botble\Marketplace\Http\Requests\StoreRequest;
use Botble\Marketplace\Models\Store;

class StoreForm extends FormAbstract
{
    use HasLocationFields;
    use HasSubmitButton;

    public function setup(): void
    {
        Assets::addScriptsDirectly('vendor/core/plugins/marketplace/js/store.js');

        $this
            ->model(Store::class)
            ->setValidatorClass(StoreRequest::class)
            ->columns(6)
            ->template('core/base::forms.form-no-wrap')
            ->hasFiles()
            ->add('name', TextField::class, NameFieldOption::make()->required()->colspan(6))
            ->add(
                'slug',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(view('plugins/marketplace::stores.partials.shop-url-field', ['store' => $this->getModel()])->render())
                    ->colspan(3)
            )
            ->add('email', EmailField::class, EmailFieldOption::make()->required()->colspan(3))
            ->add('phone', TextField::class, [
                'label' => trans('plugins/marketplace::store.forms.phone'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/marketplace::store.forms.phone_placeholder'),
                    'data-counter' => 15,
                ],
                'colspan' => 6,
            ])
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->colspan(6))
            ->add('content', EditorField::class, ContentFieldOption::make()->colspan(6))
            ->addLocationFields()
            ->addBefore(
                'state',
                'is_out_side_dhaka',
                \Botble\Base\Forms\Fields\OnOffCheckboxField::class,
                \Botble\Base\Forms\FieldOptions\OnOffFieldOption::make()
                    ->label('Is Outside Dhaka?')
                    ->defaultValue(false)
                    ->colspan(6)
                    ->attributes(['class' => 'is-outside-dhaka-checkbox'])
            )
            ->addAfter(
                'city',
                'is_inside_of_dhaka',
                SelectField::class,
                \Botble\Base\Forms\FieldOptions\SelectFieldOption::make()
                    ->label('Thana')
                    ->choices(\Botble\Ecommerce\Models\GlobalOptionValue::where('option_id', 7)->pluck('option_value', 'id')->all())
                    ->emptyValue('Select Thana')
                    ->searchable()
                    ->colspan(3)
                    ->attributes(['class' => 'dhaka-field thana-select'])
            )
            ->addAfter(
                'is_inside_of_dhaka',
                'inside_dhaka',
                SelectField::class,
                \Botble\Base\Forms\FieldOptions\SelectFieldOption::make()
                    ->label('Area')
                    ->choices(\App\Models\DhakaArea::pluck('name', 'id')->all())
                    ->emptyValue('Select Area')
                    ->searchable()
                    ->colspan(3)
                    ->attributes(['class' => 'dhaka-field area-select'])
            )
            ->addAfter(
                'inside_dhaka',
                'map_location',
                TextField::class,
                TextFieldOption::make()
                    ->label('Map Location (Google Maps Link)')
                    ->placeholder('https://maps.google.com/...')
                    ->colspan(6)
            )
            ->add(
                'company',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/marketplace::store.forms.company'))
                    ->placeholder(trans('plugins/marketplace::store.forms.company_placeholder'))
                    ->maxLength(255)
                    ->colspan(3)
            )
            ->add(
                'tax_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/marketplace::store.forms.tax_id'))
                    ->colspan(3)
                    ->maxLength(255)
            )
            ->add(
                'logo',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Logo'))
                    ->colspan(2)
            )
            ->add(
                'logo_square',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Square Logo'))
                    ->helperText(__('This logo will be used in some special cases. Such as checkout page.'))
                    ->colspan(2)
            )
            ->add(
                'cover_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(__('Cover Image'))
                    ->colspan(2)
            )
            ->add('status', SelectField::class, [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'choices' => BaseStatusEnum::labels(),
                'help_block' => [
                    TextField::class => trans('plugins/marketplace::marketplace.helpers.store_status', [
                        'customer' => CustomerStatusEnum::LOCKED()->label(),
                        'status' => BaseStatusEnum::PUBLISHED()->label(),
                    ]),
                ],
                'colspan' => 3,
            ])
            ->add('customer_id', SelectField::class, [
                'label' => trans('plugins/marketplace::store.forms.store_owner'),
                'required' => true,
                'choices' => [0 => trans('plugins/marketplace::store.forms.select_store_owner')] + Customer::query()
                    ->where('is_vendor', true)
                    ->pluck('name', 'id')
                    ->all(),
                'colspan' => 3,
            ])
            ->when(! MarketplaceHelper::hideStoreSocialLinks(), function (): void {
                $this
                    ->add('extended_info_content', HtmlField::class, [
                        'html' => view('plugins/marketplace::partials.extra-content', ['model' => $this->getModel()]),
                    ]);
            });
    }

    public function renderForm(array $options = [], $showStart = true, $showFields = true, $showEnd = true): string
    {
        $html = parent::renderForm($options, $showStart, $showFields, $showEnd);

        $html .= '
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    function initLocationToggle() {
                        // Try to find the checkbox by class (could be on input or wrapper)
                        let outsideCheckbox = document.querySelector(".is-outside-dhaka-checkbox");
                        if (outsideCheckbox && outsideCheckbox.tagName !== "INPUT") {
                            outsideCheckbox = outsideCheckbox.querySelector("input");
                        }
                        // Fallback: find by name
                        if (!outsideCheckbox) {
                            outsideCheckbox = document.querySelector("[name=\'is_out_side_dhaka\']");
                        }

                        if (!outsideCheckbox) return;

                        // Helper to find the closest form group container
                        function getContainer(fieldName) {
                            const element = document.querySelector("[name=\'" + fieldName + "\']");
                            if (!element) return null;
                            return element.closest(".mb-3") || element.closest(".form-group") || element.closest(".widget-item");
                        }

                        // Get containers
                        const stateWrap = getContainer("state");
                        const cityWrap = getContainer("city");
                        const countryWrap = getContainer("country"); // Optional
                        const thanaWrap = document.querySelector(".thana-select")?.closest(".mb-3") || document.querySelector(".thana-select")?.closest(".form-group");
                        const areaWrap = document.querySelector(".area-select")?.closest(".mb-3") || document.querySelector(".area-select")?.closest(".form-group");

                        function toggleFields() {
                            const isOutside = outsideCheckbox.checked;

                            // Toggle State/City (Show if Outside Dhaka)
                            if (stateWrap) stateWrap.style.display = isOutside ? "block" : "none";
                            if (cityWrap) cityWrap.style.display = isOutside ? "block" : "none";
                            if (countryWrap) countryWrap.style.display = isOutside ? "block" : "none";

                            // Toggle Thana/Area (Show if Inside Dhaka)
                            if (thanaWrap) thanaWrap.style.display = isOutside ? "none" : "block";
                            if (areaWrap) areaWrap.style.display = isOutside ? "none" : "block";
                        }

                        // Attach listener
                        outsideCheckbox.addEventListener("change", toggleFields);

                        // Initial run
                        toggleFields();
                    }

                    // Run immediately and after a short delay to ensure DOM is ready
                    initLocationToggle();
                    setTimeout(initLocationToggle, 500);
                    setTimeout(initLocationToggle, 1000);
                });
            </script>
        ';

        return $html;
    }
}
