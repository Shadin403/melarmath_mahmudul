<?php

namespace Khairulkabir\Steadfast\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Base\Facades\DashboardMenu;
use Khairulkabir\Steadfast\Models\Steadfast;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use Botble\Base\PanelSections\PanelSectionItem;
use Picqer\Barcode\BarcodeGeneratorPNG;


class SteadfastServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;


    public function register(): void
    {
        if (! class_exists('Picqer\Barcode\BarcodeGeneratorPNG')) {
            require __DIR__ . '/../../vendor/autoload.php';
        }
    }

    public function boot(): void
    {


        if (! is_plugin_active('ecommerce')){
            return;
        }

        $this
            ->setNamespace('plugins/steadfast')
            ->loadHelpers()
            ->loadAndPublishConfigurations(["permissions"])
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadAndPublishViews()
            ->loadMigrations();
            
            if (defined('LANGUAGE_ADVANCED_MODULE_SCREEN_NAME')) {
                \Botble\LanguageAdvanced\Supports\LanguageAdvancedManager::registerModule(Steadfast::class, [
                    'name',
                ]);
            }
            
            DashboardMenu::default()->beforeRetrieving(function () {
                DashboardMenu::registerItem([
                    'id' => 'cms-plugins-steadfast',
                    'priority' => 5,
                    'parent_id' => null,
                    'name' => 'plugins/steadfast::steadfast.name',
                    'icon' => 'fa fa-truck-fast',
                    'url' => route('steadfast.index'),
                    'permissions' => ['steadfast.index'],
                ]);
            });
            PanelSectionManager::default()->beforeRendering(function () {
                PanelSectionManager::registerItem(
                    SettingOthersPanelSection::class,
                    fn () => PanelSectionItem::make('steadfasts_configs')
                        ->setTitle(trans('SteadFast Settings'))
                        ->withIcon('ti ti-truck')
                        ->withDescription(trans('SteadFast Settings'))
                        ->withPriority(120)
                        ->withRoute('steadfast.steadfasts_configs')
                );
            });
    }
}
