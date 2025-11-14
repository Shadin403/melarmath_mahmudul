<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\Slug\Models\Slug;
use Botble\Slug\Services\SlugService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\Slug\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'settings/permalink'], function (): void {
            Route::get('', [
                'as' => 'slug.settings',
                'uses' => 'SlugController@edit',
                'permission' => 'settings.options',
            ]);

            Route::put('', [
                'as' => 'slug.settings.update',
                'uses' => 'SlugController@update',
                'permission' => 'settings.options',
                'middleware' => 'preventDemo',
            ]);
        });
    });

    Route::group(['prefix' => 'ajax/slug', 'middleware' => ['web', 'core']], function (): void {
        Route::post('create', [
            'as' => 'slug.create',
            'uses' => 'SlugController@store',
        ]);
        Route::post('/create', function (Request $request, SlugService $slugService) {
            // return $slugService->create(
            //     $request->input('value'),
            //     $request->input('slug_id'),
            //     $request->input('model')
            // );
            $slug = $request->input('value');
            $index = 1;
            $baseSlug = $slug;

            $prefix = null;
            if (!empty($request->input('model'))) {
                $prefix = SlugHelper::getPrefix($request->input('model'));
            }

            while (checkIfExistedSlug($slug, $request->input('slug_id'), $prefix)) {
                $slug = apply_filters(FILTER_SLUG_EXISTED_STRING, $baseSlug . '-' . $index++, $baseSlug, $index, $request->input('model'));
            }

            if (empty($slug)) {
                $slug = time();
            }

            return apply_filters(FILTER_SLUG_STRING, $slug, $request->input('model'));
        })->name('slug.create');
    });
});

if (! function_exists('checkIfExistedSlug')) {
    function checkIfExistedSlug(?string $slug, int|string|null $slugId, ?string $prefix): bool
    {
        return Slug::query()
            ->where([
                'key' => $slug,
                'prefix' => $prefix,
            ])
            ->where('id', '!=', $slugId)
            ->exists();
    }
}
