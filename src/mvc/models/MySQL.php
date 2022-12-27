<?php
namespace oangia\mvc\models;

use oangia\database\MySQL as Database;

class MySQL {
    public static function save($table, $data, $id) {
        return Database::update($table, $data, $id);
    }

    public static function create($table, $data) {
        return Database::create($table, $data);
    }

    public static function query($sql, $fetch = 'assoc') {
        return Database::query($sql, $fetch);
    }
}
