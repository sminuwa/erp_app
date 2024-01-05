<?php

namespace App\Classes;

use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountControl;
use App\Models\GeneralAccountLedger;
use App\Models\IntersiteTransfer;
use App\Models\Order;
use App\Models\ProductUnitMeasure;
use App\Models\Purchase;
use App\Models\PurchaseExpense;
use App\Models\StockAdjustment;
use App\Models\Store;
use App\Models\StoreProduct;
use App\Models\Supplier;

class Transaction
{


    public static function purchases($purchase_id, $date, $type = 'GRN')
    {
        /*
         * supplier payment
         * additional invoice payment
         * $formula A=Amount, Q = Quantity, W = weight
         * Find percentage of each total product prices
         * Add percentage of the total cost to total products price
         * New Cost Price = Divide the new total product price by number of quantity of each product
         * New cost price to be added to product categories
         *
         * */
        $user = auth()->user();
        $branch = $user->branch;

        $purchase_grn = Purchase::find($purchase_id);
        $supplier = $purchase_grn->supplier;
        $items = $purchase_grn->purchasedProducts;
        $expenses = $purchase_grn->expenses;
        $amount = $expense_amount = 0;
        $expense_suppliers = $product_categories = $product_amounts = $product_percentages = [];
        $products = $categories = [];
        foreach ($items as $item) {
            $product_categories[] = [
                'id' => $item->product_id,
                'category_id' => $item->product->category->id,
                'category_name' => $item->product->category->name,
                'asset_account_id' => $item->product->category->asset_account,
                'cost_account_id' => $item->product->category->cost_account,
                'amount' => ($item->unit_price * $item->quantity),
            ];
            $product_amounts[] = [
                'id' => $item->product_id,
                'amount' => ($item->unit_price * $item->quantity)
            ];
            $amount += ($item->unit_price * $item->quantity);
        }
        foreach ($product_categories as $key => $value) {
            if (isset($categories[$value['category_id']])) {
                $categories[$value['category_id']]['amount'] += $value['amount'];
                continue;
            }
            $categories[$value['category_id']] = [
                'category_id' => $value['category_id'],
                'category_name' => $value['category_name'],
                'asset_account_id' => $value['asset_account_id'],
                'cost_account_id' => $value['cost_account_id'],
                'amount' => $value['amount'],
            ];
        }

        $supplier_ledger = $asset_account = $cost_account = [];
        foreach ($categories as $cat) {
            $asset_account[] = [
                'model_id' => $cat['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => $purchase_grn->atc_no,
                'reference' => $purchase_grn->reference,
                'credit' => 0,
                'debit' => $cat['amount'],
                'date' => $purchase_grn->purchase_date,
                'user_id' => auth()->id(),
                'receipt_no' => 'A' . $purchase_grn->reference
            ];
        }
        $supplier_ledger[] = [
            'model_id' => $purchase_grn->supplier_id,
            'model_name' => 'Supplier',
            'branch_id' => $branch->id,
            'description' => $purchase_grn->atc_no,
            'reference' => $purchase_grn->reference,
            'credit' => $amount,
            'debit' => 0,
            'date' => $purchase_grn->purchase_date,
            'user_id' => auth()->id(),
            'receipt_no' => 'S' . $purchase_grn->reference
        ];

        $general_account_ledger = array_merge($asset_account, $cost_account, $supplier_ledger);
        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }
        return ['status' => false, 'message' => 'Something went wrong.'];

    }

    public static function intersite_post($intersite_id, $type = 'ITS')
    {
        /*
         * supplier payment
         * additional invoice payment
         * $formula A=Amount, Q = Quantity, W = weight
         * Find percentage of each total product prices
         * Add percentage of the total cost to total products price
         * New Cost Price = Divide the new total product price by number of quantity of each product
         * New cost price to be added to product categories
         *
         * */
        $user = auth()->user();
        $branch = $user->branch;
        $intersite = IntersiteTransfer::find($intersite_id);
        $control_account = GeneralAccountControl::where('code', 'intersite')->first();
        $items = $intersite->products;

        $amount = $expense_amount = 0;
        $product_categories = $product_amounts = [];
        $products = $categories = [];
        foreach ($items as $item) {
            $product_categories[] = [
                'id' => $item->product_id,
                'category_id' => $item->product->category->id,
                'category_name' => $item->product->category->name,
                'asset_account_id' => $item->product->category->asset_account,
                'cost_account_id' => $item->product->category->cost_account,
                'amount' => ($item->cost_price * $item->quantity),
            ];
            $product_amounts[] = [
                'id' => $item->product_id,
                'amount' => ($item->cost_price * $item->quantity)
            ];
            $amount += ($item->cost_price * $item->quantity);
        }

        foreach ($product_categories as $key => $value) {
            if (isset($categories[$value['category_id']])) {
                $categories[$value['category_id']]['amount'] += $value['amount'];
                continue;
            }
            $categories[$value['category_id']] = [
                'category_id' => $value['category_id'],
                'category_name' => $value['category_name'],
                'asset_account_id' => $value['asset_account_id'],
                'cost_account_id' => $value['cost_account_id'],
                'amount' => $value['amount'],
            ];
        }

        $control_account_ledger = $asset_account = $cost_account = [];
        foreach ($categories as $cat) {
            $asset_account[] = [
                'model_id' => $cat['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => $intersite->reference,
                'reference' => $intersite->reference,
                'credit' => $cat['amount'],
                'debit' => 0,
                'date' => $intersite->date,
                'user_id' => auth()->id(),
                'receipt_no' => 'A' . $intersite->reference
            ];
        }

        $control_account_ledger[] = [
            'model_id' => $control_account->general_account_id,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => $intersite->reference,
            'reference' => $intersite->reference,
            'credit' => 0,
            'debit' => $amount,
            'date' => $intersite->date,
            'user_id' => auth()->id(),
            'receipt_no' => 'S' . $intersite->reference
        ];

        $general_account_ledger = array_merge($asset_account, $cost_account, $control_account_ledger);
        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }
        return ['status' => false, 'message' => 'Something went wrong.'];

    }

    public static function intersite_receive($intersite_id, $type = 'ITS')
    {
        /*
         * supplier payment
         * additional invoice payment
         * $formula A=Amount, Q = Quantity, W = weight
         * Find percentage of each total product prices
         * Add percentage of the total cost to total products price
         * New Cost Price = Divide the new total product price by number of quantity of each product
         * New cost price to be added to product categories
         *
         * */
        $user = auth()->user();
        $branch = $user->branch;
        $intersite = IntersiteTransfer::find($intersite_id);
        $control_account = GeneralAccountControl::where('code', 'intersite')->first();
        $items = $intersite->products;

        $amount = $expense_amount = 0;
        $product_categories = $product_amounts = [];
        $products = $categories = [];
        foreach ($items as $item) {
            $product_categories[] = [
                'id' => $item->product_id,
                'category_id' => $item->product->category->id,
                'category_name' => $item->product->category->name,
                'asset_account_id' => $item->product->category->asset_account,
                'cost_account_id' => $item->product->category->cost_account,
                'amount' => ($item->cost_price * $item->quantity),
            ];
            $product_amounts[] = [
                'id' => $item->product_id,
                'amount' => ($item->cost_price * $item->quantity)
            ];
            $amount += ($item->cost_price * $item->quantity);
        }

        foreach ($product_categories as $key => $value) {
            if (isset($categories[$value['category_id']])) {
                $categories[$value['category_id']]['amount'] += $value['amount'];
                continue;
            }
            $categories[$value['category_id']] = [
                'category_id' => $value['category_id'],
                'category_name' => $value['category_name'],
                'asset_account_id' => $value['asset_account_id'],
                'cost_account_id' => $value['cost_account_id'],
                'amount' => $value['amount'],
            ];
        }

        $control_account_ledger = $asset_account = $cost_account = [];
        foreach ($categories as $cat) {
            $asset_account[] = [
                'model_id' => $cat['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => 'R' . $intersite->reference,
                'reference' => 'R' . $intersite->reference,
                'credit' => 0,
                'debit' => $cat['amount'],
                'date' => $intersite->date,
                'user_id' => auth()->id(),
                'receipt_no' => 'A' . 'R' . $intersite->reference
            ];
        }

        $control_account_ledger[] = [
            'model_id' => $control_account->general_account_id,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => 'R' . $intersite->reference,
            'reference' => 'R' . $intersite->reference,
            'credit' => $amount,
            'debit' => 0,
            'date' => $intersite->date,
            'user_id' => auth()->id(),
            'receipt_no' => 'S' . 'R' . $intersite->reference
        ];

        $general_account_ledger = array_merge($asset_account, $cost_account, $control_account_ledger);
        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }
        return ['status' => false, 'message' => 'Something went wrong.'];

    }

    public static function stock_adjustment($stock_adjustment_id)
    {
        /*
         * supplier payment
         * additional invoice payment
         * $formula A=Amount, Q = Quantity, W = weight
         * Find percentage of each total product prices
         * Add percentage of the total cost to total products price
         * New Cost Price = Divide the new total product price by number of quantity of each product
         * New cost price to be added to product categories
         *
         * */
        $user = auth()->user();
        $branch = $user->branch;
        $stock = StockAdjustment::find($stock_adjustment_id);
        $control_account = GeneralAccountControl::where('code', 'Adjustment')->first();
        $items = $stock->products;
        $amount = $expense_amount = 0;
        $expense_suppliers = $product_categories = $product_amounts = $product_percentages = [];
        $products = $categories = [];
        foreach ($items as $item) {
            $product_categories[] = [
                'id' => $item->product_id,
                'category_id' => $item->product->category->id,
                'category_name' => $item->product->category->name,
                'asset_account_id' => $item->product->category->asset_account,
                'cost_account_id' => $item->product->category->cost_account,
                'amount' => ($item->cost_price * $item->quantity),
            ];
            $product_amounts[] = [
                'id' => $item->product_id,
                'amount' => ($item->cost_price * $item->quantity)
            ];
            $amount += ($item->cost_price * $item->quantity);
        }

        foreach ($product_categories as $key => $value) {
            if (isset($categories[$value['category_id']])) {
                $categories[$value['category_id']]['amount'] += $value['amount'];
                continue;
            }
            $categories[$value['category_id']] = [
                'category_id' => $value['category_id'],
                'category_name' => $value['category_name'],
                'asset_account_id' => $value['asset_account_id'],
                'cost_account_id' => $value['cost_account_id'],
                'amount' => $value['amount'],
            ];
        }
        $control_account_ledger = $asset_account = $cost_account = [];
        foreach ($categories as $cat) {
            $asset_account[] = [
                'model_id' => $cat['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => $stock->description,
                'reference' => $stock->reference,
                'credit' => ($stock->operation != 'in' ? $cat['amount'] : 0), // credit if out
                'debit' => ($stock->operation == 'in' ? $cat['amount'] : 0), // debit if in
                'date' => $stock->date,
                'user_id' => auth()->id(),
                'receipt_no' => 'A' . $stock->reference
            ];
        }
        $control_account_ledger[] = [
            'model_id' => $control_account->general_account_id,
            'model_name' => 'GeneralAccount',
            'branch_id' => $branch->id,
            'description' => $stock->description,
            'reference' => $stock->reference,
            'credit' => ($stock->operation == 'in' ? $amount : 0), // credit if in
            'debit' => ($stock->operation != 'in' ? $amount : 0), // debit if out
            'date' => $stock->date,
            'user_id' => auth()->id(),
            'receipt_no' => 'S' . ($stock->operation == 'in' ? "IN_" : "OUT_") . $stock->reference
        ];

        $general_account_ledger = array_merge($asset_account, $cost_account, $control_account_ledger);
        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }
        return ['status' => false, 'message' => 'Something went wrong.'];

    }

    public static function purchase_invoices($purchase_id, $invoice_id, $formula = 'A')
    {
        /*
         * supplier payment
         * additional invoice payment
         * $formula A=Amount, Q = Quantity, W = weight
         * Find percentage of each total product prices
         * Add percentage of the total cost to total products price
         * New Cost Price = Divide the new total product price by number of quantity of each product
         * New cost price to be added to product categories
         *
         * */
        $user = auth()->user();
        $branch = $user->branch;
        $purchase_grn = Purchase::find($purchase_id);
        $invoice = PurchaseExpense::find($invoice_id);
        $total_grn = intval($purchase_grn->totalProductCost()->total);
        $total_expenses = $purchase_grn->total_expense();
        $supplier = $invoice->supplier;
        $items = $purchase_grn->purchasedProducts;
        $expenses = $purchase_grn->expenses;

        $amount = $expense_amount = 0;
        $percentages = $expense_suppliers = $product_categories = $product_amounts = $product_percentages = [];
        $products = $categories = [];
        foreach ($items as $item) {
            $product_categories[] = [
                'id' => $item->product_id,
                'category_id' => $item->product->category->id,
                'category_name' => $item->product->category->name,
                'asset_account_id' => $item->product->category->asset_account,
                'cost_account_id' => $item->product->category->cost_account,
                'amount' => ($item->unit_price * $item->quantity),
            ];
            $product_amounts[] = [
                'id' => $item->product_id,
                'amount' => ($item->unit_price * $item->quantity)
            ];
            $amount += ($item->unit_price * $item->quantity);
        }

        foreach ($product_categories as $key => $value) {
            if (isset($categories[$value['category_id']])) {
                $categories[$value['category_id']]['amount'] += $value['amount'];
                continue;
            }
            $categories[$value['category_id']] = [
                'category_id' => $value['category_id'],
                'category_name' => $value['category_name'],
                'asset_account_id' => $value['asset_account_id'],
                'cost_account_id' => $value['cost_account_id'],
                'amount' => $value['amount'],
            ];
        }


        foreach ($categories as $key => $value) {
            $percent = ($value['amount'] / $total_grn) * 100;
            $percentages[$key] = [
                'category_id' => $value['category_id'],
                'category_name' => $value['category_name'],
                'asset_account_id' => $value['asset_account_id'],
                'cost_account_id' => $value['cost_account_id'],
                'amount' => $value['amount'],
                'invoice_amount' => $invoice->amount,
                'percentage' => $percent,
                'total_amount' => ($value['amount'] / $total_grn) * $invoice->amount
            ];
        }

        $supplier_ledger = $asset_account = $cost_account = [];
        foreach ($percentages as $per) {
            $asset_account[] = [
                'model_id' => $per['asset_account_id'],
                'model_name' => 'GeneralAccount',
                'branch_id' => $branch->id,
                'description' => $invoice->description,
                'reference' => $invoice->reference,
                'credit' => 0,
                'debit' => $per['total_amount'],
                'date' => $invoice->date,
                'user_id' => auth()->id(),
                'receipt_no' => 'A' . $invoice->reference
            ];
        }
        $supplier_ledger[] = [
            'model_id' => $invoice->supplier_id,
            'model_name' => 'Supplier',
            'branch_id' => $branch->id,
            'description' => $invoice->description,
            'reference' => $invoice->reference,
            'credit' => $invoice->amount,
            'debit' => 0,
            'date' => $invoice->date,
            'user_id' => auth()->id(),
            'receipt_no' => 'S' . $invoice->reference
        ];

        $general_account_ledger = array_merge($asset_account, $cost_account, $supplier_ledger);
        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }

    }

    public static function sale(array $store_product, int $customer_id, string $reference, $date)
    {
        /*
         *
         * $products = [
         *      2=>[
         *          'quantity'=20,
         *          'cost_price'=>3043,
         *          'sold_price'=>3000
         *         ],
         *      5=>[
         *          'quantity'=>20,
         *          'cost_price'=>3043,
         *          'sold_price'=>3043
         *         ]
         * ]
         * */
        //branch
        //category
        //GLs tied to each category (Asset account, Cost Of Sale Account, Revenue Account, Customer Account)
        //Asset (Debit) based on unit cost price
        //Cost of Sale (Credit) based on unit cost price
        //Revenue (Credit) based on unit sale price
        //Customer (Debit) based on unit sale price
        //unit cost price
        //unit sale price
        //dd($store_product);
        try {
            $store_product_ids = $quantities = $sold_prices = $refund_account = $discount_account = [];
            foreach ($store_product as $key => $value) {
                $store_product_ids[] = $key;
                $quantities[] = $value['quantity'];
                $sold_prices[] = $value['sold_price'];
                $cost_prices[] = $value['cost_price'];
            }
            //            return $sold_prices;
            $customer = Customer::find($customer_id);
            $records = StoreProduct::
                whereIn('store_products.id', $store_product_ids)
                ->join('stores', 'stores.id', 'store_products.store_id')
                ->join('products', 'products.id', 'store_products.product_id')
                ->join('categories', 'categories.id', 'products.category_id')
                ->join('branches', 'branches.id', 'stores.branch_id')
                ->join('branch_product_prices', 'branch_product_prices.branch_id', 'branches.id')
                ->selectRaw("
                    store_products.id AS store_product_id,
                    branches.id AS branch_id,
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
            //            return $records;
            $asset_account = $cost_account = $revenue_account = $customer_ledger = [];
            $customer_value = $branch_id = 0;
            $order = Order::where('reference', $reference)->first();
            foreach ($records as $record) {
                // total value based on cost price
                // total value based on sale price
                $cost_value = ($store_product[$record->store_product_id]['cost_price'] * $store_product[$record->store_product_id]['quantity']);
                $sell_value = ($store_product[$record->store_product_id]['sold_price'] * $store_product[$record->store_product_id]['quantity']);
                $customer_value = ($customer_value + $sell_value);
                $branch_id = $record->branch_id;
                $asset_account[] = [
                    'model_id' => $record->asset_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => $order->description ?? 'Sale of ' . $record->category_name,
                    'reference' => $reference,
                    'credit' => $cost_value,
                    'debit' => 0,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'A' . $reference
                ];
                $cost_account[] = [
                    'model_id' => $record->cost_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => $order->description ?? 'Sale of ' . $record->category_name,
                    'reference' => $reference,
                    'credit' => 0,
                    'debit' => $cost_value,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'C' . $reference
                ];
                $revenue_account[] = [
                    'model_id' => $record->revenue_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => $order->description ?? 'Sale of ' . $record->category_name,
                    'reference' => $reference,
                    'credit' => $sell_value,
                    'debit' => 0,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'R' . $reference
                ];
            }

            //Check if customer has discount or refund
            $order = Order::where(['reference' => $reference, 'customer_id' => $customer->id])->first();
            $discount = 0;
            $refund = 0;
            if ($order != null) {
                if ($order->discount > 0) {
                    $discount = $order->discount;
                    $model = GeneralAccountControl::where('code', 'discount')->first();
                    $discount_account[] = [
                        'model_id' => $model->general_account_id,
                        'model_name' => 'GeneralAccount',
                        'branch_id' => $branch_id,
                        'description' => 'Discount for ' . $customer->name,
                        'reference' => $reference,
                        'credit' => 0,
                        'debit' => $order->discount,
                        'date' => $date,
                        'user_id' => auth()->id(),
                        'receipt_no' => 'R' . $reference
                    ];
                }
                if ($order->refund > 0) {
                    $refund = $order->refund;
                    $model = GeneralAccountControl::where('code', 'refund')->first();
                    $refund_account[] = [
                        'model_id' => $model->general_account_id,
                        'model_name' => 'GeneralAccount',
                        'branch_id' => $branch_id,
                        'description' => 'Refund for customer ' . $customer->name,
                        'reference' => $reference,
                        'credit' => $order->refund,
                        'debit' => 0,
                        'date' => $date,
                        'user_id' => auth()->id(),
                        'receipt_no' => 'R' . $reference
                    ];
                }
            }
            $customer_ledger[] = [
                'model_id' => $customer->id,
                'model_name' => 'Customer',
                'branch_id' => $branch_id,
                'description' => $order->description ?? 'Sale Reference: ' . $reference,
                'reference' => $reference,
                'credit' => 0,
                'debit' => $customer_value - $discount + $refund,
                'date' => $date,
                'user_id' => auth()->id(),
                'receipt_no' => 'S' . $reference
            ];
            $general_account_ledger = array_merge($asset_account, $cost_account, $revenue_account, $customer_ledger, $discount_account, $refund_account);
            //            return $general_account_ledger;
            if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
                return ['status' => true, 'message' => 'success'];
            }
            return ['status' => false, 'message' => 'Something went wrong.'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }

    }

    public static function credit_note(array $store_product, int $customer_id, string $reference, $date)
    {
        /*
         *
         * $products = [
         *      2=>[
         *          'quantity'=20,
         *          'cost_price'=>3043,
         *          'sold_price'=>3000
         *         ],
         *      5=>[
         *          'quantity'=>20,
         *          'cost_price'=>3043,
         *          'sold_price'=>3043
         *         ]
         * ]
         * */
        //branch
        //category
        //GLs tied to each category (Asset account, Cost Of Sale Account, Revenue Account, Customer Account)
        //Asset (Debit) based on unit cost price
        //Cost of Sale (Credit) based on unit cost price
        //Revenue (Credit) based on unit sale price
        //Customer (Debit) based on unit sale price
        //unit cost price
        //unit sale price
        try {
            $store_product_ids = $quantities = $sold_prices = [];
            foreach ($store_product as $key => $value) {
                $store_product_ids[] = $key;
                $quantities[] = $value['quantity'];
                $sold_prices[] = $value['sold_price'];
                $cost_prices[] = $value['cost_price'];
            }
            //            return $sold_prices;
            $customer = Customer::find($customer_id);
            $records = StoreProduct::
                whereIn('store_products.id', $store_product_ids)
                ->join('stores', 'stores.id', 'store_products.store_id')
                ->join('products', 'products.id', 'store_products.product_id')
                ->join('categories', 'categories.id', 'products.category_id')
                ->join('branches', 'branches.id', 'stores.branch_id')
                ->join('branch_product_prices', 'branch_product_prices.branch_id', 'branches.id')
                ->selectRaw("
                    store_products.id AS store_product_id,
                    branches.id AS branch_id,
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
            //            return $records;
            $asset_account = $cost_account = $revenue_account = $customer_ledger = [];
            $customer_value = $branch_id = 0;
            foreach ($records as $record) {
                // total value based on cost price
                // total value based on sale price
                $cost_value = ($store_product[$record->store_product_id]['cost_price'] * $store_product[$record->store_product_id]['quantity']);
                $sell_value = ($store_product[$record->store_product_id]['sold_price'] * $store_product[$record->store_product_id]['quantity']);
                $customer_value = ($customer_value + $sell_value);
                $branch_id = $record->branch_id;
                $asset_account[] = [
                    'model_id' => $record->asset_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => 'Sale of ' . $record->category_name,
                    'reference' => $reference,
                    'credit' => 0,
                    'debit' => $cost_value,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'A' . $reference
                ];
                $cost_account[] = [
                    'model_id' => $record->cost_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => 'Sale of ' . $record->category_name,
                    'reference' => $reference,
                    'credit' => $cost_value,
                    'debit' => 0,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'C' . $reference
                ];
                $revenue_account[] = [
                    'model_id' => $record->cost_account_id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $record->branch_id,
                    'description' => 'Sale of ' . $record->category_name,
                    'reference' => $reference,
                    'credit' => 0,
                    'debit' => $sell_value,
                    'date' => $date,
                    'user_id' => auth()->id(),
                    'receipt_no' => 'R' . $reference
                ];
            }
            $customer_ledger[] = [
                'model_id' => $customer->id,
                'model_name' => 'Customer',
                'branch_id' => $branch_id,
                'description' => 'Sale Reference: ' . $reference,
                'reference' => $reference,
                'credit' => $customer_value,
                'debit' => 0,
                'date' => $date,
                'user_id' => auth()->id(),
                'receipt_no' => 'S' . $reference
            ];
            $general_account_ledger = array_merge($asset_account, $cost_account, $revenue_account, $customer_ledger);
            //            return $general_account_ledger;
            if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
                return ['status' => true, 'message' => 'success'];
            }
            return ['status' => false, 'message' => 'Something went wrong.'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }

    }

    public static function transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, $type = 'TRN')
    {
        $user = auth()->user();
        $branch = $user->branch;
        if (!$branch)
            return ['status' => false, 'message' => 'This user does not have a branch.'];
        $source_name == 'Customer' ?
            $source_model = Customer::find($source_id) :
            ($source_name == 'Supplier' ?
                $source_model = Supplier::find($source_id) :
                ($source_model = GeneralAccount::find($source_id)));
        $destination_name == 'Customer' ?
            $destination_model = Customer::find($destination_id) :
            ($destination_name == 'Supplier' ?
                $destination_model = Supplier::find($destination_id) :
                ($destination_model = GeneralAccount::find($destination_id)));

        $source_account[] = [
            'model_id' => $source_model->id,
            'model_name' => class_basename($source_model),
            'branch_id' => $branch->id,
            'description' => 'Receipt on behalf of ' . $reference,
            'reference' => $reference,
            'credit' => 0,
            'debit' => $amount,
            'date' => $date,
            'user_id' => $user->id,
            'receipt_no' => $type . $reference
        ];
        $destination_account[] = [
            'model_id' => $destination_model->id,
            'model_name' => class_basename($destination_model),
            'branch_id' => $branch->id,
            'description' => 'Receipt on behalf of ' . $reference,
            'reference' => $reference,
            'credit' => $amount,
            'debit' => 0,
            'date' => $date,
            'user_id' => $user->id,
            'receipt_no' => $type . $reference
        ];

        $general_account_ledger = array_merge($source_account, $destination_account);
        if (GeneralAccountLedger::upsert($general_account_ledger, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        }
        return ['status' => false, 'message' => 'Something went wrong.'];
    }

    public static function reversal($reference, $type = 'REVERSAL')
    {
        if (is_null($reference))
            return ['status' => false, 'message' => '$reference is null.'];
        $user = auth()->user();
        $general_ledgers = GeneralAccountLedger::forReference($reference)->get();
        if (count($general_ledgers) > 0) {
            $general_account_ledgers = [];
            foreach ($general_ledgers as $general_ledger) {
                $general_account_ledgers[] = [
                    'model_id' => $general_ledger->model_id,
                    'model_name' => $general_ledger->model_name,
                    'branch_id' => $general_ledger->branch_id,
                    'description' => 'Receipt on behalf of ' . $reference,
                    'reference' => $reference,
                    'credit' => $general_ledger->credit <= 0 ? $general_ledger->debit : 0,
                    'debit' => $general_ledger->debit <= 0 ? $general_ledger->credit : 0,
                    'date' => $general_ledger->date,
                    'user_id' => $user->id,
                    'receipt_no' => $type . '_' . $reference
                ];
            }
            if (GeneralAccountLedger::upsert($general_account_ledgers, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
                return ['status' => true, 'message' => 'success'];
            }
        } else {
            return ['status' => false, 'message' => 'No transaction found for .' . $reference];
        }
        return ['status' => false, 'message' => 'Something went wrong.'];
    }

    public static function receipt($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date)
    {
        return self::transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, 'RCT');
    }

    public static function payment($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date)
    {
        return self::transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, 'PAY');
    }

    public static function journal(array $accounts_details, $reference, $date, $type = 'JOURNAL')
    {
        /*
         * source and destination account structure
         * [
         *  [source_id=>1, model_name=>Customer, source_amount=>200],
         *  [source_id=>1, model_name=>Customer, source_amount=>200]
         * ]
         * Same as destination accounts
         * */
        $user = auth()->user();
        $branch = $user->branch;
        $general_account_ledgers = [];
        foreach ($accounts_details as $account) {
            $general_account_ledgers[] = [
                'model_id' => $account['account_id'],
                'model_name' => $account['account_type'],
                'branch_id' => $branch->id,
                'description' => 'Journal on behalf of ' . $reference,
                'reference' => $reference,
                'credit' => $account['credit'],
                'debit' => $account['debit'],
                'date' => $date,
                'user_id' => $user->id,
                'receipt_no' => $type . '_' . $reference
            ];
        }
        if (GeneralAccountLedger::upsert($general_account_ledgers, ['model_id', 'model_name', 'branch_id', 'receipt_no'])) {
            return ['status' => true, 'message' => 'success'];
        } else {
            return ['status' => false, 'message' => 'Something went wrong'];
        }
    }


    public static function debit_note()
    {

    }

    public static function return_debit()
    {

    }

    public static function expense($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date)
    {
        return self::transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, 'EXP');
    }

    public static function interbank($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date)
    {
        return self::transaction($source_id, $source_name, $destination_id, $destination_name, $amount, $reference, $date, 'ITB');
    }

    public static function check_transaction_limit($customer_id, $cart_contents)
    {
        $total_sales = 0;
        //This is to determine the total amount based on the selected unit, if different from base unit
        foreach ($cart_contents as $cart) {
            $store_product_id = $cart->id;
            $unit = $cart->attributes['unit'];
            $product = StoreProduct::find($store_product_id)->product;
            if ($product->unit == $unit) {
                $total_sales += $cart->price;
            } else {
                $quantity_sold = Transaction::quantity_sold($product->id, $cart->quantity, $unit);
                $total_sales += $quantity_sold * $cart->price;
            }
        }

        $credit_limit = Customer::find($customer_id)->credit_limit;
        $balance = Customer::find($customer_id)->runningBalance() - $total_sales;
        if ($balance <= 0 && ($credit_limit == 0 || $credit_limit == 1)) {
            return false;
        }
        if (($balance <= 0 && $credit_limit > 1 && (abs($balance)) > $credit_limit)) {
            return false;
        }
        if ($balance > 0 && $credit_limit > 1 && (abs($balance) > $credit_limit)) {

            return false;
        }
        return true;
    }
    public static function quantity_sold($product_id, $quantity, $unit)
    {
        //determine the quantity sold based on unit of measure
        $unit_of_measure = ProductUnitMeasure::where(['product_id' => $product_id, 'code' => $unit])->first();

        if ($unit_of_measure == null)
            return $quantity;

        if ($unit_of_measure->type == 'division')
            return ($quantity / $unit_of_measure->value);
        if ($unit_of_measure->type == 'multiple')
            return $quantity * $unit_of_measure->value;
        return $quantity;
    }

}
