<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $purchase_id purchase id
@property bigint $product_id product id
@property int $qty_supplied qty supplied
@property date $expire_date expire date
@property decimal $unit_price unit price
@property decimal $selling_price selling price
@property tinyint $status status
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class TempPurchaseProduct extends Model
{

    /**
    * Database table name
    */
    protected $table = 'temp_purchase_products';

    /**
    * Mass assignable columns
    */
    protected $fillable=['purchase_id',
'product_id',
'qty_supplied',
'expire_date',
'unit_price',
'selling_price',
'status'];

    /**
    * Date time columns.
    */
    protected $dates=['expire_date'];


    public static function clearAll(){
        if(self::where('user_id', auth()->id())->truncate())
            return true;
        return false;
    }

    public static function products(){
        $records = self::where('user_id', auth()->id())->orderBy('id', 'desc')->get();
        return $records;
    }

    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }


}
