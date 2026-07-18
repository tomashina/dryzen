<?php


namespace Agmedia\Models\Product;


use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'product';
    
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

    /**
     * @var bool
     */
    public $timestamps = false;
    
    /**
     * Relations methods
     */
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(ProductOption::class, 'product_id', 'product_id')->with('value');
    }
    
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function descriptions()
    {
        return $this->hasMany(ProductDescription::class, 'product_id', 'product_id');
    }
    
    
    /**
     * @param $language_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function description($language_id)
    {
        return $this->hasOne(ProductDescription::class, 'product_id', 'product_id')->where('language_id', $language_id);
    }
    
    /**
     * Query scope methods
     */
    
    /**
     * @param $query
     *
     * @return mixed
     */
    public function scopeSkus($query)
    {
        return ProductOption::where('product_id', $this->product_id)->pluck('sku');
    }
    
    
    /**
     * @param      $query
     * @param null $products - Should be array or integer.
     *
     * @return mixed
     */
    public function scopeActivate($query, $products = null)
    {
        if ( ! $products) {
            return $query->update(['status' => 1]);
        }
        
        if (is_array($products) || is_a($products, Collection::class)) {
            return $query->whereIn('product_id', $products)->update(['status' => 1]);
        }
        
        return $query->where('product_id', $products)->update(['status' => 1]);
    }
    
    
    /**
     * @param      $query
     * @param null $products - Should be array or integer.
     *
     * @return mixed
     */
    public function scopeDeactivate($query, $products = null)
    {
        if ( ! $products) {
            return $query->update(['status' => 0]);
        }
        
        if (is_array($products) || is_a($products, Collection::class)) {
            return $query->whereIn('product_id', $products)->update(['status' => 0]);
        }
        
        return $query->where('product_id', $products)->update(['status' => 0]);
    }
    
}