<?php

namespace App\Classes;

use App\Models\BranchProductPrice;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\StoreProductBatch;
use Illuminate\Support\Facades\DB;

class CostPrice
{

    public static function returnDebitFormula(array $products, $batch_no, $store_id = 278,  $branch_id = 2){
        /*
         * branch id of the destination store
         * array of product list as follows
         * [
         *  2 =>[
         *        quantity => 20,
         *        price => 300,
         *        expiry = null
         *      ]
         *  5 =>[
         *        quantity => 65,
         *        price => 1300,
         *        expiry = 2023-12-31
         *      ]
         * ]
         *
         * */
        // get the existing qty
        // get existing cost
        // get total existing cost = existing qty * existing cost
        // get return qty
        // get return cost
        // get total return cost = return qty * return cost
        // qty = existing qty - return qty
        // cost = (total existing cost - total return cost) / qty
        $user = auth()->user();
        $product_ids = [];
        $total_new_cost = $records = [];
        foreach($products as $key=>$p){
            $product_ids[] = $key;
            $total_new_cost[$key] = intval($p['quantity']) * intval($p['price']);
        }
        //get record of cost prices in the database
        $quantities = [];
        $prices = StoreProduct::selectRaw("store_products.product_id, branch_product_prices.cost_price, sum(store_products.qty_available) as quantity, (branch_product_prices.cost_price * sum(store_products.qty_available)) as total_existing_cost")
            ->join('branch_product_prices', 'branch_product_prices.product_id', 'store_products.product_id')
            ->whereIn('store_products.product_id', $product_ids)->where('branch_product_prices.branch_id', $branch_id)->groupBy('store_products.product_id')->get();
        $old_product_costs = [];
        $old_product_quantity = [];
//        return $prices;
        foreach($prices as $price){
            $existing_cost[$price->product_id] = ['cost_price' => $price->cost_price, 'quantity'=>$price->quantity, 'total_existing_cost'=>$price->total_existing_cost];
        }

        foreach($products as $key=>$p){
//            return isset($existing_cost[$key]['quantity']) ? $existing_cost[$key]['quantity'] : 0;
            $records[$key] = [
                'existing_quantity' => isset($existing_cost[$key]['quantity']) ? $existing_cost[$key]['quantity'] : 0,
                'return_quantity' => $products[$key]['quantity'],
                'existing_cost' => isset($existing_cost[$key]['cost_price']) ? $existing_cost[$key]['cost_price'] : 0,
                'return_cost' => $products[$key]['price'],
                'total_existing_cost' => isset($existing_cost[$key]['total_existing_cost']) ? $existing_cost[$key]['total_existing_cost'] : 0,
                'total_return_cost' => ($products[$key]['quantity'] * $products[$key]['price']),
                'quantity' => (($p['quantity']) + (isset($existing_cost[$key]['quantity']) ? $existing_cost[$key]['quantity'] : 0)),
                'expiry_date' => $products[$key]['expiry_date'],
            ];
        }

        $store_products = $product_costs = $batch = [];
        foreach($records as $key=>$record){
            $product_costs[] = [
                'branch_id' => $branch_id,
                'product_id' => $key,
                'cost_price' => round(($record['total_existing_cost'] + $record['total_new_cost']) / $record['quantity'],2),
                'updated_by' => $user->id
            ];
            $store_products[] = [
                'store_id'=>$store_id,
                'product_id'=>$key,
                'qty_available'=>$record['quantity']
            ];
            $batch[] = [
                'batch_no' => $batch_no,
                'existing_quantity' => $record['existing_quantity'],
                'new_quantity' => $record['new_quantity'],
                'total_quantity' => $record['quantity'],
                'existing_cost_price' => $record['existing_cost'],
                'new_cost_price' => $record['new_cost'],
                'created_by' => $user->id,
                'expiry_date' => $record['expiry_date'],
            ];
        }

        DB::beginTransaction();

        if(
            StoreProduct::upsert($store_products, ['store_id','product_id'])
            && BranchProductPrice::upsert($product_costs, ['branch_id', 'product_id'])
            && StoreProductBatch::upsert($batch, ['batch_no'])
        ){
            DB::commit();
            return ['status'=>true, 'message'=>'success'];
        }else{
            return ['status'=>false, 'message'=>'Something went wrong.'];
        }
    }

    public static function newCostPrice(array $products, $batch_no,  $branch_id = 2){
        /*
         * branch id of the destination store
         * array of product list as follows
         * [
         *  2 =>[
         *        quantity => 20,
         *        price => 300,
         *        store => 2,
         *        expiry = null
         *      ]
         *  5 =>[
         *        quantity => 65,
         *        price => 1300,
         *        store => 2,
         *        expiry = 2023-12-31
         *      ]
         * ]
         *
         * */
        // get the existing qty
        // get the existing cost
        // total existing cost = existing qty * existing cost
        // get new quantity
        // get new cost
        // total new cost = new qty * new cost
        // qty = existing qty + new qty
        // cost = (total existing cost + total new cost) / qty

        $user = auth()->user();
        $product_ids = [];
        $total_new_cost = $records = [];
        foreach($products as $key=>$p){
            $product_ids[] = $key;
            $total_new_cost[$key] = intval($p['quantity']) * intval($p['price']);
        }
        //get record of cost prices in the database
        $quantities = [];
        $prices = StoreProduct::selectRaw("store_products.product_id, branch_product_prices.cost_price, sum(store_products.qty_available) as quantity, (branch_product_prices.cost_price * sum(store_products.qty_available)) as total_existing_cost")
        ->join('branch_product_prices', 'branch_product_prices.product_id', 'store_products.product_id')
        ->whereIn('store_products.product_id', $product_ids)->where('branch_product_prices.branch_id', $branch_id)->groupBy('store_products.product_id')->get();
        $old_product_costs = [];
        $old_product_quantity = [];
//        return $prices;
        foreach($prices as $price){
            $existing_cost[$price->product_id] = ['cost_price' => $price->cost_price, 'quantity'=>$price->quantity, 'total_existing_cost'=>$price->total_existing_cost];
        }

        foreach($products as $key=>$p){
//            return isset($existing_cost[$key]['quantity']) ? $existing_cost[$key]['quantity'] : 0;
            $records[$key] = [
                'existing_quantity' => isset($existing_cost[$key]['quantity']) ? $existing_cost[$key]['quantity'] : 0,
                'new_quantity' => $products[$key]['quantity'],
                'existing_cost' => isset($existing_cost[$key]['cost_price']) ? $existing_cost[$key]['cost_price'] : 0,
                'new_cost' => $products[$key]['price'],
                'total_existing_cost' => isset($existing_cost[$key]['total_existing_cost']) ? $existing_cost[$key]['total_existing_cost'] : 0,
                'total_new_cost' => ($products[$key]['quantity'] * $products[$key]['price']),
                'quantity' => (($p['quantity']) + (isset($existing_cost[$key]['quantity']) ? $existing_cost[$key]['quantity'] : 0)),
                'expiry_date' => $products[$key]['expiry_date'],
                'store_id' => $products[$key]['store_id'],
            ];
        }

        $store_products = $product_costs = $batch = [];
        foreach($records as $key=>$record){
            $product_costs[] = [
                'branch_id' => $branch_id,
                'product_id' => $key,
                'cost_price' => round(($record['total_existing_cost'] + $record['total_new_cost']) / $record['quantity'],2),
                'updated_by' => $user->id
            ];
            $store_products[] = [
                'store_id'=>$record['store_id'],
                'product_id'=>$key,
                'qty_available'=>$record['quantity']
            ];
            $batch[] = [
                'batch_no' => $batch_no,
                'existing_quantity' => $record['existing_quantity'],
                'new_quantity' => $record['new_quantity'],
                'total_quantity' => $record['quantity'],
                'existing_cost_price' => $record['existing_cost'],
                'new_cost_price' => $record['new_cost'],
                'created_by' => $user->id,
                'expiry_date' => $record['expiry_date'],
            ];
        }

        DB::beginTransaction();

        if(
            StoreProduct::upsert($store_products, ['store_id','product_id'])
            && BranchProductPrice::upsert($product_costs, ['branch_id', 'product_id'])
            && StoreProductBatch::upsert($batch, ['batch_no'])
        ){
            DB::commit();
            return ['status'=>true, 'message'=>'success'];
        }else{
            return ['status'=>false, 'message'=>'Something went wrong.'];
        }

    }
}
