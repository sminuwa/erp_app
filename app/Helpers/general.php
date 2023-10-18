<?php

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
