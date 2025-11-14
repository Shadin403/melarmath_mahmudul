<?php

namespace Botble\Booking\Tables;

use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\DateColumn;
use Botble\Table\Columns\TextColumn;
use Botble\Booking\Models\BlockedTime;

class BlockedTimeTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(BlockedTime::class)
            ->addColumns([
                IdColumn::make(),
                DateColumn::make('date')->title('Date'),
                TextColumn::make('start_time')->title('Start Time'),
                TextColumn::make('end_time')->title('End Time'),
                CreatedAtColumn::make(),
            ]);
    }
}
