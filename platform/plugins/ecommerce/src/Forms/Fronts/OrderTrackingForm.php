<?php

namespace Botble\Ecommerce\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Http\Requests\Fronts\OrderTrackingRequest;
use Botble\Theme\FormFront;

class OrderTrackingForm extends FormFront
{
    public function setup(): void
    {
        $request = request();

        // Get parameters from standard request (these will be empty when # is used in URL)
        $orderId = $request->input('order_id');
        $email = $request->input('email');
        $phone = $request->input('phone');

        // Clean the order ID if it exists (when URL doesn't contain #)
        if ($orderId) {
            $orderId = urldecode($orderId);
            $orderId = ltrim($orderId, '#');
        }

        $this
            ->contentOnly()
            ->setMethod('GET')
            ->setValidatorClass(OrderTrackingRequest::class)
            ->setUrl(route('public.orders.tracking'))
            ->add(
                'order_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(__('Order ID'))
                    ->required()
                    ->placeholder(__('Enter the order ID'))
                    ->value($orderId) // Pre-fill with URL parameter if it exists
            )
            ->when(EcommerceHelper::isLoginUsingPhone(), function (FormAbstract $form) use ($phone): void {
                $form->add(
                    'phone',
                    'tel',
                    TextFieldOption::make()
                        ->label(__('Phone number'))
                        ->placeholder(__('Enter your phone number'))
                        ->required()
                        ->value($phone)
                );
            }, function (FormAbstract $form) use ($email): void {
                $form->add(
                    'email',
                    TextField::class,
                    TextFieldOption::make()
                        ->label(__('Email'))
                        ->required()
                        ->placeholder(__('Enter your email'))
                        ->value($email)
                );
            })
            ->add('submit', 'button', [
                'label' => __('Track'),
                'attr' => [
                    'type' => 'submit',
                    'class' => 'w-100 btn btn-primary',
                ],
            ]);
    }
}
