<?php

namespace Botble\Ecommerce\Forms\Fronts\Customer;

use Botble\Theme\FormFront;
use App\Models\DhakaArea;
use Botble\Ecommerce\Models\Address;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\CheckboxField;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Ecommerce\Http\Requests\AddressRequest;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\ButtonFieldOption;
use Botble\Ecommerce\Forms\Concerns\HasLocationFields;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;

class AddressForm extends FormFront
{
    use HasLocationFields;

    protected string $formSelectInputClass;

    public function setup(): void
    {
        $model = $this->getModel();

        // Normalize commonly used model values safely for both array and object
        $modelIsEloquent = $model instanceof Address;
        $modelIsArray = is_array($model);
        $modelIsObject = is_object($model);
        $modelInsideThana = data_get($model, 'is_inside_dhaka');
        $modelInsideArea = data_get($model, 'inside_dhaka');
        $modelIsDefault = (bool) data_get($model, 'is_default', false);
        $modelHasKey = $modelIsEloquent && $model->getKey();

        // ধাপ ১: রিকোয়েস্ট থেকে ব্যবহারকারীর নির্বাচিত থানা আইডি নিন।
        // যদি কোনো থানা সিলেক্ট না করা হয় (যেমন প্রথমবার পেইজ লোড), তাহলে মডেলের পুরনো ভ্যালু ব্যবহার করুন।

        $selectedThanaId = request()->input('is_inside_dhaka', $modelInsideThana);

        // থানাগুলো আগের মতোই লোড হবে।
        $thanas = GlobalOptionValue::where('option_id', 7)->pluck('option_value', 'id')->toArray();

        // ধাপ ২: শর্ত অনুযায়ী এলাকা লোড করুন।
        $areas = []; // প্রাথমিকভাবে এলাকা অ্যারে খালি থাকবে।
        if ($selectedThanaId) {
            // যদি একটি থানা আইডি পাওয়া যায়, তবেই শুধুমাত্র সেই থানার এলাকাগুলো ডেটাবেস থেকে লোড হবে।
            // הערה: ধরে নিচ্ছি আপনার `DhakaArea` মডেলে `thana_id` নামে একটি কলাম আছে যা `global_option_values` এর `id` এর সাথে সম্পর্কিত।
            // আপনার ডেটাবেস স্ট্রাকচার অনুযায়ী এই কোয়েরি পরিবর্তন করতে হতে পারে।
            $areas = DhakaArea::where('thana_id', $selectedThanaId)->pluck('name', 'id')->toArray();
        }
        $this
            ->model(Address::class)
            ->setValidatorClass(AddressRequest::class)
            ->contentOnly()
            ->columns()
            ->add(
                'name',
                TextField::class,
                TextFieldOption::make()
                    ->addAttribute('id', 'address-name')
                    ->label(__('Full Name'))
                    ->placeholder(trans('plugins/ecommerce::addresses.name_placeholder_full'))
                    ->colspan(1)
            )
            ->add(
                'phone',
                TextField::class,
                TextFieldOption::make()
                    ->addAttribute('id', 'address-phone')
                    ->label(__('Phone'))
                    ->placeholder(trans('plugins/ecommerce::addresses.phone_placeholder_full'))
                    ->colspan(1)
            )
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()
                    ->addAttribute('id', 'address-email')
                    ->placeholder(trans('plugins/ecommerce::addresses.email_placeholder_full'))
                    ->label(__('Email'))
                    ->colspan(1)
            )


            // Toggle: Outside Dhaka (single checkbox)
            ->add(
                'address_outside_mode_wrapper_start',
                'html',
                [
                    'html' => '<fieldset style="border: 3px solid #1f8ef1; padding: 10px; border-radius: 4px;"><legend style="font-weight:600; color:#0d6efd;">' . e(trans('plugins/ecommerce::addresses.outside_dhaka_legend')) . '</legend>',
                ]
            )

            ->add(
                'is_out_side_dhaka',
                CheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/ecommerce::addresses.courier_option_label'))
                    ->addAttribute('id', 'address-outside-mode')
                    ->addAttribute('value', '1')
                    ->checked((bool) data_get($model, 'is_out_side_dhaka', false))
                    ->colspan(2)
            )
            ->add(
                'address_outside_mode_wrapper_end',
                'html',
                [
                    'html' => '</fieldset>',
                ]
            )

            ->add(
                'br',
                'html',
                [
                    'html' => '<br>',
                ]
            )

            ->add(
                'is_inside_dhaka',
                SelectField::class,
                [
                    'label' => trans('plugins/ecommerce::addresses.thana'),
                    'choices' => ['' => trans('plugins/ecommerce::addresses.thana_placeholder')] + $thanas,
                    'attr' => ['id' => 'address-thana', 'name' => 'is_inside_dhaka'],
                    'selected' => $modelInsideThana,
                    'colspan' => 1,
                ]
            )
            ->add(
                'inside_dhaka',
                SelectField::class,
                [
                    'label' => trans('plugins/ecommerce::addresses.area'),
                    'choices' => ['' => trans('plugins/ecommerce::addresses.area_placeholder')] + $areas,
                    'attr' => ['id' => 'address-area', 'name' => 'inside_dhaka'],
                    'selected' => $modelInsideArea,
                    'colspan' => 1,
                ]
            )
            ->addLocationFields()
            ->add(
                'map_location',
                TextField::class,
                TextFieldOption::make()
                    ->addAttribute('id', 'address-map-location')
                    ->addAttribute('placeholder', trans('plugins/ecommerce::addresses.map_location_placeholder'))
                    ->label(trans('plugins/ecommerce::addresses.map_location'))
                    ->helperText(trans('plugins/ecommerce::addresses.map_location_help'))

                    ->colspan(2)
            )
            ->add(
                'hr',
                'html',
                [
                    'html' => '<hr>',
                ]
            )
            ->add(
                'courier_option_wrapper',
                'html',
                [
                    'html' => '<div id="courier-option-wrapper" style="display: none;">
                        <fieldset style="border: 2px solid #28a745; padding: 15px; border-radius: 8px; margin: 15px 0;">
                            <legend style="font-weight: 600; color: #28a745; padding: 0 10px;">কুরিয়ার সেবা নির্বাচন</legend>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="sundorbon_courier" name="courier_option" value="Sundorbon Courier" ' . (data_get($model, 'courier_option') == 'Sundorbon Courier' ? 'checked' : '') . '>
                                        <label class="form-check-label" for="sundorbon_courier">
                                            <strong>Sundorbon Courier</strong><br>
                                            <small class="text-muted">সুন্দরবন কুরিয়ার - ২-৩ দিন</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="sa_paribahan" name="courier_option" value="SA Paribahan" ' . (data_get($model, 'courier_option') == 'SA Paribahan' ? 'checked' : '') . '>
                                        <label class="form-check-label" for="sa_paribahan">
                                            <strong>SA Paribahan</strong><br>
                                            <small class="text-muted">এস এ পরিবহন - ১-২ দিন</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <small class="text-info">
                                    <i class="fas fa-info-circle"></i> ঢাকার বাইরে ডেলিভারির জন্য কুরিয়ার সেবা নির্বাচন আবশ্যক।
                                </small>
                            </div>
                        </fieldset>
                    </div>',
                ]
            )

            ->add(
                'is_default',
                CheckboxField::class,
                CheckboxFieldOption::make()
                    ->label(trans('plugins/ecommerce::addresses.use_as_default'))
                    ->checked($modelIsDefault)
                    ->colspan(2)
            )
            ->add(
                'submit',
                'submit',
                ButtonFieldOption::make()
                    ->colspan(2)
                    ->label($modelHasKey ? trans('plugins/ecommerce::addresses.edit_address_new') : trans('plugins/ecommerce::addresses.add_address_new'))
                    ->cssClass('btn btn-primary mt-4')
            );
    }

    public function setFormSelectInputClass(string $cssClass): static
    {
        $this->formSelectInputClass = $cssClass;

        return $this;
    }

    public function renderForm(
        array $options = [],
        bool $showStart = true,
        bool $showFields = true,
        bool $showEnd = true
    ): string {
        foreach ($this->getFields() as &$field) {
            if ($field->getType() != SelectField::class) {
                continue;
            }

            if (isset($this->formSelectClass)) {
                $field->setOption('attr.class', $this->formSelectClass);
            }
        }

        $html = parent::renderForm($options, $showStart, $showFields, $showEnd);

        // Inject a small script to toggle inside/outside Dhaka and dynamically load areas
        $script = <<<'HTML'
<style>
    .courier-option-required {
        border-color: #dc3545 !important;
    }
</style>
<script>
(function() {
    const thanaSelect = document.getElementById('address-thana');
    const areaSelect = document.getElementById('address-area');
    const outsideCheckbox = document.getElementById('address-outside-mode');
    const courierWrapper = document.getElementById('courier-option-wrapper');
    const courierRadios = document.querySelectorAll('input[name="courier_option"]');
    // state/city may be select or input, find by name
    const stateInput = document.querySelector('[name="state"], #state, #address_state');
    const cityInput = document.querySelector('[name="city"], #city, #address_city');
    if (!thanaSelect || !areaSelect) return;

    const resetAreaOptions = () => {
        areaSelect.innerHTML = '';
        const placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Select Area';
        areaSelect.appendChild(placeholder);
    };

    const resetStateCity = () => {
        if (stateInput) {
            if (stateInput.tagName && stateInput.tagName.toLowerCase() === 'select') {
                stateInput.selectedIndex = 0;
                stateInput.dispatchEvent(new Event('change'));
            } else {
                stateInput.value = '';
            }
        }
        if (cityInput) {
            if (cityInput.tagName && cityInput.tagName.toLowerCase() === 'select') {
                cityInput.selectedIndex = 0;
                cityInput.dispatchEvent(new Event('change'));
            } else {
                cityInput.value = '';
            }
        }
    };

    const getWrapper = (el) => {
        if (!el) return null;
        const group = el.closest('.form-group');
        if (group) return group;
        const inputWrap = el.closest('.form-input-wrapper');
        return inputWrap ? inputWrap.parentElement : el.parentElement;
    };

    const stateWrap = getWrapper(stateInput);
    const cityWrap = getWrapper(cityInput);
    const thanaWrap = getWrapper(thanaSelect);
    const areaWrap = getWrapper(areaSelect);
    const courierWrap = getWrapper(courierWrapper);

    const showInsideDhaka = () => {
        if (thanaWrap) thanaWrap.style.display = '';
        if (areaWrap) areaWrap.style.display = '';
        if (stateWrap) stateWrap.style.display = 'none';
        if (cityWrap) cityWrap.style.display = 'none';
        if (courierWrapper) courierWrapper.style.display = 'none';
    };

    const showOutsideDhaka = () => {
        if (thanaWrap) thanaWrap.style.display = 'none';
        if (areaWrap) areaWrap.style.display = 'none';
        if (stateWrap) stateWrap.style.display = '';
        if (cityWrap) cityWrap.style.display = '';
        if (courierWrapper) courierWrapper.style.display = '';
    };

    const applyMode = () => {
        if (outsideCheckbox && outsideCheckbox.checked) {
            showOutsideDhaka();
        } else {
            showInsideDhaka();
        }
    };

    thanaSelect.addEventListener('change', function() {
        const thanaId = this.value;
        resetAreaOptions();
        if (!thanaId) return;

        fetch(`/get-dhaka-area/${thanaId}`)
            .then(response => response.json())
            .then(data => {
                const fetchedAreas = (data && Array.isArray(data.areas) ? data.areas : []);
                fetchedAreas.forEach(function(area) {
                    const option = document.createElement('option');
                    option.value = area.id;
                    option.textContent = area.name;
                    areaSelect.appendChild(option);
                });
                // Auto-select first fetched area if available
                if (fetchedAreas.length > 0) {
                    areaSelect.selectedIndex = 1; // index 0 is placeholder
                    areaSelect.dispatchEvent(new Event('change'));
                }
            })
            .catch(function() {
                // silently fail; user can retry by changing selection
            });
    });

    // If page loads with a thana already selected but no areas loaded, fetch and select first
    if (thanaSelect.value && areaSelect.options.length <= 1) {
        thanaSelect.dispatchEvent(new Event('change'));
    }

    if (outsideCheckbox) {
        outsideCheckbox.addEventListener('change', function() {
            if (outsideCheckbox.checked) {
                // Going outside: reset thana/area, make courier required
                if (thanaSelect) thanaSelect.value = '';
                if (areaSelect) {
                    resetAreaOptions();
                    areaSelect.selectedIndex = 0;
                }
                if (courierRadios.length > 0) {
                    courierRadios.forEach(radio => {
                        radio.setAttribute('required', 'required');
                    });
                }
            } else {
                // Going back inside: reset state/city, remove courier requirement
                resetStateCity();
                if (courierRadios.length > 0) {
                    courierRadios.forEach(radio => {
                        radio.removeAttribute('required');
                        radio.checked = false;
                    });
                }
            }
            applyMode();
        });
    }

    // Default: show Inside Dhaka (thana+area) when page loads
    applyMode();
})();
</script>

<style>
    .form-hint {
        color: red !important;
    }
    </style>
HTML;

        return $html . $script;
    }
}
