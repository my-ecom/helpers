<?php
namespace oangia\firebase\controllers;

use oangia\firebase\Model;
use oangia\web\Request;
use oangia\web\Response;

class FirebaseController {
    function __construct() {
        if (! defined('OG_API_KEY') || Request::server('HTTP_AUTHORIZATION') != OG_API_KEY) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
    }

    public function store($table) {
        $firestore = new Model;
        $data = Request::json();
        foreach ($data as $key => $value) {
            if (! isset($data[$key]['slug'])) {
                $data[$key]['slug'] = slugify($data[$key]['title']) . '-' . random_string(8);
            }
        }
        $result = [];
        foreach ($data as $key => $value) {
            $response = $firestore->create('sites/' . $_SERVER['HTTP_HOST'] . '/' . $table, $value, $value['slug']);
            $result[] = $response;
        }
        Response::json(['data' => $result]);
    }

    public function show($table, $id) {
        $firestore = new Model;
        $response = $firestore->find('sites/' . $_SERVER['HTTP_HOST'] . '/' . $table, $id);
        Response::json(['data' => $response]);
    }

    public function update($table, $id) {
        $firestore = new Model;
        $data = Request::json();
        if (! isset($data['updateTime'])) {
            $data['updateTime'] = time();
        }
        $response = $firestore->update('sites/' . $_SERVER['HTTP_HOST'] . '/' . $table, $data, $id);
        Response::json(['data' => $response]);
    }

    public function destroy($table, $id) {
        $firestore = new Model;
        $firestore->delete('sites/' . $_SERVER['HTTP_HOST'] . '/' . $table, $id);
        Response::json([]);
    }
}
