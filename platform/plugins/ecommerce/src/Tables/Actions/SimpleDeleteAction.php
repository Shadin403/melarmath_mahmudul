<?php

namespace Botble\Ecommerce\Tables\Actions;

use Botble\Table\Actions\Action;

class SimpleDeleteAction extends Action
{
    protected string $routeName;
    protected array $routeParameters = [];
    protected bool $absolute = true;

    public static function make(string $name = 'delete'): static
    {
        return parent::make($name)
            ->label(trans('core/base::tables.delete_entry'))
            ->color('danger')
            ->icon('ti ti-trash')
            ->attributes([
                'class' => 'btn btn-icon btn-sm btn-danger',
                'data-bs-toggle' => 'tooltip',
                'data-bs-original-title' => trans('core/base::tables.delete_entry'),
            ]);
    }

    public function route(string $route, array $parameters = [], bool $absolute = true): static
    {
        $this->routeName = $route;
        $this->routeParameters = $parameters;
        $this->absolute = $absolute;
        return $this;
    }

    public function render(): string
    {
        if (!isset($this->routeName) || !isset($this->item)) {
            return parent::render();
        }

        // Get the item ID
        $itemId = $this->item->getKey();

        // Merge the item ID with the route parameters
        $parameters = $this->routeParameters;
        // If the route expects the ID as the last parameter, add it
        if (empty($parameters)) {
            $parameters = [$itemId];
        } else {
            // Check if we need to add the ID to the parameters
            $parameters[] = $itemId;
        }

        // Generate the delete URL
        $deleteUrl = route($this->routeName, $parameters, $this->absolute);

        // Get the icon HTML
        $iconHtml = '<i class="fa-solid fa-trash" style="margin:0;"></i>';

        // Generate a form with DELETE method
        $form = '
        <form method="POST" action="' . $deleteUrl . '" style="display:inline;">
            ' . csrf_field() . '
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit"
                    class="' . $this->getAttribute('class', 'btn btn-icon btn-sm btn-danger') . '"
                    data-bs-toggle="' . $this->getAttribute('data-bs-toggle', 'tooltip') . '"
                    data-bs-original-title="' . $this->getAttribute('data-bs-original-title', trans('core/base::tables.delete_entry')) . '"
                    onclick="return confirm(\'' . trans('core/base::tables.confirm_delete_msg') . '\')">
                ' . $iconHtml . '
                <span class="sr-only">' . $this->getLabel() . '</span>
            </button>
        </form>';

        return $form;
    }
}
