<?php

namespace Botble\Ecommerce\Tables;

use App\Models\DhakaArea;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\Html;
use Botble\Ecommerce\Models\GlobalOptionValue;
use Botble\Ecommerce\Tables\Actions\SimpleDeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class InsideDhaka extends InsideDhakaTable
{
    public function setup(): void
    {
        $this
            ->model(DhakaArea::class)
            ->removeAllActions()
            ->addActions([
                EditAction::make()->url(function (EditAction $action) {
                    $item = $action->getItem();
                    $currentLanguage = request()->get('ref_lang');
                    $routeParameters = ['id' => $item->getKey()];

                    // Add ref_lang parameter to the route if it exists
                    if ($currentLanguage) {
                        $routeParameters['ref_lang'] = $currentLanguage;
                    }

                    return route('orders.editDhakaArea', $routeParameters);
                }),
                SimpleDeleteAction::make()->route('orders.deleteDhakaArea'),
            ]);
    }

    public function query(): Relation|Builder|QueryBuilder
    {
        $query = $this->getModel()
            ->query()
            ->select([
                'id',
                'name',
                'thana_id',
                'price',
                'created_at',
            ]);

        return $this->applyScopes($query);
    }

    public function columns(): array
    {
        return [
            IdColumn::make(),
            Column::make('name')
                ->title('Area Name')
                ->alignStart(),
            Column::make('thana_id')
                ->title('Thana'),
            Column::make('price')
                ->title('Price'),
            CreatedAtColumn::make(),
        ];
    }

    public function bulkActions(): array
    {
        return [
            DeleteBulkAction::make()->permission('orders.destroy'),
        ];
    }

    public function ajax(): JsonResponse
    {
        try {
            $data = $this->table
                ->eloquent($this->query())
                ->editColumn('name', function (DhakaArea $item) {
                    if (! $this->hasPermission('orders.edit')) {
                        return BaseHelper::clean($item->name);
                    }

                    // Get the current language parameter if it exists
                    $currentLanguage = request()->get('ref_lang');
                    $routeParameters = ['id' => $item->getKey()];

                    // Add ref_lang parameter to the route if it exists
                    if ($currentLanguage) {
                        $routeParameters['ref_lang'] = $currentLanguage;
                    }

                    return Html::link(
                        route('orders.editDhakaArea', $routeParameters),
                        BaseHelper::clean($item->name)
                    );
                })
                ->editColumn('thana_id', function (DhakaArea $item) {
                    try {
                        $thana = GlobalOptionValue::find($item->thana_id);
                        return $thana ? BaseHelper::clean($thana->option_value) : '';
                    } catch (\Exception $e) {
                        Log::error('Error fetching thana: ' . $e->getMessage());
                        return '';
                    }
                })
                ->editColumn('price', function (DhakaArea $item) {
                    try {
                        return format_price($item->price);
                    } catch (\Exception $e) {
                        Log::error('Error formatting price: ' . $e->getMessage());
                        return '';
                    }
                });

            return $this->toJson($data);
        } catch (\Exception $e) {
            Log::error('DataTables AJAX error in InsideDhaka: ' . $e->getMessage());
            throw $e;
        }
    }
}
