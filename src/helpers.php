<?php

function debugSetting()
{
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

function timeoutSetting()
{
    ini_set('memory_limit', '-1');
    set_time_limit(0);
}

function dd($var, $die=true)
{
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    if ($die) {
        die();
    }
}

function slugify($text, string $divider = '-')
{
    #echo $text . '<br/>';
    // replace non letter or digits by divider
    if (! $text) {
        return false;
    }

    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    if (! $text) {
        return false;
    }
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // trim
    $text = trim($text, $divider);
    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);
    // lowercase
    $text = strtolower($text);
    if (! $text) {
        return false;
    }
    return $text;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function get_string_between($str, $str1, $str2, $deep = 1)
{
    $str = explode($str1, $str);
    $str = explode($str2, $str[$deep]);
    return $str[0];
}

function array_unique_recursive($array)
{
    $array = array_unique($array, SORT_REGULAR);

    foreach ($array as $key => $elem) {
        if (is_array($elem)) {
            $array[$key] = array_unique_recursive($elem);
        }
    }

    return $array;
}

function is_valid_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== FALSE;
}

function toObject($obj)
{
    return (object)$obj;
}

function getCookie($key)
{
    return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
}

function array_export($data, $deep=1)
{
    $tab = '    ';
    $text = '';
    if ($deep == 1) {
        $text = '<?php';
        $text .= "\nreturn ";
    }

    if (! is_array($data)) {
        $text .= "'" . $data . "',\n";
    } else {
        $text .= "[\n";
        foreach ($data as $key => $value) {
            for ($i = 0; $i < $deep; ++$i) {
                $text .= $tab;
            }
            if (is_numeric($key)) {
                $text .= $key;
            } else {
                $text .= "'$key'";
            }
            $text .= " => " . array_export($value, $deep + 1);
        }
        $text = rtrim($text, ",\n") . "\n";
        for ($i = 0; $i < $deep - 1; ++$i) {
            $text .= $tab;
        }
        $text .= "],\n";
    }
    if ($deep == 1) {
        return rtrim($text, ",\n") . ";";
    }
    return $text;
}
