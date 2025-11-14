@extends('core/base::layouts.master')

@section('content')

<!-- load css files -->
@include('plugins/steadfast::partial.css')

<!-- load the model -->
@include('plugins/steadfast::partial.modal')

@php
    $licenseCode = $config->license_code ?? null;
    $clientName = $config->client_name ?? null;
    $licenseFile = $config->license_file ?? null;
    $isConfigComplete = $licenseCode && $clientName && $licenseFile;
@endphp

@if (!$isConfigComplete)
    <!-- License Warning Section -->
    <div class="alert alert-warning" style="max-width: 850px;margin: auto; z-index: 1055;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="text-danger mb-0">Invalid License</h5>
            <button type="button" onclick="startTrial()" class="btn btn-outline-primary fw-bold" style="padding: 8px 16px; border-radius: 6px;">Start Free Trial</button>
        </div>

        <!-- License Information Section -->
        <div class="alert alert-info">
            <strong>License Information:</strong>
            <p>Please purchase a valid license to proceed. Follow the instructions below to process your license code:</p>
            <ol>
                <li>To purchase your license code, contact us via email at <a href="mailto:khairulkabir.dev@gmail.com">khairulkabir.dev@gmail.com</a> .</li>
                <li>Once you have received the license code, return here and click on the 'Go to License Settings' button to enter the settings page.</li>
                <li>On the Dashboard>Settings>SteadFast Settings, enter your <strong>username</strong> and the <strong>license code</strong> you received after purchasing.</li>
                <li>Click <strong>'Save'</strong> to apply your license and complete the configuration.</li>
            </ol>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-secondary" onclick="location.reload()">Reload Page</button>
            <!-- Button for navigating to the settings page -->
            <a href="{{ route('steadfast.steadfasts_configs') }}" class="btn btn-primary">Go to License Settings</a>
        </div>
    </div>
    
<script>
function startTrial(){
            $.ajax({
                url: "{{ route('steadfast.start-trial') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}' 
                },
                success: function (response) {
                   
                    if (response.status) {
                        Botble.showSuccess(response.message)
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    } else {
                        Botble.showError(response.message)
                        setTimeout(function () {
                            location.reload();
                        }, 3000);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    Botble.showError('Something went wrong. Please try again later.')
                }
            });
}
</script>
@else



<div class="table-wrapper">

    <div class="card has-actions has-filter">
        <style>
            #bulk_action{
                background: #FFF;
                color: #28a745;
                border-color: #28a745;
            }
            #bulk_action:hover{
                background: #28a745;
                color: #fff;
                border-color: #28a745;
            }
            #bulk_action:focus, #bulk_action:active {
                background: #28a745;
                color: #fff;
                border-color: #28a745;
            }
            .dropdown-menu .dropdown-item:hover{
                background-color: #d4edda;
            }
        </style>
        <div class="card-header">
            <div class="w-100 justify-content-between d-flex flex-wrap align-items-center gap-1">
                <div class="d-flex flex-wrap flex-md-nowrap align-items-center gap-1">
                    <div class="dropdown d-inline-block">
                        <button class="btn dropdown-toggle" id="bulk_action" type="button" data-bs-toggle="dropdown">
                            Bulk Actions
                        </button>
                        <div class="dropdown-menu">
                            <button class="dropdown-item" id="bulkSendToSteadfast" type="button">
                            <i class="pe-2 fas fa-shipping-fast"></i>  Send to Steadfast
                            </button>
                            <button class="dropdown-item" id="bulkCourierCheck" type="button">
                             <i class="pe-2 fa fa-eye"></i>  Fraud Check
                            </button>


                            <div class="dropdown-submenu">
                                <button class="dropdown-item justify-content-between">
                                    <div class="d-flex align-items-center" >
                                        <i class="pe-2 pt-0 pb-0 mt-0 mb-0 fa fa-print"></i>
                                        Bulk print
                                    </div>
                                    <i class="fa-solid fa-chevron-right pe-2"></i>
        
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" id="bulkprint" onclick="bulkprint(this)" type="button">
                                        <i class="pe-2 fa fa-print"></i>Invoice
                                    </button>
                                    <button class="dropdown-item" id="bulkprint" onclick="bulkprint(this)" data-type="label" type="button">
                                        <i class="pe-2 fa fa-print"></i> Label
                                    </button>
                                        
                                </div>
                            </div>


                            
                            
                        </div>
                    </div>

                    <div class="table-search-input">
                        <label>
                            <input type="search" class="form-control input-sm" placeholder="Search..." style="min-width: 120px">
                            <button type="button" title="Search" class="search-icon"><i class="fa fa-search"></i></button>
                            <button type="button" title="Clear" class="search-reset-icon" style="display: none;"><i class="fa fa-x"></i></button>
                        </label>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1" id="flaxbody">




                        <div class="balance-button" id="balanceButton">
                        <span class="balance-icon">৳</span>
                        <span class="balance-text">Check Balance</span>
                        <div class="balance-amount" ><span class="symbole" > ৳ </span><div id="balanceAmount">0</div> </div>
                        </div>




                    <button class="btn action-item btn-primary" id="openModalBtn" type="button" >
                        <span >
                            <i class="fa fa-eye"></i> Fraud Check
                    </button>

                    <button class="btn" id="reLoadButton">
                        <i class="fa fa-refresh"></i> <span> Reload</span>
                    </button>
                </div>
            </div>
        </div>

        <style>
            @media(max-width: 600px){
                #reLoadButton span {
                    display:none;
                    
                }
                #flaxbody{
                    display: flex;
                    justify-content: space-between;
                    width: 100%;
                }
                
                 #reLoadButton {
                   padding:12px 15px;
                   background: #28a745;
                 }
                
                #reLoadButton i {
                    padding: 0;
                    margin: 0;
                    color: #fff;
                }
            }
        
            .btn-send-to-steadfast {
                background-color: #d4edda;  /* Light green background */
                color: #155724;             /* Dark green text */
                border: 1px solid #c3e6cb;  /* Border to match the background */
                
                border-radius: 5px;
                cursor: pointer;
            }

            /* Styling for the Success button (second image) */
            .btn-success-to-steadfast {
                background-color: #28a745;  /* Darker green background */
                color: #fff;                /* White text */
                border: 1px solid #28a745;  /* Same color as background */
                
                border-radius: 5px;
                cursor: not-allowed;        /* Disable click (optional, for visual effect) */
            }
            .text-white{
                color: #fff !important;
            }


        </style>

        <div class="card-table">
            <div class="table-responsive table-has-actions table-has-filter">
                <table class="table card-table table-vcenter table-striped table-hover dataTable no-footer dtr-inline">
                    <thead>
                        <tr>
                            <th><input class="form-check-input m-0 align-middle table-check-all" style="display:block" name="all_checkboxes" type="checkbox"></th>
                            <th>Orders</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Fraud Check</th>
                            <th>Amount</th>
                            <th>Send to Stead Fast</th>
                            <th>Print</th>
                            <th>Consignment ID</th>
                            <th>Delivery Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>

                                <td  class="w-1 text-start no-column-visibility dtr-control">
                                    <input class="form-check-input m-0 align-middle checkboxes" style="display:block" type="checkbox" name="id[]" data-phone="{{ optional($item->shippingAddress)->phone ? optional($item->shippingAddress)->phone : '' }}" value="{{ $item->id }}">
                                </td>
                                <td> <a class="d-flex" href="{{route('orders.edit', $item->id)}}" > {{ $item->code }}<i style="padding-left: 10px;" class="pl-2 fa fa-external-link-alt"></i></a> </td>
                                <td>
                                    <div style="white-space: nowrap;">
                                    @php
                                        $createdAt = \Carbon\Carbon::parse($item->created_at);
                                        $now = \Carbon\Carbon::now();

                                        if ($createdAt->isToday()) {
                                            // Display as 'Today' with time (e.g., 'Today 12:00')
                                            $displayDate = 'Today ' . $createdAt->format('H:i');
                                        } elseif ($createdAt->isTomorrow()) {
                                            // Display as 'Tomorrow'
                                            $displayDate = 'Tomorrow';
                                        } else {
                                            // Display as full date (e.g., 'Aug 19, 2024')
                                            $displayDate = $createdAt->format('M d, Y');
                                        }
                                    @endphp

                                    {{ $displayDate }}
                                    </div>
                                    
                                </td>
                                <td  class="text-center column-key-4">
                                    @php
                                        $badgeClass = '';
                                        switch ($item->status) {
                                            case 'canceled':
                                                $badgeClass = 'badge bg-danger text-white';
                                                break;
                                            case 'pending':
                                                $badgeClass = 'badge bg-warning text-warning-fg text-white';
                                                break;
                                            case 'completed':
                                                $badgeClass = 'badge bg-success text-success-fg text-white';
                                                break;
                                            case 'processing':
                                                $badgeClass = 'badge bg-info text-success-fg text-white';
                                                break;                                                
                                            default:
                                                $badgeClass = 'bg-light text-dark'; // Default case if no status matches
                                                break;
                                        }
                                    @endphp

                                    <span class="badge {{ $badgeClass }}">
                                        {{ $item->status }}
                                    </span>
                                </td>

                              <td>


                              @if (optional($item->shippingAddress)->phone)
                                    <span class="status-icon" style="white-space: nowrap;">
                                     <div class="btn btn-secondary btn-success-to-steadfast" 
                                            style="border-radius: 10px;  border: solid 2px lightcyan; padding:8px 10px; display: inline-block; cursor: pointer;" 
                                            onclick="courierCheck('{{ optional($item->shippingAddress)->phone }}')">
                                            <i class="fa fa-eye p-0 m-0"></i>
                                        </div>
                                        <span class="status-text" id="status-text-50 " style="display: inline-block;">
                                            {{ optional($item->shippingAddress)->phone }}
                                        </span>
                                        
                                    </span>
                                @endif

                               
                                
                               
                              </td>

                                <td class="text-center"  style="min-width: min-content;width: 100%;"> 
                                    <input type="text" style="min-width: 100px;width: inherit;text-align: -webkit-center;" id="amount_{{ $item->id }}" class="form-control" style="wight" value="{{ isset($item->steadfast_amount) && !empty($item->steadfast_amount) ? number_format($item->steadfast_amount, 2) : number_format($item->amount, 2) }}">
                                </td>
                                <td class="text-center" >
                                    @if (!$item->steadfast_is_sent)
                                    <button onclick="sendAmount({{ $item->id }}, this)" class="btn btn-primary"> Send</button>
                                    @else
                                        <div class="btn btn-success-to-steadfast" type="button" disabled>Success</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    
<style>
    .custom-warning {
        background-color: #fff3cd; /* Light yellow background for warning */
        color: #856404; /* Dark color for text */
        border: 1px solid #ffeeba; /* Border with a slightly darker yellow */
        border-radius: 5px; /* Rounded corners */
        padding: 8px 12px; /* Padding for better spacing inside the warning box */
        /* Removed margin to fit inside the container smoothly */
    }
    .padding-right-5{
        padding-right: 8px;
    }
    .dropdown-menu {
        min-width: auto !important;
    } 
    .dropdown-item {
          min-width: auto !important;
    }
    
    td .dropdown button{
        background: #FFF;
        color: #28a745;
        border-color: #28a745;
        
    }
    
    td .dropdown-menu .dropdown-item:hover {
        background-color: #d4edda;
     
    }
    
    td .btn-check:checked+.btn, .btn.active, .btn.show, .btn:first-child:active, :not(.btn-check)+.btn:active, td .dropdown button:hover
    {
        background-color: #28a745;
        border-color: #28a745;
        color: #fff;
    }
    
</style>

@if($item->steadfast_is_sent)
<div class="dropdown d-inline-block">
    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="fa fa-print"></i> Print
    </button>
    <div class="dropdown-menu">
        @if($item->steadfast_is_sent)
            <!-- Print Invoice Option -->
            <a class="dropdown-item" target="_blank" href="{{ route('steadfast.print-invoice', $item->id) }}">
                <i class="fa fa-print padding-right-5"></i> Invoice
            </a>
        @else
			<div style="padding: 5px;">
              
            <!-- Custom Warning Message for Invoice -->
            <div class="dropdown-item custom-warning">
                Please send first to print invoice
            </div>
      </div>
        @endif

        @if($item->steadfast_is_sent)
            <!-- Print Label Option -->
            <a class="dropdown-item" target="_blank" href="{{ route('steadfast.print-invoice', ['id' => $item->id, 'type' => 'label']) }}">
                <i class="fa fa-print padding-right-5"></i> Label
            </a>
        @else
      <div style="padding: 5px;">
            <!-- Custom Warning Message for Label -->
            <div class="dropdown-item custom-warning">
                Please send first to print label
            </div>
      </div>
        @endif
    </div>
</div>
@else 
<div class=" text-center btn-send-to-steadfast py-2 px-2"></div>
@endif



                                    
                                    
                                    
                               

                                   
                                </td>
                                <td class="text-center" ><div class=" text-center btn-send-to-steadfast py-2 px-2" >{{ $item->steadfast_consignment_id ?: '' }}</div></td>
                                <td class="text-center" style="white-space: nowrap;" >
                                    <span class="status-icon">
                                        @if ($item->stdf_delivery_status == 'error')
                                            <button onclick="showError({{ $item->id }}, this)" class="btn btn-light" style="    border-color: red;color: black;background-color: lightcoral;">
                                                <i class="fa fa-exclamation-circle"></i> Show Error
                                            </button>
                                        @elseif ($item->steadfast_is_sent )
                                        <div class="btn" 
                                            style="border-radius: 10px; background: lightblue; border: solid 2px lightcyan;" 
                                            onclick="updateDeliveryStatus('{{ $item->id }}', this)">
                                            <i class="fa fa-refresh icon-loading p-0 m-0" style="cursor: pointer;"></i>
                                        </div>
                                        <span class="status-text" id="status-text-{{ $item->id }}">
                                            {{ $item->stdf_delivery_status ?: 'Unknown' }}
                                        </span>
                                        @else
                                            <span class="status-text">-</span>

                                        @endif
                                    </span>
                                </td>


                                <td class="text-center" style="white-space: nowrap;" >{{ number_format($item->amount, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="card-footer d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                    <div class="dataTables_length">
                        <form id="perPageForm" method="GET" action="{{ route('steadfast.index') }}">
                            <label>
                                <select name="perPage"  class="form-select form-select-sm" onchange="document.getElementById('perPageForm').submit()">
                                    <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="30" {{ request('perPage') == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ request('perPage') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                                    <option value="500" {{ request('perPage') == 500 ? 'selected' : '' }}>500</option>
                                    <option value="-1" {{ request('perPage') == -1 ? 'selected' : '' }}>All</option>
                                </select>
                            </label>
                        </form>
                    </div>

                        <div class="m-0 text-muted">
                            <div class="dataTables_info" role="status" aria-live="polite">
                                <span class="dt-length-records">
                                    <i class="icon svg-icon-ti-ti-world"></i>
                                    Showing 1 to {{ $data->count() }} of {{ $data->total() }} records
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



{{ $dataTable->scripts() }}
<!-- load the script -->
@include('plugins/steadfast::partial.script')

@endif


@endsection
