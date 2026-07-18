<?php


namespace Agmedia\Models\Order;


use Agmedia\Models\Product\ProductOption;
use Illuminate\Database\Eloquent\Model;

class OrderOption extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'order_option';
    
    /**
     * @var string
     */
    protected $primaryKey = 'order_option_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'order_option_id'
    ];
    
    /**
     * Relations methods
     */
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function optionData()
    {
        return $this->hasOne(ProductOption::class, 'product_option_value_id', 'product_option_value_id');
    }
    
}