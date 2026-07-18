<?php

namespace Agmedia\Models\Attribute;

use Illuminate\Database\Eloquent\Model;

class AttributeGroupDescription extends Model
{

    /**
     * @var string
     */
    protected $table = 'attribute_group_description';

    /**
     * @var string
     */
    protected $primaryKey = 'attribute_group_id';

    /**
     * @var array
     */
    protected $guarded = [
        'attribute_group_id'
    ];
}