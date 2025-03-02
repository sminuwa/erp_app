<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
   @property bigint $return_debit_id return debit id
@property bigint $store_product_id store product id
@property int $current_quantity current quantity
@property int $original_quantity_sold original quantity sold
@property double $price price
@property tinyint $status status
@property timestamp $created_at created at
@property timestamp $updated_at updated at
@property StoreProduct $storeProduct belongsTo
@property ReturnDebit $returnDebit belongsTo
   
 */
class ReturnDebitItem extends Model
{

    /**
     * Database table name
     */
    protected $table = 'return_debit_items';

    /**
     * Mass assignable columns
     */
    protected $fillable = [
        'return_debit_id',
        'store_product_id',
        'current_quantity',
        'original_quantity_sold',
        'price',
        'status'
    ];

    /**
     * Date time columns.
     */
    protected $dates = [];

    /**
     * storeProduct
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * returnDebit
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function returnDebit()
    {
        return $this->belongsTo(ReturnDebit::class, 'return_debit_id');
    }




}