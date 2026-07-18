<?php


namespace Agmedia\Models\Order;


use Illuminate\Database\Eloquent\Model;

class OrderTotal extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'order_total';
    
    /**
     * @var string
     */
    protected $primaryKey = 'order_total_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'order_total_id'
    ];
    
}