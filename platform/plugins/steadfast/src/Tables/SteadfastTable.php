<?php

namespace Khairulkabir\Steadfast\Tables;

use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Services\DataTable;
use Botble\Ecommerce\Models\Order;
use Botble\Base\Facades\Html;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Botble\Base\Facades\Assets;

class SteadfastTable extends DataTable
{
    


    public function html(): HtmlBuilder
    {
        Assets::addScripts(['datatables', 'moment', 'datepicker'])
        ->addStyles(['datatables', 'datepicker'])
        ->addStylesDirectly('vendor/core/core/table/css/table.css')
        ->addScriptsDirectly([
            'vendor/core/core/base/libraries/bootstrap3-typeahead.min.js',
            'vendor/core/core/table/js/table.js',
            'vendor/core/core/table/js/filter.js',
            
        ]);

    if (setting('datatables_pagination_type') == 'dropdown') {
        Assets::addScriptsDirectly(['vendor/core/core/base/libraries/datatables/extensions/Pagination/js/dataTables.pagination.min.js'])
            ->addStylesDirectly(['vendor/core/core/base/libraries/datatables/extensions/Pagination/css/dataTables.pagination.min.css']);
    }
        return $this->builder();
    }

   
}
