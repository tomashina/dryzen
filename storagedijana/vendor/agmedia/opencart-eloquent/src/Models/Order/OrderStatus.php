<?php


namespace Agmedia\Models\Order;


use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'order_status';
    
    /**
     * @var string
     */
    protected $primaryKey = 'order_status_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'order_status_id'
    ];
    
    
}