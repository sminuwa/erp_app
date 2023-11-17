<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StockCard extends Model
{
    use HasFactory;

    public static function createRecord(array $records, string $reference, $date, $type) {
        /*
         * find store_product record and get the following
         * array of records looks like below
         *  [
         *    [ store_id => 1,
         *      product_id => 2,
         *      quantity => 210,
         *      is_credit => true
         *    ],
         *    [ store_id => 1,
         *      product_id => 2,
         *      quantity => 210,
         *      is_credit => false
         *    ]
         *  ]
         * then calculate quantity before and quantity after from the store_product table record
         * check the transaction is credit or debit
         * if credit assign the credit value otherwise the debit
         * update the stock card table with required param
         * types: 1= opening balance, 2= Purchase, 3=Sale, 4 = Transfer, 5 = Adjustment
         *
         * */
        $cards = [];
        foreach($records as $record){
            $credit = $debit = $qty_after =$qty_before  = 0;
            $store_product = StoreProduct::where(['store_id'=>$record->store_id, 'product_id'=>$record->product_id])->first();
            if($store_product){
                $qty_available = $store_product->qty_available;
                $qty_before = $qty_available;
                $qty_after = $qty_available - $record->quantity;
                if($record->is_credit) // meaning if record type is credit then assign the quantity to credit else debit
                    $credit = $quantity;
                else
                    $debit = $quantity;
            }
            $cards[] = [
                'store_id' => $record->store_id,
                'product_id' => $$record->product_id,
                'cr' => $credit,
                'dr' => $debit,
                'qty_before' => $qty_before,
                'qty_after' => $qty_after,
                'refno' => $reference,
                'type' => $type,
                'date' => $date,
                'user_id' => auth()->id(),
                'priority' => $type,
            ];
        }

        DB::beginTransaction();
        if(StockCard::upsert($cards, ['store_id', 'product_id'])){
            DB::commit();
        }else{
            DB::rollBack();
        }

    }
}
