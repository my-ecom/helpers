<?php
namespace oangia\database;

class MySQL {
    private $mysqli;
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
        return new MySQL(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }

    public function select($table, $id) {
        $sql = 'SELECT * FROM ' . $table . ' WHERE id=' . $id;
        return MySQL::query($sql);
    }

    public static function create($table, $data) {
        $fields = [];
        $values = [];
        foreach ($data as $key => $value) {
            $fields[] = '`' . $key . '`';
            $values[] = MySQL::deletectValue($value);
        }
        $sql = 'INSERT INTO `' . $table . '` (' . implode(', ', $fields). ') VALUES (' . implode(', ', $values) . ')';
        return MySQL::query($sql);
    }

    public static function update($table, $data, $id) {
        $sql = 'UPDATE TABLE `' . $table . '` SET ';
        $values = [];
        foreach ($data as $key => $value) {
            $values[] = '`' . $key . '`' . '=' . MySQL::deletectValue($value);
        }
        $sql .= implode(', ', $values);
        $sql .= ' WHERE id=' . $id;
        return MySQL::query($sql);
    }

    public function delete($table, $id) {
        #$sql = 'DELETE FROM ' . $table . ' WHERE id=' . $id;
        #return MySQL::query($sql);
    }

    public static function query($sql, $fetch = 'assoc')
    {
        global $mysql;
        if (! isset($mysql)) {
            $mysql = MySQL::connect();
        }
        try {
            $mysqli_result = $mysql->mysqli->query($sql);
        } catch (\Exception $e) {
            return [$mysql->mysqli->error, $sql];
        }
        if ($mysqli_result === TRUE) {
            return ['rows_affected' => $mysql->mysqli->affected_rows, 'last_id' => $mysql->mysqli->insert_id];
        }
        if (is_object($mysqli_result) && get_class($mysqli_result) == 'mysqli_result') {
            $result = [];
            while ($row = ($fetch == 'row')?$mysqli_result->fetch_row():$mysqli_result->fetch_assoc()) {
                $result[] = $row;
            }
            $mysqli_result->free_result();
            return $result;
        }
    }

    private static function deletectValue($value) {
        if (is_numeric($value)) return $value;
        if (is_array($value)) return '"' . str_replace('"', '\"', json_encode($value)) . '"';
        return '"' . str_replace('"', '\"', $value) . '"';
    }
}
