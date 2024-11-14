<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "todo_list";

$mysqli = new mysqli($host, $user, $pass, $db);

if ($mysqli->connect_error) {
    die("Connection failed.");
}
