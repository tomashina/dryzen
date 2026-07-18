<?php


namespace Agmedia\Models\Product;


use Illuminate\Database\Eloquent\Model;

class ProductDescription extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'product_description';
    
    /**
     * @var string
     */
    protected $primaryKey = 'product_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'product_id'
    ];
}