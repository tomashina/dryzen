<?php


namespace Agmedia\Models\Customer;


use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'customer';
    
    /**
     * @var string
     */
    protected $primaryKey = 'customer_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'customer_id'
    ];


    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group()
    {
        return $this->hasOne(CustomerGroup::class, 'customer_group_id', 'customer_group_id');
    }


    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address()
    {
        return $this->hasOne(Address::class, 'address_id', 'address_id');
    }
}