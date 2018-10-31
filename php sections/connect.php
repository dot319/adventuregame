<?php

$conn = new mysqli("localhost", "root", "", "AdventureGame");
if ($conn->connect_error) {
    die("Failed to connect to database: $conn->connect_error<br />");
}

?>