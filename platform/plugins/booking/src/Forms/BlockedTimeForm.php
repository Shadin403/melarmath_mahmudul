<?php

namespace Botble\Booking\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Base\Forms\Fields\TimePickerField;
use Botble\Booking\Models\BlockedTime;
use Botble\Booking\Http\Requests\BlockedTimeRequest;

class BlockedTimeForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(BlockedTime::class)
            ->setValidatorClass(BlockedTimeRequest::class)
            ->add('date', DatePickerField::class, [
                'label' => 'Date',
                'attr'  => ['required' => true],
            ])
            ->add('start_time', TimePickerField::class, [
                'label' => 'Start time',
                'attr'  => ['required' => true],
            ])
            ->add('end_time', TimePickerField::class, [
                'label' => 'End time',
                'attr'  => ['required' => true],
            ]);
    }
}
