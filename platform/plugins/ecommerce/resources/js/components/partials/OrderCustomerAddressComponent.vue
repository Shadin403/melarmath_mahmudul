<template>
    <ec-modal id="add-customer" :title="__('order.create_new_customer')" :ok-title="__('order.save')"
        :cancel-title="__('order.cancel')" @shown="loadCountries($event)" @ok="$emit('create-new-customer', $event)">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label required">পুরো নাম</label>
                    <input type="text" class="form-control" v-model="address.name" name="name"
                        placeholder="পুরো নাম লিখুন" required />
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">ফোন নাম্বার</label>
                    <input type="text" class="form-control" v-model="address.phone" name="phone"
                        placeholder="ফোন নাম্বার" />
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">ইমেইল</label>
            <input type="email" class="form-control" v-model="address.email" name="email" placeholder="ইমেইল লিখুন" />
        </div>

        <!-- Outside Dhaka Toggle -->
        <fieldset style="border: 3px solid #1f8ef1; padding: 10px; border-radius: 4px; margin-bottom: 12px;">
            <legend style="font-weight:600; color:#0d6efd;">ঢাকার বাইরে</legend>
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="create-order-outside-mode"
                        v-model="outsideDhakaMode" @change="toggleAddressMode">
                    <label class="form-check-label" for="create-order-outside-mode">
                        কুরিয়ার এর মাধ্যমে
                    </label>
                </div>
            </div>
        </fieldset>

        <div class="mb-3">
            <label class="form-label required">{{ __('order.country') }}</label>
            <select class="form-select" v-model="address.country" name="country" @change="loadStates($event)">
                <option v-for="(countryName, countryCode) in countries" :value="countryCode" v-bind:key="countryCode">
                    {{ countryName }}
                </option>
            </select>
        </div>

        <!-- State/Division Field - Show when outside Dhaka -->
        <div v-if="outsideDhakaMode" class="mb-3">
            <label class="form-label">বিভাগ</label>
            <select v-if="use_location_data" class="form-select" v-model="address.state" name="state"
                @change="loadCities($event)">
                <option value="">বিভাগ নির্বাচন করুন</option>
                <option v-for="state in states" :value="state.id" v-bind:key="state.id">
                    {{ state.name }}
                </option>
            </select>
            <input v-else type="text" class="form-control" v-model="address.state" name="state"
                placeholder="বিভাগ লিখুন" />
        </div>

        <!-- City/District Field - Show when outside Dhaka -->
        <div v-if="outsideDhakaMode" class="mb-3">
            <label class="form-label">জেলা</label>
            <select v-if="use_location_data" v-model="address.city" class="form-select" name="city">
                <option value="">জেলা নির্বাচন করুন</option>
                <option v-for="city in cities" :value="city.id" v-bind:key="city.id">
                    {{ city.name }}
                </option>
            </select>
            <input v-else type="text" class="form-control" v-model="address.city" name="city"
                placeholder="জেলা লিখুন" />
        </div>

        <!-- Dhaka Thana Field - Show when inside Dhaka -->
        <div v-if="!outsideDhakaMode" class="mb-3">
            <label class="form-label">ঢাকার ভিতরে</label>
            <select class="form-select" v-model.number="address.is_inside_dhaka" name="is_inside_dhaka"
                @change="loadAreas">
                <option value="">থানা নির্বাচন করুন</option>
                <option v-for="(option_value, id) in thanas" :value="id" v-bind:key="id">
                    {{ option_value }}
                </option>
            </select>
        </div>

        <!-- Dhaka Area Field - Show when inside Dhaka -->
        <div v-if="!outsideDhakaMode" class="mb-3">
            <label class="form-label">ঢাকার ভিতরে এরিয়া</label>
            <select class="form-select" v-model="address.inside_dhaka" name="inside_dhaka">
                <option value="">এলাকা নির্বাচন করুন</option>
                <option v-for="area in areas" :value="area.id" v-bind:key="area.id">
                    {{ area.name }}
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label required">বিস্তারিত ঠিকানা</label>
            <input type="text" class="form-control" v-model="address.address" name="address" placeholder="ঠিকানা লিখুন"
                required />
        </div>

        <!-- Map Location Field -->
        <div class="mb-3">
            <label class="form-label">Map Location</label>
            <div class="input-group">
                <input type="url" class="form-control" v-model="address.map_location" name="map_location"
                    placeholder="Google Maps বা অন্য কোনো মানচিত্র লিংক লিখুন (ঐচ্ছিক)" />
                <span class="input-group-text" style="cursor: pointer;" @click="openGoogleMaps"
                    title="Open Google Maps">
                    <svg class="icon svg-icon-ti-ti-external-link" xmlns="http://www.w3.org/2000/svg" width="16"
                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path>
                        <path d="M11 13l9 -9"></path>
                        <path d="M15 4h5v5"></path>
                    </svg>
                </span>
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> গুগল ম্যাপস থেকে আপনার অবস্থানের লিংক কপি করে এখানে পেস্ট করুন
            </small>
        </div>

        <!-- Courier Option Section - Only show when outside Dhaka -->
        <div v-if="outsideDhakaMode">
            <fieldset style="border: 2px solid #28a745; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <legend style="font-weight: 600; color: #28a745; padding: 0 10px;">কুরিয়ার সেবা নির্বাচন</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="create_sundorbon_courier"
                                name="courier_option" v-model="address.courier_option" value="Sundorbon Courier">
                            <label class="form-check-label" for="create_sundorbon_courier">
                                <strong>Sundorbon Courier</strong><br>
                                <small class="text-muted">সুন্দরবন কুরিয়ার - ২-৩ দিন</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="create_sa_paribahan" name="courier_option"
                                v-model="address.courier_option" value="SA Paribahan">
                            <label class="form-check-label" for="create_sa_paribahan">
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
        </div>

        <div v-if="zip_code_enabled" class="mb-3">
            <label class="form-label">জিপ কোড</label>
            <input type="text" class="form-control" v-model="address.zip_code" name="zip_code"
                placeholder="জিপ কোড লিখুন" />
        </div>
    </ec-modal>

    <ec-modal id="edit-email" :title="__('order.update_email')" :ok-title="__('order.update')"
        :cancel-title="__('order.close')" @ok="$emit('update-customer-email', $event)">
        <div class="mb-3 position-relative">
            <label class="form-label">{{ __('order.email') }}</label>
            <input class="form-control" v-model="customer.email" />
        </div>
    </ec-modal>

    <ec-modal id="edit-address" :title="__('order.update_address')" :ok-title="__('order.save')"
        :cancel-title="__('order.cancel')" @shown="shownEditAddress" @ok="$emit('update-order-address', $event)">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label required">পুরো নাম</label>
                    <input type="text" class="form-control customer-address-name" v-model="address.name" name="name"
                        placeholder="পুরো নাম লিখুন" required />
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">ফোন নাম্বার</label>
                    <input type="text" class="form-control customer-address-phone" v-model="address.phone" name="phone"
                        placeholder="ফোন নাম্বার" />
                </div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">ইমেইল</label>
            <input type="email" class="form-control customer-address-email" v-model="address.email" name="email"
                placeholder="ইমেইল লিখুন" />
        </div>
        <!-- Outside Dhaka Toggle -->
        <fieldset style="border: 3px solid #1f8ef1; padding: 10px; border-radius: 4px; margin-bottom: 12px;">
            <legend style="font-weight:600; color:#0d6efd;">ঢাকার বাইরে</legend>
            <div class="form-group">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="edit-order-outside-mode"
                        v-model="outsideDhakaMode" @change="toggleAddressMode">
                    <label class="form-check-label" for="edit-order-outside-mode">
                        কুরিয়ার এর মাধ্যমে
                    </label>
                </div>
            </div>
        </fieldset>

        <div class="mb-3">
            <label class="form-label required">{{ __('order.country') }}</label>
            <select class="form-select customer-address-country" v-model="address.country" name="country"
                @change="loadStates($event)">
                <option v-for="(countryName, countryCode) in countries" :selected="address.country === countryCode"
                    :value="countryCode" v-bind:key="countryCode">
                    {{ countryName }}
                </option>
            </select>
        </div>

        <!-- State/Division Field - Show when outside Dhaka -->
        <div v-if="outsideDhakaMode" class="mb-3">
            <label class="form-label">বিভাগ</label>
            <select v-if="use_location_data" class="form-select customer-address-state" v-model="address.state"
                name="state" @change="loadCities($event)">
                <option value="">বিভাগ নির্বাচন করুন</option>
                <option v-for="state in states" :selected="address.state === state.id" :value="state.id"
                    v-bind:key="state.id">
                    {{ state.name }}
                </option>
            </select>
            <input v-else type="text" class="form-control customer-address-state" v-model="address.state" name="state"
                placeholder="বিভাগ লিখুন" />
        </div>

        <!-- City/District Field - Show when outside Dhaka -->
        <div v-if="outsideDhakaMode" class="mb-3">
            <label class="form-label">জেলা</label>
            <select v-if="use_location_data" v-model="address.city" class="form-select customer-address-city"
                name="city">
                <option value="">জেলা নির্বাচন করুন</option>
                <option v-for="city in cities" :value="city.id" v-bind:key="city.id">
                    {{ city.name }}
                </option>
            </select>
            <input v-else type="text" class="form-control customer-address-city" v-model="address.city" name="city"
                placeholder="জেলা লিখুন" />
        </div>

        <!-- Dhaka Thana Field - Show when inside Dhaka -->
        <div v-if="!outsideDhakaMode" class="mb-3">
            <label class="form-label">ঢাকার ভিতরে</label>
            <select class="form-select customer-address-thana" v-model="address.is_inside_of_dhaka"
                name="is_inside_of_dhaka" @change="loadAreas">
                <option value="">থানা নির্বাচন করুন</option>
                <option v-for="(option_value, id) in thanas" :value="id" v-bind:key="id">
                    {{ option_value }}
                </option>
            </select>
        </div>

        <!-- Dhaka Area Field - Show when inside Dhaka -->
        <div v-if="!outsideDhakaMode" class="mb-3">
            <label class="form-label">ঢাকার ভিতরে এরিয়া</label>
            <select class="form-select customer-address-area" v-model="address.inside_dhaka" name="inside_dhaka">
                <option value="">এলাকা নির্বাচন করুন</option>
                <option v-for="area in areas" :value="area.id" v-bind:key="area.id">
                    {{ area.name }}
                </option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label required">বিস্তারিত ঠিকানা</label>
            <input type="text" class="form-control customer-address-address" v-model="address.address" name="address"
                placeholder="ঠিকানা লিখুন" required />
        </div>

        <!-- Map Location Field - Always visible like name, phone, email -->
        <div class="mb-3">
            <label class="form-label">Map Location</label>
            <div class="input-group">
                <input type="url" class="form-control customer-address-map-location" v-model="address.map_location"
                    name="map_location" placeholder="Google Maps বা অন্য কোনো মানচিত্র লিংক লিখুন (ঐচ্ছিক)" />
                <span class="input-group-text" style="cursor: pointer;" @click="openGoogleMaps"
                    title="Open Google Maps">
                    <svg class="icon svg-icon-ti-ti-external-link" xmlns="http://www.w3.org/2000/svg" width="16"
                        height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 6h-6a2 2 0 0 0 -2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-6"></path>
                        <path d="M11 13l9 -9"></path>
                        <path d="M15 4h5v5"></path>
                    </svg>
                </span>
            </div>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> গুগল ম্যাপস থেকে আপনার অবস্থানের লিংক কপি করে এখানে পেস্ট করুন
            </small>
        </div>


        <!-- Courier Option Section - Only show when outside Dhaka -->
        <div v-if="outsideDhakaMode">
            <fieldset style="border: 2px solid #28a745; padding: 15px; border-radius: 8px; margin: 15px 0;">
                <legend style="font-weight: 600; color: #28a745; padding: 0 10px;">কুরিয়ার সেবা নির্বাচন</legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="edit_sundorbon_courier"
                                name="courier_option" v-model="address.courier_option" value="Sundorbon Courier">
                            <label class="form-check-label" for="edit_sundorbon_courier">
                                <strong>Sundorbon Courier</strong><br>
                                <small class="text-muted">সুন্দরবন কুরিয়ার - ২-৩ দিন</small>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="edit_sa_paribahan" name="courier_option"
                                v-model="address.courier_option" value="SA Paribahan">
                            <label class="form-check-label" for="edit_sa_paribahan">
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
        </div>

        <div v-if="zip_code_enabled" class="mb-3">
            <label class="form-label">জিপ কোড</label>
            <input type="text" class="form-control customer-address-zip-code" v-model="address.zip_code" name="zip_code"
                placeholder="জিপ কোড লিখুন" />
        </div>
    </ec-modal>
</template>

<script>
export default {
    props: {
        customer: {
            type: Object,
            default: {},
        },
        address: {
            type: Object,
            default: {},
        },
        zip_code_enabled: {
            type: Number,
            default: 0,
        },
        use_location_data: {
            type: Number,
            default: 0,
        },
        is_inside_of_dhaka: {
            type: String,
            default: '',
        },
        inside_dhaka: {
            type: String,
            default: '',
        },
        is_out_side_dhaka: {
            type: [Number, String, Boolean],
            default: false,
        },
    },

    data: function () {
        return {
            countries: [],
            states: [],
            cities: [],
            thanas: [],
            areas: [],
            outsideDhakaMode: false,
        }
    },

    mounted: function () {
        this.loadCountries()
        this.loadThanas()

        // Initialize outside Dhaka mode from props or address data
        // If is_inside_of_dhaka has a value, then it's NOT outside Dhaka
        // If is_out_side_dhaka is 1, then it IS outside Dhaka
        let isOutside = false

        if (this.address.is_out_side_dhaka !== undefined && this.address.is_out_side_dhaka !== null) {
            isOutside = Boolean(parseInt(this.address.is_out_side_dhaka) === 1)
        } else if (this.is_out_side_dhaka !== undefined && this.is_out_side_dhaka !== null && this.is_out_side_dhaka !== false) {
            isOutside = Boolean(parseInt(this.is_out_side_dhaka) === 1)
        } else if (this.address.is_inside_of_dhaka || this.is_inside_of_dhaka) {
            // If inside_of_dhaka has value, then it's inside Dhaka (not outside)
            isOutside = false
        }

        this.outsideDhakaMode = isOutside
        this.address.is_out_side_dhaka = this.outsideDhakaMode ? 1 : 0

        console.log("is_out_side_dhaka prop:", this.is_out_side_dhaka)
        console.log("address.is_out_side_dhaka:", this.address.is_out_side_dhaka)
        console.log("outsideDhakaMode:", this.outsideDhakaMode)

        console.log("this.is_inside_of_dhaka", this.is_inside_of_dhaka)
        this.address.is_inside_of_dhaka = this.is_inside_of_dhaka;
        console.log("this.address.is_inside_of_dhaka", this.address.is_inside_of_dhaka)
        if (this.inside_dhaka) {
            this.address.inside_dhaka = /^\d+$/.test(this.inside_dhaka) ? parseInt(this.inside_dhaka) : this.inside_dhaka;
        }
    },
    methods: {

        loadThanas: function () {
            axios
                .get(route('admin.ajax.thanas'))
                .then((res) => {
                    this.thanas = res.data.data
                })
                .catch((error) => {
                    Botble.handleError(error.response.data)
                })
        },

        loadAreas: function (event) {
            if (event.type === 'change') {
                this.address.inside_dhaka = null;
            }
            const selectedThana = event.target.value
            if (selectedThana) {
                axios
                    .get(route('admin.ajax.areas', { thana_id: selectedThana }))
                    .then((res) => {
                        this.areas = res.data.data
                    })
                    .catch((error) => {
                        Botble.handleError(error.response.data)
                    })

                console.log(selectedThana)
            } else {
                this.areas = []
            }
        },
        shownEditAddress: function ($event) {
            this.loadCountries($event)

            // Initialize outside Dhaka mode based on address data
            // Priority: is_out_side_dhaka > is_inside_of_dhaka
            let isOutside = false

            if (this.address.is_out_side_dhaka !== undefined && this.address.is_out_side_dhaka !== null) {
                isOutside = Boolean(parseInt(this.address.is_out_side_dhaka) === 1)
            } else if (this.is_out_side_dhaka !== undefined && this.is_out_side_dhaka !== null && this.is_out_side_dhaka !== false) {
                isOutside = Boolean(parseInt(this.is_out_side_dhaka) === 1)
            } else if (this.address.is_inside_of_dhaka || this.is_inside_of_dhaka) {
                // If inside_of_dhaka has value, then it's inside Dhaka (not outside)
                isOutside = false
            }

            this.outsideDhakaMode = isOutside

            console.log("shownEditAddress - is_out_side_dhaka:", this.address.is_out_side_dhaka || this.is_out_side_dhaka)
            console.log("shownEditAddress - is_inside_of_dhaka:", this.address.is_inside_of_dhaka || this.is_inside_of_dhaka)
            console.log("shownEditAddress - outsideDhakaMode:", this.outsideDhakaMode)

            if (this.address.country) {
                this.loadStates($event, this.address.country)
            }

            if (this.address.state) {
                this.loadCities($event, this.address.state)
            }
            if (this.is_inside_of_dhaka) {
                this.loadAreas({ target: { value: this.is_inside_of_dhaka } })
            }
        },

        toggleAddressMode: function () {
            // Update the address object
            this.address.is_out_side_dhaka = this.outsideDhakaMode ? 1 : 0

            console.log("toggleAddressMode - outsideDhakaMode:", this.outsideDhakaMode)
            console.log("toggleAddressMode - is_out_side_dhaka:", this.address.is_out_side_dhaka)

            if (this.outsideDhakaMode) {
                // Clear Dhaka-specific fields when switching to outside
                this.address.is_inside_of_dhaka = null
                this.address.inside_dhaka = null
                this.areas = []

                console.log("Switched to outside Dhaka - cleared inside fields")
            } else {
                // Clear state/city and courier when switching to inside Dhaka
                this.address.state = null
                this.address.city = null
                this.address.courier_option = null
                this.states = []
                this.cities = []

                console.log("Switched to inside Dhaka - cleared outside fields")
            }
            this.$emit('address-changed', this.address);
        },
        loadCountries: function () {
            let context = this
            if (_.isEmpty(context.countries)) {
                axios
                    .get(route('ajax.countries.list'))
                    .then((res) => {
                        context.countries = res.data.data
                    })
                    .catch((res) => {
                        Botble.handleError(res.response.data)
                    })
            }
        },
        loadStates: function ($event, country_id) {
            if (!this.use_location_data) {
                return false
            }

            let context = this
            axios
                .get(route('ajax.states-by-country', { country_id: country_id || $event.target.value }))
                .then((res) => {
                    context.states = res.data.data
                })
                .catch((res) => {
                    Botble.handleError(res.response.data)
                })
        },
        loadCities: function ($event, state_id) {
            if (!this.use_location_data) {
                return false
            }

            let context = this
            axios
                .get(route('ajax.cities-by-state', { state_id: state_id || $event.target.value }))
                .then((res) => {
                    context.cities = res.data.data
                })
                .catch((res) => {
                    Botble.handleError(res.response.data)
                })
        },

        openGoogleMaps: function () {
            window.open('https://maps.google.com', '_blank');
        },
    },
    watch: {
        address: {
            handler: function (newAddress) {
                // Update outside Dhaka mode when address changes
                // Handle both string and integer values properly
                const isOutside = this.address.is_out_side_dhaka
                this.outsideDhakaMode = Boolean(parseInt(isOutside) === 1)

                if (this.address.country) {
                    this.loadStates(null, this.address.country)
                }

                if (this.address.state) {
                    this.loadCities(null, this.address.state)
                }
                this.$emit('address-changed', newAddress);
            },
            deep: true
        },
        is_inside_of_dhaka: function () {
            if (this.is_inside_of_dhaka) {
                this.loadAreas({ target: { value: this.is_inside_of_dhaka } })
            }
        },
    },
}
</script>
