<?php
namespace oangia;

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

    private function isJson($string) {
       json_decode($string);
       return json_last_error() === JSON_ERROR_NONE;
    }
}
