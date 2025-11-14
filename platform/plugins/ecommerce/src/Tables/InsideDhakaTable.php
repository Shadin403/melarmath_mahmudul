<?php

namespace Botble\Ecommerce\Tables;

use App\Models\DhakaArea;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Ecommerce\Enums\ProductTypeEnum;
use Botble\Ecommerce\Facades\EcommerceHelper;
use Botble\Ecommerce\Tables\Actions\SimpleDeleteAction;
use Botble\Location\Models\Country;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;
use Botble\Table\BulkChanges\TextBulkChange;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\NameColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Illuminate\Database\Eloquent\Builder;

class InsideDhakaTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(DhakaArea::class)
            ->addActions([
                EditAction::make()->route('orders.editDhakaArea'),
                SimpleDeleteAction::make()->route('orders.deleteDhakaArea'),
            ]);
    }

    public function buttons(): array
    {
        return [
            CreateHeaderAction::make()->label('Area Create')->route('orders.createInsideOfDhaka'),
            CreateHeaderAction::make()->label('Thana Create')->route('global-option.edit', ['option' => 7]), // Pass the option ID in an array
        ];
    }
}
