<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $store_id store id @property bigint $product_id product id @property double $selling_price selling price @property tinyint $status status @property bigint $updated_by updated by @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class StoreProductPrice extends Model

{

    /**
     * Database table name
     */
    protected $table = 'store_product_prices';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['store_id', 'product_id', 'selling_price','cost_price', 'status', 'updated_by'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function user(){
        return $this->belongsTo(User::class,'updated_by');
    }
}