<?php

namespace App\Http\Controllers;

use App\Imports\Upload;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentDetail;
use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Http\Request;

class OpeningBalanceController extends Controller
{
    //
    public function customerBalance(Request $request){
        $method = $request->method();
        $user = auth()->user();
        $user_id = $user->id;
        $branch = $user->branch;
        if($method == 'POST'){
            $file = $request->file;
            $date = $request->date;
            $branch_id = $request->branch_id;
            $rows = $this->getRecordFromExcel(Upload::class, $file);
            $title = $rows[0];
            $code = searchIndex($title, 'code'); // code/account number
            $debit = searchIndex($title, 'debit'); //serial number
            $credit = searchIndex($title, 'credit'); //serial number
            $records = [];
            $all_codes = [];
            $all_credit = [];
            $all_debit = [];
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $all_codes[]= $value[$code];
                if (trim($value[$code]) == null)
                    continue;
            }
            $customers = Customer::select('id', 'code')->whereIn('code',$all_codes)->get()->toArray();
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $customer = searchForId(trim($value[$code]), $customers);
                if(!$customer)
                    continue;
                if (trim($value[$code]) == null)
                    continue;
                $all_credit[] = str_replace(',','',trim($value[$credit]));
                $all_debit[] = str_replace(',','',trim($value[$debit]));
                $records[] = [
                    'model_id' => $customer->id,
                    'model_name' => 'Customer',
                    'branch_id' => $branch_id,
                    'description' => 'OPENING BALANCE',
                    'reference' => 'OPENINGBALANCE',
                    'credit' => str_replace(',','',$value[$credit]),
                    'debit' => str_replace(',','',$value[$debit]),
                    'date' => $date,
                    'user_id' => $user_id,
                    'receipt_no' => 'OPENINGBALANCE',
                ];
            }

            return $records;

            /*$income = array_sum($all_credit) - array_sum($all_debit);
            return $income;*/
            if(GeneralAccountLedger::upsert($records,['model_id', 'model_name', 'branch_id'])) {
                return view('pages.opening-balance.customer-balance', compact('records'));
            }
            return back()->with('error', 'Something went wrong');
        }

        return view('pages.opening-balance.customer-balance');
    }

    public function customerLimit(Request $request){
        $method = $request->method();
        $user = auth()->user();
        $branch = $user->branch;
        if($method == 'POST'){
            $file = $request->file;
            $date = $request->date;
            $branch_id = $request->branch_id;
            $rows = $this->getRecordFromExcel(Upload::class, $file);
            $title = $rows[0];
            $code = searchIndex($title, 'code'); // code/account number
            $limit = searchIndex($title, 'limit'); //serial number
            $records = [];
            $all_codes = [];
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $all_codes[]= $value[$code];
            }
            $customers = Customer::select('id', 'code')->whereIn('code',$all_codes)->get()->toArray();
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $customer = searchForId(trim($value[$code]), $customers);
                if (!$customer)
                    continue;
                $records[] = [
                    'id' => $customer->id,
                    'credit_limit' => $value[$limit],
                ];

            }

            if(Customer::upsert($records,['id'])) {
                return view('pages.opening-balance.customer-limit', compact('records'));
            }
            return back()->with('error', 'Something went wrong');
        }

        return view('pages.opening-balance.customer-limit');
    }

    public function inventoryValuation(Request $request){
        $method = $request->method();
        $user = auth()->user();
        $branch = $user->branch;
        if($method == 'POST'){
            $file = $request->file;
            $date = $request->date;
            $branch_id = $request->branch_id;
            $branch = Branch::find($branch_id);
            $rows = $this->getRecordFromExcel(Upload::class, $file);
            $title = $rows[0];
            $product = searchIndex($title, 'product'); // code/account number
            $store = searchIndex($title, 'store'); //serial number
            $quantity = searchIndex($title, 'quantity'); //serial number
            $cost = searchIndex($title, 'cost'); //serial number
            $product_store = $stock_card = $unique_stores = [];
            $all_products = $all_stores = [];
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                if (is_null($value[$store]))
                    continue;
                if (is_null($value[$product]))
                    continue;
                $all_products[] = $value[$product];
                $all_stores[]= $value[$store];
                if(!in_array(trim($value[$store]),$unique_stores))
                    $unique_stores[] = trim($value[$store]);
            }
//            return $unique_stores;
            $records = [];
            $products = Product::select('id','code')->whereIn('code',$all_products)->get()->toArray();
            $stores = Store::select('id','code')->whereIn('code',$unique_stores)->get()->toArray();
            foreach($unique_stores as $s){
                foreach ($rows as $key => $value) {
                    if ($key < 1)
                        continue; // skip first and second row
                    $pr = searchForId(trim($value[$product]), $products);
                    $st = searchForId(trim($value[$store]), $stores);
                    if (!$st || !$pr)
                        continue;
                    if($value[$store] == $s) {
                        $records[$s][] = [
                            'store_id' => $st->id,
                            'product_id' => $pr->id,
                            'quantity' => $value[$quantity],
                            'cost_price' => $value[$cost],
                        ];
                    }
                }
            }

            $stock_adjustments = $stock_adjustment_details = $stock_references = [];
            foreach($records as $k => $val){
                $reference = 'STK'.date('ymdHis').$k;
                $records[$k] = [
                    'reference' =>$reference,
                    'details'=>$val
                ];
                $stock_references[] = $reference;
                $stock_adjustments[] = [
                    'reference'=>$reference,
                    'branch_id'=>$branch_id,
                    'date'=>$date,
                    'operation'=>'in',
                    'description' => $branch->code.'Opening Balance',
                    'status'=>0,
                    'created_by'=>$user->id
                ];

            }

            if(StockAdjustment::upsert($stock_adjustments, ['reference', 'branch_id'])){
                $stock_ad = StockAdjustment::select('id', 'reference')->whereIn('reference', $stock_references)->get()->toArray();
                foreach($records as $k => $val){
                    $stock_adjust = searchForId(trim($val['reference']), $stock_ad);
                    foreach($val['details'] as $details){
                        $stock_adjustment_details[]= [
                            'stock_adjustment_id' => $stock_adjust->id,
                            'store_id' => $details['store_id'],
                            'product_id' => $details['product_id'],
                            'quantity' => $details['quantity'],
                            'cost_price' => $details['cost_price'],
                            'expiry_date' => null,
                        ];
                    }

                }
                if(StockAdjustmentDetail::upsert($stock_adjustment_details,['stock_adjustment_id', 'store_id','product_id','quantity'])){
                    return view('pages.opening-balance.inventory-valuation', compact('records'));
                }
            }

            return back()->with('error', 'Something went wrong');
        }

        return view('pages.opening-balance.inventory-valuation');
    }

    public function accountLedger(Request $request){
        $method = $request->method();
        $user = auth()->user();
        $branch = $user->branch;
        if($method == 'POST'){
            $file = $request->file;
            $date = $request->date;
            $branch_id = $request->branch_id;
            $rows = $this->getRecordFromExcel(Upload::class, $file);
            $title = $rows[0];
            $code = searchIndex($title, 'account'); // code/account number
            $debit = searchIndex($title, 'debit'); //serial number
            $credit = searchIndex($title, 'credit'); //serial number
            $records = [];
            $all_codes = [];
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $all_codes[]= $value[$code];
            }
            $accounts = GeneralAccount::select('id', 'number')->whereIn('number',$all_codes)->get()->toArray();
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $account = searchForId(trim($value[$code]), $accounts);
                if(!$account)
                    continue;
                $records[] = [
                    'model_id' => $account->id,
                    'model_name' => 'GeneralAccount',
                    'branch_id' => $branch_id,
                    'description' => 'OPENING BALANCE',
                    'reference' => 'OPENINGBALANCE',
                    'credit' => str_replace(',','',$value[$credit]),
                    'debit' => str_replace(',','',$value[$debit]),
                    'date' => $date,
                    'user_id' => $user->id,
                    'receipt_no' => 'OPENINGBALANCE',
                ];

            }

            if(GeneralAccountLedger::upsert($records,['model_id', 'model_name', 'branch_id'])) {
                return view('pages.opening-balance.account-ledger', compact('records'));
            }
            return back()->with('error', 'Something went wrong');
        }

        return view('pages.opening-balance.account-ledger');
    }

    public function supplierBalance(Request $request){
        $method = $request->method();
        $user = auth()->user();
        $branch = $user->branch;
        if($method == 'POST'){
            $file = $request->file;
            $date = $request->date;
            $branch_id = $request->branch_id;
            $rows = $this->getRecordFromExcel(Upload::class, $file);
            $title = $rows[0];
            $code = searchIndex($title, 'code'); // code/account number
            $debit = searchIndex($title, 'debit'); //serial number
            $credit = searchIndex($title, 'credit'); //serial number
            $records = [];
            $all_codes = [];
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $all_codes[]= $value[$code];
            }
            $suppliers = Supplier::select('id', 'code')->whereIn('code',$all_codes)->get()->toArray();
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $supplier = searchForId(trim($value[$code]), $suppliers);
                if (!$supplier)
                    continue;
                $records[] = [
                    'model_id' => $supplier->id,
                    'model_name' => 'Supplier',
                    'branch_id' => $branch_id,
                    'description' => 'OPENING BALANCE',
                    'reference' => 'OPENINGBALANCE',
                    'credit' => str_replace(',','',$value[$credit]),
                    'debit' => str_replace(',','',$value[$debit]),
                    'date' => $date,
                    'user_id' => $user->id,
                    'receipt_no' => 'OPENINGBALANCE',
                ];

            }

            if(GeneralAccountLedger::upsert($records,['model_id', 'model_name', 'branch_id'])) {
                return view('pages.opening-balance.supplier-balance', compact('records'));
            }
            return back()->with('error', 'Something went wrong');
        }


        return view('pages.opening-balance.supplier-balance');
    }
}
