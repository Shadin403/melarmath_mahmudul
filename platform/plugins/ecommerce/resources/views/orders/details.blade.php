@extends(BaseHelper::getAdminMasterLayoutTemplate())



@push('header-action')

    <form action="{{ route('orders.order-details') }}" method="GET" class="flex items-center space-x-2">

        <input

            type="datetime-local"

            name="from"

            class="form-control"

            value="{{ request('from') }}"

            placeholder="From Date & Time"

        />

        <input

            type="datetime-local"

            name="to"

            class="form-control"

            value="{{ request('to') }}"

            placeholder="To Date & Time"

        />



        <x-core::button

            tag="button"

            type="submit"

        >

            {{ trans('Filter') }}

        </x-core::button>

    </form>



    @php

        $from = request('from');

        $to = request('to');

    @endphp



    <x-core::button

        href="{{ route('orders.print-order-details', ['from' => $from, 'to' => $to]) }}"

        tag="a"

        id="print-button"

        target="_blank"

        icon="ti ti-download"

    >

        {{ trans('print') }}

    </x-core::button>

@endpush



@section('content')

<style>

    .form-check-input {

        width: 0.9em;

        height: 0.9em;

    }

</style>

    <div id="main-order-content">

        



        



        <div class="row row-cards">

            <div class="col-md-9">

                <x-core::card class="mb-3">

                    



                    <x-core::card.body>

                        <div class="row">

                            <div class="col-md-12">

                                @foreach ($orders as $order)

                                <div class="d-flex flex-wrap gap-2">

                                <div class="d-flex flex-column align-items-center border rounded p-2 me-2 mb-2 shadow-sm text-center" style="min-width: 120px;">

                                <h3>{{ $order->first()->product_name }}</h3>

                                </div>



                                

                                    @foreach ($order as $details)

                                        @php

                                        

                                            $attributes = Arr::get($details->options, 'attributes');

                                            
                                        
                                            $pattern = '/(?:Weight|⚖️ ওজন ⚖️|\*⚖ওজন⚖\*|ওজন)\s*:\s*([^),]+(?:\s*\(±\))?)/u';
                                            $weight = 0;

                                            if (preg_match($pattern, $attributes, $matches)) {

                                                $weight = trim($matches[1]);

                                            }

                                            $optionValue = '';

                                            if (array_key_exists('optionCartValue', $details->product_options)) {

                                                $array = $details->product_options['optionCartValue'];

                                                $firstKey = array_key_first($array);

                                                $optionValue = $array[$firstKey][0]['option_value'];

                                            }

                                        @endphp



                                        <div class="d-flex position-relative flex-column align-items-center border rounded p-2 me-2 mb-2 shadow-sm text-center" style="min-width: 120px;">
                                            <span>Quantity: {{ $details->qty }}</span>

                                            @php $inputId = 'exclude_' . $details->id; @endphp

                                            <input id="{{ $inputId }}" data-id="{{ $details->id }}" class="exclude-checkbox form-check-input position-absolute top-0 end-0 m-1" type="checkbox" name="excluded_items[]" value="{{ $details->id }}" />



                                            <label for="{{$inputId}}">

                                            @if ($attributes)

                                                <span>{{ $weight }}</span>

                                            @endif

                                            <hr class="my-1 w-100" />

                                            <span>{{ $optionValue }}</span>

                                            </label>



                                            <input

                                                type="text"

                                                class="form-control form-control-sm mt-1 note-input"

                                                name="notes[{{ $details->id }}]"

                                                placeholder="Note"

                                                data-note-for="{{ $details->id }}"

                                                style="display: none;"

                                            />

                                        </div>

                                    @endforeach

                                </div>

                            @endforeach

                            

                        </div>

                    </x-core::card.body>



                

                </x-core::card>



                



            

            </div>



        </div>



    

    </div>



    <script>

        document.getElementById('print-button').addEventListener('click', function (e) {

            e.preventDefault();



            const checked = document.querySelectorAll('.exclude-checkbox:checked');

            const urlParams = new URLSearchParams(window.location.search);

            const baseUrl = this.getAttribute('href').split('?')[0];



            checked.forEach(c => {

                const itemId = c.value;

                const noteInput = document.querySelector(`[name="notes[${itemId}]"]`);

                const note = noteInput ? noteInput.value : '';

                urlParams.append('excluded_items[]', itemId);

                urlParams.append(`notes[${itemId}]`, note);

            });



            const finalUrl = `${baseUrl}?${urlParams.toString()}`;

            window.open(finalUrl, '_blank');

        });





        document.querySelectorAll('.exclude-checkbox').forEach(checkbox => {

            checkbox.addEventListener('change', function () {

                const id = this.dataset.id;

                const noteInput = document.querySelector(`[data-note-for="${id}"]`);

                if (this.checked) {

                    noteInput.style.display = 'block';

                } else {

                    noteInput.style.display = 'none';

                }

            });

        });



    </script>

@endsection





