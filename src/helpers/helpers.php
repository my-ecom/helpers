<?php
// general helper functions
function debug_setting()
{
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

function timeout_setting()
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

function save_file($file, $content, $mode = 0777) {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, $mode, true);
    }
    file_put_contents($file, $content);
}

function get_file($file, $default = '') {
    $content = @file_get_contents($file);
    if (! $content) {
        return $default;
    }
    return $content;
}

function get_ob($callback, $params = []) {
    ob_start();
    if (is_array($callback)) {
        $controller = new $callback[0];
        $action = $callback[1];
        $response = call_user_func_array([$controller, $action], $params);
    } else {
        $response = call_user_func_array($callback, $params);
    }
    echo $response;
    return ob_get_clean();
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

function add_client_info(&$data) {
    $data['ip'] = get_user_ip();
    $data['user_agent'] = get_user_agent();
}

function view($name, $args = []) {
    foreach ($args as $key => $value) {
        ${$key} = $value;
    }
    $page = debug_backtrace()[1]['function'];
    ob_start();
    include(APP_PATH . '/public/cache/templates/' . $_SERVER['HTTP_HOST'] . '_main.php');
    $template = ob_get_clean();
    //dd($template);
    ob_start();
    include(APP_PATH . '/views/' . $name . '.php');
    $content = ob_get_clean();
    return str_replace('{{content}}', $content, $template);
}
