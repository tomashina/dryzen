<?php


namespace Agmedia\Models\Order;


use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'order_product';
    
    /**
     * @var string
     */
    protected $primaryKey = 'order_product_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'order_product_id'
    ];
    
    /**
     * Relations methods
     */
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function option()
    {
        return $this->hasOne(OrderOption::class, 'order_product_id', 'order_product_id')->with('optionData');
    }
    
}