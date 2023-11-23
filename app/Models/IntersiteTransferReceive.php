<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $intersite_transfer_id intersite transfer id
@property bigint $source_store_id source store id
@property bigint $product_id product id
@property bigint $store_id store id
@property decimal $quantity quantity
@property decimal $cost_price cost price
@property tinyint $status status
@property timestamp $created_at created at
@property timestamp $updated_at updated at

 */
class IntersiteTransferReceive extends Model
{

    /**
    * Database table name
    */
    protected $table = 'intersite_transfer_receives';

    /**
    * Mass assignable columns
    */
    protected $fillable=['intersite_transfer_id',
'source_store_id',
'product_id',
'store_id',
'quantity',
'cost_price',
'status'];

    /**
    * Date time columns.
    */
    protected $dates=[];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function intersite()
    {
        return $this->belongsTo(IntersiteTransfer::class, 'intersite_transfer_id');
    }


}
