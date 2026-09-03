<?php
// Vaihda oikeat
$servername = "db";
$username = "root";
$password = "root";
$dbname = "autot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Yhteys epäonnistui: " . $conn->connect_error);
}
?>
