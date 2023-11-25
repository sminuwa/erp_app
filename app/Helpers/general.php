<?php

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
            case 'uppercase': return 'ABTC';
            case 'propercase': return 'Abtc';
            case 'lowercase': return 'abtc';
        }
    }elseif ($type == 'short') {
        switch ($style) {
            case 'uppercase': return 'ALBABELLO';
            case 'propercase': return 'Albabello';
            case 'lowercase': return 'albabello';
        }
    }
    else {
        switch ($style) {
            case 'uppercase': return 'ALBABELLO TRADING COMPANY LIMITED';
            case 'propercase': return 'Albabello Trading Company Limited';
            case 'lowercase': return 'albabello trading company limited';
        }
    }
}

function app_logo(){
    return asset('assets/backend/img/logo.png');
}


function in_array_r($needle, $haystack, $strict = true) {
    foreach ($haystack as $item) {
        if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
            return true;
        }
    }

    return false;
}

function currency_sign($currency = 'NG')
{
    if ($currency == 'NG')
        return "₦";
    return "₦";
}


function ceiling($num, $nearest){
    return ceil($num / $nearest) * $nearest;
}

function roundDown($number, $nearest){
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
