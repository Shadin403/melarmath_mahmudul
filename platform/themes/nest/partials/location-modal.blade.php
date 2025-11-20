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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const locationModal = new bootstrap.Modal(document.getElementById('locationModal'));
        const thanaSelect = document.getElementById('thanaSelect');
        const areaSelect = document.getElementById('areaSelect');
        const areaNameInput = document.getElementById('areaNameInput');
        const locationForm = document.getElementById('locationForm');

        // Check if location is already selected
        const userLocation = "{{ Session::get('user_selected_location')['area_name'] ?? '' }}";

        if (!userLocation) {
            // Load Thanas
            fetch("{{ route('public.ajax.thanas') }}")
                .then(response => response.json())
                .then(data => {
                    data.forEach(thana => {
                        const option = document.createElement('option');
                        option.value = thana
                            .id; // Using ID for fetching areas, but name for saving if needed
                        option.text = thana.name;
                        option.dataset.name = thana.name;
                        thanaSelect.appendChild(option);
                    });
                    locationModal.show();
                });
        }

        // Handle Thana Change
        thanaSelect.addEventListener('change', function() {
            const thanaId = this.value;
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
                        areaSelect.disabled = false;
                    });
            }
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
