<?php


namespace Agmedia\Models\Product;


use Agmedia\Helpers\Config;
use Agmedia\Models\Option\OptionValueDescription;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'product_option_value';
    
    /**
     * @var string
     */
    protected $primaryKey = 'product_option_value_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'product_option_value_id'
    ];
    
    /**
     * Relations methods
     */
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function value()
    {
        return $this->hasOne(OptionValueDescription::class, 'option_value_id', 'option_value_id')->where('language_id',
            Config::getLanguage());
    }
    
}