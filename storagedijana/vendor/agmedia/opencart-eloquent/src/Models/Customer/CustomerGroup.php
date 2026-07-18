<?php


namespace Agmedia\Models\Customer;


use Illuminate\Database\Eloquent\Model;

class CustomerGroup extends Model
{
    
    /**
     * @var string
     */
    protected $table = 'customer_group';
    
    /**
     * @var string
     */
    protected $primaryKey = 'customer_group_id';
    
    /**
     * @var array
     */
    protected $guarded = [
        'customer_group_id'
    ];
    
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function descriptions()
    {
        return $this->hasMany(CustomerGroupDescription::class, 'customer_group_id', 'customer_group_id');
    }
    
    
    /**
     * @param int|string $language_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function description($language_id)
    {
        return $this->hasOne(CustomerGroupDescription::class, 'customer_group_id', 'customer_group_id')->where('language_id', $language_id);
    }
}