<?php

namespace Khairulkabir\Steadfast\Forms\Settings;

use Khairulkabir\Steadfast\Models\SteadfastsConfig;
use Khairulkabir\Steadfast\Http\Requests\Settings\SteadfastRequest;
use Botble\Setting\Forms\SettingForm;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\MediaImageField; // For logo upload
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;

class SteadfastForm extends SettingForm
{
    public function buildForm(): void
    {
        parent::buildForm();

        // Retrieve the config, or create a new one if it doesn't exist
        $config = SteadfastsConfig::firstOrNew();

        $this
            ->setupModel($config)
            ->setSectionTitle('Steadfast API Settings')
            ->setSectionDescription(trans('plugins/steadfast::steadfast.details'))
            ->setValidatorClass(SteadfastRequest::class)

            ->add('client_name', 
            TextField::class, 
            TextFieldOption::make()
                ->label(__('client_name'))
                ->value($config->client_name)
               
        )

            ->add('license_code', 
                TextField::class, 
                TextFieldOption::make()
                    ->label(__('License Code'))
                    ->value($config->license_code)
                   
            )

            // Add Enable/Disable flag
            ->add('enable_disable', 
                OnOffCheckboxField::class, 
                CheckboxFieldOption::make()
                    ->label(__('Enable/Disable'))
                    ->value($config->enable_disable)
                   
            )
           

            // Add Notes checkbox
            ->add('notes', 
                OnOffCheckboxField::class, 
                CheckboxFieldOption::make()
                    ->label(__('Notes'))
                    ->value($config->notes)
                    
            )



            // Add API Key field
            ->add('api_key', 
                TextField::class, 
                TextFieldOption::make()
                    ->label(__('API Key'))
                    ->value($config->api_key)
            )

            // Add Secret Key field
            ->add('secret_key', 
                TextField::class, 
                TextFieldOption::make()
                    ->label(__('Secret Key'))
                    ->value($config->secret_key)
            )

            // Add Notes checkbox
            ->add('use_as_invoice_info', 
                OnOffCheckboxField::class, 
                CheckboxFieldOption::make()
                    ->label(__('Use (This Business Info)  As Invoice Info'))
                    ->value($config->use_as_invoice_info)
            )


            
            // Add Business Name field
            ->add('business_name', 
                TextField::class, 
                TextFieldOption::make()
                    ->label(__('Business Name'))
                    ->value($config->business_name)
            )

            // Add Business Address field
            ->add('business_address', 
                TextField::class, 
                TextFieldOption::make()
                    ->label(__('Business Address'))
                    ->value($config->business_address)
            )

            // Add Business Email field
            ->add('business_email', 
                TextField::class, 
                TextFieldOption::make()
                    ->label(__('Business Email'))
                    ->value($config->business_email)
            )

            // Add Business Number field
            ->add('business_number', 
                TextField::class, 
                TextFieldOption::make()
                    ->label(__('Business Number'))
                    ->value($config->business_number)
            )

           
            ->add('business_logo', 
                MediaImageField::class, 
                TextFieldOption::make()
                    ->label(__('Business Logo'))
                    ->value($config->business_logo)
            )

                // Add Terms & Conditions field
                ->add('terms_conditions', 
                TextField::class, 
                TextFieldOption::make()
                    ->label(__('Terms & Conditions'))
                    ->value($config->terms_conditions)
                )

             // Add Terms & Conditions field
             ->add('callback_url', 
             TextField::class, 
             TextFieldOption::make()
                 ->label(__('callback_url'))
                 ->value(route('steadfast.webhook'))
                 ->disabled()
             )


                 // Add Terms & Conditions field
             ->add('auth_token', 
             TextField::class, 
             TextFieldOption::make()
                 ->label(__('auth_token'))
                 ->value($config->auth_token)
                 ->helperText('
                     <button type="button" id="generateAuthToken" class="btn btn-secondary" style="margin-left: 5px;">Generate</button>
                     <script>
                         document.getElementById("generateAuthToken").addEventListener("click", function() {
                             var authTokenInput = document.getElementById("auth_token");
                             authTokenInput.value = generateToken(42);
                         });
                         
                         function generateToken(length) {
                             var characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
                             var token = "";
                             for (var i = 0; i < length; i++) {
                                 token += characters.charAt(Math.floor(Math.random() * characters.length));
                             }
                             return token;
                         }
                     </script>
                 ')
                 )

         
             // Add Notes checkbox
             ->add('bdcourier_API', 
             OnOffCheckboxField::class, 
             CheckboxFieldOption::make()
                 ->label(__('Enable BD Courier API'))
                 ->value($config->bdcourier_API)
         )


         
         // Add Business Name field
         ->add('bdcourier_auth', 
             TextField::class, 
             TextFieldOption::make()
                 ->label(__('bdcourier_auth_token'))
                 ->value($config->bdcourier_auth)
    );

            
         
    }
}
