<?php

class OldHouseDB
{
    private $hostname = "localhost";
    private $dbname = "oldhouse";
    private $username = "root";
    private $password = "";
    public $conn;


    public function connect()
    {
        try {
            $this->conn = new mysqli($this->hostname, $this->username, $this->password, $this->dbname);
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function closeConnection()
    {
        $this->conn->close();
    }
}
