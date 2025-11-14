<?php

namespace Botble\PopupChat\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Theme\Facades\Theme;

class PopupChatServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this
            ->setNamespace('plugins/botble-popup-chat-icon')
            ->loadHelpers()
            ->loadAndPublishViews();

        $this->app->register(HookServiceProvider::class);

        Theme::asset()
            ->usePath(false)
            ->add(
                'popup-chat-css',
                asset('vendor/core/plugins/botble-popup-chat-icon/css/popup-chat.min.css')
            );

        add_filter(THEME_FRONT_FOOTER, function (string|null $data): string {
            return $data . view('plugins/botble-popup-chat-icon::show');
        });
    }
}
