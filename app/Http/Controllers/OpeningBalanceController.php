<?php

namespace App\Http\Controllers;

use App\Imports\Upload;
use App\Models\Customer;
use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use App\Models\Supplier;
use Illuminate\Http\Request;

class OpeningBalanceController extends Controller
{
    //
    public function customerBalance(Request $request){
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
            $customers = Customer::whereIn('code',$all_codes)->get()->toArray();
            foreach ($rows as $key => $value) {
                if ($key < 1)
                    continue; // skip first and second row
                $customer = searchForId(trim($value[$code]), $customers);
                if(!$customer)
                    continue;
                $records[] = [
                    'model_id' => $customer->id,
                    'model_name' => 'Customer',
                    'branch_id' => $branch_id,
                    'description' => 'OPENING BALANCE',
                    'reference' => 'OPENINGBALANCE',
                    'credit' => $value[$credit],
                    'debit' => $value[$debit],
                    'date' => $date,
                    'user_id' => $user->id,
                    'receipt_no' => 'OPENINGBALANCE',
                ];

            }

            if(GeneralAccountLedger::upsert($records,['model_id', 'model_name', 'branch_id'])) {
                return view('pages.opening-balance.customer-balance', compact('records'));
            }
            return back()->with('error', 'Something went wrong');
        }

        return view('pages.opening-balance.customer-balance');
    }

    public function customerLimit(Request $request){
        $method = $request->method();
        if($method == 'POST'){
            $file = $request->file;
            $rows = $this->getRecordFromExcel(Upload::class, $file);
            $title = $rows[1];
            $sno = searchIndex($title, 'sno'); //serial number
            return $rows;
        }

        return view('pages.opening-balance.customer-limit');
    }

    public function inventoryValuation(Request $request){
        $method = $request->method();
        if($method == 'POST'){
            $file = $request->file;
            $rows = $this->getRecordFromExcel(Upload::class, $file);
            $title = $rows[1];
            $sno = searchIndex($title, 'sno'); //serial number
            return $rows;
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
            $accounts = GeneralAccount::whereIn('number',$all_codes)->get()->toArray();
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
                    'credit' => $value[$credit],
                    'debit' => $value[$debit],
                    'date' => $date,
                    'user_id' => $user->id,
                    'receipt_no' => 'OPENINGBALANCE',
                ];

            }

            if(GeneralAccountLedger::upsert($records,['model_id', 'model_name', 'branch_id'])) {
                return view('pages.opening-balance.customer-balance', compact('records'));
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
            $suppliers = Supplier::whereIn('code',$all_codes)->get()->toArray();
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
                    'credit' => $value[$credit],
                    'debit' => $value[$debit],
                    'date' => $date,
                    'user_id' => $user->id,
                    'receipt_no' => 'OPENINGBALANCE',
                ];

            }

            if(GeneralAccountLedger::upsert($records,['model_id', 'model_name', 'branch_id'])) {
                return view('pages.opening-balance.customer-balance', compact('records'));
            }
            return back()->with('error', 'Something went wrong');
        }


        return view('pages.opening-balance.supplier-balance');
    }
}
