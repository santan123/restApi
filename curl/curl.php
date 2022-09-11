<?php
$url = "http://localhost/API/api/adminLogin.php";
$curl = curl_init();
$field =
    [
        "email" => "admin@admin.com",
        "password" => "password"
    ];
curl_setopt_array(
    $curl,
    [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['Content-Type:application/json'],
        CURLOPT_POSTFIELDS => json_encode($field)
    ]
);
$response = curl_exec($curl);
curl_close($curl);
echo $response;
