<?php

namespace Khairulkabir\Steadfast\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Khairulkabir\Steadfast\Http\Requests\SteadfastRequest;
use Khairulkabir\Steadfast\Models\Steadfast;

class SteadfastForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Steadfast())
            ->setValidatorClass(SteadfastRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'choices' => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
