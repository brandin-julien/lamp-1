<?php
$config = [
    "host" => "mysql:host=127.0.0.1;dbname=toto",
    "user" => "root",
    "password" => "",
];

$pdo = new PDO($config['host'], $config['user'], $config['password']);
