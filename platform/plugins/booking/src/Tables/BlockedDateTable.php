<?php

namespace Botble\Booking\Tables;

use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\DateColumn;
use Botble\Booking\Models\BlockedDate;

class BlockedDateTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(BlockedDate::class)
            ->addColumns([
                IdColumn::make(),
                DateColumn::make('date')->title('Closed Date'),
                CreatedAtColumn::make(),
            ]);
    }
}
