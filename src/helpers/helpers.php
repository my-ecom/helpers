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

function getUserIP()
{
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
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

function to_object($obj)
{
    return (object)$obj;
}

function __cookie($key)
{
    return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
}

function __get($key) {
    return isset($_GET[$key]) ? $_GET[$key] : '';
}

function __post($key) {
    return isset($_POST[$key]) ? $_POST[$key] : '';
}

function is_json($string) {
   json_decode($string);
   return json_last_error() === JSON_ERROR_NONE;
}

function is_mobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function is_xhr() {
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
    {
        return true;
    }
    return false;
}

function get_ob($callback) {
    ob_start();
    $callback();
    return ob_get_clean();
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
