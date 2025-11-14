<?php

namespace Botble\Ecommerce\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariationTranslation extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ec_product_variations_translations';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = null;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'lang_code',
        'ec_product_variations_id',
        'variation_title',
        'variation_desc',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model's primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed
     */
    public function getKey()
    {
        return $this->getAttribute('lang_code') . '_' . $this->getAttribute('ec_product_variations_id');
    }

    /**
     * Get the primary key value for a save query.
     *
     * @param  mixed  $key
     * @return mixed
     */
    protected function getKeyForSaveQuery($key = null)
    {
        if (is_null($key)) {
            $key = $this->getKeyName();
        }

        if (is_array($key)) {
            return array_map(function ($k) {
                return $this->getAttribute($k);
            }, $key);
        }

        return $this->getAttribute($key);
    }

    /**
     * Set the keys for a save update query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSaveQuery($query)
    {
        $query->where('lang_code', $this->getAttribute('lang_code'))
            ->where('ec_product_variations_id', $this->getAttribute('ec_product_variations_id'));

        return $query;
    }

    /**
     * Set the keys for a select query.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function setKeysForSelectQuery($query)
    {
        return $this->setKeysForSaveQuery($query);
    }

    /**
     * Get the primary key name for the model.
     *
     * @return string|array
     */
    public function getKeyName()
    {
        return ['lang_code', 'ec_product_variations_id'];
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }

    /**
     * Get the value of the model's route key.
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        return $this->getKey();
    }

    /**
     * Determine if the model has composite primary keys.
     *
     * @return bool
     */
    public function hasCompositeUniqueKey()
    {
        return true;
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        // Handle composite key attributes directly to avoid mutator issues
        if (in_array($key, ['lang_code', 'ec_product_variations_id'])) {
            $this->attributes[$key] = $value;
            return $this;
        }

        // For other attributes, use the parent implementation
        return parent::setAttribute($key, $value);
    }
}
