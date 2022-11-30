<?php
namespace oangia\web;

class ApiController {
    function __construct() {
        if (! defined('OG_API_KEY') || Request::server('HTTP_AUTHORIZATION') != OG_API_KEY) {
            Response::json(['message' => 'Unauthorized'], 401);
        }

        global $db;
        $this->db = $db;
    }

    public function index($table) {
        $sql = 'SELECT * FROM ' . $table . '';
        return $this->run($sql);
    }

    public function store($table) {
        $data = Request::json();
        $keys = [];
        $values = [];
        foreach ($data as $key => $value) {
            $keys[] = $key;
            if (is_numeric($value)) {
                $values[] = $value;
            } else {
                $values[] = '"' . str_replace('"', '\"', $value) . '"';
            }
        }
        $sql = 'INSERT INTO ' . $table . '(' . implode(',', $keys) . ') VALUES(' . implode(',', $values). ')';
        return $this->run($sql);
    }

    public function show($table, $id, $field = 'id') {
        $result = $this->run('SELECT * FROM ' . $table . ' WHERE id = ' . $id . ' LIMIT 1');
        if (count($result) == 1) {
           return $result[0];
        }
        return $result;
    }

    public function update($table, $id, $field = 'id') {
        $data = Request::json();
        $values = [];
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
                $values[] = $key . '=' . $value;
            } else {
                $values[] = $key . '=' . '"' . str_replace('"', '\"', $value) . '"';
            }
        }
        $sql = 'UPDATE ' . $table . ' SET ' . implode(',', $values) . ' WHERE ' . $field . '=' . $id;
        return $this->run($sql);
    }

    public function destroy($table, $id, $field = 'id') {
        $sql = 'DELETE FROM ' . $table . ' WHERE ' . $field . '=' . $id;
        return $this->run($sql);
    }

    private function run($sql) {
        return $this->db->query($sql, 'assoc');
    }
}
