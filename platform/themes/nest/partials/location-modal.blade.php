<div class="modal fade custom-modal" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content"
            style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); position: relative;">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                style="position: absolute; top: 20px; right: 20px; z-index: 10;"></button>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-12">
                        <div class="p-5">
                            <div class="text-center mb-4">
                                <div class="icon-box mb-3 mx-auto"
                                    style="width: 80px; height: 80px; background: #e8f6ea; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                    <i class="fi-rs-marker" style="font-size: 40px; color: #3BB77E;"></i>
                                </div>
                                <h3 class="mb-2" style="color: #253D4E; font-weight: 700;">{{ __('Select Location') }}
                                </h3>
                                <p class="text-muted">
                                    {{ __('Choose your delivery area to see available products and faster delivery options.') }}
                                </p>
                            </div>

                            <form id="locationForm">
                                <div class="form-group mb-4">
                                    <label for="thanaSelect" class="form-label fw-bold"
                                        style="color: #253D4E;">{{ __('Thana') }}</label>
                                    <select class="form-control form-select" id="thanaSelect" name="thana_name" required
                                        style="height: 50px; border-radius: 10px; background-color: #f7f8f9; border: 1px solid #ececec;">
                                        <option value="">{{ __('Select Your Thana') }}</option>
                                    </select>
                                </div>

                                <div class="form-group mb-4">
                                    <label for="areaSelect" class="form-label fw-bold"
                                        style="color: #253D4E;">{{ __('Area') }}</label>
                                    <select class="form-control form-select" id="areaSelect" name="area_id" disabled
                                        required
                                        style="height: 50px; border-radius: 10px; background-color: #f7f8f9; border: 1px solid #ececec;">
                                        <option value="">{{ __('Select Your Area') }}</option>
                                    </select>
                                    <input type="hidden" name="area_name" id="areaNameInput">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-fill-out btn-block hover-up"
                                        style="height: 50px; border-radius: 10px; font-size: 16px; font-weight: 600; background-color: #3BB77E; border: none;">
                                        {{ __('Confirm Location') }} <i class="fi-rs-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #locationModal .modal-content {
        animation: fadeInUp 0.4s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    #locationModal .form-control:focus {
        box-shadow: none;
        border-color: #3BB77E;
        background-color: #fff;
    }

    @media (max-width: 768px) {
        .modal.fade.custom-modal.show {
            display: block;
            z-index: 99999;
            background: rgba(0, 0, 0, 0.5);
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const locationModal = new bootstrap.Modal(document.getElementById('locationModal'));
        const thanaSelect = document.getElementById('thanaSelect');
        const areaSelect = document.getElementById('areaSelect');
        const areaNameInput = document.getElementById('areaNameInput');
        const locationForm = document.getElementById('locationForm');

        // Get user location from session
        @php
            $userLocation = Session::get('user_selected_location');
        @endphp
        const sessionAreaId = "{{ $userLocation['area_id'] ?? '' }}";
        const sessionThanaName = "{{ $userLocation['thana_name'] ?? '' }}";

        console.log('Session Area ID:', sessionAreaId);
        console.log('Session Thana Name:', sessionThanaName);

        // Load Thanas
        fetch("{{ route('public.ajax.thanas') }}")
            .then(response => response.json())
            .then(data => {
                let selectedThanaId = null;

                data.forEach(thana => {
                    const option = document.createElement('option');
                    option.value = thana.id;
                    option.text = thana.name;
                    option.dataset.name = thana.name;
                    thanaSelect.appendChild(option);

                    // Track which thana to pre-select
                    if (sessionThanaName && thana.name === sessionThanaName) {
                        selectedThanaId = thana.id;
                        console.log('Found matching thana:', thana.name, 'ID:', thana.id);
                    }
                });

                // Pre-select thana if found
                if (selectedThanaId) {
                    thanaSelect.value = selectedThanaId;
                    console.log('Pre-selecting thana ID:', selectedThanaId);
                    // Trigger change to load areas
                    loadAreas(selectedThanaId, sessionAreaId);
                }

                // Show modal only if no location selected
                if (!sessionAreaId) {
                    console.log('No session area, showing modal');
                    locationModal.show();
                } else {
                    console.log('Session area exists, not showing modal');
                }
            });

        // Function to load areas
        function loadAreas(thanaId, selectedAreaId = null) {
            areaSelect.innerHTML = '<option value="">{{ __('Select Area') }}</option>';
            areaSelect.disabled = true;

            if (thanaId) {
                let url = "{{ route('public.ajax.areas', ['thanaId' => 0]) }}";
                fetch(url.replace('/0', '/' + thanaId))
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(area => {
                            const option = document.createElement('option');
                            option.value = area.id;
                            option.text = area.name;
                            option.dataset.name = area.name;
                            areaSelect.appendChild(option);
                        });

                        // Pre-select area if provided
                        if (selectedAreaId) {
                            console.log('Pre-selecting area ID:', selectedAreaId);
                            areaSelect.value = selectedAreaId;
                            const selectedOption = areaSelect.options[areaSelect.selectedIndex];
                            if (selectedOption) {
                                areaNameInput.value = selectedOption.dataset.name;
                                console.log('Area pre-selected:', selectedOption.text);
                            }
                        }

                        areaSelect.disabled = false;
                    });
            }
        }

        // Handle Thana Change
        thanaSelect.addEventListener('change', function() {
            loadAreas(this.value);
        });

        // Handle Area Change
        areaSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            areaNameInput.value = selectedOption.dataset.name;
        });

        // Handle Form Submit
        locationForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const thanaName = thanaSelect.options[thanaSelect.selectedIndex].dataset.name;
            const formData = new FormData(this);
            formData.append('thana_name', thanaName);

            fetch("{{ route('public.ajax.set-location') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        locationModal.hide();
                        window.location.reload();
                    }
                });
        });

        // Expose open function globally
        window.openLocationModal = function() {
            // Load Thanas if empty
            if (thanaSelect.options.length <= 1) {
                fetch("{{ route('public.ajax.thanas') }}")
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(thana => {
                            const option = document.createElement('option');
                            option.value = thana.id;
                            option.text = thana.name;
                            option.dataset.name = thana.name;
                            thanaSelect.appendChild(option);
                        });
                        locationModal.show();
                    });
            } else {
                locationModal.show();
            }
        }
    });
</script>
