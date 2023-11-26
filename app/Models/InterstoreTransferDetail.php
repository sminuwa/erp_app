<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $interstore_transfer_id interstore transfer id
@property bigint $product_id product id
@property bigint $source_store_id source store id
@property bigint $destination_store_id destination store id
@property decimal $quantity quantity
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class InterstoreTransferDetail extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'interstore_transfer_details';

    /**
    * Mass assignable columns
    */
    protected $fillable=['interstore_transfer_id',
'product_id',
'source_store_id',
'destination_store_id',
'quantity'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}