<?php


namespace Agmedia\Models\Customer;


use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'address';
    
    /**
     * @var string
     */
    protected $primaryKey = 'address_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'address_id'
    ];

}