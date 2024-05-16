<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $store_id store id @property bigint $product_id product id @property int $qty_available qty available @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class StoreProduct extends Model

{

    /**
     * Database table name
     */
    protected $table = 'store_products';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['store_id', 'product_id', 'qty_available'];

    /**
     * Date time columns.
     */
    protected $dates = [];

    public function QTYAvailable()
    {
        return $this->qty_available;
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    
    public static function scopeForProducts($query, int $store_id = null)
    {
        if (is_null($store_id))
            return $query;
        return $query->where('store_id', $store_id);
    }
}