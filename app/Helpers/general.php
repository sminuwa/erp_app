<?php

use App\Models\GeneralAccount;
use App\Models\GeneralAccountLedger;
use App\Models\InterBank;
use App\Models\InterstoreTransfer;
use App\Models\Journal;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PurchaseExpense;
use App\Models\Receipt;

const TRANSACTION_TYPE_OPENING_BALANCE = 0;
const TRANSACTION_TYPE_GRN = 1;
const TRANSACTION_TYPE_INTERSITE = 2;
const TRANSACTION_TYPE_INTERSTORE = 3;
const TRANSACTION_TYPE_ADJUSTMENT = 4;
const TRANSACTION_TYPE_SALE = 5;
const TRANSACTION_TYPE_CREDIT_NOTE = 6;
const TRANSACTION_TYPE_RETURN_DEBIT = 7;

function app_name($type = 'full', $style = 'uppercase')
{
    if ($type == 'abbr') {
        switch ($style) {
            case 'uppercase':
                return 'ABTC';
            case 'propercase':
                return 'Abtc';
            case 'lowercase':
                return 'abtc';
        }
    } elseif ($type == 'short') {
        switch ($style) {
            case 'uppercase':
                return 'ALBABELLO';
            case 'propercase':
                return 'Albabello';
            case 'lowercase':
                return 'albabello';
        }
    } else {
        switch ($style) {
            case 'uppercase':
                return 'ALBABELLO TRADING COMPANY LIMITED';
            case 'propercase':
                return 'Albabello Trading Company Limited';
            case 'lowercase':
                return 'albabello trading company limited';
        }
    }
}

function app_logo()
{
    return asset('assets/backend/img/logo.png');
}


function in_array_r($needle, $haystack, $strict = true)
{
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

function remove_non_numeric($string)
{
    return (float) preg_replace("/[^\d.]+/", "", $string);
}

function currency_sign($currency = 'NG')
{
    if ($currency == 'NG')
        return "₦";
    return "₦";
}


function spinner($width = 36, $height = 36)
{
    return '<img src="' . asset('commons/images/spinner_v1.svg') . '" width="' . $width . '" height="' . $height . '"/>';
}

function ceiling($num, $nearest)
{
    return ceil($num / $nearest) * $nearest;
}

function roundDown($number, $nearest)
{
    $result = ($number - fmod($number, $nearest)) + $nearest;
    return $result;
}

function generateRandomString($length = 5)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function convertNumberToWords($number)
{
    $hyphen = '-';
    $conjunction = ' and ';
    $separator = ', ';
    $negative = 'negative ';
    $decimal = ' point ';
    $dictionary = array(
        0 => 'zero',
        1 => 'one',
        2 => 'two',
        3 => 'three',
        4 => 'four',
        5 => 'five',
        6 => 'six',
        7 => 'seven',
        8 => 'eight',
        9 => 'nine',
        10 => 'ten',
        11 => 'eleven',
        12 => 'twelve',
        13 => 'thirteen',
        14 => 'fourteen',
        15 => 'fifteen',
        16 => 'sixteen',
        17 => 'seventeen',
        18 => 'eighteen',
        19 => 'nineteen',
        20 => 'twenty',
        30 => 'thirty',
        40 => 'fourty',
        50 => 'fifty',
        60 => 'sixty',
        70 => 'seventy',
        80 => 'eighty',
        90 => 'ninety',
        100 => 'hundred',
        1000 => 'thousand',
        1000000 => 'million',
        1000000000 => 'billion',
        1000000000000 => 'trillion',
        1000000000000000 => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert number to words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convertNumberToWords(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens = ((int) ($number / 10)) * 10;
            $units = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convertNumberToWords($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convertNumberToWords($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convertNumberToWords($remainder);
            }
            break;

    }

    if (null !== $fraction && is_numeric($fraction) && $fraction > 0) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);

    }

    return ucwords($string);
}
function monthName($number)
{
    switch ($number) {
        case 1:
            return "January";
            break;
        case 2:
            return "February";
            break;
        case 3:
            return "March";
            break;
        case 4:
            return "April";
            break;
        case 5:
            return "May";
            break;
        case 6:
            return "June";
            break;
        case 7:
            return "July";
            break;
        case 8:
            return "August";
            break;
        case 9:
            return "September";
            break;
        case 10:
            return "October";
            break;
        case 12:
            return "December";
            break;
    }
}
function customerLastTransaction($customer_id)
{
    return DB::table('general_account_ledgers')
        ->where('model_id', $customer_id)
        ->where('model_name', 'Customer')
        ->orderBy('date', 'DESC')
        ->first();

}
function transactionDecription($reference)
{//This function is meant fo particularly the description of the transaction captured
    if (substr($reference, 0, 3) == "PAY")
        return DB::table('payments')->where('receipt_no', $reference)->first()->description ?? '';
    if (substr($reference, 0, 3) == "RCT")
        return DB::table('receipts')->where('receipt_no', $reference)->first()->description ?? '';
    if (substr($reference, 0, 3) == "STK")
        return DB::table('stock_adjustments')->where('reference', $reference)->first()->description ?? '';
    if (substr($reference, 0, 3) == "INV")
        return DB::table('orders')->where('reference', $reference)->first()->description ?? '';
    if (substr($reference, 0, 3) == "CRN")
        return DB::table('credit_notes')->where('reference', $reference)->first()->description ?? '';
}
function storeByCode($code)
{
    return DB::table('stores')
        ->where('code', $code)
        ->first();

}



function searchIndex($array, $column_name)
{
    foreach ($array as $key => $value) {
        if (stripos($value, $column_name) !== false) {
            return $key;
        }
    }
    return null;
}

function searchForId($search_value, $array)
{
    // Iterating over main array
    foreach ($array as $key1 => $val1) {
        $temp_path = array();
        // Adding current key to search path
        array_push($temp_path, $key1);
        // Check if this value is an array
        // with atleast one element
        if (is_array($val1) and count($val1)) {
            // Iterating over the nested array
            foreach ($val1 as $key2 => $val2) {
                if (strtoupper($val2) == strtoupper($search_value)) {
                    // Adding current key to search path
                    array_push($temp_path, $key2);
                    return (object) $array[$key1] = $val1;
                }
            }
        } elseif (strtoupper($val1) == strtoupper($search_value)) {
            return (object) $array[$key1] = $val1;
        }
    }
    return null;
}
function getReferenceId($reference)
{
    if (strpos($reference, 'RCT') !== false) {
        return Receipt::where('receipt_no', $reference)->first()->id ?? null;
    }
    if (strpos($reference, 'INV') !== false) {
        return Order::where('reference', $reference)->first()->id ?? null;
    }
    if (strpos($reference, 'PAY') !== false) {
        return Payment::where('receipt_no', $reference)->first()->id ?? null;
    }
    if (strpos($reference, 'JNL') !== false) {
        return Journal::where('reference', $reference)->first()->id ?? null;
    }
    if (strpos($reference, 'ITB') !== false) {
        return InterBank::where('reference', $reference)->first()->id ?? null;
    }
    if (strpos($reference, 'ITS') !== false) {
        return InterstoreTransfer::where('reference', $reference)->first()->id ?? null;
    }
    if (strpos($reference, 'OPE') !== false) {
        return null;
    }
    if (strpos($reference, 'PNV') !== false) {
        return PurchaseExpense::where('reference', $reference)->first()->id ?? null;
    }

    function categoryId($category)
    {
        return DB::table('categories')
            ->where('name', 'LIKE', $category)
            ->first()->id ?? null;

    }
    function branchId($branch)
    {
        return DB::table('branches')
            ->where('name', 'LIKE', $branch)
            ->first()->id ?? null;

    }

}
function getContraAccount($reference, $credit, $debit)
{
    // Determine the query conditions based on reference prefix
    $query = GeneralAccountLedger::where('reference', $reference)
        ->join('general_accounts', 'general_account_ledgers.model_id', 'general_accounts.id');

    // if (str_contains($reference, 'RCT')) {
    //     $query->where('general_account_ledgers.debit', '>', 0);
    // } elseif (str_contains($reference, 'PAY')) {
    //     $query->where('general_account_ledgers.credit', '>', 0);
    // } elseif (str_contains($reference, 'ITB')) {
    //     $query->where('general_account_ledgers.model_name', 'GeneralAccount');
    //     $query->where('general_account_ledgers.credit', '>', 0);
    // }
    if($credit >0){
        $query->where('general_account_ledgers.debit', '>', 0);
    }
    if($debit >0){
        $query->where('general_account_ledgers.credit', '>', 0);
    }

    // Execute the query and process results
    // $numbers = $query->get()->pluck('number');
    $numbers = $query->first()->number ?? '';
    
    return $numbers;//->isNotEmpty() ? $numbers->implode('|') : '';
}