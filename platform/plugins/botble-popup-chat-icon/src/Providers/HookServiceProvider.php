<?php

namespace Botble\PopupChat\Providers;

use Botble\Theme\Events\RenderingThemeOptionSettings;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app['events']->listen(RenderingThemeOptionSettings::class, function (): void {
            add_action(RENDERING_THEME_OPTIONS_PAGE, [$this, 'addThemeOptions'], 55);
        });
    }

    public function addThemeOptions(): void
    {
        theme_option()
            ->setSection([
                'title' => __('Chat Button'),
                'id' => 'opt-text-subsection-chat-buttons',
                'subsection' => true,
                'icon' => 'fa fa-comments',
                'fields' => [
                    [
                        'id' => 'chat_btn_facebook',
                        'type' => 'text',
                        'label' => __('Link chat Facebook'),
                        'attributes' => [
                            'name' => 'chat_btn_facebook',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id' => 'chat_btn_messenger',
                        'type' => 'text',
                        'label' => __('Link Messenger'),
                        'attributes' => [
                            'name' => 'chat_btn_messenger',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id' => 'chat_btn_youtube',
                        'type' => 'text',
                        'label' => __('Link YouTube'),
                        'attributes' => [
                            'name' => 'chat_btn_youtube',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id' => 'chat_btn_whatsapp',
                        'type' => 'text',
                        'label' => __('Link WhatsApp'),
                        'attributes' => [
                            'name' => 'chat_btn_whatsapp',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                    [
                        'id' => 'hotline',
                        'type' => 'text',
                        'label' => __('Hotline'),
                        'attributes' => [
                            'name' => 'hotline',
                            'value' => null,
                            'options' => [
                                'class' => 'form-control',
                            ],
                        ],
                    ],
                ],
            ]);
    }
}
