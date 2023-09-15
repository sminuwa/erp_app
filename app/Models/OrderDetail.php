<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $order_id order id @property bigint $product_id product id @property int $quantity quantity @property double $unit_cost unit cost @property double $total total @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class OrderDetail extends Model

{

    /**
     * Database table name
     */
    protected $table = 'order_details';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['order_id', 'product_id', 'quantity', 'unit_cost', 'total'];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function storeProduct()
    {
        return $this->belongsTo(StoreProduct::class);
    }
    public function product()
    {
        return $this->storeProduct()->product();
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function branch()
    {
        return $this->storeProduct()->select('branches.*')->join('stores','stores.id','store_products.store_id')->join('branches','branches.id','stores.branch_id');
    }

}