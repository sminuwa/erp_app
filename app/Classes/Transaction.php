<?php

namespace App\Classes;

use App\Models\Customer;
use App\Models\GeneralAccountLedger;
use App\Models\Store;
use App\Models\StoreProduct;

class Transaction
{


    public static function purchase(){

    }

    /*
     * $products = [2=>20,5=>10]
     * */
    public static function sale(array $store_product, int $customer_id, string $reference, $date){
        //branch
        //category
        //GLs tied to each category (Asset account, Cost Of Sale Account, Revenue Account, Customer Account)
        //Asset (Debit) based on unit cost price
        //Cost of Sale (Credit) based on unit cost price
        //Revenue (Credit) based on unit sale price
        //Customer (Debit) based on unit sale price
        //unit cost price
        //unit sale price
        try{
            $store_product_ids = $quantities = [];
            foreach($store_product as $key=>$value){
                $store_product_ids[] = $key;
                $quantities[] = $value;
            }
            $customer = Customer::find($customer_id);
            $records = StoreProduct::
            whereIn('store_products.id',$store_product_ids)
                ->join('stores', 'stores.id', 'store_products.store_id')
                ->join('products', 'products.id', 'store_products.product_id')
                ->join('categories', 'categories.id', 'products.category_id')
                ->join('branches', 'branches.id', 'stores.branch_id')
                ->join('branch_product_prices', 'branch_product_prices.branch_id', 'branches.id')
                ->selectRaw("
                    store_products.id AS store_product_id,
                    branches.id AS branch_id,
                    (SELECT cost_price FROM branch_product_prices WHERE branch_id = branches.id AND product_id = products.id) as cost_price,
                    (SELECT selling_price FROM branch_product_prices WHERE branch_id = branches.id AND product_id = products.id) as sale_price,
                    categories.name AS category_name,
                    (SELECT id FROM general_accounts WHERE id = categories.asset_account LIMIT 1) AS asset_account_id,
                    (SELECT number FROM general_accounts WHERE id = categories.asset_account LIMIT 1) AS asset_account_number,
                    (SELECT id FROM general_accounts WHERE id = categories.cost_account LIMIT 1) AS cost_account_id,
                    (SELECT number FROM general_accounts WHERE id = categories.cost_account LIMIT 1) AS cost_account_number,
                    (SELECT id FROM general_accounts WHERE id = categories.revenue_account LIMIT 1) AS revenue_account_id,
                    (SELECT number FROM general_accounts WHERE id = categories.revenue_account LIMIT 1) AS revenue_account_number
                ")
                ->groupBy('store_products.id')
                ->get();
            $asset_account = $cost_account = $revenue_account = $customer_ledger = [];
            $customer_value = $branch_id = 0;
            foreach($records as $record){
                // total value based on cost price
                // total value based on sale price
                $cost_value = ($record->cost_price * $store_product[$record->store_product_id]);
                $sell_value = ($record->sale_price * $store_product[$record->store_product_id]);
                $customer_value = ($customer_value+$sell_value);
                $branch_id = $record->branch_id;
                $asset_account[] = [
                    'model_id' => $record->asset_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => 'Sale of '.$record->category_name,
                    'reference' => $reference,
                    'credit' => $cost_value,
                    'debit' => 0,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'A'.$reference
                ];
                $cost_account[] = [
                    'model_id' => $record->cost_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => 'Sale of '.$record->category_name,
                    'reference' => $reference,
                    'credit' => 0,
                    'debit' => $cost_value,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'C'.$reference
                ];
                $revenue_account[] = [
                    'model_id' => $record->cost_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => 'Sale of '.$record->category_name,
                    'reference' => $reference,
                    'credit' => $sell_value,
                    'debit' => 0,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'R'.$reference
                ];
            }
            $customer_ledger[] = [
                'model_id' => $customer->id,
                'model_name' => 'Customer',
                'branch_id' => $branch_id,
                'description' => 'Sale Reference: '.$reference,
                'reference' => $reference,
                'credit' => 0,
                'debit' => $customer_value,
                'date' => $date,
                'user_id' => auth()->id(),
                'receipt_no' => 'S'.$reference
            ];
            $general_account_ledger = array_merge($asset_account, $cost_account, $revenue_account, $customer_ledger);
//            return $general_account_ledger;
            if(GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])){
                return ['status'=>true, 'message'=>'success'];
            }
            return ['status'=>false, 'message'=>'Something went wrong.'];
        }catch(\Exception $e){
            return ['status'=>false, 'message'=>$e->getMessage()];
        }

    }

    public static function receipt(){

    }

    public static function payment(){

    }

    public static function journal(){

    }

    public static function credit_note(){

    }

    public static function debit_note(){

    }

    public static function return_debit(){

    }

    public static function expense(){

    }

    public static function interbank(){

    }




}
