<?php
namespace oangia\web;

class Model {
    function __construct() {
        global $db;
        if (! isset($db)) {
            $db = Database::connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        }
        $this->db = $db;
    }

    public function all($table) {
        $sql = 'SELECT * FROM ' . $table;
        return $this->run($sql);
    }

    public function find($table, $value, $field = 'id') {
        if (!is_numeric($value)) {
            $value = '"' . $value . '"';
        }
        $sql = 'SELECT * FROM ' . $table . ' WHERE ' . $field . '='. $value .' LIMIT 1';
        $result = $this->run($sql);
        if (! $result) {
            return null;
        }
        return $result[0];
    }

    public function create($table, $data) {
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

    public function update($table, $id, $data, $field = 'id') {
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

    public function delete($table, $value, $field = 'id') {
        if (!is_numeric($value)) {
            $value = '"' . $value . '"';
        }
        $sql = 'SELECT * FROM ' . $table . ' WHERE ' . $field . '='. $value;
        return $this->run($sql);
    }

    public function run($sql) {
        return $this->db->query($sql, 'assoc');
    }
}
