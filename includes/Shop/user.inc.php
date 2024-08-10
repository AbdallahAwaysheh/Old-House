<?php
session_start();

class User
{
    public $userID;

    public function __construct()
    {
        if (isset($_SESSION['user_id'])) {
            $this->userID = $_SESSION['user_id'];
        }
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
    }
}
