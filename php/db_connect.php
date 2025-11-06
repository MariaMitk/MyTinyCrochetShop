<?php
$servername = "db";
$username = "root";
$password = "secret";
$dbname = "crochet_shop";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Select the database once connected
if (!$conn->select_db($dbname)) {
    die("Database selection failed: " . $conn->error);
}
?>
