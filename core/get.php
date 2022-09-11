<?php
require_once __DIR__ . "/../config/Dbcontroller.php";
class GET
{

    private $conn;

    //Constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getCourseData()
    {
        $db = new Dbcontroller();
        $response = $db->select_all("course");
        if ($response === false) {
            http_response_code(405);
            return  json_encode(
                [
                    'message' => 'Method Not Allowed'
                ]
            );
        } else {
            $mydata = [];
            foreach ($response as $row) {
                $data = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'unit' => $row['unit'],
                    'code' => $row['unit']
                ];
                array_push($mydata, $data);
            }
            return json_encode($mydata);
        }
    }
}
