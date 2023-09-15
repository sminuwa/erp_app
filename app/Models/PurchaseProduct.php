<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 @property bigint $purchase_id purchase id @property bigint $product_id product id @property int $qty_supplied qty supplied @property decimal $unit_price unit price @property tinyint $status status @property timestamp $created_at created at @property timestamp $updated_at updated at
 
 */
class PurchaseProduct extends Model

{

    /**
     * Database table name
     */
    protected $table = 'purchase_products';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['purchase_id', 'product_id', 'qty_supplied', 'unit_price', 'status'];

    /**
     * Date time columns.
     */
    protected $dates = [];
    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id', 'id');
    }

}