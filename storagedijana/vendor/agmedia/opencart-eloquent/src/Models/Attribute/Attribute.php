<?php

namespace Agmedia\Models\Attribute;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{

    /**
     * @var string
     */
    protected $table = 'attribute';

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


    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group()
    {
        return $this->hasOne(AttributeGroup::class, 'attribute_group_id', 'attribute_group_id');
    }

}