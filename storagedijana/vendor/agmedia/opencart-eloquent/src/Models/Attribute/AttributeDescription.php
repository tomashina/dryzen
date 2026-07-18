<?php

namespace Agmedia\Models\Attribute;

use Illuminate\Database\Eloquent\Model;

class AttributeDescription extends Model
{

    /**
     * @var string
     */
    protected $table = 'attribute_description';

    /**
     * @var string
     */
    protected $primaryKey = 'attribute_id';

    /**
     * @var array
     */
    protected $guarded = [
        'attribute_id'
    ];
}