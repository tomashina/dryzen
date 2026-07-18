<?php

namespace Agmedia\Api\Models;

use Illuminate\Database\Eloquent\Model;

class OC_OrderManagerSales extends Model
{

    /**
     * @var string
     */
    protected $table = 'order_manager_sales';

    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $guarded = [
        'id'
    ];
}