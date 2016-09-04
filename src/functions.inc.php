<?php

function pr($var = array()){
    echo "<pre>";
        print_r($var);
    echo "</pre>";
}

function array_last(Array $array){
   if(!empty($array)){
        return end($array);
    }
    return false; 
}

function array_pop_last(Array &$array){
    if(!empty($array)){
        $_array = $array;
        end($_array);
        $last_key = key($_array);
        $last = $array[$last_key];
        unset($array[$last_key]);
        return $last;
    }
    return false;
}

function array_associate_key_value(Array $array = array()){
    $_array = array();
    foreach ($array as $value){
        list($k, $v) = explode('=', $value);
        $_array[$k] = $v;
    }
    return $_array;
}

function stripSlashesDeep($value) {
    $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
    return $value;
}

function unregisterGlobals() {
    if (ini_get('register_globals')) {
        $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
        foreach ($array as $value) {
            foreach ($GLOBALS[$value] as $key => $var) {
                if ($var === $GLOBALS[$key]) {
                    unset($GLOBALS[$key]);
                }
            }
        }
    }
}

function str_replace_first($search, $replace, $subject){
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    return false;
}

function from_file_json($file){
    return json_decode(file_get_contents($file .'.json'), true);
}