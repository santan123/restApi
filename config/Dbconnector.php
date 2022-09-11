<?php
class Dbconnector
{
    //declaration of variable
    private $serverName = 'localhost';
    private $serverUsername = 'root';
    private $serverPassword = '';
    public $con;

    //constructor
    public function __construct()
    {
        //Exception Handling
        try {
            $conn = new PDO("mysql:host=$this->serverName;dbname=schoolmgt", $this->serverUsername, $this->serverPassword);
            $conn->setAttribute(PDO::ERRMODE_EXCEPTION, PDO::ATTR_ERRMODE);
            $this->con = $conn;
            return $this->con;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
}
