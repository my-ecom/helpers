<?php
namespace oangia\web;

class Request
{
    public static function json($required = [])
    {
        $content = trim(file_get_contents("php://input"));
        if (! $this->isJson($content)) {
            return false;
        }
        $data = json_decode($content, true);
        foreach ($required as $item) {
            if (! isset($data[$item])) {
                return false;
            }
        }
        return $data;
    }

    public static function is_xhr() {
        if (strtolower(Request::server('CONTENT_TYPE')) != 'application/json') return false;
        return true;
    }

    public static function is_method($method) {
        return Request::server('REQUEST_METHOD') == $method;
    }

    public static function cookie($key)
    {
        return isset($_COOKIE[$key]) ? $_COOKIE[$key] : '';
    }

    public static function server($key)
    {
        return isset($_SERVER[$key]) ? $_SERVER[$key] : '';
    }

    public static function get($key) {
        return isset($_GET[$key]) ? $_GET[$key] : '';
    }

    public static function post($key) {
        return isset($_POST[$key]) ? $_POST[$key] : '';
    }
}
