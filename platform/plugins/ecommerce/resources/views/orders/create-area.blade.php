@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div id="main-area-content">
        <div class="row justify-content-center">
            <div class="col-xxl-9 col-xl-8 col-lg-10">
                <div class="row">
                    <div class="col-md-9">
                        <x-core::card class="mb-3">
                            <x-core::card.header>
                                <x-core::card.title>
                                    {{ trans('core/base::forms.create') }} {{ trans('Area') }}
                                </x-core::card.title>
                            </x-core::card.header>

                            <x-core::card.body>
                                {!! Form::open([
                                    'route' => 'orders.saveDhakaArea',
                                    'method' => 'POST',
                                    'id' => 'main-area-form',
                                ]) !!}

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label required" for="thana_id">{{ __('Thana') }}</label>
                                            <select class="form-select select-search-full" name="thana_id" id="thana_id"
                                                required>
                                                <option value="">{{ __('Select a Thana...') }}</option>
                                                @foreach ($thana as $singleThana)
                                                    <option value="{{ $singleThana->id }}">{{ $singleThana->option_value }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div id="areas-container">
                                    <div class="area-row mb-3" data-index="0">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <label class="form-label required">{{ __('Area Name') }}
                                                    ({{ __('Bengali') }})</label>
                                                <input type="text" class="form-control area-name" name="areas[0][name]"
                                                    required maxlength="250" placeholder="এলাকার নাম বাংলায় লিখুন">
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label required">{{ __('Amount') }}</label>
                                                <input type="number" class="form-control area-price" name="areas[0][price]"
                                                    required step="0.01" min="0" placeholder="Amount">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-success add-area-btn btn-sm"
                                                    title="Add Area" style="min-width: 40px; height: 40px;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                        viewBox="0 0 1024 1024"
                                                        style="display: inline-block; vertical-align: middle;">
                                                        <path fill="currentColor"
                                                            d="M480 480V128a32 32 0 0 1 64 0v352h352a32 32 0 1 1 0 64H544v352a32 32 0 1 1-64 0V544H128a32 32 0 0 1 0-64z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {!! Form::close() !!}
                            </x-core::card.body>
                        </x-core::card>
                    </div>

                    <div class="col-md-3 right-sidebar d-flex flex-column-reverse flex-md-column">
                        <x-core::card class="mb-3">
                            <x-core::card.header>
                                <x-core::card.title>
                                    {{ trans('core/base::forms.publish') }}
                                </x-core::card.title>
                            </x-core::card.header>

                            <x-core::card.body>
                                <div class="btn-list d-grid">
                                    <x-core::button type="submit" form="main-area-form" name="submitter" value="save"
                                        color="success" icon="ti ti-device-floppy">
                                        {{ trans('core/base::forms.save') }}
                                    </x-core::button>

                                    <x-core::button type="submit" form="main-area-form" name="submitter" value="apply"
                                        color="info" icon="ti ti-device-floppy">
                                        {{ trans('core/base::forms.save_and_continue') }}
                                    </x-core::button>

                                    <x-core::button tag="a" :href="route('orders.inside-dhaka')" color="secondary"
                                        icon="ti ti-arrow-left">
                                        {{ trans('core/base::forms.cancel') }}
                                    </x-core::button>
                                </div>
                            </x-core::card.body>
                        </x-core::card>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let areaIndex = 1;

            // Add new area row
            document.addEventListener('click', function(e) {
                if (e.target.closest('.add-area-btn')) {
                    e.preventDefault();
                    addAreaRow();
                }

                // Remove area row
                if (e.target.closest('.remove-area-btn')) {
                    e.preventDefault();
                    const row = e.target.closest('.area-row');
                    if (document.querySelectorAll('.area-row').length > 1) {
                        row.remove();
                        updateIndices();
                    }
                }
            });

            function addAreaRow() {
                const container = document.getElementById('areas-container');
                const newRow = document.createElement('div');
                newRow.className = 'area-row mb-3';
                newRow.setAttribute('data-index', areaIndex);

                newRow.innerHTML = `
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" class="form-control area-name" name="areas[${areaIndex}][name]"
                                required maxlength="250" placeholder="এলাকার নাম বাংলায় লিখুন">
                        </div>
                        <div class="col-md-5">
                            <input type="number" class="form-control area-price" name="areas[${areaIndex}][price]"
                                required step="0.01" min="0" placeholder="Amount">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-success add-area-btn btn-sm me-2" title="Add Area" style="min-width: 40px; height: 40px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 1024 1024" style="display: inline-block; vertical-align: middle;">
                                    <path fill="currentColor" d="M480 480V128a32 32 0 0 1 64 0v352h352a32 32 0 1 1 0 64H544v352a32 32 0 1 1-64 0V544H128a32 32 0 0 1 0-64z"/>
                                </svg>
                            </button>
                            <button type="button" class="btn btn-danger remove-area-btn btn-sm" title="Remove Area" style="min-width: 40px; height: 40px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 1024 1024" style="display: inline-block; vertical-align: middle;">
                                    <path fill="currentColor" d="M160 256H96a32 32 0 0 1 0-64h256V95.936a32 32 0 0 1 32-32h256a32 32 0 0 1 32 32V192h256a32 32 0 1 1 0 64h-64v672a32 32 0 0 1-32 32H192a32 32 0 0 1-32-32zm448-64v-64H416v64zM224 896h576V256H224zm192-128a32 32 0 0 1-32-32V416a32 32 0 0 1 64 0v320a32 32 0 0 1-32 32m192 0a32 32 0 0 1-32-32V416a32 32 0 0 1 64 0v320a32 32 0 0 1-32 32"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                `;

                container.appendChild(newRow);
                areaIndex++;
            }

            function updateIndices() {
                const rows = document.querySelectorAll('.area-row');
                rows.forEach((row, index) => {
                    row.setAttribute('data-index', index);
                    const nameInput = row.querySelector('.area-name');
                    const priceInput = row.querySelector('.area-price');

                    if (nameInput) nameInput.name = `areas[${index}][name]`;
                    if (priceInput) priceInput.name = `areas[${index}][price]`;
                });
                areaIndex = rows.length;
            }
        });
    </script>
@endsection
