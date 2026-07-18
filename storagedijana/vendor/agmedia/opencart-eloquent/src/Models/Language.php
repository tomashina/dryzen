<?php


namespace Agmedia\Models;


use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'language';
    
    /**
     * @var string
     */
    protected $primaryKey = 'language_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'language_id'
    ];
}