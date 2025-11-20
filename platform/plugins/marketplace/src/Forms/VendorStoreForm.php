<?php

namespace Botble\Marketplace\Forms;

use Botble\Marketplace\Forms\Fields\CustomEditorField;
use Botble\Marketplace\Http\Requests\Fronts\VendorStoreRequest;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Ecommerce\Models\GlobalOptionValue;
use App\Models\DhakaArea;

class VendorStoreForm extends StoreForm
{
    public function setup(): void
    {
        parent::setup();

        // Get Thana and Area data
        $thanas = GlobalOptionValue::where('option_id', 7)->pluck('option_value', 'id')->all();
        $areas = DhakaArea::pluck('name', 'id')->all();

        $this
            ->setValidatorClass(VendorStoreRequest::class)
            ->modify('content', CustomEditorField::class)
            ->remove(['status', 'customer_id'])
            ->addAfter(
                'address',
                'is_out_side_dhaka',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label('Is Outside Dhaka?')
                    ->defaultValue(false)
                    ->colspan(6)
                    ->attributes(['class' => 'is-outside-dhaka-checkbox'])
            )
            ->addAfter(
                'is_out_side_dhaka',
                'is_inside_of_dhaka',
                SelectField::class,
                SelectFieldOption::make()
                    ->label('Thana')
                    ->choices($thanas)
                    ->emptyValue('Select Thana')
                    ->searchable()
                    ->colspan(3)
                    ->attributes(['class' => 'dhaka-field thana-select'])
            )
            ->addAfter(
                'is_inside_of_dhaka',
                'inside_dhaka',
                SelectField::class,
                SelectFieldOption::make()
                    ->label('Area')
                    ->choices($areas)
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
            ->addAfter('map_location', 'location_script', 'html', [
                'html' => '
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            function initLocationToggle() {
                                const outsideCheckbox = document.querySelector(".is-outside-dhaka-checkbox input");
                                if (!outsideCheckbox) return;

                                // Helper to find the closest form group container
                                function getContainer(fieldName) {
                                    const element = document.querySelector("[name=\'" + fieldName + "\']");
                                    if (!element) return null;
                                    return element.closest(".mb-3") || element.closest(".form-group");
                                }

                                // Get containers
                                const stateWrap = getContainer("state");
                                const cityWrap = getContainer("city");
                                const countryWrap = getContainer("country");
                                const thanaWrap = document.querySelector(".thana-select")?.closest(".mb-3");
                                const areaWrap = document.querySelector(".area-select")?.closest(".mb-3");

                                function toggleFields() {
                                    const isOutside = outsideCheckbox.checked;

                                    // Toggle State/City/Country (Show if Outside Dhaka)
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
                ',
                'colspan' => 6,
            ]);
    }
}
