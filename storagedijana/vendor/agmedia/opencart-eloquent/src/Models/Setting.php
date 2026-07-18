<?php


namespace Agmedia\Models;


use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'setting';
    
    /**
     * @var string
     */
    protected $primaryKey = 'setting_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'setting_id'
    ];
}