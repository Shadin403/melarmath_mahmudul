<?php

namespace App\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Botble\Language\Models\LanguageMeta;


class DhakaArea extends BaseModel
{
    protected $table = 'ec_dhaka_area';

    protected $fillable = [
        'name',
        'thana_id',
        'price'
    ];

    /**
     * @return HasMany
     */
    public function translations(): HasMany
    {
        return $this->hasMany(DhakaAreaTranslation::class, 'ec_dhaka_area_id');
    }

    /**
     * Get translation for specific language
     */
    public function getTranslation($field, $locale, $fallback = true)
    {
        $translation = $this->translations()->where('lang_code', $locale)->first();

        if ($translation && isset($translation->{$field})) {
            return $translation->{$field};
        }

        // Fallback to default value if translation not found
        return $fallback ? $this->{$field} : null;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function languageMeta()
    {
        return $this->morphMany(LanguageMeta::class, 'reference');
    }

    /**
     * Update or create translation for specific language
     */
    public function updateTranslation($locale, $data)
    {
        $translation = $this->translations()->where('lang_code', $locale)->first();

        if ($translation) {
            $translation->update($data);
        } else {
            // Create new translation with proper model instantiation
            $translation = new DhakaAreaTranslation();
            $translation->ec_dhaka_area_id = $this->id;
            $translation->lang_code = $locale;

            // Set attributes individually to avoid setAttribute issues
            foreach ($data as $key => $value) {
                $translation->{$key} = $value;
            }
            $translation->save();
        }

        return $translation;
    }
}
