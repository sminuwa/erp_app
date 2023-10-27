<?php

namespace App\Classes;

use App\Models\BranchProductPrice;
use App\Models\Store;
use App\Models\StoreProduct;

class CostPrice
{

    public static function newCostPrice(array $products, $store_id = 278,  $branch_id = 2){
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
        $prices = BranchProductPrice::whereIn('product_id', $product_ids)->where('branch_id', $branch_id)->get();
        $quantities = StoreProduct::whereIn('product_id', $product_ids)->where('store_id', $store_id)->get();
        $old_product_costs = [];
        $old_product_quantity = [];
        foreach($prices as $pr){
            return $pr;
        }
        foreach($quantities as $qqt){

        }
        return $quantities;
        return $product_ids;
        return $products;

    }
}
