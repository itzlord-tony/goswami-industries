<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "goswami_industry";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
?>
