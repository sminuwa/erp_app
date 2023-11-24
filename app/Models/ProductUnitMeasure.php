<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
   @property bigint $product_id product id
@property varchar $code code
@property enum $type type
@property int $amount amount
@property timestamp $created_at created at
@property timestamp $updated_at updated at
   
 */
class ProductUnitMeasure extends Model 
{
    const TYPE_DIVISION='division';

const TYPE_MULTIPLE='multiple';

    /**
    * Database table name
    */
    protected $table = 'product_unit_measures';

    /**
    * Mass assignable columns
    */
    protected $fillable=['product_id',
'code',
'type',
'amount'];

    /**
    * Date time columns.
    */
    protected $dates=[];




}