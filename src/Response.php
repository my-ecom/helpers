<?php
namespace oangia;

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
}
