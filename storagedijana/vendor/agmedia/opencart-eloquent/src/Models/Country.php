<?php


namespace Agmedia\Models;


use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'country';
    
    /**
     * @var string
     */
    protected $primaryKey = 'country_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'country_id'
    ];
}