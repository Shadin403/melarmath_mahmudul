<?php

namespace Botble\Booking\Enums;

use Botble\Base\Supports\Enum;
use Html;

class ReservationStatusEnum extends Enum
{
    public const PENDING   = 'pending';
    public const CONFIRMED = 'confirmed';
    public const CANCELED  = 'canceled';

    public static $langPath = 'plugins/booking::booking';

    public function toHtml(): string
    {
        return match ($this->value) {
            self::PENDING   => Html::tag('span', 'Pending',   ['class' => 'badge bg-warning'])->toHtml(),
            self::CONFIRMED => Html::tag('span', 'Confirmed', ['class' => 'badge bg-success'])->toHtml(),
            self::CANCELED  => Html::tag('span', 'Canceled',  ['class' => 'badge bg-danger'])->toHtml(),
        };
    }
}
