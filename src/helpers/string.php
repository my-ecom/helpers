<?php
function is_json($string) {
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

function slugify($text, string $divider = '-')
{
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

function random_string($length = 10) {
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
    if (count($str) == 1) return '';
    $str = explode($str2, $str[$deep]);
    return $str[0];
}

function valid_url($url)
{
    return filter_var($url, FILTER_VALIDATE_URL) !== FALSE;
}

function minimize_css($css){
    $css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
    $css = preg_replace('/\s{2,}/', ' ', $css);
    $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
    $css = preg_replace('/;}/', '}', $css);
    return $css;
}
