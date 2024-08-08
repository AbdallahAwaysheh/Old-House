<?php
session_start();

class User
{
    public static $userID;


    public function __construct()
    {
        if (isset($_SESSION['user_id'])) {
            self::$userID = $_SESSION['user_id'];
        }
    }
}


$user = new User();
