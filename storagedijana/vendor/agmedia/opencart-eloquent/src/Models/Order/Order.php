<?php


namespace Agmedia\Models\Order;


use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'order';
    
    /**
     * @var string
     */
    protected $primaryKey = 'order_id';

    public $timestamps = false;
    
    /**
     * @var array
     */
    protected $guarded = [
        'order_id'
    ];
    
    /**
     * Relations methods
     */
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id', 'order_id')->with('option');
    }
    
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function totals()
    {
        return $this->hasMany(OrderTotal::class, 'order_id', 'order_id')->orderBy('sort_order');
    }
    
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status()
    {
        return $this->hasOne(OrderStatus::class, 'order_status_id', 'order_status_id');
    }
    
}