<?php

namespace Botble\Booking\Forms;

use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Base\Forms\Fields\TimePickerField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\Booking\Http\Requests\BookingRequest;   
use Botble\Booking\Models\Booking;               

class BookingForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Booking::class)
            ->setValidatorClass(BookingRequest::class)

            ->add('name', TextField::class, [
                'label' => 'Name',
                'attr'  => ['required' => true],
            ])
            ->add('email', TextField::class, ['label' => 'Email'])
            ->add('phone', TextField::class, ['label' => 'Phone'])

            ->add('date', DatePickerField::class, [
                'label' => 'Date',
                'attr'  => ['required' => true],
            ])
            ->add('start_time', TimePickerField::class, [
                'label' => 'Start Time',
                'attr'  => ['required' => true],
            ])
            ->add('end_time', TimePickerField::class, [
                'label' => 'End Time',
                'attr'  => ['required' => true],
            ])

            ->add('status', SelectField::class, [
                'label'   => 'Status',
                'choices' => [
                    'pending'   => 'Pending',
                    'confirmed' => 'Confirmed',
                    'canceled'  => 'Canceled',
                ],
                'attr'    => ['required' => true],
            ])

            ->add('note', TextareaField::class, ['label' => 'Note'])
            ->setBreakFieldPoint('status');
    }
}
