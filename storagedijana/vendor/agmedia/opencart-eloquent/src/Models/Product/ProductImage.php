<?php


namespace Agmedia\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'product_image';
    
    /**
     * @var string
     */
    protected $primaryKey = 'product_image_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'product_image_id'
    ];
    
}