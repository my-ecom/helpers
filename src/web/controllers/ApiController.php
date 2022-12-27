<?php
namespace oangia\web\controllers;

use oangia\web\Request;
use oangia\web\Response;
use oangia\web\entities\Post;
use oangia\web\entities\Product;
use oangia\web\entities\Comment;
use oangia\helpers\iString;
use oangia\web\Client;
use oangia\web\database\Database;

class ApiController extends Controller {
    function __construct() {
        parent::__construct();
        if (! defined('OG_API_KEY') || Request::server('HTTP_AUTHORIZATION') != OG_API_KEY) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
    }

    public function index($table) {
        return $this->model->all($table);
    }

    public function store($table) {
        $data = Request::json();
        return $this->model->create($table, $data);
    }

    public function createPost() {
        $data = Request::json();
        foreach ($data as $key => $value) {
            if (! isset($data[$key]['slug'])) {
                $data[$key]['slug'] = iString::slugify($value['title']) . '-' . iString::generateRandomString(8);
            } else {
                $data[$key]['slug'] = $value['slug'];
            }
            $data[$key]['site'] = $_SERVER['HTTP_HOST'];
        }
        $result = ['Affected rows' => 0, 'Last Insert Id' => 0];
        foreach ($data as $value) {
            $insert = Database::create('posts', $value);
            $result['Last Insert Id'] = $insert['Last Insert Id'];
            $result['Affected rows'] += $insert['Affected rows'];
        }
        return $result;
    }

    public function createProduct() {
        $data = Request::json();
        foreach ($data as $key => $value) {
            if (! isset($value['slug'])) {
                $data[$key]['slug'] = iString::slugify($value['title']) . '-' . iString::generateRandomString(8);
            } else {
                $data[$key]['slug'] = $value['slug'];
            }
            $data[$key]['site'] = $_SERVER['HTTP_HOST'];
            $data[$key]['variations'] = json_encode($value['variations']);
            $data[$key]['price'] = json_encode($value['price']);
            $data[$key]['thumbnail'] = $value['gallery'][0];
            $data[$key]['gallery'] = json_encode($value['gallery']);
            if (isset($value['opt_gallery'])) {
                $data[$key]['opt_gallery'] = json_encode($value['opt_gallery']);
            }
        }
        $result = ['Affected rows' => 0, 'Last Insert Id' => 0];
        foreach ($data as $value) {
            $insert = Database::create('products', $value);
            $result['Last Insert Id'] = $insert['Last Insert Id'];
            $result['Affected rows'] += $insert['Affected rows'];
        }
        return $result;
    }

    public function show($table, $id, $field = 'id') {
        return $this->model->find($table, $id, $field);
    }

    public function update($table, $id, $field = 'id') {
        $data = Request::json();
        return $this->model->update($table, $id, $data, $field);
    }

    public function destroy($table, $id, $field = 'id') {
      return $this->model->delete($table, $id, $field);
    }

    private function run($sql) {
        return $this->db->query($sql, 'assoc');
    }
}
