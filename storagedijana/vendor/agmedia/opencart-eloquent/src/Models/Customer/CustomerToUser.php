<?php


namespace Agmedia\Models\Customer;


use Illuminate\Database\Eloquent\Model;

class CustomerToUser extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'customer_to_user';

    /**
     * @var bool
     */
    public $timestamps = false;

}