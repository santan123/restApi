<?php
require_once __DIR__ . "/../config/Dbconnector.php";
class Post
{

    private $conn;

    //Constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function checkAdminLogin($data)
    {
        $db = new Dbcontroller();
        $response = $db->AdminLogin($data);
        return  $response;
    }
}
