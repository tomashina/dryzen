<?php


namespace Agmedia\Models\Option;


use Illuminate\Database\Eloquent\Model;

class OptionValueDescription extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'option_value_description';
    
    /**
     * @var string
     */
    protected $primaryKey = 'option_value_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'option_value_id'
    ];
}