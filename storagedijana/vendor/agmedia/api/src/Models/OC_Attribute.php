<?php

namespace Agmedia\Api\Models;

use Agmedia\Api\Helper\Helper;
use Agmedia\Helpers\Log;
use Agmedia\Models\Attribute\Attribute;
use Agmedia\Models\Attribute\AttributeDescription;

class OC_Attribute
{

    /**
     * Returns attribute ID or null.
     *
     * @param string $attribute
     *
     * @return mixed
     */
    public static function makeAttribute(string $attribute)
    {
        $has = AttributeDescription::query()->where('name', $attribute)->first();

        if ( ! $has) {
            $id = Attribute::insertGetId([
                'attribute_group_id' => agconf('import.default_attribute_group'),
                'sort_order' => 0
            ]);

            if ($id) {
                AttributeDescription::insert([
                    'attribute_id' => $id,
                    'language_id' => agconf('import.default_language'),
                    'name' => Helper::setText($attribute)
                ]);
            }

            return $id;
        }

        return $has->attribute_id;
    }
}