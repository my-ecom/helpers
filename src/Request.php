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
}
