<?php
namespace oangia\web\database;

use oangia\web\Response;

class Database
{
    private $mysqli;
    public $error = false;
    public $fetch;

  	private function __construct($host, $user, $password, $name)
  	{
        $this->mysqli = @new \mysqli($host, $user, $password, $name);
        if ($this->mysqli->connect_errno) {
            Response::json(['message' => "Failed to connect to MySQL: " . $this->mysqli->connect_error], 500);
        }
  	}

    function __destruct()
    {
        $this->mysqli->close();
    }

    public static function connect() {
        return new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }

    public static function create($table, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = $key;
            if (is_numeric($value)) {
                $values[] = $value;
            } elseif (is_array($value)) {
                $values[] = '"' . str_replace('"', '\"', json_encode($value)) . '"';
            } else {
                $values[] = '"' . str_replace('"', '\"', $value) . '"';
            }
        }
        $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $fields). ') VALUES (' . implode(',', $values) . ')';
        return Database::query($sql);
    }

    public static function query($sql, $fetch = 'row')
    {
        global $db;
        if (! isset($db)) {
            $db = Database::connect();
        }
        $mysqli_result = $db->mysqli->query($sql);
        if ($mysqli_result === TRUE) {
            return ['Affected rows' => $db->mysqli->affected_rows, 'Last Insert Id' => $db->mysqli->insert_id];
        }
        if (is_object($mysqli_result) && get_class($mysqli_result) == 'mysqli_result') {
            $result = [];
            while ($row = ($fetch == 'row')?$mysqli_result->fetch_row():$mysqli_result->fetch_assoc()) {
                $result[] = $row;
            }
            $mysqli_result->free_result();
            return $result;
        }
        return $db->mysqli->error;
    }
}
