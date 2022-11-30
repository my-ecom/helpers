<?php
namespace oangia\web;

use oangia\Response;

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

    public static function connect($host, $user, $password, $name) {
        $db = new Database($host, $user, $password, $name);
        return $db;
    }

    public function query($sql, $fetch = 'row')
    {
        $mysqli_result = $this->mysqli->query($sql);
        if ($mysqli_result === TRUE) {
            return ['Affected rows' => $this->mysqli->affected_rows, 'Last Insert Id' => $this->mysqli->insert_id];
        }
        if (is_object($mysqli_result) && get_class($mysqli_result) == 'mysqli_result') {
            $result = [];
            while ($row = ($fetch == 'row')?$mysqli_result->fetch_row():$mysqli_result->fetch_assoc()) {
                $result[] = $row;
            }
            $mysqli_result->free_result();
            return $result;
        }
        $this->error = $this->mysqli->error;
        return;
    }
}
