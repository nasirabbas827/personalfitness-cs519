<?php
// Connect to the database
$conn = new mysqli("localhost", "root", "", "fitness-cs519db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>