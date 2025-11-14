@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div id="main-order-content">
        <div class="row row-cards">
            <div class="col-md-12">
                <x-core::card class="mb-3">
                    <x-core::card.header>
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Inside Dhaka Areas</h3>
                            <div class="d-flex gap-2">
                                <a href="{{ route('global-option.edit', 7) }}" class="btn btn-warning">
                                    <i class="ti ti-plus"></i> Manage Thana Options
                                </a>
                                <a href="{{ route('orders.createInsideOfDhaka') }}" class="btn btn-primary">
                                    <i class="ti ti-plus"></i> Create New Area
                                </a>
                                <!-- Added button as requested -->
                                <a href="http://sobkisobazar.test/admin/ecommerce/options/edit/7" class="btn btn-info">
                                    <i class="ti ti-settings"></i> Additional Options
                                </a>
                            </div>
                        </div>
                    </x-core::card.header>

                    <x-core::card.body>
                        <div class="table-wrapper">
                            {!! $datatable->renderTable() !!}
                        </div>
                    </x-core::card.body>
                </x-core::card>
            </div>
        </div>
    </div>
@endsection
