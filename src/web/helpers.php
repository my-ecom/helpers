<?php
use oangia\web\Route;

define('WEB_PATH', __DIR__);

function site_url() {
    if (! defined('SITE_URL')) {
        define('SITE_URL', getProtocol() . '://' . $_SERVER['SERVER_NAME']);
    }
    return SITE_URL;
}

function getProtocol() {
    if (isset($_SERVER['HTTPS']) &&
        ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            return 'https';
    }
    else {
        return 'http';
    }
}



function home_url() {
  return site_url();
}

function get_search_query() {
    return isset($_GET['s'])? $_GET['s']:'';
}

function __assets($file) {
    return site_url() . '/assets/' . $file;
}

function view_part($name, $args = []) {
    foreach ($args as $key => $value) {
        ${$key} = $value;
    }
    include(APP_PATH . '/views/parts/' . $name . '.php');
}

function admin_view($name, $args = []) {
    foreach ($args as $key => $value) {
        ${$key} = $value;
    }
    ob_start();
    include(WEB_PATH . '/views/' . $name . '.php');
    $content = ob_get_clean();
    echo $content;
}
