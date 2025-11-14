
<style>

    /* Modal Styles */
    #errorModal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        display: flex; /* Use flexbox for centering */
        align-items: center; /* Center vertically */
        justify-content: center; /* Center horizontally */
        animation: fadeIn 0.5s; /* Animation for showing modal */
    }

    /* Modal Content Box */
    #errorModal .modal-content {
        background-color: #fefefe;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 800px; /* Set a maximum width */
        max-height: 70%; /* Set a maximum height */
        overflow-y: auto; /* Enable vertical scroll if needed */
        overflow-x: auto; /* Enable horizontal scroll */
        border-radius: 8px; /* Add rounded corners */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Add a shadow effect */
    }

    /* Close Button */
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    #errorModal .pre {
        background-color: #1e1e1e; /* Black background */
        color: #d4d4d4; /* Light text for contrast */
        padding: 10px;
        border-radius: 5px;
        font-size: 14px;
        overflow-y: auto; /* Enable horizontal scrolling if needed */
    }

    /* Keyframes for fadeIn effect */
    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }

    .search-input {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }

    #searchInput {
        width: 100%;
        padding: 10px 15px;
        border-radius: 32px;
        border: 1px solid #ccc;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    #searchInput:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.25);
        outline: none;
    }

    .search-btn {
        background-color: #007bff;
        border: none;
        border-radius: 50%;
        padding: 10px;
        margin-left: 10px;
        width: 45px;
        height: 45px;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.3s ease;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .search-btn i {
        color: #fff;
        font-size: 18px;
    }

    .search-btn:hover {
        background-color: #0056b3;
        transform: scale(1.1);
    }

    .search-btn:focus {
        outline: none;
    }


    .form-check {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-bottom: 15px;
        position: relative;
        cursor: pointer;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
    }

    .form-check:hover {
        background-color: #e9ecef;
        border-color: #ced4da;
    }

    .form-check-input {
        display: none; /* Hide default checkbox */
    }

    .form-check-icon {
        width: 24px;
        height: 24px;
        display: flex;
        justify-content: center;
        align-items: center;
        border: 2px solid #ddd;
        border-radius: 5px;
        margin-right: 10px;
        transition: all 0.3s ease;
    }

    .form-check-icon i {
        display: none;
        color: #0d6efd;
    }

    .form-check-input:checked ~ .form-check-icon {
        border-color: #0d6efd;
        background-color: #e9ecef;
    }

    .form-check-input:checked ~ .form-check-icon i {
        display: inline;
    }

    .form-check-label {
        font-size: 1.1rem;
        font-weight: 500;
        color: #495057;
        margin: 0;
    }

.balance-button {
    position: relative;
    display: inline-flex;
    align-items: center;
    padding: 3px 3px;
    border-radius: 5px;
    background-color: #28a745;
    color: #ffffff;
    border: 2px solid #28a745;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    overflow: hidden;
    transition: background-color 0.3s ease;
    white-space: nowrap;
}


.balance-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    margin-right: 8px;
    padding: 8px 12px;
    font-weight: bold;
    transition: transform 0.5s ease, opacity 0.5s ease;
    opacity: 1;
    color: #28a745;
    border-radius: 5px;
    background: white;
    font-size: xx-large;
}

    .balance-text {
      transition: opacity 0.5s ease;
      font-size: small;
    }

    /* Loading animation */
    .balance-button.loading .balance-icon {
      animation: fadeInOut 1s infinite;
    }

    /* Keyframes for fade animation */
    @keyframes fadeInOut {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    /* Hide text and slide icon to cover the button */
    .balance-loaded .balance-text {
      opacity: 0;
    }

    .balance-loaded .balance-icon {
      transform: translateX(320%); /* Slide icon to cover the text */
      margin-right: 0;
    }

    /* Balance amount styling */
      /* Balance amount styling */
    .balance-amount {
        position: absolute;
        top: auto;
        left: 50%;
        transform: translateX(-150px);
        color: white;
        opacity: 0;
        transition: opacity 0.3s ease, transform 0.5s ease;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    
.symbole {
    position: relative;
    font-size: x-large;
    padding-right: 3px;
    top: -2px;
}
.dropdown-submenu .dropdown-menu {

    display: none; /* Initially hidden */
}
    


    /* Show balance amount */
    .balance-loaded .balance-amount {
      opacity: 1;
      transform: translateX(-50px);
    }
    
    @media (max-width: 400px) {
       .result-section .markdown>table>:not(caption)>*>*,.result-section .table>:not(caption)>*>* {
           padding: 5px;
        }
        
        .result-section .badge {
           letter-spacing: normal !important;
           padding-left: 0 !important;
           padding-right: 0 !important;
           white-space: normal !important;
           text-transform: none !important;
        }
    }


</style>
