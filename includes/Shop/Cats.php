<?php
include_once "./includes/connect.php";

class Categories extends OldHouseDB
{
    public $categories = [];
    public function readCats()
    {

        try {
            $this->connect();
            $sql = "SELECT * FROM category;";
            $result = $this->conn->query($sql);

            if ($result === false) {
                throw new Exception("Query Error: " . $this->conn->error);
            }

            while ($row = $result->fetch_assoc()) {
                $this->categories[] = $row;
            }

            $this->closeConnection();
            return $this->categories;
        } catch (Exception $e) {
            die("ERROR :" . $e->getMessage());
        }
    }
}
