<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $credit_note_id credit note id
@property bigint $store_product_id store product id
@property int $quantity quantity
@property int $original_quantity_sold original quantity sold
@property double $sold_price sold price
@property double $total total
@property decimal $selling_price selling price
@property decimal $cost_price cost price
@property int $avail_qty_before_sale avail qty before sale
@property tinyint $status status
@property bigint $last_modified_by last modified by
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class CreditNoteDetail extends Model 
{
    
    /**
    * Database table name
    */
    protected $table = 'credit_note_details';

    /**
    * Mass assignable columns
    */
    protected $fillable=['credit_note_id',
'store_product_id',
'quantity',
'original_quantity_sold',
'sold_price',
'total',
'selling_price',
'cost_price',
'avail_qty_before_sale',
'status',
'last_modified_by'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}