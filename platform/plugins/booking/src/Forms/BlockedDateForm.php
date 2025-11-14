<?php

namespace Botble\Booking\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Booking\Models\BlockedDate;
use Botble\Booking\Http\Requests\BlockedDateRequest;

class BlockedDateForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(BlockedDate::class)
            ->setValidatorClass(BlockedDateRequest::class)
            ->add('date', DatePickerField::class, [
                'label' => 'Closed Date',
                'attr'  => ['required' => true],
            ]);
    }
}
