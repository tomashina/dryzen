<?php


namespace Agmedia\Models;


use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'user';
    
    /**
     * @var string
     */
    protected $primaryKey = 'user_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'user_id'
    ];
}