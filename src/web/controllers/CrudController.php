<?php

namespace oangia\web\controllers;
use oangia\web\database\Database;
use oangia\web\Route;

class CrudController extends Controller {
    function __construct() {
        parent::__construct();
        $this->db = Database::connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }

    public function index($table) {
        $sql = 'SELECT * FROM ' . $table;
        $entities = $this->db->query($sql, 'assoc');
        $sql = 'SHOW COLUMNS FROM ' .$table;
        $fields = $this->db->query($sql, 'assoc');
        return admin_view('admin/crud/index', compact('entities', 'fields'));
    }

    public function create($table) {
        $sql = 'SHOW COLUMNS FROM ' .$table;
        $fields = $this->db->query($sql, 'assoc');
        return admin_view('admin/crud/create', compact('table', 'fields'));
    }

    public function store($table) {
        $fields = array_keys($_POST);
        $values = [];
        foreach ($_POST as $key => $value) {
            if ($value == 'current_timestamp()') {
              $values[] = $value;
            } elseif (is_numeric($value)) {
              $values[] = $value;
            } else {
              $values[] = '"' . $value . '"';
            }
        }
        $sql = 'INSERT INTO ' . $table
          . ' (' . implode(',', $fields) . ') VALUES ('
          . implode(',', $values) . ')';
        $result = $this->db->query($sql);
        Route::redirect('/sus/admin/' . $table . '/' . $result['Last Insert Id'] . '/edit');
    }

    public function edit($table, $id) {
        $entity = $this->db->query('SELECT * FROM ' . $table . ' WHERE id = ' . $id . ' LIMIT 1', 'assoc');
        $entity = $entity[0];
        $sql = 'SHOW COLUMNS FROM ' .$table;
        $fields = $this->db->query($sql, 'assoc');
        return admin_view('admin/crud/edit', compact('table', 'entity', 'fields'));
    }

    public function update($table, $id) {
        $values = [];
        foreach ($_POST as $key => $value) {
            if ($value == 'current_timestamp()') {
              $values[] = $key . '=' . $value;
            } elseif (is_numeric($value)) {
              $values[] = $key . '=' . $value;
            } else {
              $values[] = $key . '=' . '"' . $value . '"';
            }
        }
        $sql = 'UPDATE ' . $table
          . ' SET ' . implode(',', $values)
          . ' WHERE id=' . $id;
        $result = $this->db->query($sql);
        Route::redirect('/sus/admin/' . $table . '/' . $id . '/edit');
    }
}
