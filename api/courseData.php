<?php
header('Access-Control-Allow-Origin:*');
header('Content-Type: application/json');
require_once __DIR__ . "/../initialize.php";
$response = $get->getCourseData();
echo $response;
