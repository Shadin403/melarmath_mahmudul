


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
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    Botble.showError('Something went wrong. Please try again later.')
                }
            });
}

            


function updateDeliveryStatus(orderId, buttonElement) {
    var button = $(buttonElement);
    var statusTextElement = $('#status-text-' + orderId);
        if (button.val() === "cancelled") {
            alert("The product was cancelled.");
        }

    statusTextElement.text('Checking...');
    button.find('i').addClass('fa-spin');

    // Make the AJAX request to update the delivery status
    $.ajax({
        url: '{{ route("steadfast.delivery-status") }}', 
        method: 'POST',
        data: {
            id: orderId,
            _token: '{{ csrf_token() }}' 
        },
        success: function(response) {
            if (response.status) {
                statusTextElement.text(response.delivery_status);
                location.reload();
            } else {
                // Show error in the status text
                statusTextElement.text('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('Something went wrong: ' + error);
            statusTextElement.text('Error updating status');
        },
        complete: function() {
            // Remove the loading spinner
            button.find('i').removeClass('fa-spin');
        }
    });
}



function showError(id, buttonElement) {
        // Send the order ID via AJAX POST request
        $.ajax({
            url: '{{ route("steadfast.show-error") }}', 
            method: 'POST',
            data: {
                id: id,
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
              
                openErrorModal( response.message );
            },
            error: function(xhr, status, error) {
                console.log('Something went wrong: ' + error);
            }
        });
    }

    function openErrorModal(errorMessage) {
    // Create a modal if it doesn't exist
    if ($('#errorModal').length == 0) {
        $('body').append(`
            <div id="errorModal" class="modal">
                <div class="modal-content">
                    <span class="close-button">&times;</span>
                    <div class="modal-body">
                        <pre class="error-message"></pre>
                    </div>
                </div>
            </div>
        `);
    }

    // If errorMessage is a valid JSON, pretty print it
    let formattedMessage;
    try {
        // Parse and pretty print the JSON if it's a valid JSON string
        const parsedJson = JSON.parse(errorMessage);
        formattedMessage = JSON.stringify(parsedJson, null, 4); // Pretty print with 4 spaces
    } catch (e) {
        // If not JSON, just display the error message as-is
        formattedMessage = errorMessage;
    }

    // Set the formatted error message in the modal
    $('.modal-body .error-message').text(formattedMessage);

    // Display the modal with animation
    $('#errorModal').fadeIn();

    // Close the modal when clicking on the close button or outside the modal
    $('.close-button').on('click', function () {
        $('#errorModal').fadeOut();
    });

    // Optional: Close the modal if clicking outside the content
    $(document).on('click', function (event) {
        if ($(event.target).is('#errorModal')) {
            $('#errorModal').fadeOut();
        }

       
    });
}

window.showError = showError;




// This makes sendAmount globally accessible
function sendAmount(id, buttonElement) {
    // Get the amount for the order
    var amount = $('#amount_' + id).val();
    var button = $(buttonElement); // Get the button element that was clicked

    // Add a loading icon and disable the button
    button.html('<i class="fa fa-spinner fa-spin"></i> Sending...').attr('disabled', true);

    // Send the order ID and amount via AJAX POST request
    $.ajax({
        url: '{{ route('steadfast.create-order') }}', // Your route for sending a single order
        method: 'POST',
        contentType: 'application/json', // Ensure we send JSON content
        data: JSON.stringify({
            ids: JSON.stringify([id]), // Encode the ID as a JSON array
            amounts: JSON.stringify([amount]), // Encode the amount as a JSON array
            _token: '{{ csrf_token() }}' // CSRF token for security
        }),
        success: function(response) {
            console.log(response);

            if (response.status) {
                // Change the button to success state after sending
                button.html('Success').removeClass('btn-primary').addClass('btn-success-to-steadfast').attr('disabled', true);
                location.reload(); // Optionally reload the page
            } else {
            
                button.html('Send').attr('disabled', false); // Re-enable the button
                location.reload();
            }
        },
        error: function(xhr, status, error) {
            console.log('Something went wrong: ' + error);
            // Re-enable the button and restore text
            button.html('Send').attr('disabled', false);
        }
    });
}

function showBulkChange(){


            var ids = [];
            $('input[name="id[]"]:checked').each(function() {
                ids.push($(this).val());
            });

            // Check if any orders are selected and a status is chosen
            if (ids.length === 0) {
                alert('Please select at least one order.');
                return;
            }



            $('.modal-bulk-changed-items').modal('show');
}




$(document).ready(function () {



    $('.table-check-all').on('change', function () {
        var isChecked = $(this).is(':checked'); // Check if "Select All" is checked
        $('input[name="id[]"]').prop('checked', isChecked); // Select or deselect all checkboxes
    });

    // Handle "Send to Steadfast" bulk action
    $('#bulkSendToSteadfast').on('click', function () {
        var ids = [];
        var amounts = [];

        // Loop through only selected checkboxes
        $('input[name="id[]"]:checked').each(function () {
            var id = $(this).val(); // Get the ID of the selected order
            var amount = $('#amount_' + id).val(); // Get the corresponding amount for the selected order
            ids.push(id); // Push the ID to the array
            amounts.push(amount); // Push the amount to the array
        });

        console.log(JSON.stringify(ids));
        console.log(JSON.stringify(amounts));

        // If no checkboxes are selected, show an alert and exit
        if (ids.length === 0) {
            alert('Please select at least one order to send to Steadfast.');
            return;
        }

        // Send the selected IDs and amounts via AJAX POST request
        $.ajax({
            url: '{{ route('steadfast.create-order') }}', // Your route for bulk orders
            method: 'POST',
            contentType: 'application/json', // Ensure we send JSON content
            data: JSON.stringify({
                ids: JSON.stringify(ids), // Encode the IDs as JSON array
                amounts: JSON.stringify(amounts), // Encode the amounts as JSON array
                _token: '{{ csrf_token() }}' // CSRF token for security
            }),
            success: function(response) {
                if (response.status) {
                    location.reload(); // Optionally reload the page
                } 
                location.reload();
            
            },
            error: function(xhr, status, error) {
                console.log('Something went wrong: ' + error);
            }
        });
    });

    $('#bulkCourierCheck').on('click', function () {
        var phoneNumbers = [];

        // Loop through only selected checkboxes and collect phone numbers
        $('input[name="id[]"]:checked').each(function () {
            var phone = $(this).data('phone'); // Get the phone number of the selected order
            if (phone) {
                phoneNumbers.push(phone); // Push the phone number to the array
            }
        });

        // Log the phone numbers for verification
        console.log("Selected phone numbers:", phoneNumbers);

        // If no checkboxes are selected, show an alert and exit
        if (phoneNumbers.length === 0) {
            alert('Please select at least one order for fraud check');
            return;
        }

        // Open the modal
        $('#searchModal').modal('show');

        // Populate the input field with selected phone numbers joined by a comma
        $('#searchInput').val(phoneNumbers.join(', '));

        // Set CMS and API checkbox values based on desired logic
        let cmsChecked = true; // Default CMS checked
        let apiChecked = true; // Default API unchecked

        // Apply checkbox states
        $('#checkBox1').prop('checked', cmsChecked);
        $('#checkBox2').prop('checked', apiChecked);

        // Fetch order data upon modal display
        fetchOrderData(phoneNumbers, cmsChecked ? 1 : 0, apiChecked ? 1 : 0);
    });




// Open modal when button is clicked
$('#openModalBtn').on('click', function () {
    $('#searchModal').modal('show');
});

        // Search input functionality (just for demonstration)
        $('#searchInput').on('input', function () {
            let searchTerm = $(this).val().toLowerCase();
            $('#resultsList li').each(function () {
                let listItem = $(this).text().toLowerCase();
                if (listItem.includes(searchTerm)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
});




function bulkprint(element) {

        
// Collect selected order IDs
var selectedOrderIds = [];

 // Check if the button has a data-type attribute and store its value in `type`
 var type = $(element).data('type') ? $(element).data('type') : null;




 $('input[name="id[]"]:checked').each(function () {
     selectedOrderIds.push($(this).val());
 });

 // Check if any orders are selected
 if (selectedOrderIds.length === 0) {
     alert('Please select at least one order for printing.');
     return;
 }

 // AJAX request to fetch and render bulk invoices
 $.ajax({
     url: '{{ route("steadfast.bulk-print-invoice") }}', // Update this to match your route name
     method: 'POST',
     contentType: 'application/json',
     data: JSON.stringify({
         ids: selectedOrderIds, // Pass the selected order IDs
         type: type,
         _token: '{{ csrf_token() }}' // CSRF token for security
     }),
     success: function (response) {
         if (response.status) {
             // Open the rendered HTML in a new print window
             var printWindow = window.open('', '_blank');
             printWindow.document.open();
             printWindow.document.write(response.html); // Inject HTML from the response
             printWindow.document.close();
             printWindow.print(); // Trigger print dialog

             
             // Check if type is "label" and apply custom styles for 10x15 cm paper
             if (type === "label") {
                    var style = `
                        <style>
                            @page {
                                size: 10cm 15cm;
                                margin: 0;
                            }
                            body {
                                margin: 0;
                                padding: 0;
                            }
                        </style>
                    `;
                    printWindow.document.head.insertAdjacentHTML('beforeend', style);
                }

                printWindow.print(); // Trigger print dialog
         } else {
             // Display an error message if the request was unsuccessful
             alert(response.message);
         }
     },
     error: function (xhr, status, error) {
         console.error('Error during AJAX request:', error);
         alert('Failed to fetch invoices for printing. Please try again.');
     }
 });

}




function courierCheck(number){
    $('#searchModal').modal('show');
    let phoneNumber = number;
    let cmsChecked = 1;
    let apiChecked = 0;


    // Set the phone number in the input field
    $('#searchInput').val(phoneNumber);

    // Set the CMS checkbox as checked
    $('#checkBox1').prop('checked', true);


   
    fetchOrderData(phoneNumber, cmsChecked, apiChecked);

}



// Function to show the loading icon while making the AJAX request
function showLoadingIcon() {
    $('.result-section').html(`
        <div class="loading-spinner text-center">
           
        </div>
    `);
}

// Function to hide the loading icon
function hideLoadingIcon() {
    $('.loading-spinner').remove();
}

// Function to calculate and update the progress bar
function updateProgressBar(cmsSuccessRatio, apiSuccessRatio, phoneNumber) {
    const averageSuccessRatio = (cmsSuccessRatio + apiSuccessRatio) / 2;
    const cancelRatio = 100 - averageSuccessRatio;

    const progressBarSuccess = $(`#rsResult-${phoneNumber} .progress-bar.bg-success`);
    const progressBarCancel = $(`#rsResult-${phoneNumber} .progress-bar.bg-danger`);

    // Update the success progress bar
    progressBarSuccess.css('width', `${averageSuccessRatio}%`);
    progressBarSuccess.attr('aria-valuenow', averageSuccessRatio);
    progressBarSuccess.text(`${averageSuccessRatio.toFixed(2)}% success`);

    // Update the cancel progress bar
    progressBarCancel.css('width', `${cancelRatio}%`);
    progressBarCancel.attr('aria-valuenow', cancelRatio);
    progressBarCancel.text(`${cancelRatio.toFixed(2)}% Cancel`);

    // Make the progress bar visible
    $(`#rsResult-${phoneNumber} .progress`).show();
}

function populateResults(data) {
    $('.result-section').empty(); // Clear the result section

    data.forEach(phoneData => {
        const phoneNumber = phoneData.phone;
        const phoneDataContent = phoneData.data;

        // Create a result container for each phone number
        let resultHtml = `
            <div class="p-3" id="rsResult-${phoneNumber}" style="background: lightgray; border-radius: 20px; border: solid 1px gray; display:block;">
                <div class="px-2">
                    <h3 class="d-flex">Phone Number: <span class="px-2">${phoneNumber}</span></h3>
                </div>`;

        // Show CMS Section if CMS data exists
        if (phoneDataContent.cms) {
            let cmsHtml = `
                <div class="p-2 pb-3 cms-section" style="background: white; border-radius: 20px; border: solid 1px gray;">
                    <h3 class="p-0 m-0">From CMS</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Courier Name</th>
                                <th>Total</th>
                                <th>Success</th>
                                <th>Cancel</th>
                            </tr>
                        </thead>
                        <tbody>`;

            phoneDataContent.cms.orders.forEach(order => {
                cmsHtml += `<tr>
                                <td>${order.shipping_company_name}</td>
                                <td>${order.total_orders}</td>
                                <td>${order.completed_orders}</td>
                                <td>${order.canceled_orders}</td>
                            </tr>`;
            });

            cmsHtml += `</tbody></table>
                        <div class="d-flex justify-content-between">
                            <span class="badge bg-secondary px-3 py-2" style="color: white;">Total parcels: ${phoneDataContent.cms.overall_stats.overall_total_orders}</span>
                            <span class="badge bg-success px-3 py-2" style="color: white;">Success parcels: ${phoneDataContent.cms.overall_stats.overall_completed_orders}</span>
                            <span class="badge bg-danger px-3 py-2" style="color: white;">Cancel parcels: ${phoneDataContent.cms.overall_stats.overall_canceled_orders}</span>
                        </div>
                </div>`;

            resultHtml += cmsHtml;
        }

        // Show API Section if API data exists
        if (phoneDataContent.api) {
            if (phoneDataContent.api.error) {
                // Show error message if there's an error in the API response
                let apiErrorHtml = `
                    <div class="p-2 mt-3 pb-3 api-section" style="background: white; border-radius: 20px; border: solid 1px gray;">
                        <h3 class="p-0 m-0">From API</h3>
                        <div class="alert alert-danger" role="alert">
                            ${phoneDataContent.api.error}
                        </div>
                    </div>`;
                
                resultHtml += apiErrorHtml;
            } else {
                // Display API data as a table if no error
                let apiHtml = `
                    <div class="p-2 mt-3 pb-3 api-section" style="background: white; border-radius: 20px; border: solid 1px gray;">
                        <h3 class="p-0 m-0">From API</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Courier Name</th>
                                    <th>Total</th>
                                    <th>Success</th>
                                    <th>Cancel</th>
                                </tr>
                            </thead>
                            <tbody>`;
                
                phoneDataContent.api.orders.forEach(order => {
                    apiHtml += `<tr>
                                    <td>${order.shipping_company_name}</td>
                                    <td>${order.total_orders}</td>
                                    <td>${order.completed_orders}</td>
                                    <td>${order.canceled_orders}</td>
                                </tr>`;
                });

                apiHtml += `</tbody></table>
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-secondary px-3 py-2" style="color: white;">Total parcels: ${phoneDataContent.api.overall_stats.overall_total_orders}</span>
                                <span class="badge bg-success px-3 py-2" style="color: white;">Success parcels: ${phoneDataContent.api.overall_stats.overall_completed_orders}</span>
                                <span class="badge bg-danger px-3 py-2" style="color: white;">Cancel parcels: ${phoneDataContent.api.overall_stats.overall_canceled_orders}</span>
                            </div>
                    </div>`;
                
                resultHtml += apiHtml;
            }
        }

        // Close resultHtml div and append to result section
        resultHtml += `</div>`;
        $('.result-section').append(resultHtml);
    });

    hideLoadingIcon();
}

function fetchOrderData(phoneNumber, cmsChecked, apiChecked) {
    showLoadingIcon();

    $.ajax({
        url: '{{route('steadfast.fake-order-check')}}',  // Your endpoint here
        method: 'get',
        data: {
            number: phoneNumber,
            cms: cmsChecked ? 1 : 0,  // Pass 1 if checked, else 0
            api: apiChecked ? 1 : 0   // Pass 1 if checked, else 0
        },
        success: function(response) {
            if (response.status) {
                populateResults(response.data);
            } else {
                alert('Error fetching data');
                hideLoadingIcon();
            }
        },
        error: function(jqXHR, textStatus, errorThrown) {
        let errorMsg = '';

        // Check if the server returned a JSON response with error details
        if (jqXHR.responseJSON && jqXHR.responseJSON.message) {
            errorMsg = jqXHR.responseJSON.message;
        } else if (textStatus) {
            errorMsg = textStatus;
        } else {
            errorMsg = 'An unexpected error occurred.';
        }

        alert('Failed to fetch data: ' + errorMsg + ' (Error: ' + errorThrown + ')');
        hideLoadingIcon();
    }
    });
}

$('#searchModal').on('hidden.bs.modal', function () {
    $('#searchInput').val(''); // Clear the input field value
    $('#checkBox1').prop('checked', false); // Uncheck the CMS checkbox
    $('#checkBox2').prop('checked', false); // Uncheck the API checkbox
    $('.result-section').empty();  // Clear all result data
});


// Handle search and checkbox functionality
$('#searchBtn').on('click', function() {
    let phoneNumber = $('#searchInput').val();
    let cmsChecked = $('#checkBox1').is(':checked');
    let apiChecked = $('#checkBox2').is(':checked');

    // If neither checkbox is checked, show an alert
    if (!cmsChecked && !apiChecked) {
        alert('Please select at least one option (CMS or API).');
        return;  // Exit the function to prevent API call
    }

    fetchOrderData(phoneNumber, cmsChecked, apiChecked);
});

   



   



</script>


<script>
 $('#balanceButton').on('click', function () {
    const button = $(this);
    const balanceAmount = $('#balanceAmount');
    balanceAmount.text('');
    // Add loading class for animation
    button.addClass('loading');

    // Perform AJAX request
    $.ajax({
      url: '{{route('steadfast.check-amount')}}', // Replace with the actual route to your controller method
      type: 'get',
      dataType: 'json',
      success: function (response) {
        // Check for successful response
       
        if (response.status === 200) {
          // Update balance amount and add loaded class to show it
          balanceAmount.text(response.current_balance);
          button.removeClass('loading').addClass('balance-loaded');

          setTimeout(function() {
             button.removeClass('balance-loaded');
           }, 5000);
        } else {

          Botble.showError(response.message)
          button.removeClass('loading');
        }
      },
      error: function (xhr, status, error) {

        Botble.showError('Error retrieving balance: ' + error)
        button.removeClass('loading');
      }
    });
  });
</script>



<script>
function showDropdown(button) {
    // Prevent the main dropdown from closing when clicking the submenu
    event.stopPropagation(); // Stops the click event from propagating to parent elements

    // Use jQuery to select the sibling .dropdown-menu and show it
    $(button).siblings('.dropdown-menu').show(); // This will set the display property to 'block'
}

         // When the reLoad button is clicked
    $('#reLoadButton').on('click', function() {
        location.reload(); // Reload the current page
    });

</script>