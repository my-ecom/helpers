<?php
namespace oangia\web;

class ApiController {
    function __construct() {
        if (! defined('OG_API_KEY') || Request::server('HTTP_AUTHORIZATION') != OG_API_KEY) {
            Response::json(['message' => 'Unauthorized'], 401);
        }
        $this->model = new Model;
    }

    public function index($table) {
        return $this->model->all($table);
    }

    public function store($table) {
        $data = Request::json();
        return $this->model->create($table, $data);
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
