<?php
require_once __DIR__ . "/config/Dbcontroller.php";
require_once __DIR__ . "/core/get.php";
require_once __DIR__ . "/core/post.php";
$db = new Dbcontroller();
$get = new GET($db);
$post = new Post($db);
