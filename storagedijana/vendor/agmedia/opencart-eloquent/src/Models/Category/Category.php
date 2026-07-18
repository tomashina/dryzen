<?php


namespace Agmedia\Models\Category;


use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'category';
    
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
    
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function descriptions()
    {
        return $this->hasMany(CategoryDescription::class, 'category_id', 'category_id');
    }
    
    
    /**
     * @param $language_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function description($language_id)
    {
        return $this->hasOne(CategoryDescription::class, 'category_id', 'category_id')->where('language_id', $language_id);
    }
    
    /**
     * Query scope methods
     */
    
    /**
     * @param      $query
     * @param null $categories - Should be array or integer.
     *
     * @return mixed
     */
    public function scopeActivate($query, $categories = null)
    {
        if ( ! $categories) {
            return $query->update(['status' => 1]);
        }
        
        if (is_array($categories) || is_a($categories, Collection::class)) {
            return $query->whereIn('category_id', $categories)->update(['status' => 1]);
        }
        
        return $query->where('category_id', $categories)->update(['status' => 1]);
    }
    
    
    /**
     * @param      $query
     * @param null $categories - Should be array or integer.
     *
     * @return mixed
     */
    public function scopeDeactivate($query, $categories = null)
    {
        if ( ! $categories) {
            return $query->update(['status' => 0]);
        }
        
        if (is_array($categories) || is_a($categories, Collection::class)) {
            return $query->whereIn('category_id', $categories)->update(['status' => 0]);
        }
        
        return $query->where('category_id', $categories)->update(['status' => 0]);
    }
}