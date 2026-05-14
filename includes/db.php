<?php

$host = getenv("mysql-oteg.railway.internal");
$user = getenv("root");
$pass = getenv("vnyyIoBcQaSLvfIqJVtRGOinRfcXpTjM");
$dbname = getenv("railway");
$port = getenv("3306");

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

?>