<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property varchar $reference reference
 * @property bigint $branch_id branch id
 * @property bigint $store_id store id
 * @property bigint $product_id product id
 * @property varchar $quantity quantity
 * @property varchar $cost_price cost price
 * @property date $date date
 * @property timestamp $created_at created at
 * @property timestamp $updated_at updated at
 */
class ProductValuation extends Model
{

    /**
     * Database table name
     */
    protected $table = 'product_valuations';

    /**
     * Mass assignable columns
     */
    protected $fillable = ['reference',
        'branch_id',
        'store_id',
        'product_id',
        'quantity',
        'cost_price',
        'date'];

    /**
     * Date time columns.
     */
    protected $dates = ['date'];


    public static function createRecord($reference, $store_id, $product_id, $branch_id, $quantity, $cost_price, $date)
    {

        $record = self::where(['store_id' => $store_id, 'product_id' => $product_id, 'branch_id' => $branch_id, 'cost_price' => $cost_price])->first();
        if (!$record)
            $record = new self;
        $record->reference = $reference;
        $record->branch_id = $branch_id;
        $record->store_id = $store_id;
        $record->product_id = $product_id;
        $record->quantity = $quantity;
        $record->cost_price = $cost_price;
        $record->date = $date;
        $record->action_by = auth()->id();
        if ($record->save())
            return true;
        return false;
    }

    public static function createBatchRecord(array $records, string $reference=null, $date=null)
    {
        /*
         * find store_product record and get the following
         * array of records looks like below
         *  [
         *    [ store_id => 1,
         *      product_id => 2,
         *      quantity => 210,
         *      operation => 'out
         *    ],
         *    [ store_id => 1,
         *      product_id => 2,
         *      quantity => 210,
         *      operation => 'in'
         *    ]
         *  ]
         * then calculate quantity before and quantity after from the store_product table record
         * check the transaction is credit or debit
         * if credit assign the credit value otherwise the debit
         * update the stock card table with required param
         * types: 1= opening balance, 2= Purchase, 3=Sale, 4 = Transfer, 5 = Adjustment
         *
         * */
        $valuations = [];
        foreach ($records as $record) {
            $record = self::where(['store_id' => $record['store_id'], 'product_id' => $record['product_id'], 'branch_id' => $record['branch_id'], 'cost_price' => $record['cost_price']])->first();
            $valuations[] = [
                'reference' => $reference,
                'branch_id' => $record['branch_id'],
                'store_id' => $record['store_id'],
                'product_id' => $record['product_id'],
                'quantity' => $record['quantity'],
                'cost_price' => $record['cost_price'],
                'date' => $date,
                'action_by' => $record['action_by'] ?? auth()->id(),

            ];
        }

        if (self::upsert($valuations, ['store_id', 'product_id','branch_id', 'reference'])) {
            return true;
        } else {
            return false;
        }

    }

}
