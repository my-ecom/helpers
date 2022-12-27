<?php
namespace oangia\web;

class Response
{
    public static function json($data, $code = 200)
    {
        header('Content-Type: application/json');
        switch ($code) {
            case 200:
                header("HTTP/1.1 200 OK");
                break;
            case 401:
                header("HTTP/1.1 401 Unauthorized");
                break;
            case 500:
                header('HTTP/1.1 500 Internal Server Error');
                break;
        }
        $data = array_merge(['code' => $code], $data);
        echo json_encode($data);
        die();
    }

    public static function xml($content) {
        header('Content-Type: application/xml; charset=utf-8');
        echo $content;
        die();
    }

    public static function txt($content) {
        header('Content-Type: text/plain; charset=utf-8');
        echo $content;
        die();
    }
}
