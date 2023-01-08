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

if (! function_exists('dd')) {
    function dd($var, $die=true)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        if ($die) {
            die();
        }
    }
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
