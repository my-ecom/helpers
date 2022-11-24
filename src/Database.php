<?php

namespace oangia;

class Database
{
    private $mysqli;
    public $error = false;
    public $fetch;

  	function __construct($fetch = 'row')
  	{
        $this->fetch = $fetch;
        $this->mysqli = @new \mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($this->mysqli->connect_errno) {
            Response::json(['message' => "Failed to connect to MySQL: " . $this->mysqli->connect_error], 500);
        }
  	}

    function __destruct()
    {
        $this->mysqli->close();
    }

    public function query($sql)
    {
        $mysqli_result = $this->mysqli->query($sql);
        if ($mysqli_result === TRUE) {
            return ['Affected rows' => $this->mysqli->affected_rows, 'Last Insert Id' => $this->mysqli->insert_id];
        }
        if (is_object($mysqli_result) && get_class($mysqli_result) == 'mysqli_result') {
            $result = [];
            while ($row = ($this->fetch == 'row')?$mysqli_result->fetch_row():$mysqli_result->fetch_assoc()) {
                $result[] = $row;
            }
            $mysqli_result->free_result();
            return $result;
        }
        $this->error = $this->mysqli->error;
        return;
    }
}
