<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property varchar $batch_no batch no
@property varchar $existing_quantity existing quantity
@property varchar $new_quantity new quantity
@property varchar $total_quantity total quantity
@property date $expiry_date expiry date
@property bigint $created_by created by
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class StoreProductBatch extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'store_product_batches';

    /**
    * Mass assignable columns
    */
    protected $fillable=['batch_no',
'existing_quantity',
'new_quantity',
'total_quantity',
'expiry_date',
'created_by'];

    /**
    * Date time columns.
    */
    protected $dates=['expiry_date'];




}