<?php

namespace App\Classes;

use App\Models\BranchProductPrice;
use App\Models\Purchase;
use App\Models\PurchaseExpense;
use App\Models\StockCard;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\StoreProductBatch;
use Illuminate\Support\Facades\DB;


class CostPrice
{

    public static function returnDebitFormula(array $products, $batch_no, $store_id, $branch_id)
    {
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
        foreach ($products as $key => $p) {
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
        foreach ($prices as $price) {
            $existing_cost[$price->product_id] = ['cost_price' => $price->cost_price, 'quantity' => $price->quantity, 'total_existing_cost' => $price->total_existing_cost];
        }

        foreach ($products as $key => $p) {
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
        foreach ($records as $key => $record) {
            $product_costs[] = [
                'branch_id' => $branch_id,
                'product_id' => $key,
                'cost_price' => round(($record['total_existing_cost'] + $record['total_new_cost']) / $record['quantity'], 2),
                'updated_by' => $user->id
            ];
            $store_products[] = [
                'store_id' => $store_id,
                'product_id' => $key,
                'qty_available' => $record['quantity']
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

        if (
            StoreProduct::upsert($store_products, ['store_id', 'product_id'])
            && BranchProductPrice::upsert($product_costs, ['branch_id', 'product_id'])
            && StoreProductBatch::upsert($batch, ['batch_no'])
        ) {
            DB::commit();
            return ['status' => true, 'message' => 'success'];
        } else {
            return ['status' => false, 'message' => 'Something went wrong.'];
        }
    }

    public static function additionalInvoiceCostPrice($purchase_id, $invoice_id, $type = TRANSACTION_TYPE_GRN, $formula = "A")
    {

        /*
         * branch id of the destination store
         * array of product list as follows
         * [
         *  2 =>[
         *        quantity => 20,
         *        price => 300,
         *        store_id => 2,
         *        expiry = null
         *      ]
         *  5 =>[
         *        quantity => 65,
         *        price => 1300,
         *        store_id => 2,
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
        $purchase_grn = Purchase::find($purchase_id);
        $invoice = PurchaseExpense::find($invoice_id);
        $total_grn = intval($purchase_grn->totalProductCost()->total);
        $total_new_cost = $records = $products = [];
        $items = $purchase_grn->purchasedProducts;
        foreach ($items as $item) {
            $products[$item->product_id] = [
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
                'store_id' => $item->store_id,
                'expiry_date' => $item->expire_date,
            ];
        }


        foreach ($products as $key => $p) {
            $product_ids[] = $key;
            $total_new_cost[$key] = intval($p['quantity']) * intval($p['price']);
            $details = StoreProduct::selectRaw("
                store_products.product_id,
                qty_available,
                (SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $purchase_grn->branch_id limit 1) as cost_price,
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key) as quantity,
                ((SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $purchase_grn->branch_id limit 1) *
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key)) as total_existing_cost
                ")
                ->where('store_products.product_id', $key)->first();
            if ($details != null) {
                $prices[$details->product_id] = [
                    'product_id' => $details->product_id,
                    'grn_cost' => intval($p['quantity']) * intval($p['price']),
                    'cost_price' => $details->cost_price,
                    'qty_available' => $details->qty_available,
                    'total_quantity' => $details->quantity,
                    'total_existing_cost' => $details->total_existing_cost,
                ];
            }
        }

        foreach ($prices as $key => $value) {
            $percent = ($value['grn_cost'] / $total_grn) * 100;
            $percentages[$key] = [
                'product_id' => $value['product_id'],
                'invoice_amount' => $invoice->amount,
                'percentage' => $percent,
                'total_amount' => ($value['grn_cost'] / $total_grn) * $invoice->amount,
                'total_existing_cost' => $value['total_existing_cost'],
                'total_quantity' => $value['total_quantity'],
                'final_cost' => (
                    (($value['grn_cost'] / $total_grn) * $invoice->amount)
                    + $value['total_existing_cost'])
                    / $value['total_quantity'],
            ];
        }

        $store_products = $product_costs = $batch = $stock_card_param = [];
        foreach ($percentages as $key => $percentage) {

            $product_costs[] = [
                'branch_id' => $purchase_grn->branch_id,
                'product_id' => $key,
                'cost_price' => $percentage['final_cost'],
                'updated_by' => $user->id
            ];
        }


        DB::beginTransaction();

        if (BranchProductPrice::upsert($product_costs, ['branch_id', 'product_id'])) {
            if (Transaction::purchase_invoices($purchase_grn->id, $invoice->id, $formula)['status']) {
                DB::commit();
                return ['status' => true, 'message' => 'success'];
            }
        }
        return ['status' => false, 'message' => 'Something went wrong.'];

    }

    public static function additionalInvoiceCostPriceReversal($purchase_id, $invoice_id, $type = TRANSACTION_TYPE_GRN, $formula = "A")
    {

        /*
         * branch id of the destination store
         * array of product list as follows
         * [
         *  2 =>[
         *        quantity => 20,
         *        price => 300,
         *        store_id => 2,
         *        expiry = null
         *      ]
         *  5 =>[
         *        quantity => 65,
         *        price => 1300,
         *        store_id => 2,
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
        $purchase_grn = Purchase::find($purchase_id);
        $invoice = PurchaseExpense::find($invoice_id);
        $total_grn = intval($purchase_grn->totalProductCost()->total);
        $total_new_cost = $records = $products = [];
        $items = $purchase_grn->purchasedProducts;
        foreach ($items as $item) {
            $products[$item->product_id] = [
                'quantity' => $item->quantity,
                'price' => $item->unit_price,
                'store_id' => $item->store_id,
                'expiry_date' => $item->expire_date,
            ];
        }


        foreach ($products as $key => $p) {
            $product_ids[] = $key;
            $total_new_cost[$key] = intval($p['quantity']) * intval($p['price']);
            $details = StoreProduct::selectRaw("
                store_products.product_id,
                qty_available,
                (SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $purchase_grn->branch_id limit 1) as cost_price,
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key) as quantity,
                ((SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $purchase_grn->branch_id limit 1) *
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key)) as total_existing_cost
                ")
                ->where('store_products.product_id', $key)->first();
            $prices[$details->product_id] = [
                'product_id' => $details->product_id,
                'grn_cost' => intval($p['quantity']) * intval($p['price']),
                'cost_price' => $details->cost_price,
                'qty_available' => $details->qty_available,
                'total_quantity' => $details->quantity,
                'total_existing_cost' => $details->total_existing_cost,
            ];
        }

        foreach ($prices as $key => $value) {
            $percent = ($value['grn_cost'] / $total_grn) * 100;
            $percentages[$key] = [
                'product_id' => $value['product_id'],
                'invoice_amount' => $invoice->amount,
                'percentage' => $percent,
                'total_amount' => ($value['grn_cost'] / $total_grn) * $invoice->amount,
                'total_existing_cost' => $value['total_existing_cost'],
                'total_quantity' => $value['total_quantity'],
                'final_cost' => (($value['total_existing_cost']) - (($value['grn_cost'] / $total_grn) * $invoice->amount))
                    / $value['total_quantity'],
            ];
        }

        $store_products = $product_costs = $batch = $stock_card_param = [];
        foreach ($percentages as $key => $percentage) {

            $product_costs[] = [
                'branch_id' => $purchase_grn->branch_id,
                'product_id' => $key,
                'cost_price' => $percentage['final_cost'],
                'updated_by' => $user->id
            ];
        }


        DB::beginTransaction();

        if (BranchProductPrice::upsert($product_costs, ['branch_id', 'product_id'])) {
            if (Transaction::reversal($invoice->reference)['status']) {
                DB::commit();
                return ['status' => true, 'message' => 'success'];
            }
        }
        return ['status' => false, 'message' => 'Something went wrong.'];

    }

    public static function interstore(array $products, $batch_no, $branch_id, $date, $type = TRANSACTION_TYPE_INTERSTORE, $operation = 'in')
    {
        /*
         * branch id of the destination store
         * array of product list as follows
         * [
         *  2 =>[
         *        quantity => 20,
         *        price => 300,
         *        source_store_id => 2,
         *        destination_store_id => 2,
         *        expiry = null
         *      ]
         *  5 =>[
         *        quantity => 65,
         *        price => 1300,
         *        source_store_id => 2,
         *        destination_store_id => 2,
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
        //check if products are in the store if not add them
        $products_id = $stores_id = [];
        DB::beginTransaction();
        foreach ($products as $key => $value) {
            if (!StoreProduct::where(['product_id' => $key, 'store_id' => $value['destination_store_id']])->first()) {
                StoreProduct::create([
                    'product_id' => $key,
                    'store_id' => $value['destination_store_id'],
                    'qty_available' => 0,
                ]);
            }
        }
        DB::commit();
        $s = 'source';
        $d = 'destination';
        foreach ($products as $key => $p) {
            $product_ids[] = $key;
            $source_store_id = $p['source_store_id'];
            $destination_store_id = $p['destination_store_id'];
            $total_new_cost[$key] = intval($p['quantity']) * intval($p['price']);
            $source_details = StoreProduct::selectRaw("
                store_products.product_id,
                qty_available,
                (SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $branch_id limit 1) as cost_price,
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key) as quantity,
                ((SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $branch_id limit 1) *
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key)) as total_existing_cost
                ")
                ->where(['store_products.product_id' => $key, 'store_products.store_id' => $source_store_id])->first();
            $destination_details = StoreProduct::selectRaw("
                store_products.product_id,
                qty_available,
                (SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $branch_id limit 1) as cost_price,
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key) as quantity,
                ((SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $branch_id limit 1) *
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key)) as total_existing_cost
                ")
                ->where(['store_products.product_id' => $key, 'store_products.store_id' => $destination_store_id])->first();
            $prices[$s][$source_details->product_id] = [
                'product_id' => $source_details->product_id,
                'cost_price' => $source_details->cost_price,
                'qty_available' => $source_details->qty_available,
                'total_quantity' => $source_details->quantity,
                'total_existing_cost' => $source_details->total_existing_cost,
            ];
            $prices[$d][$destination_details->product_id] = [
                'product_id' => $destination_details->product_id,
                'cost_price' => $destination_details->cost_price,
                'qty_available' => $destination_details->qty_available,
                'total_quantity' => $destination_details->quantity,
                'total_existing_cost' => $destination_details->total_existing_cost,
            ];
        }


        foreach ($prices as $key => $price) {
            foreach ($price as $p) {
                $existing_cost[$key][$p['product_id']] = ['cost_price' => $p['cost_price'], 'quantity' => $p['total_quantity'], 'total_existing_cost' => $p['total_existing_cost']];
            }
        }

        foreach ($products as $key => $p) {
            //          return isset($existing_cost[$key]['quantity']) ? $existing_cost[$key]['quantity'] : 0;
            $records[$s][$key] = [
                'qty_available' => $prices[$s][$key]['qty_available'] ?? 0,
                'existing_quantity' => isset($existing_cost[$s][$key]['quantity']) ? $existing_cost[$s][$key]['quantity'] : 0,
                'new_quantity' => $products[$key]['quantity'],
                'existing_cost' => isset($existing_cost[$s][$key]['cost_price']) ? $existing_cost[$s][$key]['cost_price'] : 0,
                'new_cost' => $products[$key]['price'],
                'total_existing_cost' => isset($existing_cost[$s][$key]['total_existing_cost']) ? $existing_cost[$s][$key]['total_existing_cost'] : 0,
                'total_new_cost' => ($products[$key]['quantity'] * $products[$key]['price']),
                'quantity' => (($p['quantity']) + (isset($existing_cost[$s][$key]['quantity']) ? $existing_cost[$s][$key]['quantity'] : 0)),
                'expiry_date' => $products[$key]['expiry_date'],
                'store_id' => $products[$key]['source_store_id'],
            ];
            $records[$d][$key] = [
                'qty_available' => $prices[$d][$key]['qty_available'] ?? 0,
                'existing_quantity' => isset($existing_cost[$d][$key]['quantity']) ? $existing_cost[$d][$key]['quantity'] : 0,
                'new_quantity' => $products[$key]['quantity'],
                'existing_cost' => isset($existing_cost[$d][$key]['cost_price']) ? $existing_cost[$d][$key]['cost_price'] : 0,
                'new_cost' => $products[$key]['price'],
                'total_existing_cost' => isset($existing_cost[$d][$key]['total_existing_cost']) ? $existing_cost[$d][$key]['total_existing_cost'] : 0,
                'total_new_cost' => ($products[$key]['quantity'] * $products[$key]['price']),
                'quantity' => (($p['quantity']) + (isset($existing_cost[$d][$key]['quantity']) ? $existing_cost[$d][$key]['quantity'] : 0)),
                'expiry_date' => $products[$key]['expiry_date'],
                'store_id' => $products[$key]['destination_store_id'],
            ];
        }

        $source_store_products = $destination_store_products = $source_stock_card_param = $destination_stock_card_param = [];
        foreach ($records as $key => $record) {
            foreach ($record as $k => $r) {
                if ($key == $s) {
                    $source_stock_card_param[] = [
                        'store_id' => $r['store_id'],
                        'product_id' => $k,
                        'quantity' => $r['new_quantity'],
                        'operation' => 'out',
                    ];
                    $source_store_products[] = [
                        'store_id' => $r['store_id'],
                        'product_id' => $k,
                        'qty_available' => $r['qty_available'] - $r['new_quantity']
                    ];
                }
                if ($key == $d) {
                    $destination_stock_card_param[] = [
                        'store_id' => $r['store_id'],
                        'product_id' => $k,
                        'quantity' => $r['new_quantity'],
                        'operation' => 'in',
                    ];
                    $destination_store_products[] = [
                        'store_id' => $r['store_id'],
                        'product_id' => $k,
                        'qty_available' => $r['qty_available'] + $r['new_quantity']
                    ];
                    $batch[] = [
                        'batch_no' => $batch_no,
                        'store_id' => $r['store_id'],
                        'product_id' => $k,
                        'existing_quantity' => $r['qty_available'],
                        'new_quantity' => $r['new_quantity'],
                        'total_quantity' => $r['qty_available'] + $r['new_quantity'],
                        'existing_cost_price' => $r['existing_cost'],
                        'new_cost_price' => $r['existing_cost'],
                        'created_by' => $user->id,
                        'expiry_date' => $r['expiry_date'],
                    ];
                }

            }
        }
        $stock_card_param = array_merge($source_stock_card_param, $destination_stock_card_param);
        $store_products = array_merge($source_store_products, $destination_store_products);
        DB::beginTransaction();
        if (
            StockCard::createBatchRecord($stock_card_param, $batch_no, $date, $type)
            && StoreProduct::upsert($store_products, ['store_id', 'product_id'])
            && StoreProductBatch::upsert($batch, ['batch_no'])
        ) {
            DB::commit();
            return ['status' => true, 'message' => 'success'];
        } else {
            DB::rollBack();
            return ['status' => false, 'message' => 'Something went wrong.'];
        }

    }

    public static function newCostPrice(array $products, $batch_no, $branch_id, $date, $type = TRANSACTION_TYPE_OPENING_BALANCE, $operation = 'in')
    {
        /*
         * branch id of the destination store
         * array of product list as follows
         * [
         *  2 =>[
         *        quantity => 20,
         *        price => 300,
         *        store_id => 2,
         *        expiry = null
         *      ]
         *  5 =>[
         *        quantity => 65,
         *        price => 1300,
         *        store_id => 2,
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
        //check if products are in the store if not add them
        $products_id = $stores_id = [];
        DB::beginTransaction();
        foreach ($products as $key => $value) {
            if (!StoreProduct::where(['product_id' => $key, 'store_id' => $value['store_id']])->first()) {
                StoreProduct::create([
                    'product_id' => $key,
                    'store_id' => $value['store_id'],
                    'qty_available' => 0,
                ]);
            }
        }
        DB::commit();

        foreach ($products as $key => $p) {
            $product_ids[] = $key;
            $store_id = $p['store_id'];
            $total_new_cost[$key] = intval($p['quantity']) * intval($p['price']);
            $details = StoreProduct::selectRaw("
                store_products.product_id,
                qty_available,
                (SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $branch_id limit 1) as cost_price,
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key) as quantity,
                ((SELECT cost_price FROM branch_product_prices WHERE product_id = $key AND branch_id = $branch_id limit 1) *
                (SELECT sum(qty_available) as quantity FROM store_products WHERE product_id = $key)) as total_existing_cost
                ")
                ->where(['store_products.product_id' => $key, 'store_products.store_id' => $store_id])->first();
            $prices[$details->product_id] = [
                'product_id' => $details->product_id,
                'cost_price' => $details->cost_price,
                'qty_available' => $details->qty_available,
                'total_quantity' => $details->quantity,
                'total_existing_cost' => $details->total_existing_cost,
            ];
        }

        foreach ($prices as $price) {
            $existing_cost[$price['product_id']] = ['cost_price' => $price['cost_price'], 'quantity' => $price['total_quantity'], 'total_existing_cost' => $price['total_existing_cost']];
        }

        foreach ($products as $key => $p) {
            //          return isset($existing_cost[$key]['quantity']) ? $existing_cost[$key]['quantity'] : 0;
            $records[$key] = [
                'qty_available' => $prices[$key]['qty_available'] ?? 0,
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

        $store_products = $product_costs = $batch = $stock_card_param = [];
        if ($operation == 'in') {
            foreach ($records as $key => $record) {
                $stock_card_param[] = [
                    'store_id' => $record['store_id'],
                    'product_id' => $key,
                    'quantity' => $record['new_quantity'],
                    'operation' => $operation,
                ];
                $product_costs[] = [
                    'branch_id' => $branch_id,
                    'product_id' => $key,
                    'cost_price' => ($record['total_existing_cost'] + $record['total_new_cost']) / $record['quantity'],
                    'updated_by' => $user->id
                ];
                $store_products[] = [
                    'store_id' => $record['store_id'],
                    'product_id' => $key,
                    'qty_available' => $record['qty_available'] + $record['new_quantity']
                ];
                $batch[] = [
                    'batch_no' => $batch_no,
                    'store_id' => $record['store_id'],
                    'product_id' => $key,
                    'existing_quantity' => $record['qty_available'],
                    'new_quantity' => $record['new_quantity'],
                    'total_quantity' => $record['qty_available'] + $record['new_quantity'],
                    'existing_cost_price' => $record['existing_cost'],
                    'new_cost_price' => ($record['total_existing_cost'] + $record['total_new_cost']) / $record['quantity'],
                    'created_by' => $user->id,
                    'expiry_date' => $record['expiry_date'],
                ];
            }
            DB::beginTransaction();
            if (
                StockCard::createBatchRecord($stock_card_param, $batch_no, $date, $type)
                && StoreProduct::upsert($store_products, ['store_id', 'product_id'])
                && BranchProductPrice::upsert($product_costs, ['branch_id', 'product_id'])
                && StoreProductBatch::upsert($batch, ['batch_no'])
            ) {
                DB::commit();
                return ['status' => true, 'message' => 'success'];
            } else {
                DB::rollBack();
                return ['status' => false, 'message' => 'Something went wrong.'];
            }
        }
        if ($operation == 'out') {
            foreach ($records as $key => $record) {
                $stock_card_param[] = [
                    'store_id' => $record['store_id'],
                    'product_id' => $key,
                    'quantity' => $record['new_quantity'],
                    'operation' => $operation,
                ];
                $store_products[] = [
                    'store_id' => $record['store_id'],
                    'product_id' => $key,
                    'qty_available' => $record['qty_available'] - $record['new_quantity']
                ];
            }
            DB::beginTransaction();
            if (
                StockCard::createBatchRecord($stock_card_param, $batch_no, $date, $type)
                && StoreProduct::upsert($store_products, ['store_id', 'product_id'])
            ) {
                DB::commit();
                return ['status' => true, 'message' => 'success'];
            } else {
                DB::rollBack();
                return ['status' => false, 'message' => 'Something went wrong.'];
            }
        }
        return ['status' => false, 'message' => 'Something went wrong.'];
    }

}
