<?php
// Ensure this file exists and contains the Docker connection logic
$servername = "db";
$username = "root";
$password = "secret";
$dbname = "crochet_shop"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ FATAL DB CONNECTION ERROR: " . $conn->connect_error);
} else {
    echo "✅ SUCCESS! Database connected and credentials verified.";
    $conn->close();
}
?>