<?php


namespace Agmedia\Models\Category;


use Illuminate\Database\Eloquent\Model;

class CategoryPath extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'category_path';
    
    /**
     * @var string
     */
    protected $primaryKey = 'path_id';
    
}