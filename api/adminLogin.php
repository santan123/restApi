<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type: application/json');
require_once __DIR__ . "/../initialize.php";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    //Ok
    http_response_code(200);

    //$receive_data = json_decode(file_get_contents("php://input"), true);
    $response = $post->checkAdminLogin($_POST);
    echo $response;
} else {
    //Bad Request
    http_response_code(400);
    echo json_encode(
        [
            "message" => "Bad Request"
        ]
    );
}
