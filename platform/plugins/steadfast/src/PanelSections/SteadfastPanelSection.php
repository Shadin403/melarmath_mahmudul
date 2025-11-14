<?php

namespace Khairulkabir\Steadfast\PanelSections;

use Botble\Base\PanelSections\PanelSection;

class SteadfastPanelSection extends PanelSection
{
    public function setup(): void
    {
        $this
            ->setId('settings.{id}')
            ->setTitle('{title}')
            ->withItems([
                //
            ]);
    }
}
