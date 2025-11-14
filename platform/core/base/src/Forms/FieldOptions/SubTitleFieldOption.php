<?php

namespace Botble\Base\Forms\FieldOptions;

class SubTitleFieldOption extends TextFieldOption
{
    public static function make(): static
    {
        return parent::make()
            ->label('Sub Title')
            ->placeholder('Sub Title')
            ->maxLength(250);
    }
}
