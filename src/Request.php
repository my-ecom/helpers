<?php
namespace oangia;

class Request
{
    public static function json()
    {
        $content = trim(file_get_contents("php://input"));
        $data = json_decode($content, true);
        return $data;
    }

    public static function get($key)
    {
        if (! isset($_GET[$key])) return '';
        return $_GET[$key];
    }

    public static function post($key)
    {
        if (! isset($_POST[$key])) return '';
        return $_POST[$key];
    }
}
