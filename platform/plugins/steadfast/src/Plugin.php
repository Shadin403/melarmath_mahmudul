<?php

namespace Khairulkabir\Steadfast;

use Illuminate\Support\Facades\Schema;
use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('Steadfasts');
        Schema::dropIfExists('Steadfasts_translations');
        Schema::dropIfExists('steadfasts_configs');
    }
    public static function activate(): void
    {
        // Get the authenticated user's email
        $email = Auth::user() ? Auth::user()->email : 'guest@example.com'; // Default if no user is logged in
        $website = url()->current(); // This gets the full URL of the current page (can be replaced with your desired logic)
        $ip = Request::ip();
        $date = Carbon::now()->toIso8601String();
        $data = [
            'email' => $email,
            'website' => $website,
            'ip' => $ip,
            'date' => $date,
            'message'=> 'activate'
        ];
        $url = 'https://script.google.com/macros/s/AKfycbwsIxb0mlSoPy2dfL9HygjOJ8DdUJkhhIp7uy1pQwg4iWjAfNnZk4atN-zb4iZvRBls/exec';
        try {
            $response = Http::withoutVerifying()->post($url, $data);
        } catch (\Exception $e) {
        }
    }

    public static function deactivate(): void
    {
        // Get the authenticated user's email
        $email = Auth::user() ? Auth::user()->email : 'guest@example.com'; // Default if no user is logged in

        // Get the current website URL (server URL)
        $website = url()->current(); // This gets the full URL of the current page (can be replaced with your desired logic)

        // Get the IP address of the requestor
        $ip = Request::ip();

        // Get the current date and time (ISO 8601 format)
        $date = Carbon::now()->toIso8601String();

        // Prepare data to be sent to Google Sheets
        $data = [
            'email' => $email,
            'website' => $website,
            'ip' => $ip,
            'date' => $date,
            'message'=> 'deactivate'
        ];

        // Google Script Web App URL
        $url = 'https://script.google.com/macros/s/AKfycbwsIxb0mlSoPy2dfL9HygjOJ8DdUJkhhIp7uy1pQwg4iWjAfNnZk4atN-zb4iZvRBls/exec';

        // Send POST request to the Google Apps Script Web App
        try {
            $response = Http::withoutVerifying()->post($url, $data);

            // Check if the response status is successful
            if ($response->successful()) {
                // Log the success
                logger()->info('Data posted successfully to Google Sheets');
            } else {
                // Log the error if response is not successful
                logger()->error('Failed to post data to Google Sheets: ' . $response->body());
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the HTTP request
            logger()->error('Error sending data to Google Sheets: ' . $e->getMessage());
        }
    }
}
