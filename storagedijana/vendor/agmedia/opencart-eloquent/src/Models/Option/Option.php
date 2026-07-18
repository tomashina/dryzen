<?php


namespace Agmedia\Models\Option;


use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'option';
    
    /**
     * @var string
     */
    protected $primaryKey = 'option_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'option_id'
    ];
    
}