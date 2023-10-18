<?php

namespace App\Classes;

use App\Models\BranchProductPrice;

class CostPrice
{

    public static function newCostPrice(array $products, $branch_id = 2){
        /*
         * branch id of the destination store
         * array of product list as follows
         * [
         *  2 =>[
         *        quantity => 20,
         *        price => 300
         *      ]
         *  5 =>[
         *        quantity => 65,
         *        price => 1300
         *      ]
         * ]
         *
         * */
        $product_ids = [];
        foreach($products as $key=>$p){
            $product_ids[] = $key;
        }

        //get record of cost prices in the database
        $quantity = $cost_price = 0;
        $records = BranchProductPrice::whereIn('product_id', $product_ids)->where('branch_id', $branch_id)->get();
        if($records){
            foreach($records as $record){

            }
        }
        return $records;
        return $product_ids;
        return $products;

    }
}
