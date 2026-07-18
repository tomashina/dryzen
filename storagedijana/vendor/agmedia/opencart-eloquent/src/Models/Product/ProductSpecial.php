<?php


namespace Agmedia\Models\Product;


use Illuminate\Database\Eloquent\Model;

class ProductSpecial extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'product_special';
    
    /**
     * @var string
     */
    protected $primaryKey = 'product_special_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'product_special_id'
    ];
}