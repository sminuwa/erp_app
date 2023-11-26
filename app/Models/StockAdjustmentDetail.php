<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $stock_adjustment_id stock adjustment id
@property bigint $store_id store id
@property bigint $product_id product id
@property decimal $quantity quantity
@property decimal $cost_price cost price
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class StockAdjustmentDetail extends Model
{

    /**
    * Database table name
    */
    protected $table = 'stock_adjustment_details';

    /**
    * Mass assignable columns
    */
    protected $fillable=['stock_adjustment_id',
'store_id',
'product_id',
'quantity',
'cost_price'];

    /**
    * Date time columns.
    */
    protected $dates=[];


    public function stockAdjustment(){
        return $this->belongsTo(StockAdjustment::class);
    }


}
