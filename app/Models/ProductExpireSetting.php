<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $product_id product id
@property int $no_of_days no of days
@property bigint $user_id user id
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property User $user belongsTo
@property Product $product belongsTo
   
 */
class ProductExpireSetting extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'product_expire_settings';

    /**
    * Mass assignable columns
    */
    protected $fillable=['product_id',
'no_of_days',
'user_id'];

    /**
    * Date time columns.
    */
    protected $dates=[];

    /**
    * user
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    /**
    * product
    *
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }




}