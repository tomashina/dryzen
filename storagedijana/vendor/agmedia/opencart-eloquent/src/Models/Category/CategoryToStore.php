<?php


namespace Agmedia\Models\Category;


use Illuminate\Database\Eloquent\Model;

class CategoryToStore extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'category_to_store';
    
    /**
     * @var string
     */
    protected $primaryKey = 'category_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'category_id'
    ];
    
}