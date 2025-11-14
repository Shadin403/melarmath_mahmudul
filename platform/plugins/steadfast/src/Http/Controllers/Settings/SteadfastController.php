<?php

namespace Khairulkabir\Steadfast\Http\Controllers\Settings;

use Botble\Base\Forms\FormBuilder;
use Khairulkabir\Steadfast\Forms\Settings\SteadfastForm;
use Khairulkabir\Steadfast\Http\Requests\Settings\SteadfastRequest;
use Botble\Setting\Http\Controllers\SettingController;
use Khairulkabir\Steadfast\Models\SteadfastsConfig;
use Khairulkabir\Steadfast\Services\SteadfastApiServices;

class SteadfastController extends SettingController
{
    public function edit(FormBuilder $formBuilder)
    {
        $this->pageTitle('Steadfast Configurations');

        return $formBuilder->create(SteadfastForm::class)->renderForm();
    }

    public function update(SteadfastRequest $request)
    {
        $validatedData = $request->validated();

        $message= "update successfully";

        // Check if enable_disable is set to 1 and validate license fields

        if ($validatedData['enable_disable'] == 1) {
            if (empty($validatedData['license_code']) || empty($validatedData['client_name'])) {
                return redirect()->back()->withErrors('Enable API requires both License File and Client Name to be set.');
            }
            // Attempt license verification
            $apiService = new SteadfastApiServices();
            $response = $apiService->verify_license();

            if (!$response['status']) {
                return redirect()->back()->withErrors($response['message']);
            }
        
        }

        $config = SteadfastsConfig::firstOrNew(['id' => 1]); // Assuming there's only one config row


        $config->fill($validatedData)->save();

        session()->flash('success', $message);
       return redirect()->back();
    }
}
