<?php
include 'db_connect.php';
$sql = "CREATE TABLE IF NOT EXISTS subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50),
  last_name VARCHAR(50),
  email VARCHAR(100),
  phone VARCHAR(20),
  age INT
)";
$conn->query($sql);
echo "Table created.";
$conn->close();
?>
