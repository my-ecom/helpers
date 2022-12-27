<?php
namespace oangia\web;
use oangia\helpers\iString;

class Request
{
    public static function json($required = [])
    {
        $content = trim(file_get_contents("php://input"));
        if (! iString::isJson($content)) {
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

    public static function validate($fields) {
        $data = Request::json();
        foreach ($fields as $field => $validate) {
            $validate = explode('|', $validate);
            foreach ($validate as $item) {
                if (strpos($item, ':') !== false) {
                    $item = explode(':', $item);
                    $value = $item[1];
                    $item = $item[0];
                }
                switch ($item) {
                    case 'required':
                        if (! isset($data[$field]) || ! $data[$field]) {
                            Response::json(['message' => $field . ' is required'], 400);
                        }
                        break;
                    case 'email':
                        if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            Response::json(['message' => 'Email not valid'], 400);
                        }
                        break;
                    case 'min':
                        if (strlen($data[$field]) < $value) {
                            Response::json(['message' => $field . ' field require min ' . $value . ' characters'], 400);
                        }
                        break;
                    case 'max':
                        if (strlen($data[$field]) > $value) {
                            Response::json(['message' => $field . ' field require max ' . $value . ' characters'], 400);
                        }
                        break;
                }
            }
        }
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
