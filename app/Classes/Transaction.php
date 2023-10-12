<?php

namespace App\Classes;

use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\Supplier;

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

    public static function transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, $type='TRN'){
        $user = auth()->user();
        $branch = $user->branch;
        if(!$branch)
            return ['status'=>false, 'message'=>'This user does not have a branch.'];
        $source_name == 'Customer' ?
            $source_model = Customer::find($source_id) :
            ($source_name == 'Supplier' ?
                $source_model = Supplier::find($source_id) :
                    ($source_model = GeneralAccount::find($source_id) ));
        $destination_name == 'Customer' ?
            $destination_model = Customer::find($destination_id) :
            ($destination_name == 'Supplier' ?
                $destination_model = Supplier::find($destination_id) :
                ($destination_model = GeneralAccount::find($destination_id) ));

        $source_account[] = [
            'model_id' => $source_model->id,
            'model_name' => class_basename($source_model),
            'branch_id' => $branch->id,
            'description' => 'Receipt on behalf of '.$reference,
            'reference' => $reference,
            'credit' => 0,
            'debit' => $amount,
            'date' => $date,
            'user_id' => $user->id,
            'receipt_no' => $type.$reference
        ];
        $destination_account[] = [
            'model_id' => $destination_model->id,
            'model_name' => class_basename($destination_model),
            'branch_id' => $branch->id,
            'description' => 'Receipt on behalf of '.$reference,
            'reference' => $reference,
            'credit' => $amount,
            'debit' => 0,
            'date' => $date,
            'user_id' => $user->id,
            'receipt_no' => $type.$reference
        ];

        $general_account_ledger = array_merge($source_account, $destination_account);
        if(GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])){
            return ['status'=>true, 'message'=>'success'];
        }
        return ['status'=>false, 'message'=>'Something went wrong.'];
    }

    public static function reversal($reference, $type = 'REVERSAL'){
        if(is_null($reference))
            return ['status'=>false, 'message'=>'Reference can not be null.'];
        $user = auth()->user();
        $general_ledgers = GeneralAccountLedger::forReference($reference)->get();
        $general_account_ledgers = [];
        foreach($general_ledgers as $general_ledger){
            $general_account_ledgers[] = [
                'model_id' => $general_ledger->model_id,
                'model_name' => $general_ledger->model_name,
                'branch_id' => $general_ledger->branch_id,
                'description' => 'Receipt on behalf of '.$reference,
                'reference' => $reference,
                'credit' => $general_ledger->credit <= 0 ? $general_ledger->debit : 0,
                'debit' => $general_ledger->debit <= 0 ? $general_ledger->credit : 0,
                'date' => $general_ledger->date,
                'user_id' => $user->id,
                'receipt_no' => $type.'_'.$reference
            ];
        }
        if(GeneralAccountLedger::upsert($general_account_ledgers, ['model_id', 'model_name', 'branch_id', 'receipt_no'])){
            return ['status'=>true, 'message'=>'success'];
        }
        return ['status'=>false, 'message'=>'Something went wrong.'];
    }

    public static function receipt($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date){
        return self::transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, 'RCT');
    }

    public static function payment($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date){
        return self::transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, 'PAY');
    }

    public static function journal(array $source_accounts, array $destination_account, $reference, $date, $type = 'JOURNAL' ){
        /*
         * source and destination account structure
         * [
         *  [source_id=>1, model_name=>Customer, source_amount=>200],
         *  [source_id=>1, model_name=>Customer, source_amount=>200]
         * ]
         * Same as destination accounts
         * */

        foreach($source_accounts as $source){
            return $source;
        }
    }

    public static function credit_note(){

    }

    public static function debit_note(){

    }

    public static function return_debit(){

    }

    public static function expense($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date){
        return self::transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, 'EXP');
    }

    public static function interbank($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date){
        return self::transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, 'ITB');
    }




}
