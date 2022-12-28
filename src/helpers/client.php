<?php
function get_user_ip() {
    // Get real visitor IP behind CloudFlare network
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
              $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
              $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var( $client, FILTER_VALIDATE_IP ))
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

function get_user_agent() {
    return __server("HTTP_USER_AGENT");
}

function is_mobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", get_user_agent());
}

function __cookie($key)
{
    return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
}

function __server($key)
{
    return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
}

function __get($key) {
    return isset($_GET[$key]) ? $_GET[$key] : '';
}

function __session($key) {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : '';
}

function __post($key) {
    return isset($_POST[$key]) ? $_POST[$key] : '';
}
